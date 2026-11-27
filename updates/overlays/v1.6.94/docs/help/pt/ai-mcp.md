# IA: Ligar o ChatGPT

Escolha como ligar o editor. Pode voltar mais tarde pelo separador IA.

## ChatGPT via Secure MCP Tunnel

1. Na OpenAI Platform, crie um túnel por site e uma chave Tunnels Read + Use. Indique aqui apenas o ID.
2. Instale o pacote MCP e o cliente oficial OpenAI. Defina uma pasta privada de rascunhos e guarde a chave nos segredos do serviço. Execute: `instance-from-cms.sh`.
3. No ChatGPT, adicione um servidor MCP personalizado com o nome do site, modo Tunnel e o mesmo ID. Teste com list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## API direta EWC (sem túnel)

Para um cliente próprio, publique /mcp por HTTPS e use um token EWC dedicado em Authorization: Bearer. O acesso é apenas de leitura por predefinição.

O ChatGPT não aceita este token EWC. O acesso direto ao editor privado exige OAuth 2.1 compatível, não incluído. [OAuth](https://developers.openai.com/plugins/build/auth)

## Testar a edição

`list_pages` → `draft_page` → `preview_draft` → confirmação explícita → `publish`.

Abra a página na administração e compare a pré-visualização. O modo de leitura não permite alterações.
