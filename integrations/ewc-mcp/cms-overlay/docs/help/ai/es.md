# IA: Conectar ChatGPT

Elige cómo conectar el editor. Puedes volver desde la pestaña IA.

## ChatGPT mediante Secure MCP Tunnel

1. Crea un túnel para este sitio en OpenAI Platform y una clave Tunnels Read + Use. Introduce aquí solo el ID.
2. Instala el paquete MCP y el cliente oficial de OpenAI. Configura un directorio privado de borradores y guarda la clave en los secretos del servicio. Ejecuta: `instance-from-cms.sh`.
3. Añade un servidor MCP personalizado en ChatGPT con el nombre del sitio, modo túnel y el mismo ID. Prueba con list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## API directa de EWC (sin túnel)

Para un cliente propio, publica /mcp por HTTPS y usa un token EWC dedicado en Authorization: Bearer. Por defecto, solo lectura.

ChatGPT no admite ese token EWC. La conexión directa al editor privado requiere OAuth 2.1 compatible, no incluido. [OAuth](https://developers.openai.com/plugins/build/auth)

## Probar la edición

`list_pages` → `draft_page` → `preview_draft` → confirmación explícita → `publish`.

Abre la página en el panel y compara la vista previa. El modo de solo lectura no permite editar.
