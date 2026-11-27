# REST API для страниц EWC

В админке откройте **ИИ → REST API** и выпустите ключ. По умолчанию API выключен. Выберите «Чтение и правка страниц», если клиенту нужно сохранять правки. Ключ даёт доступ к страницам активного сайта, включая неопубликованные, и отображается при выдаче один раз. Храните его в секретах клиента и передавайте в заголовке `Authorization: Bearer <ключ>`. Замена ключа сразу отзывает предыдущий.

| Метод и адрес | Действие |
| --- | --- |
| `GET /api/v1/pages?page=1&per_page=25` | Список страниц, включая переводы. До 100 записей на страницу. |
| `GET /api/v1/pages/index.php` | Метаданные и HTML главной страницы. Для других страниц используйте `resource` из списка. |
| `PATCH /api/v1/pages/index.php` | Сохранение переданных полей. Укажите `Content-Type: application/json` и перенесите заголовок `ETag` из GET в `If-Match`, включая кавычки. |

Например, отправьте тело `{"title":"Услуги","html":"<p>Новое описание</p>"}`. Поля `title`, `heading`, `description`, `keywords` и `html` принимают строки. Поле `title` обязательно для страницы и при передаче должно быть непустым. Правки публикуются с учётом существующего статуса страницы. Ответ `412 page_has_changed` означает, что нужно заново прочитать страницу и совместить изменения.

При установке в папку адрес будет, например, `https://example.com/test/api/v1/pages`. Для Apache маршрут входит в штатный `.htaccess`. При своей конфигурации веб-сервера направьте этот путь в `app/page-route.php`, передайте PHP оригинальный URI, `Authorization`, корректный `SCRIPT_NAME` с префиксом установки и серверный признак HTTPS. Для разработки подходит встроенный PHP-сервер на loopback с `router.php`. Токен привязан к установке CMS и действует для её активного набора страниц.

## English

Open **AI → REST API**, generate a key, and select read or edit permissions. Use the endpoint displayed in the admin panel and send `Authorization: Bearer <key>`. The key gives access to unpublished pages. Copy it when generated and keep it in the client’s secret storage. Replacing the key revokes the previous one.

List pages with `GET /api/v1/pages`, then GET a specific page using its `resource` URL. To update an existing page, PATCH that URL with an `application/json` object containing the fields to change and send the GET response’s quoted ETag in `If-Match`. Editable string fields are `title`, `heading`, `description`, `keywords` and `html`. A `412` response indicates a concurrent change. For a subdirectory installation the endpoint includes the installation prefix. Custom server configurations must forward the original request URI and Authorization, set the correct script path, and signal HTTPS to PHP.
