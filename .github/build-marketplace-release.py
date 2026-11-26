import hashlib
import json
import re
import sys
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile, ZipInfo


def digest(data):
    return hashlib.sha256(data).hexdigest()


def write_entry(archive, name, data):
    entry = ZipInfo(name, (2026, 1, 1, 0, 0, 0))
    entry.compress_type = ZIP_DEFLATED
    entry.create_system = 3
    entry.external_attr = 0o100644 << 16
    archive.writestr(entry, data)


def overlay_zip(source, target, replacements):
    with ZipFile(source) as base, ZipFile(target, 'w') as archive:
        for entry in base.infolist():
            if entry.filename not in replacements:
                archive.writestr(entry, base.read(entry))
        for name, data in sorted(replacements.items()):
            write_entry(archive, name, data)


def json_bytes(data):
    return json.dumps(data, ensure_ascii=False, indent=2).encode('utf-8')


root, full_base, update_base, overlay, target = (Path(name) for name in sys.argv[1:])
release = json.loads(root.joinpath('updates/release.json').read_text(encoding='utf-8'))
version, shop_version = release['version'], release['shopVersion']
for value in (version, shop_version):
    if not re.fullmatch(r'\d+\.\d+\.\d+', value):
        raise SystemExit('Invalid release version')

files = {path.relative_to(overlay).as_posix(): path.read_bytes() for path in overlay.rglob('*') if path.is_file()}
module_pattern = re.compile(r'shop/(?:\.htaccess|[a-z][a-z0-9-]*\.(?:php|css))')
engine_pattern = re.compile(r'(?:app|admin)/[a-z][a-z0-9-]*\.php|templates/(?:layout\.php|css/site\.css|js/admin\.js)|router\.php|update\.php')
docs_pattern = re.compile(r'docs/(?:marketplace\.md|help/[a-z]{2}\.md)')
if not all(module_pattern.fullmatch(name) or engine_pattern.fullmatch(name) or docs_pattern.fullmatch(name) for name in files):
    raise SystemExit('Unexpected file in release overlay')
module = {name: data for name, data in files.items() if module_pattern.fullmatch(name)}
engine = {name: data for name, data in files.items() if engine_pattern.fullmatch(name)}
for name, expected in [('app/version.php', version), ('shop/version.php', shop_version)]:
    if f"return '{expected}';".encode() not in files[name]:
        raise SystemExit(f'Incorrect version in {name}')

target.mkdir(parents=True, exist_ok=True)
overlay_zip(full_base, target / 'php-site.zip', {'php-site/' + name: data for name, data in files.items()})
with ZipFile(update_base) as base:
    manifest = json.loads(base.read('manifest.json'))
manifest['version'] = version
manifest['files'].update({name: digest(data) for name, data in engine.items()})
manifest['files'] = dict(sorted(manifest['files'].items()))
overlay_zip(update_base, target / 'php-site-update.zip', {**engine, 'manifest.json': json_bytes(manifest)})

with ZipFile(target / 'marketplace.zip', 'w') as archive:
    for name, data in sorted(module.items()):
        write_entry(archive, name, data)
    write_entry(archive, 'manifest.json', json_bytes({'module': 'shop', 'version': shop_version,
                                                     'files': {name: digest(data) for name, data in sorted(module.items())}}))
