import hashlib
import json
import re
import sys
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile, ZipInfo


def json_bytes(data):
    return (json.dumps(data, ensure_ascii=False, indent=2) + '\n').encode()


def write(archive, name, data):
    entry = ZipInfo(name, (2026, 1, 1, 0, 0, 0))
    entry.compress_type = ZIP_DEFLATED
    entry.create_system = 3
    entry.external_attr = 0o100644 << 16
    archive.writestr(entry, data)


def overlay_zip(source, target, replacements, discard=lambda name: False):
    with ZipFile(source) as base, ZipFile(target, 'w') as archive:
        for entry in base.infolist():
            if entry.filename not in replacements and not discard(entry.filename):
                archive.writestr(entry, base.read(entry))
        for name, data in sorted(replacements.items()):
            write(archive, name, data)


def digests(files):
    return {name: hashlib.sha256(data).hexdigest() for name, data in sorted(files.items())}


root, full_base, update_base, overlay, target = (Path(name) for name in sys.argv[1:])
release = json.loads((root / 'updates/release.json').read_text(encoding='utf-8'))
version, shop_version = release['version'], release['shopVersion']
if not all(re.fullmatch(r'\d+\.\d+\.\d+', value) for value in [version, shop_version]):
    raise SystemExit('Invalid release version')

module_pattern = re.compile(r'admin/shop/(?:\.htaccess|[a-z][a-z0-9-]*\.(?:php|css))')
engine_pattern = re.compile(
    r'(?:app|admin)/[a-z][a-z0-9_-]*\.php|templates/[a-z][a-z0-9_-]*\.php'
    r'|templates/(?:css/site\.css|js/(?:site|admin)\.js)|router\.php|update\.php|\.htaccess'
)
docs_pattern = re.compile(r'docs/(?:content-api\.md|dezinfektori\.md|marketplace\.md|admin-faq(?:\.en)?\.md|help/(?:[a-z]{2}/)?[A-Za-z0-9-]+\.md)|admin/languages/zh\.php|README\.md')
files = {file.relative_to(overlay).as_posix(): file.read_bytes() for file in overlay.rglob('*') if file.is_file()}
if not all(path == 'shop/index.php' or any(regex.fullmatch(path)
           for regex in [module_pattern, engine_pattern, docs_pattern]) for path in files):
    raise SystemExit('Unexpected overlay path')
module = {path.removeprefix('admin/'): data for path, data in files.items() if module_pattern.fullmatch(path)}
engine = {path: data for path, data in files.items() if engine_pattern.fullmatch(path) and path != 'update.php'}
for path, expected in [('app/version.php', version), ('admin/shop/version.php', shop_version)]:
    if f"return '{expected}';".encode() not in files[path]:
        raise SystemExit(f'Unexpected version in {path}')

target.mkdir(parents=True, exist_ok=True)
overlay_zip(full_base, target / 'php-site.zip', {'php-site/' + path: data for path, data in files.items()},
            lambda name: name.startswith('php-site/shop/') and not name.endswith('/'))
with ZipFile(update_base) as base:
    manifest = json.loads(base.read('manifest.json'))
manifest['files'].update(digests(engine))
manifest['files'].pop('update.php', None)
manifest['files'] = dict(sorted(manifest['files'].items()))
manifest['version'] = version
overlay_zip(update_base, target / 'php-site-update.zip', {**engine, 'manifest.json': json_bytes(manifest)},
            lambda name: name == 'update.php')

with ZipFile(target / 'marketplace.zip', 'w') as archive:
    for name, data in sorted(module.items()):
        write(archive, name, data)
    write(archive, 'manifest.json', json_bytes({
        'module': 'shop', 'version': shop_version, 'layout': 2, 'files': digests(module)
    }))

presets = root / 'marketplace-integrations/presets.json'
if presets.is_file():
    modules = json.loads(presets.read_text(encoding='utf-8'))
    catalog = {}
    for addon_id, module in modules.items():
        driver, addon_version = module['driver'], module['version']
        if not all(re.fullmatch(r'[a-z0-9-]+', slug) for slug in [addon_id, driver]):
            raise SystemExit('Invalid integration identity')
        if not re.fullmatch(r'\d+\.\d+\.\d+', addon_version):
            raise SystemExit('Invalid integration version')
        directory = target / 'integrations' / addon_id
        directory.mkdir(parents=True, exist_ok=True)
        with ZipFile(directory / (addon_version + '.zip'), 'w') as addon:
            write(addon, 'integration.json', json_bytes({'id': addon_id, **module}))
        catalog[addon_id] = {'driver': driver, 'version': addon_version}
    (target / 'integrations/catalog.json').write_bytes(json_bytes({'schema': 1, 'modules': catalog}))

