# AI: Connect ChatGPT

Choose how to connect to this site's editor. You can return later from the AI tab.

## ChatGPT via Secure MCP Tunnel

1. In OpenAI Platform create a tunnel for this site and a restricted Tunnels Read + Use key for its runtime. Enter only the Tunnel ID here.
2. Install the MCP package and official OpenAI client on the host. Set a private drafts directory and inject the key from your service’s secret store. Run: `instance-from-cms.sh`.
3. In ChatGPT add a custom MCP server: site name, Tunnel mode, and the same Tunnel ID. Test with list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## Direct EWC API (no tunnel)

For your own client, expose /mcp over HTTPS and configure a dedicated EWC token. Send it in the Authorization: Bearer header. Access is read-only by default.

The EWC token cannot be entered in ChatGPT. For a direct connection to a private editor, configure compatible OAuth 2.1. OAuth is not included in this package. [OAuth](https://developers.openai.com/plugins/build/auth)

## Check editing

`list_pages` → `draft_page` → `preview_draft` → explicit approval → `publish`.

Open the page in the admin area and compare the preview. Read-only mode does not permit edits.
