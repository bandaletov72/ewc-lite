# KI: ChatGPT verbinden

Wähle die Verbindung zum Editor. Die Einrichtung ist später im Tab „KI“ verfügbar.

## ChatGPT über Secure MCP Tunnel

1. Erstelle in OpenAI Platform einen Tunnel für diese Website und einen Schlüssel mit Tunnels Read + Use. Gib hier nur die Tunnel-ID ein.
2. Installiere das MCP-Paket und den offiziellen OpenAI-Client. Lege ein privates Entwurfsverzeichnis an und hinterlege den Schlüssel im Geheimnisspeicher des Dienstes. Starte: `instance-from-cms.sh`.
3. Füge in ChatGPT einen eigenen MCP-Server mit Website-Name, Tunnelmodus und derselben Tunnel-ID hinzu. Teste mit list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## Direkte EWC-API (ohne Tunnel)

Für eigene Clients stelle /mcp per HTTPS bereit und nutze ein separates EWC-Token im Header Authorization: Bearer. Standardmäßig nur Lesezugriff.

Das EWC-Token kann nicht in ChatGPT eingegeben werden. Der direkte Zugang zum privaten Editor benötigt kompatibles OAuth 2.1; es ist nicht enthalten. [OAuth](https://developers.openai.com/plugins/build/auth)

## Bearbeitung prüfen

`list_pages` → `draft_page` → `preview_draft` → ausdrückliche Bestätigung → `publish`.

Öffne die Seite im Adminbereich und vergleiche die Vorschau. Im Lesemodus sind keine Änderungen möglich.
