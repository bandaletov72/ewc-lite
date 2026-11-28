# REST API для страниц EWC

В админке откройте **ИИ → REST API** и выпустите ключ. По умолчанию API выключен. Выберите «Чтение и правка страниц», если клиенту нужно сохранять правки. Ключ даёт доступ к страницам активного сайта, включая неопубликованные, и отображается при выдаче один раз. Храните его в секретах клиента и передавайте в заголовке `Authorization: Bearer <ключ>`. Замена ключа сразу отзывает предыдущий.

| Метод и адрес | Действие |
| --- | --- |
| `GET /api/v1/pages?page=1&per_page=25` | Список страниц, включая переводы. До 100 записей на страницу. |
| `GET /api/v1/pages/index.php` | Метаданные и HTML главной страницы. Для других страниц используйте `resource` из списка. |
| `PATCH /api/v1/pages/index.php` | Сохранение переданных полей. Укажите `Content-Type: application/json` и перенесите заголовок `ETag` из GET в `If-Match`, включая кавычки. |

Например, отправьте тело `{"title":"Услуги","html":"<p>Новое описание</p>"}`. Поля `title`, `heading`, `description`, `keywords` и `html` принимают строки. Поле `title` обязательно для страницы и при передаче должно быть непустым. Правки публикуются с учётом существующего статуса страницы. Ответ `412 page_has_changed` означает, что нужно заново прочитать страницу и совместить изменения.

При установке в папку адрес будет, например, `https://example.com/test/api/v1/pages`. Для Apache маршрут входит в штатный `.htaccess`. При своей конфигурации веб-сервера направьте этот путь в `app/page-route.php`, передайте PHP оригинальный URI, `Authorization`, корректный `SCRIPT_NAME` с префиксом установки и серверный признак HTTPS. Для разработки подходит встроенный PHP-сервер на loopback с `router.php`. Токен привязан к установке CMS и действует для её активного набора страниц.

## Создание, перенос и удаление страниц

Для этих операций включите GPT Actions в «ИИ → REST API» и выдайте ключ с правом правки. Отправьте JSON в `POST /api/v1/pages/page-lifecycle-preview`, проверьте ответ и после подтверждения отправьте тот же запрос с возвращённым `preview_token` в `POST /api/v1/pages/page-lifecycle-apply`.

- Создание: `{"operation":"create","path":"services/new-page/index.php","changes":{"title":"Новая страница","html":"<p>Описание</p>"}}`.
- Перенос: `operation: "move"`, текущие `path` и `revision` из `GET /api/v1/pages/page`, а также `folder` существующего раздела. Переводы страницы переезжают вместе с ней.
- Удаление: `operation: "delete"`, `path` и `revision`. Удаляется файл выбранной страницы. Папка, вложенные страницы и переводы сохраняются.

Токен действует десять минут. Главная и служебные страницы защищены. Если адрес занят или ревизия изменилась, запросите свежий предпросмотр.

## Приватный GPT в ChatGPT

1. В **ИИ → REST API** отметьте **«Разрешить подключение через GPT Actions»**. Выпустите ключ или сохраните права существующего ключа. Для сохранения правок выберите «Чтение и правка страниц».
2. Разверните **ChatGPT · GPT Actions**. Скопируйте схему в **Actions**, а текст инструкции в **Instructions**. В настройках Actions выберите **API Key → Bearer** и введите ключ EWC. Сохраните GPT с доступом **«Только я»**.
3. Проверьте чтение страницы и запросите правку. GPT покажет «Было / Станет» и дождётся подтверждения. При сохранении ChatGPT запрашивает подтверждение действия, а EWC проверяет версию страницы и точное совпадение правок с предпросмотром.

| Метод и адрес | Действие |
| --- | --- |
| `GET /api/v1/pages/search?q=услуги` | Поиск по названию, заголовку и пути. |
| `GET /api/v1/pages/page?path=contacts/index.php` | Страница и её `revision`. |
| `POST /api/v1/pages/preview` | Принимает `path`, `revision` и `changes`. Возвращает изменённые поля до и после, а также токен предпросмотра со сроком действия 10 минут. |
| `POST /api/v1/pages/apply` | Принимает те же поля и `preview_token`. Ошибка `412` требует перечитать страницу и снова показать правки. |

Подключение GPT Actions требует публичного HTTPS. Для больших страниц используйте обычный REST API. Ключ не включается в схему или инструкции.

Ответ для страницы перечисляет компоненты в `components`. Для инфоблока используйте `GET /api/v1/pages/iblock` с путём страницы и ID таблицы. Запросы `POST /api/v1/pages/iblock-preview` и `POST /api/v1/pages/iblock-apply` принимают те же поля, ревизию, режим `append`, `update` или `replace` и строки с ключами столбцов. Для `update` укажите первую строку, считая с 1. Изменение общей таблицы отображается на всех страницах, где она вставлена.

