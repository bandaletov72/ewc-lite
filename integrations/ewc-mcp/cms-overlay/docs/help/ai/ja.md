# AI: ChatGPT を接続

サイトエディターへの接続方法を選択します。後から「AI」タブで設定できます。

## Secure MCP Tunnel 経由の ChatGPT

1. OpenAI Platform でサイトごとのトンネルと Tunnels Read + Use キーを作成します。ここにはトンネル ID のみ入力します。
2. MCP パッケージと OpenAI 公式クライアントをインストールします。非公開の下書きディレクトリを設定し、キーをサービスのシークレットに保存します。実行: `instance-from-cms.sh`.
3. ChatGPT でサイト名、Tunnel モード、同じ ID を設定してカスタム MCP サーバーを追加します。list_pages で確認します。

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## EWC API へ直接接続（トンネルなし）

独自クライアントでは /mcp を HTTPS で公開し、EWC 専用トークンを Authorization: Bearer で送信します。既定では読み取り専用です。

ChatGPT に EWC トークンは入力できません。非公開エディターに直接接続するには、同梱されていない互換 OAuth 2.1 が必要です。 [OAuth](https://developers.openai.com/plugins/build/auth)

## 編集を確認

`list_pages` → `draft_page` → `preview_draft` → 明示的な承認 → `publish`.

管理画面でページを開き、プレビューと比較します。読み取り専用では編集できません。
