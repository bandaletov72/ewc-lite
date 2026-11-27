const assert = require("node:assert/strict");
const { spawn } = require("node:child_process");
const fs = require("node:fs");
const net = require("node:net");
const path = require("node:path");
const readline = require("node:readline");

async function freePort() {
  const socket = net.createServer();
  await new Promise((resolve, reject) => {
    socket.once("error", reject);
    socket.listen(0, "127.0.0.1", resolve);
  });
  const { port } = socket.address();
  await new Promise(resolve => socket.close(resolve));
  return port;
}

async function run() {
  const [phpExe, cleanCms, testDirectory] = process.argv.slice(2);
  assert.ok(phpExe && cleanCms && testDirectory,
    "Usage: node test/smoke.cjs PHP_BINARY CLEAN_EWC_ROOT SCRATCH_DIRECTORY");
  fs.mkdirSync(path.resolve(testDirectory), { recursive: true });
  const scratch = fs.mkdtempSync(path.join(path.resolve(testDirectory), "ewc-"));
  const root = path.join(scratch, "site");
  const state = path.join(scratch, "state");
  fs.cpSync(path.resolve(cleanCms), root, { recursive: true, errorOnExist: true });
  fs.mkdirSync(state);
  const php = path.resolve(phpExe);
  fs.writeFileSync(path.join(root, "admin", ".shop-enabled.php"),
    '<?php http_response_code(404); exit; __halt_compiler();\n{"enabled":true}\n');

  const port = await freePort();
  const base = 'http://127.0.0.1:' + port;
  const http = spawn(php, ["-S", "127.0.0.1:" + port, "router.php"], { cwd: root });
  const plugin = spawn(php, [path.resolve(__dirname, "../server.php"), root, state, base]);
  let logs = "";
  for (const process of [http, plugin]) {
    process.stderr.on("data", chunk => { logs += chunk; });
  }
  const pending = new Map();
  const lines = readline.createInterface({ input: plugin.stdout });
  lines.on("line", line => {
    const message = JSON.parse(line);
    pending.get(message.id)?.(message);
    pending.delete(message.id);
  });
  let id = 0;
  function rpc(method, params) {
    const requestId = ++id;
    return new Promise((resolve, reject) => {
      const timeout = setTimeout(() => reject(new Error("No reply: " + method)), 6000);
      pending.set(requestId, message => {
        clearTimeout(timeout);
        resolve(message);
      });
      plugin.stdin.write(JSON.stringify({ jsonrpc: "2.0", id: requestId, method, params }) + "\n");
    });
  }
  async function tool(name, args = {}, failure = null) {
    const { result, error } = await rpc("tools/call", { name, arguments: args });
    assert.equal(error, undefined);
    if (failure) {
      assert.equal(result.isError, true, name);
      assert.match(result.content[0].text, failure, name);
      return;
    }
    assert.equal(result.isError, undefined, name + ": " + result.content[0].text);
    return result.structuredContent;
  }
  async function get(url) {
    const response = await fetch(url);
    assert.equal(response.status, 200, url);
    return response.text();
  }
  const copy = (text, style = "text") => [{ style, text }];
  const design = { accent: "#315647", background: "#f4f1e8", surface: "#fffdf7", textColor: "#1d2b23" };
  try {
    for (let tries = 0; ; tries += 1) {
      try { await get(base); break; } catch (error) {
        if (tries >= 20) throw error;
        await new Promise(resolve => setTimeout(resolve, 100));
      }
    }
    const initial = await rpc("initialize", { protocolVersion: "2025-11-25" });
    assert.equal(initial.result.serverInfo.name, "ewc-lite");
    const modern = { "io.modelcontextprotocol/protocolVersion": "2026-07-28" };
    const discovery = await rpc("server/discover", { _meta: modern });
    assert.deepEqual(discovery.result.supportedVersions, ["2026-07-28"]);
    assert.equal(discovery.result.resultType, "complete");
    assert.equal((await rpc("tools/list", { _meta: modern })).result.resultType, "complete");
    assert.ok((await rpc("tools/list")).result.tools.some(item => item.name === "draft_shop_product"));
    assert.ok((await tool("list_pages")).pages.some(page => page.path === "/contacts/"));
    await tool("draft_page", { path: "/admin/page/", title: "No", blocks: copy("No") }, /valid logical/);
    await tool("draft_page", { path: "/missing/page/", title: "No", blocks: copy("No") }, /parent section/);
    await tool("draft_page", { path: "/catalog/", title: "No", blocks: copy("No") }, /reserved by the enabled Shop/);

    const page = await tool("draft_page", {
      path: "/contacts/workshop/", title: "Мастерская", blocks: copy("Скульптура и керамика [include] <script>probe()</script>")
    });
    assert.equal(fs.existsSync(path.join(root, "contacts", "workshop", "index.php")), false);
    assert.equal((await tool("preview_draft", { draftId: page.draftId })).proposed.title, "Мастерская");
    const pageReceipt = await tool("publish", { draftId: page.draftId });
    assert.deepEqual(await tool("publish", { draftId: page.draftId }), pageReceipt);
    let body = await get(pageReceipt.target);
    assert.match(body, /Скульптура и керамика/);
    assert.match(body, /&lt;script&gt;probe/);
    assert.doesNotMatch(body, /<script>probe/);
    const firstEdit = await tool("draft_add_content", { path: "/contacts/workshop/", blocks: copy("Первая правка") });
    const secondEdit = await tool("draft_add_content", { path: "/contacts/workshop/", blocks: copy("Вторая правка") });
    await tool("publish", { draftId: firstEdit.draftId });
    await tool("publish", { draftId: secondEdit.draftId }, /target changed/);
    body = await get(pageReceipt.target);
    assert.match(body, /Первая правка/);
    assert.doesNotMatch(body, /Вторая правка/);

    const category = await tool("draft_shop_category", { name: "Керамика", slug: "ceramics" });
    const categoryReceipt = await tool("publish", { draftId: category.draftId });
    await get(categoryReceipt.target);
    const categories = await tool("list_shop_categories");
    const categoryId = categories.categories.find(item => item.name === "Керамика").id;
    const product = await tool("draft_shop_product", {
      categoryId, slug: "oat-bowl", title: "Керамическая миска", summary: "Ручная работа",
      sku: "CER-10", price: "1290.50", stock: 3, type: "physical", active: true,
      blocks: copy("Глазурь и обжиг")
    });
    const productReceipt = await tool("publish", { draftId: product.draftId });
    assert.deepEqual(await tool("publish", { draftId: product.draftId }), productReceipt);
    body = await get(productReceipt.target);
    assert.match(body, /Керамическая миска/);
    assert.match(body, /Глазурь и обжиг/);
    await tool("draft_shop_product", {
      categoryId, slug: "oat-bowl-other", title: "Дубликат", sku: "CER-10",
      price: "100", stock: 1, type: "physical", active: true
    }, /SKU is already in use/);

    const site = await tool("draft_site", {
      ...design, themeId: "mcp-smoke", name: "Тихая мастерская", font: "serif",
      pages: [
        { path: "/", title: "Тихая мастерская", blocks: copy("Коллекция ручной работы", "hero") },
        { path: "/collection/", title: "Коллекция", listChildren: true, blocks: copy("Предметы для дома") }
      ],
      products: [{ path: "/collection/cup/", title: "Стакан", sku: "CER-20", price: "550", blocks: copy("Фарфор") }]
    });
    await tool("publish", { draftId: site.draftId });
    assert.equal((await tool("list_pages")).activeTheme, "system");
    assert.doesNotMatch(await get(base), /Коллекция ручной работы/);
    const activation = await tool("draft_activate_theme", { themeId: "mcp-smoke" });
    assert.equal(activation.activatedContent, "theme");
    await tool("publish", { draftId: activation.draftId });
    assert.equal((await tool("list_pages")).activeTheme, "mcp-smoke");
    body = await get(base);
    assert.match(body, /Коллекция ручной работы/);
    assert.match(body, /templates\/themes\/mcp-smoke\/css\/theme\.css/);
    assert.match(await get(base + "/collection/cup/"), /550[.,]00/);
    const ownPage = await tool("draft_page", { path: "/collection/more/", title: "Новинка", blocks: copy("Новый текст") });
    await tool("publish", { draftId: ownPage.draftId });
    assert.match(await get(ownPage.target), /Новый текст/);
    const restyle = await tool("draft_restyle", { ...design, accent: "#353c78", radius: 24 });
    await tool("publish", { draftId: restyle.draftId });
    const css = await get(base + "/templates/themes/mcp-smoke/css/theme.css");
    assert.match(css, /#353c78/);
    const restore = await tool("draft_activate_theme", { themeId: "system" });
    await tool("publish", { draftId: restore.draftId });
    assert.equal((await tool("list_pages")).activeTheme, "system");
    assert.ok((await tool("list_pages")).pages.some(item => item.path === "/contacts/workshop/"));
    assert.doesNotMatch(await get(base), /Коллекция ручной работы/);
    console.log("MCP, CMS pages, revision conflicts, shop, theme installation and switching passed.");
  } catch (error) {
    console.error(logs.slice(-5000));
    throw error;
  } finally {
    plugin.stdin.end();
    plugin.kill();
    http.kill();
  }
}

run().catch(error => { console.error(error); process.exitCode = 1; });
