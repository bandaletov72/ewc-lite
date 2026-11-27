# AI: 连接 ChatGPT

选择如何连接网站编辑器。可稍后在“AI”选项卡重新打开设置。

## 通过 Secure MCP Tunnel 连接 ChatGPT

1. 在 OpenAI Platform 为站点创建独立隧道和具有 Tunnels Read + Use 权限的密钥。此处只填写隧道 ID。
2. 安装 MCP 套件及 OpenAI 官方客户端。设置私有草稿目录，把密钥保存在服务的机密存储中。运行： `instance-from-cms.sh`.
3. 在 ChatGPT 添加自定义 MCP 服务器，填入站点名、Tunnel 模式及相同 ID。用 list_pages 验证。

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## 直接连接 EWC API（无需隧道）

自有客户端可通过 HTTPS 访问 /mcp，并使用 Authorization: Bearer 发送专用 EWC 令牌。默认只读。

ChatGPT 不接受该 EWC 令牌。直连私有编辑器需要兼容的 OAuth 2.1，本套件未实现。 [OAuth](https://developers.openai.com/plugins/build/auth)

## 验证编辑

`list_pages` → `draft_page` → `preview_draft` → 明确批准 → `publish`.

在管理后台打开页面并与预览比较。只读模式无法编辑。
