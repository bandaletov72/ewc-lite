#!/usr/bin/env python3
import json, os, secrets, shutil, socket, subprocess, sys, tempfile, time
from contextlib import ExitStack
from pathlib import Path
from urllib.error import HTTPError
from urllib.request import Request, urlopen

php, clean_site = map(lambda p: Path(p).resolve(), sys.argv[1:3])
root = Path(__file__).resolve().parents[1]

def free_port():
    with socket.socket() as s:
        s.bind(('127.0.0.1', 0))
        return s.getsockname()[1]

def instance(stack, path, writes):
    site, state = path/'site', path/'state'
    shutil.copytree(clean_site, site)
    state.mkdir(mode=0o700)
    port = free_port()
    url, token = f'http://127.0.0.1:{port}', secrets.token_hex(32)
    env = dict(os.environ, EWC_MCP_SITE_ROOT=str(site), EWC_MCP_STATE_DIR=str(state),
               EWC_MCP_SITE_URL=url, EWC_MCP_HTTP_TOKEN=token, EWC_MCP_TEST_WRITES=str(int(writes)))
    proc = subprocess.Popen([str(php),'-S',f'127.0.0.1:{port}',str(root/'http.php')],
                            env=env, cwd=root, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    def cleanup():
        proc.terminate()
        proc.wait(timeout=5)
    stack.callback(cleanup)
    for _ in range(100):
        if proc.poll() is not None:
            raise RuntimeError('PHP HTTP server exited')
        try:
            with socket.create_connection(('127.0.0.1', port), 0.2):
                break
        except OSError:
            time.sleep(0.025)
    else:
        raise RuntimeError('HTTP endpoint did not start')
    return url, token, site, state

def raw(inst, body=None, token=True, suffix='/mcp', **extra):
    url, key, _, _ = inst
    headers = dict(Accept='application/json, text/event-stream')
    if body is not None:
        headers['Content-Type'] = 'application/json'
    if token:
        headers['Authorization'] = 'Bearer ' + (key if token is True else token)
    headers.update(extra)
    try:
        with urlopen(Request(url+suffix, body, headers), timeout=5) as response:
            return response.status, response.read()
    except HTTPError as err:
        return err.code, err.read()

def rpc(i, method, params=None, *, ident=1, expect=200, headers=None):
    msg = dict(jsonrpc='2.0', method=method, params=params or {})
    if ident is not None:
        msg['id'] = ident
    status, body = raw(i, json.dumps(msg).encode(), **(headers or {}))
    assert status == expect, (status, body)
    return json.loads(body) if body else None

def result(i, method, params=None, **kwargs):
    msg = rpc(i, method, params, **kwargs)
    assert not msg.get('error'), msg
    return msg['result']

def call(i, name, args=None, **kwargs):
    out = result(i,'tools/call',dict(name=name,arguments=args or {}),**kwargs)
    assert not out.get('isError'), out
    return out['structuredContent']

with tempfile.TemporaryDirectory(prefix='ewc-http-') as tmp, ExitStack() as stack:
    base = Path(tmp)
    a, b = instance(stack,base/'a',True), instance(stack,base/'b',False)
    msg = b'{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
    assert raw(a,msg,token=False)[0] == 401
    assert raw(a,msg,token=b[1])[0] == 401
    assert raw(a,msg,Origin='https://invalid.example')[0] == 403
    assert raw(a,msg,Host='invalid.example')[0] == 403
    assert raw(a,msg,suffix='/src/tools.php')[0] == 404
    assert raw(a)[0] == 405
    assert raw(a,b'{broken')[0] == 400
    assert raw(a,msg,**{'MCP-Protocol-Version':'0000-00-00'})[0] == 400

    standard = {'MCP-Protocol-Version':'2025-11-25'}
    modern = {'MCP-Protocol-Version':'2026-07-28'}
    assert result(a,'initialize',{'protocolVersion':'2025-11-25'})['serverInfo']['name']=='ewc-lite'
    assert rpc(a,'notifications/initialized',ident=None,expect=202,headers=standard) is None
    assert result(a,'server/discover',headers=modern)['supportedVersions']==['2026-07-28']
    tools = result(a,'tools/list',headers=standard)['tools']
    wire = subprocess.run([str(php),str(root/'server.php'),str(a[2]),str(a[3]),a[0]],
                          input=msg+b'\n',capture_output=True,check=True)
    assert tools == json.loads(wire.stdout)['result']['tools']
    assert tools == result(a,'tools/list',headers=modern)['tools']
    assert len(tools) == 14
    assert all(t['annotations']['readOnlyHint'] for t in result(b,'tools/list')['tools'])
    assert rpc(b,'tools/call',{'name':'publish'})['error']['code'] == -32602
    assert [p['path'] for p in call(a,'list_pages',headers=modern)['pages']] == ['/','/contacts/','/privacy/']
    assert [p['path'] for p in call(b,'list_pages')['pages']] == ['/','/contacts/','/privacy/']
    path = '/contacts/http-isolation-check/'
    draft = call(a,'draft_page',{'path':path,'title':'HTTP test','blocks':[{'style':'text','text':'Test only'}]})
    draft_id = draft['draftId']
    assert call(a,'preview_draft',{'draftId':draft_id})['proposed']['title'] == 'HTTP test'
    assert result(b,'tools/call',{'name':'preview_draft','arguments':{'draftId':draft_id}})['isError']
    assert not (a[2]/'contacts/http-isolation-check/index.php').exists()
    call(a,'publish',{'draftId':draft_id})
    assert path in [p['path'] for p in call(a,'list_pages')['pages']]
    assert path not in [p['path'] for p in call(b,'list_pages')['pages']]
    assert (a[2]/'contacts/http-isolation-check/index.php').is_file()
    assert not (b[2]/'contacts/http-isolation-check/index.php').exists()
print('OK: 14 identical tool definitions; stdio/HTTP; auth, guards, draft/preview/publish, two-install isolation.')
