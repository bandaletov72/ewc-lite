"""Fresh install, skip/reopen, auth/CSRF, config persistence and direct MCP. No external calls."""
from contextlib import contextmanager
from http.cookiejar import CookieJar
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from html import unescape
from pathlib import Path
import json
import os
import re
import secrets
import shutil
import socket
import ssl
import threading
import subprocess
import sys
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

php, source = (str(Path(path).resolve()) for path in sys.argv[1:3])
kit = Path(__file__).resolve().parents[1]


def request(client, url, fields=None, body=None, headers=None):
    payload = urllib.parse.urlencode(fields).encode() if fields is not None else body
    r = urllib.request.Request(url, data=payload, headers=headers or {})
    try:
        response = client.open(r, timeout=3)
    except urllib.error.HTTPError as error:
        response = error
    with response:
        return response.status, response.geturl(), response.read().decode()


def field(html, name):
    match = re.search(r'name="' + name + r'" value="([^"]+)"', html)
    assert match, (name, html[:400])
    return unescape(match.group(1))


@contextmanager
def serve(directory, router, env=None):
    with socket.socket() as s:
        s.bind(('127.0.0.1', 0))
        port = s.getsockname()[1]
    base = f'http://127.0.0.1:{port}'
    with tempfile.TemporaryFile() as log:
        proc = subprocess.Popen([php, '-S', f'127.0.0.1:{port}', str(router)],
                                cwd=directory, stdout=log, stderr=log, env=env)
        try:
            for _ in range(100):
                if proc.poll() is not None:
                    raise RuntimeError('PHP exited')
                try:
                    with socket.create_connection(('127.0.0.1', port), timeout=.2):
                        break
                except OSError:
                    time.sleep(.015)
            else:
                raise RuntimeError('PHP did not start')
            yield base
        except BaseException:
            log.seek(0)
            print(log.read().decode(), file=sys.stderr)
            raise
        finally:
            proc.terminate()
            proc.wait(timeout=5)


def setup(browser, admin):
    html = request(browser, admin)[2]
    pw = secrets.token_hex(12)
    result = request(browser, admin, dict(action='setup', csrf=field(html, 'csrf'),
                   username='tester', password=pw, confirmation=pw))
    assert result[0] == 200 and result[1].endswith('?ai=1') and 'skip-chatgpt' in result[2], result
    assert 'Secure MCP Tunnel' in result[2] and 'OAuth 2.1' in result[2]
    return result[2]


