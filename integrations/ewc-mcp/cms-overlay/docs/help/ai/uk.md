# ШІ: Підключити ChatGPT

Оберіть спосіб підключення до редактора. Пізніше можна повернутися через вкладку «ШІ».

## ChatGPT через Secure MCP Tunnel

1. В OpenAI Platform створіть окремий тунель для сайту та ключ Tunnels Read + Use. Введіть тут лише ID тунелю.
2. Встановіть пакет MCP і офіційний клієнт OpenAI. Задайте приватну теку чернеток і ключ у секретах служби. Запустіть: `instance-from-cms.sh`.
3. У ChatGPT додайте власний MCP-сервер: назва сайту, режим Tunnel і той самий ID. Перевірте через list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## Прямий API EWC (без тунелю)

Для власного клієнта опублікуйте /mcp через HTTPS і передавайте окремий токен EWC в Authorization: Bearer. Типово лише читання.

Токен EWC не можна ввести в ChatGPT. Прямий доступ до закритого редактора потребує сумісного OAuth 2.1, якого немає в пакеті. [OAuth](https://developers.openai.com/plugins/build/auth)

## Перевірити редагування

`list_pages` → `draft_page` → `preview_draft` → явне підтвердження → `publish`.

Відкрийте сторінку в адмінці й порівняйте попередній перегляд. Режим читання не дозволяє змін.
