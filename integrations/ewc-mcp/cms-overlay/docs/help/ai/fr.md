# IA: Connecter ChatGPT

Choisissez comment connecter l’éditeur. Vous pourrez revenir depuis l’onglet IA.

## ChatGPT via Secure MCP Tunnel

1. Sur OpenAI Platform, créez un tunnel par site et une clé Tunnels Read + Use. Saisissez uniquement l’ID du tunnel ici.
2. Installez le paquet MCP et le client officiel OpenAI. Définissez un dossier de brouillons privé et placez la clé dans les secrets du service. Lancez : `instance-from-cms.sh`.
3. Dans ChatGPT, ajoutez un serveur MCP personnalisé avec le nom du site, le mode Tunnel et le même ID. Testez avec list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## API EWC directe (sans tunnel)

Pour un client propre, exposez /mcp en HTTPS et utilisez un jeton EWC dédié dans Authorization: Bearer. Lecture seule par défaut.

Ce jeton EWC ne peut pas être saisi dans ChatGPT. L’accès direct à l’éditeur privé exige OAuth 2.1 compatible, non inclus. [OAuth](https://developers.openai.com/plugins/build/auth)

## Vérifier la modification

`list_pages` → `draft_page` → `preview_draft` → confirmation explicite → `publish`.

Ouvrez la page dans l’administration et comparez l’aperçu. Le mode lecture seule interdit les modifications.