with tempfile.TemporaryDirectory(prefix='ewc-wizard-test-') as temp:
    tmp = Path(temp)
    site = tmp / 'site'
    shutil.copytree(source, site)
    (site / 'admin/.regional.php').write_text(
        '<?php http_response_code(404); exit; __halt_compiler();\n{"languages":["ru","en"]}')
    state = tmp / 'private-drafts'
    state.mkdir(mode=0o700)
    anonymous = urllib.request.build_opener()
    browser = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(CookieJar()))
    config = site / 'admin/.ai-chatgpt.php'

    with serve(site, site / 'router.php') as base:
        admin = base + '/admin/'
        ai = admin + '?ai=1'
        assert 'save-chatgpt' not in request(anonymous, ai)[2]
        wiz = setup(browser, admin)
        code, where, html = request(browser, ai, dict(action='skip-chatgpt', csrf=field(wiz, 'csrf')))
        assert code == 200 and where.endswith('?install=1') and not config.exists()
        code, where, html = request(browser, where, dict(action='skip-installation', csrf=field(html, 'csrf')))
        assert code == 200 and where == admin and 'ai=1' in html, where
        html = request(browser, ai)[2]
        assert 'skip-chatgpt' not in html and 'save-chatgpt' in html
        csrf, rev = [field(html, name) for name in ('csrf', 'revision')]
        draft = dict(action='save-chatgpt', csrf=csrf, revision=rev,
                     site_url='https://Example.com/demo', tunnel_id='tunnel_' + 'A'*32,
                     mode='tunnel', secret='should-never-be-stored')
        assert request(browser, ai, dict(draft, csrf='wrong'))[0] == 403 and not config.exists()
        for bad in ('https://user@example.com/', 'http://outside.example/', 'https://example.com/?x=1',
                    'https://example.com/../', 'https://example.com/\\other'):
            assert not config.exists()
            assert 'Введите HTTPS' in request(browser, ai, dict(draft, site_url=bad))[2]
        assert 'Проверьте идентификатор' in request(browser, ai, dict(draft, tunnel_id='bad'))[2]
        assert not config.exists()
        assert 'Настройки сохранены' in request(browser, ai, draft)[2]
        assert request(anonymous, admin + '.ai-chatgpt.php')[0] == 404
        assert 'should-never-be-stored' not in config.read_text()
        cfg = json.loads(config.read_text().split('\n', 1)[1])
        assert cfg == {'url': 'https://example.com/demo/', 'tunnel': draft['tunnel_id'], 'mode': 'tunnel'}
        command = subprocess.check_output([php, str(kit / 'instance-config.php'), str(site)], text=True)
        assert command.strip().split('\t') == [cfg['tunnel'], cfg['url']]
        assert 'Настройки изменились' in request(browser, ai, draft)[2]
        html = request(browser, ai + '&ui_lang=en')[2]
        assert 'Install the plugin and use the editor' in html
        assert 'Connect ChatGPT' in request(browser, admin + '?help=1')[2]
        direct = dict(draft, mode='direct', revision=field(html, 'revision'))
        assert 'Settings saved' in request(browser, ai, direct)[2]
        assert json.loads(config.read_text().split('\n', 1)[1]) == {
            'url': cfg['url'], 'mode':'direct', 'tunnel':''}
        refused = subprocess.run([php, str(kit / 'instance-config.php'), str(site)], capture_output=True)
        assert refused.returncode != 0

    other = tmp / 'site-b'
    shutil.copytree(source, other)
    with serve(other, other / 'router.php') as base:
        other_browser = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(CookieJar()))
        admin = base + '/admin/'
        wiz = setup(other_browser, admin)
        code, where, html = request(other_browser, admin + '?ai=1',
            dict(draft, csrf=field(wiz, 'csrf'), revision=field(wiz, 'revision')))
        assert where.endswith('?install=1') and (other / 'admin/.ai-chatgpt.php').is_file()
        assert request(other_browser, admin + '?ai=1')[2].find('skip-chatgpt') == -1

    # PHP built-in test harness only: emulate the trusted HTTPS=on FastCGI parameter.
    # No environment-dependent bypass exists in direct.php.
    direct_host = 'mcp.example.com'
    route = tmp / 'test-router.php'
    route.write_text("<?php\nif (getenv('TEST_TLS') === '1') $_SERVER['HTTPS'] = 'on';\n"
                     + "require " + repr(str(kit / 'direct.php')) + ";\n")
    token = secrets.token_urlsafe(48)
    env = dict(os.environ, EWC_MCP_SITE_ROOT=str(site), EWC_MCP_STATE_DIR=str(state),
               EWC_MCP_DIRECT_HOST=direct_host, EWC_MCP_DIRECT_TOKEN=token, TEST_TLS='1')
    headers = {'Host':direct_host, 'Content-Type':'application/json', 'Authorization':'Bearer '+token}
    def rpc(url, method, params=None, extra=None):
        packet = json.dumps({'jsonrpc':'2.0','id':1,'method':method,'params':params or {}}).encode()
        return request(anonymous, url + '/mcp', body=packet, headers=dict(headers, **(extra or {})))

    for mode, opts, total in [('insecure', {'TEST_TLS':'0'}, 0), ('read', {}, 4),
                              ('drafts', {'EWC_MCP_DIRECT_WRITES':'1'}, 13),
                              ('full', {'EWC_MCP_DIRECT_WRITES':'1','EWC_MCP_DIRECT_PUBLISH':'1'}, 14)]:
        with serve(tmp, route, dict(env, **opts)) as endpoint:
            code, _, body = rpc(endpoint, 'tools/list')
            if mode == 'insecure':
                assert code == 404
                continue
            assert code == 200 and len(json.loads(body)['result']['tools']) == total, (mode, code, body)
            assert rpc(endpoint, 'tools/list', extra={'Authorization':''})[0] == 401
            assert rpc(endpoint, 'tools/list', extra={'Host':'other.example.com'})[0] == 403
            assert rpc(endpoint, 'tools/list', extra={'Origin':'https://other.example.com'})[0] == 403
            assert request(anonymous, endpoint + '/mcp?key=bad', headers=headers)[0] == 404
            status, _, body = rpc(endpoint, 'tools/call', {'name':'list_pages'})
            assert status == 200 and '/contacts/' in body and '/privacy/' in body, body
            if mode != 'full':
                assert 'error' in json.loads(rpc(endpoint, 'tools/call', {'name':'publish'})[2])
    class TestTlsProxy(BaseHTTPRequestHandler):
        target = ''
        def log_message(self, *args):
            pass
        def do_POST(self):
            data = self.rfile.read(int(self.headers['Content-Length']))
            code, _, body = request(anonymous, self.target + self.path, body=data,
                                    headers=dict(self.headers.items()))
            self.send_response(code)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(body.encode())

    # TLS is real here. Only the handoff from the trusted HTTPS proxy to PHP-FPM is emulated.
    cert, key = tmp / 'test.crt', tmp / 'test.key'
    subprocess.run(['openssl', 'req', '-x509', '-newkey', 'rsa:2048', '-nodes',
                    '-subj', '/CN=localhost', '-days', '1', '-out', str(cert), '-keyout', str(key)],
                   stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, check=True)
    with ThreadingHTTPServer(('127.0.0.1', 0), TestTlsProxy) as tls_proxy:
        public_host = 'localhost:' + str(tls_proxy.server_address[1])
        context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
        context.load_cert_chain(cert, key)
        tls_proxy.socket = context.wrap_socket(tls_proxy.socket, server_side=True)
        with serve(tmp, route, dict(env, EWC_MCP_DIRECT_HOST=public_host)) as endpoint:
            TestTlsProxy.target = endpoint
            loop = threading.Thread(target=tls_proxy.serve_forever, daemon=True)
            loop.start()
            try:
                args = [php, '-d', 'openssl.cafile=' + str(cert), str(kit / 'direct-client.php'),
                        'https://' + public_host + '/mcp']
                pages = subprocess.check_output(args, env=dict(os.environ, EWC_MCP_DIRECT_TOKEN=token), text=True)
                assert '/contacts/' in pages and '/privacy/' in pages
                refused = subprocess.run(args, env=dict(os.environ, EWC_MCP_DIRECT_TOKEN=secrets.token_urlsafe(48)),
                                         capture_output=True, text=True)
                assert refused.returncode != 0 and 'HTTP 401' in refused.stderr
            finally:
                tls_proxy.shutdown()
                loop.join()
print('OK: onboarding, auth, validation, locale, direct MCP permissions and PHP client over verified local TLS')
