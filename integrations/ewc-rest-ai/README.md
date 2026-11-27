# EWC Lite REST client for Gemini and Claude

This Node.js client prepares page, infoblock and marketplace edits through the Gemini API or the Claude Messages API. It prints the preview returned by EWC and asks for explicit terminal confirmation before saving.

1. Run EWC over HTTPS. Open **AI → REST API**, enable **GPT Actions**, and issue an EWC key. Enable the marketplace permission if you want to manage products. Choose write access for previews and saves.
2. Install Node.js 20 or later and download `editor.mjs`. Set `EWC_SITE_URL` to your EWC installation URL, including the installation directory when applicable. Set `EWC_TOKEN` to the EWC key and `AI_MODEL` to a tool-capable model ID from your provider.
3. Set `GEMINI_API_KEY` or `ANTHROPIC_API_KEY` in the terminal environment. Run `node editor.mjs gemini` or `node editor.mjs claude` and enter your request.
4. Review the actual server diff. Type `APPLY` to save that proposal. A stale or expired preview requires a new request.

The client authenticates to EWC with its own key and sends relevant content to the selected provider for processing. The provider never receives the EWC key or preview token. The client does not expose a save tool to the model.

## Подключение

В **ИИ → REST API** включите GPT Actions и выпустите ключ. При необходимости разрешите работу с маркетплейсом. Запустите клиент с Node.js 20 или новее. В окружении задайте `EWC_SITE_URL`, `EWC_TOKEN`, `AI_MODEL` и ключ выбранного провайдера. После запроса проверьте предпросмотр и введите `APPLY`, чтобы сохранить показанные правки.

Примеры запросов подходят для страниц и таблиц. «Найди на странице уничтожения клопов прайс и предложи 20 услуг для добавления» или «Повышай цены выбранных вариантов на 10%, сохранив текущие цены как старые». Используйте конкретные товары или артикулы для перерасчёта.
