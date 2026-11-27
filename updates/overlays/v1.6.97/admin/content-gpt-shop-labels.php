<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return [
    'ru' => ['Разрешить GPT Actions работать с маркетплейсом', 'После включения доступа к маркетплейсу скопируйте в GPT обновлённую схему и инструкцию. Фото выбираются из медиатеки сайта.'],
    'en' => ['Allow GPT Actions to manage the marketplace', 'After enabling marketplace access, copy the updated schema and instructions into the GPT. Select photos from the site media library.'],
    'uk' => ['Дозволити GPT Actions працювати з маркетплейсом', 'Після надання доступу скопіюйте оновлену схему й інструкцію в GPT. Фото вибираються з медіатеки сайту.'],
    'de' => ['GPT Actions für den Marktplatz zulassen', 'Kopieren Sie nach der Aktivierung das aktualisierte Schema und die Anweisungen ins GPT. Bilder stammen aus der Mediathek.'],
    'fr' => ['Autoriser GPT Actions sur la place de marché', 'Après activation, copiez le schéma et les consignes actualisés dans le GPT. Choisissez les photos dans la médiathèque du site.'],
    'es' => ['Permitir que GPT Actions gestione la tienda', 'Tras activar el acceso a la tienda, copia el esquema y las instrucciones actualizados al GPT. Elige fotos de la biblioteca del sitio.'],
    'pt' => ['Permitir que GPT Actions gira a loja', 'Após ativar o acesso à loja, copie o esquema e as instruções atualizados para o GPT. Escolha fotos da biblioteca do site.'],
    'ja' => ['GPT Actions にショップの操作を許可', 'ショップへのアクセスを有効にしたら、スキーマと指示を GPT に再コピーします。写真はメディアライブラリから選びます。'],
    'ko' => ['GPT Actions의 마켓플레이스 관리 허용', '마켓플레이스 접근을 켜고 업데이트된 스키마와 지침을 GPT에 복사하세요. 사진은 사이트 미디어 라이브러리에서 선택합니다.'],
    'vi' => ['Cho phép GPT Actions quản lý cửa hàng', 'Sau khi bật quyền truy cập cửa hàng, sao chép lược đồ và hướng dẫn mới vào GPT. Chọn ảnh từ thư viện của trang web.'],
    'zh' => ['允许 GPT Actions 管理商城', '启用商城访问后，将更新后的架构和指令复制到 GPT。照片从网站媒体库中选择。'],
    'prompt' => [
        'ru' => 'В маркетплейсе найди товары, разделы и фотографии через lookupEwcShop. Для пересчёта перечисли конкретные варианты и уточни правило. Суммы передавай в минимальных денежных единицах. Выбирай фото только по ID из медиатеки. Перед записью вызови previewEwcShopChange. Покажи все изменения и для фото укажи ссылки и подписи. Новые товары без указания публиковать создавай скрытыми. Только после отдельного явного подтверждения вызови applyEwcShopChange с точными operation, revision, data и preview_token. При смене цены, фото или текста и при ошибке 412 покажи новый предпросмотр и снова дождись согласия.',
        'en' => 'For the marketplace, find products, categories and media via lookupEwcShop. For recalculation, identify specific offers and clarify the rule. Send money in minor units. Select images by media library ID. Call previewEwcShopChange before writing. Show every change and include photo links and captions. Keep new products unpublished unless the user requests publication. Only after separate explicit confirmation call applyEwcShopChange with the original operation, revision, data and preview_token. After any edit to the proposal or a 412 response, show a new preview and wait for confirmation again.',
    ],
];
