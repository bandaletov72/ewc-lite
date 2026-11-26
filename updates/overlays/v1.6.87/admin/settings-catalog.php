<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_find_settings(string $query, string $adminUrl, string $language, int $limit = 0): array
{
    $settingsTerms = [
        ['error_reporting', 'settings=1#php-error-reporting', 'ошибки php диагностика warnings errors уровень'],
        [site_php_admin_labels($language)[0], 'maintenance=1#php-info', 'информация php phpinfo php version модули версии диагностика'],
        ['Вид поиска', 'settings=1#site-search-settings', 'поиск лупа подсказки search magnifier compact'],
        [site_scroll_animation_labels($language)[0], 'settings=1#scroll-animation', 'анимация прокрутка эффект scroll motion animation'],
        ['Основные', 'settings=1', 'название сайт футер подвал хлебные крошки breadcrumbs поиск лупа search'],
        ['Логотипы и иконки сайта', 'settings=1#branding', 'лого светлый тёмный favicon фавикон картинки icon'],
        [site_appearance_labels($language)[0], 'theme=1', 'тема и цвета системная палитра тёмная светлая авто dark light color'],
        [site_appearance_labels($language)[1], 'fonts=1', 'типографика шрифт Google Fonts font локально'],
        [site_appearance_labels($language)[2], 'libraries=1', 'codemirror css js jquery bootstrap font awesome mui'],
        ['Региональные настройки', 'region=1', 'валюта язык дата время часовой пояс gmt currency timezone language'],
        [site_phone_country_labels($language)[0], 'region=1#phone-country', 'страна код телефон country phone calling code'],
        ['Шаблоны оформления', 'layout=1', 'шапка подвал меню header footer sidebar mobile-menu'],
        [site_design_labels($language)[0], 'design=1', 'темы дизайн копия шаблон design theme override copy'],
        [admin_page_template_labels($language)[0], 'page_templates=1', 'шаблоны страниц главная товар page templates preset'],
        ['Служебные заголовки', 'headers=1', 'head body счётчик скрипт canonical meta html'],
        ['Функции', 'functions=1', 'functions.php php translit транслитерация помощники helpers'],
        ['CSS и JS', 'assets=1', 'стили скрипты stylesheet javascript'],
        ['Домены', 'domains=1', 'домен поддомен dns регион domain host'],
        [site_city_language($language)['group'], 'cities=1', 'города и домены адрес email часы работы телефоны cities domains phone'],
        [site_city_contact_labels($language)[0], 'settings=1#message-recipients', 'почта получатели сообщений формы email mail recipient'],
        ['Подшаблоны', 'snippets=1', 'переменные сниппет адрес email часы работы city tel snippet token шаблоны оформления'],
        ['Компоненты', 'components=1', 'карусель галерея список файлов ширина gal fotos files width arrows'],
        ['Формы', 'forms=1', 'форма письмо email почта копии captcha поля телефон form'],
        ['Антиспам', 'spam=1', 'спам слова запрет ip блокировка spam block'],
        ['Общие настройки CAPTCHA', 'captcha=1', 'рекапча google капча я не робот ключи'],
        [site_review_labels($GLOBALS['adminLanguage'] ?? 'ru')[0], 'reviews=1', 'отзывы reviews rating модерация'],
        ['Обратные звонки', 'callbacks=1', 'телефон callback ip телефония zadarma'],
        ['Внешние сервисы', 'integrations=1#services', 'интеграции метрика аналитика пиксель telegram cloudflare webhook api'],
        ['Яндекс Карты и Google Maps', 'integrations=1#maps', 'карта точки кластер координаты масштаб map cluster'],
        ['Контакты и соцсети', 'contacts=1', 'телефон telegram vk whatsapp youtube instagram max иконки соцсети contact'],
        ['Маркетплейс', 'shop=1', 'магазин витрина заказы каталог импорт marketplace shop csv yml'],
        ['Пользователи', 'users=1', 'роли права кабинеты users accounts registration'],
        ['Разрешить регистрацию пользователей', 'settings=1#account-registration', 'регистрация пользователи пароли письма registration users password email'],
        ['Обновления', 'updates=1', 'github версия движок тема спам фильтры расписание обновление update'],
        [admin_schedule_labels($language)[0], 'backups=1', 'резервная копия архив бекап бэкап backup restore schedule'],
        ['Обслуживание', 'maintenance=1', 'индекс поиск переиндексация кеш кэш расписание cache index'],
        ['Служебные файлы', 'maintenance=1#service-files', '.htaccess robots.txt sitemap.xml xml карта сайта'],
        ['Редиректы', 'maintenance=1#redirects', 'перенаправления редиректы redirects 301 url'],
        ['Страницы ошибок', 'maintenance=1#error-pages', 'страница ошибка errors 403 404 410 429 500 502 503 504'],
        ['PHP-модули', 'modules=1', 'код php дата расширение модули extension module'],
        ['Инфоблоки', 'iblocks=1', 'таблица данные csv цены база data price'],
    ];
    $words = preg_split('/[^\p{L}\p{N}]+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
    if (!$words || count($words) > 8) {
        return [];
    }
    $matches = [];
    foreach ($settingsTerms as list($label, $link, $terms)) {
        $translated = site_translation($label, $language);
        $searchable = $translated . ' ' . $label . ' ' . $terms . ' ' . site_translation($terms, $language);
        foreach ($words as $word) {
            if (!preg_match('~' . preg_quote($word, '~') . '~iu', $searchable)) {
                continue 2;
            }
        }
        $matches[] = ['title' => $translated, 'url' => $adminUrl . '?' . $link];
        if ($limit > 0 && count($matches) >= $limit) {
            break;
        }
    }
    return $matches;
}
