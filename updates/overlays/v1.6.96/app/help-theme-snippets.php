<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return [
    'ru' => ['Общие для темы', 'Эти подшаблоны относятся к теме «%s». Для отдельных доменов можно указать свои значения.'],
    'en' => ['Shared within the theme', 'These snippets belong to the “%s” theme. Individual domains can specify their own values.'],
    'uk' => ['Спільні для теми', 'Ці підшаблони належать до теми «%s». Для окремих доменів можна вказати власні значення.'],
    'de' => ['Für dieses Theme', 'Diese Bausteine gehören zum Theme „%s“. Einzelne Domains können eigene Werte festlegen.'],
    'fr' => ['Commun au thème', 'Ces fragments appartiennent au thème « %s ». Chaque domaine peut définir ses propres valeurs.'],
    'es' => ['Comunes para el tema', 'Estos fragmentos pertenecen al tema «%s». Cada dominio puede tener sus propios valores.'],
    'pt' => ['Comuns ao tema', 'Estes fragmentos pertencem ao tema «%s». Cada domínio pode definir os seus próprios valores.'],
    'ja' => ['テーマ内で共通', 'これらのスニペットは「%s」テーマに属します。ドメインごとに独自の値を設定できます。'],
    'ko' => ['테마 내 공통', '이 스니펫은 ‘%s’ 테마에 속합니다. 도메인별로 값을 지정할 수 있습니다.'],
    'vi' => ['Dùng chung trong giao diện', 'Các đoạn mẫu này thuộc giao diện “%s”. Từng tên miền có thể đặt giá trị riêng.'],
    'zh' => ['主题内共享', '这些片段属于“%s”主题。各域名可以设置自己的值。'],
];