Для фото включите создание изображений в GPT Builder. GPT может передать JPEG, PNG или WebP из беседы в `POST /api/v1/pages/image-import` через `openaiFileIdRefs`. На сервере требуется cURL. EWC принимает до пяти файлов за один запрос и сохраняет их в отдельной папке медиатеки. Замену фотографий на странице нужно отдельно провести через предпросмотр и подтверждение правки HTML.

Обычный REST-клиент может передать готовые фотографии в тот же маршрут как `multipart/form-data`. Включите GPT Actions и используйте ключ с правами правки. Передайте путь страницы в `path` и каждый файл в отдельном поле `images[]`. Допустимы JPEG, PNG и WebP до 8192 пикселей по каждой стороне, до пяти файлов и до 8 МиБ суммарно. Ограничения PHP на размер загрузки также действуют. Ответ содержит `images[].src` для HTML и `images[].url` для просмотра. Загрузка сама по себе страницу не меняет.

```sh
curl -H "Authorization: Bearer $EWC_TOKEN" \
  -F 'path=about/index.php' \
  -F 'images[]=@inspection.jpg' -F 'images[]=@team.jpg' \
  "$EWC_URL/api/v1/pages/image-import"
```

Для каталога включите отдельное разрешение **GPT Actions работать с маркетплейсом** и обновите схему в GPT. Доступны поиск товаров и вариантов, поиск по медиатеке, создание и редактирование товара, выбор главного фото, добавление снимков с подписями, текущая и старая цена, а также процентный пересчёт выбранных вариантов. Суммы передаются в минимальных денежных единицах. Новые товары по умолчанию скрыты. Пересчёт охватывает до 25 вариантов за подтверждение.

В **ИИ → REST API → Gemini · Claude** есть ссылки и команды для клиента. Клиент использует API провайдера для подготовки правок и REST EWC для их предпросмотра. Только локальное подтверждение в терминале выполняет запись.

В **Пользователи → Настройки доступа** отдельно настраиваются вход в кабинет и саморегистрация. Чтобы сотрудник мог добавлять и редактировать товары, назначьте ему роль «Редактор каталога». В **Медиатеке** доступны папки, плитка и список, множественное выделение, поворот и ресайз оригинальных изображений через GD.

## English

Open **AI → REST API**, generate a key, and select read or edit permissions. Use the endpoint displayed in the admin panel and send `Authorization: Bearer <key>`. The key gives access to unpublished pages. Copy it when generated and keep it in the client’s secret storage. Replacing the key revokes the previous one.

To create, move or delete a page, enable GPT Actions and allow edits. POST `operation` and `path` to `/api/v1/pages/page-lifecycle-preview`. For `create`, provide `changes` including `title`. For `move` and `delete`, provide the latest `revision`, and for `move` also provide an existing `folder`. After confirmation, POST the same request and its `preview_token` to `/api/v1/pages/page-lifecycle-apply`. Moves include translations. Deletion removes the selected page file only.

List pages with `GET /api/v1/pages`, then GET a specific page using its `resource` URL. To update an existing page, PATCH that URL with an `application/json` object containing the fields to change and send the GET response’s quoted ETag in `If-Match`. Editable string fields are `title`, `heading`, `description`, `keywords` and `html`. A `412` response indicates a concurrent change. For a subdirectory installation the endpoint includes the installation prefix. Custom server configurations must forward the original request URI and Authorization, set the correct script path, and signal HTTPS to PHP.

To connect a private ChatGPT GPT, enable **GPT Actions** under **AI → REST API**. Copy the schema and instructions from this section, configure **API Key → Bearer** with the EWC key, and set the GPT to **Only me**. The site must be publicly reachable over HTTPS. The GPT displays the preview before asking for approval. The server validates the page revision and the exact previewed changes. A preview expires in ten minutes.

The page response lists components, including linked infoblocks and inline images. A linked infoblock supports previewed append, update and replace operations. ChatGPT can import approved generated or uploaded images into the library and separately preview replacing the page markup. Marketplace access is optional and covers product edits, library images, offer prices and repricing. The Gemini and Claude terminal client reads the same REST API. **Users → Access settings** manages sign-in and registration. The standalone media library supports folders, grid/list views, bulk selection, rotation and resizing in place.

With GPT Actions enabled and a write key, any REST client can also POST multipart form data to `/api/v1/pages/image-import`. Send the page path as `path` and one `images[]` part per JPEG, PNG or WebP file. Send up to five files, 8 MiB combined, with each dimension at most 8192 pixels. PHP upload limits still apply. Use `images[].src` from the response in a separate page preview and apply request.
