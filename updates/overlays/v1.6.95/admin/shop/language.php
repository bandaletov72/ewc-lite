<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$labels = [
'en'=>[
'marketplace'=>'Marketplace','catalog'=>'Catalog','categories'=>'Categories','products'=>'Products','offers'=>'Offers and variants','fields'=>'Fields',
'aliases'=>'Previous product URLs','values'=>'Field values','placements'=>'Additional categories','images'=>'Images','prices'=>'Price types','kits'=>'Bundles','settings'=>'Settings',
'catalog_page'=>'CMS storefront page','currency'=>'Accounting currency','price_labels'=>'Price labels, type=Label','page_size'=>'Cards per page','guest'=>'Allow guest checkout',
'templates'=>'Editable storefront templates','template_category'=>'Category row','template_item'=>'Catalog card','template_product'=>'Product detail',
'template_basket'=>'Basket indicator','template_checkout'=>'Checkout','template_account'=>'Account orders','template_page'=>'Standalone page',
'edit_product'=>'Edit product description','summary'=>'Short description','description'=>'Full description','save'=>'Save','sheet_help'=>'Pipe-separated CSV with header',
'reindex'=>'Rebuild catalog cache','import'=>'Import','import_help'=>'Preview and validate the import before publishing. Missing products remain in the catalog.',
'source'=>'Source name','format'=>'File format','separator'=>'CSV separator','mapping'=>'Mappings, one per line','replace_descriptions'=>'Update descriptions and images',
'preview'=>'Preview import','apply'=>'Apply','upload'=>'Upload','new'=>'New','updated'=>'Updated','confirmed'=>'Confirmed','packed'=>'Packed',
'issued'=>'Issued','cancelled'=>'Cancelled','status'=>'Status','orders'=>'Orders','name'=>'Name','email'=>'Email','phone'=>'Phone','address'=>'Address',
'delivery'=>'Delivery','pickup'=>'Pickup','shipping'=>'Delivery fee','checkout'=>'Checkout','basket'=>'Basket','account'=>'Account','staff'=>'Marketplace workspace',
'home'=>'Website','login'=>'Log in','add'=>'Add to basket','unavailable'=>'Unavailable','search'=>'Search the catalog','sort'=>'Sort','filters'=>'Filters',
'pages'=>'Pages','price'=>'Price','min'=>'From','max'=>'To','yes'=>'Yes','no'=>'No','brand'=>'Brand','quantity'=>'Quantity','total'=>'Total',
'remove'=>'Remove','empty_cart'=>'Your basket is empty.','new_desc'=>'Newest first','new_asc'=>'Oldest first',
'price_desc'=>'Highest price first','price_asc'=>'Lowest price first','no_products'=>'No products found.','order_received'=>'Order received',
'cart_updated'=>'Basket updated.','saved'=>'Saved.','csv'=>'Invalid file or encoding.','header'=>'Invalid header',
'row'=>'Invalid record','tree'=>'Invalid category hierarchy','storage'=>'Could not read or save data.','session'=>'Reload the page and try again.',
'not_found'=>'Record not found.','stock'=>'Check stock and quantities in your basket.','status_invalid'=>'Invalid order status change.',
'contact_invalid'=>'Check your contact and delivery details.','changed_price'=>'Prices changed. Review your order and try again.',
'try_again'=>'Please try again later.','forbidden'=>'Access denied.','conflict'=>'The catalog has changed. Reload or create a new preview.',
'photo_invalid'=>'Choose a supported image up to 12 MB.','xml_extension'=>'YML import requires XMLReader and SimpleXML.',
'currency_mismatch'=>'Currency differs from the accounting currency.','mail_order'=>'Order confirmation','mail_status'=>'Order status change',
'mail_subject'=>'Email subject','mail_body'=>'Email text',
],
'ru'=>[
'marketplace'=>'Маркетплейс','catalog'=>'Каталог','categories'=>'Разделы','products'=>'Товары','offers'=>'Предложения и варианты','fields'=>'Поля',
'aliases'=>'Прежние адреса товаров','values'=>'Значения полей','placements'=>'Дополнительные разделы','images'=>'Изображения','prices'=>'Виды цен','kits'=>'Комплекты','settings'=>'Настройки',
'catalog_page'=>'Страница витрины в CMS','currency'=>'Валюта учёта','price_labels'=>'Подписи цен, тип=Название','page_size'=>'Карточек на странице','guest'=>'Разрешить оформление без регистрации',
'templates'=>'Редактируемые шаблоны витрины','template_category'=>'Строка раздела','template_item'=>'Карточка в каталоге','template_product'=>'Страница товара',
'template_basket'=>'Значок корзины','template_checkout'=>'Оформление заказа','template_account'=>'Заказы в кабинете','template_page'=>'Страница маркетплейса',
'edit_product'=>'Править описание товара','summary'=>'Краткое описание','description'=>'Полное описание','save'=>'Сохранить','sheet_help'=>'CSV с разделителем | и строкой заголовков',
'reindex'=>'Перестроить кеш каталога','import'=>'Импорт','import_help'=>'Проверьте предпросмотр перед публикацией. Товары, пропущенные в файле, остаются в каталоге.',
'source'=>'Название источника','format'=>'Формат файла','separator'=>'Разделитель CSV','mapping'=>'Сопоставления, по одному на строку','replace_descriptions'=>'Обновлять описания и изображения',
'preview'=>'Предпросмотр импорта','apply'=>'Применить','upload'=>'Загрузить','new'=>'Новый','updated'=>'Обновлено','confirmed'=>'Подтверждён','packed'=>'Собран',
'issued'=>'Выдан','cancelled'=>'Отменён','status'=>'Статус','orders'=>'Заказы','name'=>'Имя','email'=>'Электронная почта','phone'=>'Телефон','address'=>'Адрес',
'delivery'=>'Получение','pickup'=>'Самовывоз','shipping'=>'Доставка','checkout'=>'Оформить заказ','basket'=>'Корзина','account'=>'Личный кабинет','staff'=>'Рабочая область маркетплейса',
'home'=>'Сайт','login'=>'Войти','add'=>'В корзину','unavailable'=>'Нет в наличии','search'=>'Поиск по каталогу','sort'=>'Сортировка','filters'=>'Фильтры',
'pages'=>'Страницы','price'=>'Цена','min'=>'От','max'=>'До','yes'=>'Да','no'=>'Нет','brand'=>'Бренд','quantity'=>'Количество','total'=>'Итого','remove'=>'Удалить',
'empty_cart'=>'Корзина пуста.','new_desc'=>'Сначала новые','new_asc'=>'Сначала старые','price_desc'=>'Сначала дорогие','price_asc'=>'Сначала дешёвые',
'no_products'=>'Товары не найдены.','order_received'=>'Заказ принят','cart_updated'=>'Корзина обновлена.','saved'=>'Сохранено.',
'csv'=>'Проверьте формат и кодировку файла.','header'=>'Неверный заголовок','row'=>'Проверьте запись','tree'=>'Проверьте вложенность разделов',
'storage'=>'Не удалось прочитать или сохранить данные.','session'=>'Обновите страницу и попробуйте снова.',
'not_found'=>'Запись не найдена.','stock'=>'Проверьте наличие и количество товаров в корзине.','status_invalid'=>'Недопустимое изменение статуса.',
'contact_invalid'=>'Проверьте контакты и данные доставки.','changed_price'=>'Цена изменилась. Проверьте заказ и подтвердите его снова.',
'try_again'=>'Попробуйте чуть позже.','forbidden'=>'Доступ запрещён.','conflict'=>'Каталог изменился. Обновите страницу или создайте новый предпросмотр.',
'photo_invalid'=>'Выберите изображение допустимого формата до 12 МБ.','xml_extension'=>'Для импорта YML нужны XMLReader и SimpleXML.',
'currency_mismatch'=>'Валюта не совпадает с валютой учёта.','mail_order'=>'Подтверждение заказа','mail_status'=>'Изменение статуса заказа',
'mail_subject'=>'Тема письма','mail_body'=>'Текст письма',
],
];

$productLabels = [
    'en' => [
        'product_name'=>'Name', 'product_sku'=>'SKU', 'product_add'=>'Add product',
        'product_edit'=>'Edit product', 'product_copy'=>'Copy product', 'product_copy_noun'=>'copy',
        'product_search'=>'Name or SKU', 'product_category'=>'Category', 'product_all_categories'=>'All categories',
        'product_all_statuses'=>'All statuses', 'product_active'=>'Active', 'product_inactive'=>'Inactive',
        'product_reset'=>'Reset', 'product_columns'=>'Columns', 'product_image'=>'Image',
        'product_stock'=>'Stock', 'product_type'=>'Product type', 'product_created'=>'Created',
        'product_actions'=>'Actions', 'product_close'=>'Close', 'product_cancel'=>'Cancel',
        'product_confirm_delete'=>'Delete this product?', 'product_slug'=>'URL slug', 'product_slug_auto'=>'Generated from the name if blank',
        'product_physical'=>'Physical product', 'product_digital'=>'Digital product', 'product_service'=>'Service',
        'product_add_offer'=>'Add offer', 'product_variant'=>'Variant', 'product_slug_used'=>'This product URL is already in use.',
        'product_in_bundle'=>'Remove this product from the bundles that use it before deleting it.',
    ],
    'ru' => [
        'product_name'=>'Название', 'product_sku'=>'Артикул', 'product_add'=>'Добавить товар',
        'product_edit'=>'Редактировать товар', 'product_copy'=>'Копировать товар', 'product_copy_noun'=>'копия',
        'product_search'=>'Название или артикул', 'product_category'=>'Раздел', 'product_all_categories'=>'Все разделы',
        'product_all_statuses'=>'Все статусы', 'product_active'=>'Активен', 'product_inactive'=>'Неактивен',
        'product_reset'=>'Сбросить', 'product_columns'=>'Столбцы', 'product_image'=>'Изображение',
        'product_stock'=>'Остаток', 'product_type'=>'Тип товара', 'product_created'=>'Дата создания',
        'product_actions'=>'Действия', 'product_close'=>'Закрыть', 'product_cancel'=>'Отмена',
        'product_confirm_delete'=>'Удалить этот товар?', 'product_slug'=>'Адрес товара', 'product_slug_auto'=>'Пустое поле заполнится по названию',
        'product_physical'=>'Физический товар', 'product_digital'=>'Цифровой товар', 'product_service'=>'Услуга',
        'product_add_offer'=>'Добавить предложение', 'product_variant'=>'Вариант', 'product_slug_used'=>'Этот адрес товара уже используется.',
        'product_in_bundle'=>'Перед удалением исключите товар из комплектов, в которые он входит.',
    ],
    'de' => [
        'product_name'=>'Name', 'product_sku'=>'Artikelnummer', 'product_add'=>'Produkt hinzufügen',
        'product_edit'=>'Produkt bearbeiten', 'product_copy'=>'Produkt kopieren', 'product_copy_noun'=>'Kopie',
        'product_search'=>'Name oder Artikelnummer', 'product_category'=>'Kategorie', 'product_all_categories'=>'Alle Kategorien',
        'product_all_statuses'=>'Alle Status', 'product_active'=>'Aktiv', 'product_inactive'=>'Inaktiv',
        'product_reset'=>'Zurücksetzen', 'product_columns'=>'Spalten', 'product_image'=>'Bild',
        'product_stock'=>'Bestand', 'product_type'=>'Produkttyp', 'product_created'=>'Erstellt',
        'product_actions'=>'Aktionen', 'product_close'=>'Schließen', 'product_cancel'=>'Abbrechen',
        'product_confirm_delete'=>'Dieses Produkt löschen?', 'product_slug'=>'URL-Kürzel', 'product_slug_auto'=>'Wird bei leerem Feld aus dem Namen erzeugt',
        'product_physical'=>'Physisches Produkt', 'product_digital'=>'Digitales Produkt', 'product_service'=>'Dienstleistung',
        'product_add_offer'=>'Angebot hinzufügen', 'product_variant'=>'Variante', 'product_slug_used'=>'Diese Produkt-URL wird bereits verwendet.',
        'product_in_bundle'=>'Entfernen Sie das Produkt vor dem Löschen aus allen Paketen.',
    ],
    'es' => [
        'product_name'=>'Nombre', 'product_sku'=>'SKU', 'product_add'=>'Añadir producto',
        'product_edit'=>'Editar producto', 'product_copy'=>'Copiar producto', 'product_copy_noun'=>'copia',
        'product_search'=>'Nombre o SKU', 'product_category'=>'Categoría', 'product_all_categories'=>'Todas las categorías',
        'product_all_statuses'=>'Todos los estados', 'product_active'=>'Activo', 'product_inactive'=>'Inactivo',
        'product_reset'=>'Restablecer', 'product_columns'=>'Columnas', 'product_image'=>'Imagen',
        'product_stock'=>'Existencias', 'product_type'=>'Tipo de producto', 'product_created'=>'Fecha de creación',
        'product_actions'=>'Acciones', 'product_close'=>'Cerrar', 'product_cancel'=>'Cancelar',
        'product_confirm_delete'=>'¿Eliminar este producto?', 'product_slug'=>'Slug de la URL', 'product_slug_auto'=>'Se genera a partir del nombre si queda vacío',
        'product_physical'=>'Producto físico', 'product_digital'=>'Producto digital', 'product_service'=>'Servicio',
        'product_add_offer'=>'Añadir oferta', 'product_variant'=>'Variante', 'product_slug_used'=>'Esta URL de producto ya está en uso.',
        'product_in_bundle'=>'Antes de eliminar el producto, retíralo de los paquetes que lo incluyen.',
    ],
    'fr' => [
        'product_name'=>'Nom', 'product_sku'=>'Référence', 'product_add'=>'Ajouter un produit',
        'product_edit'=>'Modifier le produit', 'product_copy'=>'Copier le produit', 'product_copy_noun'=>'copie',
        'product_search'=>'Nom ou référence', 'product_category'=>'Catégorie', 'product_all_categories'=>'Toutes les catégories',
        'product_all_statuses'=>'Tous les statuts', 'product_active'=>'Actif', 'product_inactive'=>'Inactif',
        'product_reset'=>'Réinitialiser', 'product_columns'=>'Colonnes', 'product_image'=>'Image',
        'product_stock'=>'Stock', 'product_type'=>'Type de produit', 'product_created'=>'Date de création',
        'product_actions'=>'Actions', 'product_close'=>'Fermer', 'product_cancel'=>'Annuler',
        'product_confirm_delete'=>'Supprimer ce produit ?', 'product_slug'=>'Identifiant URL', 'product_slug_auto'=>'Généré à partir du nom si le champ est vide',
        'product_physical'=>'Produit physique', 'product_digital'=>'Produit numérique', 'product_service'=>'Service',
        'product_add_offer'=>'Ajouter une offre', 'product_variant'=>'Variante', 'product_slug_used'=>'Cette URL de produit est déjà utilisée.',
        'product_in_bundle'=>'Retirez le produit des lots qui le contiennent avant de le supprimer.',
    ],
    'ja' => [
        'product_name'=>'商品名', 'product_sku'=>'SKU', 'product_add'=>'商品を追加',
        'product_edit'=>'商品を編集', 'product_copy'=>'商品をコピー', 'product_copy_noun'=>'コピー',
        'product_search'=>'商品名またはSKU', 'product_category'=>'カテゴリ', 'product_all_categories'=>'すべてのカテゴリ',
        'product_all_statuses'=>'すべての状態', 'product_active'=>'有効', 'product_inactive'=>'無効',
        'product_reset'=>'リセット', 'product_columns'=>'表示列', 'product_image'=>'画像',
        'product_stock'=>'在庫', 'product_type'=>'商品タイプ', 'product_created'=>'作成日',
        'product_actions'=>'操作', 'product_close'=>'閉じる', 'product_cancel'=>'キャンセル',
        'product_confirm_delete'=>'この商品を削除しますか？', 'product_slug'=>'URLスラッグ', 'product_slug_auto'=>'空欄の場合は商品名から生成されます',
        'product_physical'=>'物理商品', 'product_digital'=>'デジタル商品', 'product_service'=>'サービス',
        'product_add_offer'=>'オファーを追加', 'product_variant'=>'バリエーション', 'product_slug_used'=>'この商品のURLは既に使用されています。',
        'product_in_bundle'=>'商品を削除する前に、この商品を含むセットから取り除いてください。',
    ],
    'ko' => [
        'product_name'=>'상품명', 'product_sku'=>'SKU', 'product_add'=>'상품 추가',
        'product_edit'=>'상품 편집', 'product_copy'=>'상품 복사', 'product_copy_noun'=>'사본',
        'product_search'=>'상품명 또는 SKU', 'product_category'=>'카테고리', 'product_all_categories'=>'모든 카테고리',
        'product_all_statuses'=>'모든 상태', 'product_active'=>'활성', 'product_inactive'=>'비활성',
        'product_reset'=>'초기화', 'product_columns'=>'표시 열', 'product_image'=>'이미지',
        'product_stock'=>'재고', 'product_type'=>'상품 유형', 'product_created'=>'생성일',
        'product_actions'=>'작업', 'product_close'=>'닫기', 'product_cancel'=>'취소',
        'product_confirm_delete'=>'이 상품을 삭제할까요?', 'product_slug'=>'URL 슬러그', 'product_slug_auto'=>'비워 두면 상품명으로 생성됩니다',
        'product_physical'=>'실물 상품', 'product_digital'=>'디지털 상품', 'product_service'=>'서비스',
        'product_add_offer'=>'판매 옵션 추가', 'product_variant'=>'옵션', 'product_slug_used'=>'이 상품 URL은 이미 사용 중입니다.',
        'product_in_bundle'=>'삭제하기 전에 이 상품이 포함된 묶음에서 제거하세요.',
    ],
    'pt' => [
        'product_name'=>'Nome', 'product_sku'=>'SKU', 'product_add'=>'Adicionar produto',
        'product_edit'=>'Editar produto', 'product_copy'=>'Copiar produto', 'product_copy_noun'=>'cópia',
        'product_search'=>'Nome ou SKU', 'product_category'=>'Categoria', 'product_all_categories'=>'Todas as categorias',
        'product_all_statuses'=>'Todos os estados', 'product_active'=>'Ativo', 'product_inactive'=>'Inativo',
        'product_reset'=>'Limpar', 'product_columns'=>'Colunas', 'product_image'=>'Imagem',
        'product_stock'=>'Estoque', 'product_type'=>'Tipo de produto', 'product_created'=>'Data de criação',
        'product_actions'=>'Ações', 'product_close'=>'Fechar', 'product_cancel'=>'Cancelar',
        'product_confirm_delete'=>'Excluir este produto?', 'product_slug'=>'Slug da URL', 'product_slug_auto'=>'Gerado a partir do nome se ficar em branco',
        'product_physical'=>'Produto físico', 'product_digital'=>'Produto digital', 'product_service'=>'Serviço',
        'product_add_offer'=>'Adicionar oferta', 'product_variant'=>'Variante', 'product_slug_used'=>'Esta URL de produto já está em uso.',
        'product_in_bundle'=>'Antes de excluir, remova o produto dos conjuntos que o incluem.',
    ],
    'uk' => [
        'product_name'=>'Назва', 'product_sku'=>'Артикул', 'product_add'=>'Додати товар',
        'product_edit'=>'Редагувати товар', 'product_copy'=>'Копіювати товар', 'product_copy_noun'=>'копія',
        'product_search'=>'Назва або артикул', 'product_category'=>'Розділ', 'product_all_categories'=>'Усі розділи',
        'product_all_statuses'=>'Усі статуси', 'product_active'=>'Активний', 'product_inactive'=>'Неактивний',
        'product_reset'=>'Скинути', 'product_columns'=>'Стовпці', 'product_image'=>'Зображення',
        'product_stock'=>'Залишок', 'product_type'=>'Тип товару', 'product_created'=>'Дата створення',
        'product_actions'=>'Дії', 'product_close'=>'Закрити', 'product_cancel'=>'Скасувати',
        'product_confirm_delete'=>'Видалити цей товар?', 'product_slug'=>'Адреса товару', 'product_slug_auto'=>'Порожнє поле заповниться за назвою',
        'product_physical'=>'Фізичний товар', 'product_digital'=>'Цифровий товар', 'product_service'=>'Послуга',
        'product_add_offer'=>'Додати пропозицію', 'product_variant'=>'Варіант', 'product_slug_used'=>'Ця адреса товару вже використовується.',
        'product_in_bundle'=>'Перед видаленням вилучіть товар із комплектів, до яких він входить.',
    ],
    'vi' => [
        'product_name'=>'Tên sản phẩm', 'product_sku'=>'SKU', 'product_add'=>'Thêm sản phẩm',
        'product_edit'=>'Sửa sản phẩm', 'product_copy'=>'Sao chép sản phẩm', 'product_copy_noun'=>'bản sao',
        'product_search'=>'Tên hoặc SKU', 'product_category'=>'Danh mục', 'product_all_categories'=>'Tất cả danh mục',
        'product_all_statuses'=>'Tất cả trạng thái', 'product_active'=>'Đang hoạt động', 'product_inactive'=>'Đã tắt',
        'product_reset'=>'Đặt lại', 'product_columns'=>'Cột', 'product_image'=>'Hình ảnh',
        'product_stock'=>'Tồn kho', 'product_type'=>'Loại sản phẩm', 'product_created'=>'Ngày tạo',
        'product_actions'=>'Thao tác', 'product_close'=>'Đóng', 'product_cancel'=>'Hủy',
        'product_confirm_delete'=>'Xóa sản phẩm này?', 'product_slug'=>'Đường dẫn URL', 'product_slug_auto'=>'Tự tạo từ tên nếu để trống',
        'product_physical'=>'Sản phẩm vật lý', 'product_digital'=>'Sản phẩm số', 'product_service'=>'Dịch vụ',
        'product_add_offer'=>'Thêm lựa chọn bán hàng', 'product_variant'=>'Biến thể', 'product_slug_used'=>'URL sản phẩm này đã được sử dụng.',
        'product_in_bundle'=>'Hãy gỡ sản phẩm khỏi các gói có chứa nó trước khi xóa.',
    ],
    'zh' => [
        'product_name'=>'名称', 'product_sku'=>'SKU', 'product_add'=>'添加商品',
        'product_edit'=>'编辑商品', 'product_copy'=>'复制商品', 'product_copy_noun'=>'副本',
        'product_search'=>'名称或 SKU', 'product_category'=>'分类', 'product_all_categories'=>'全部分类',
        'product_all_statuses'=>'全部状态', 'product_active'=>'启用', 'product_inactive'=>'停用',
        'product_reset'=>'重置', 'product_columns'=>'显示列', 'product_image'=>'图片',
        'product_stock'=>'库存', 'product_type'=>'商品类型', 'product_created'=>'创建日期',
        'product_actions'=>'操作', 'product_close'=>'关闭', 'product_cancel'=>'取消',
        'product_confirm_delete'=>'删除此商品？', 'product_slug'=>'网址标识', 'product_slug_auto'=>'留空时根据商品名称生成',
        'product_physical'=>'实体商品', 'product_digital'=>'数字商品', 'product_service'=>'服务',
        'product_add_offer'=>'添加报价', 'product_variant'=>'规格', 'product_slug_used'=>'此商品网址已被使用。',
        'product_in_bundle'=>'删除前，请先从包含此商品的套装中移除它。',
    ],
];
$inventoryLabels = [
    'en' => [
        'product_barcode'=>'Barcode', 'product_search'=>'Name, SKU or barcode', 'product_unassigned'=>'Stock without a warehouse',
        'warehouses'=>'Warehouses', 'warehouse_name'=>'Warehouse name', 'warehouse_add'=>'Add warehouse',
        'warehouse_exists'=>'A warehouse with this name already exists.',
    ],
    'ru' => [
        'product_barcode'=>'Штрихкод', 'product_search'=>'Название, артикул или штрихкод', 'product_unassigned'=>'Остаток без склада',
        'warehouses'=>'Склады', 'warehouse_name'=>'Название склада', 'warehouse_add'=>'Добавить склад',
        'warehouse_exists'=>'Склад с таким названием уже существует.',
    ],
    'de' => [
        'product_barcode'=>'Barcode', 'product_search'=>'Name, Artikelnummer oder Barcode', 'product_unassigned'=>'Bestand ohne Lager',
        'warehouses'=>'Lager', 'warehouse_name'=>'Lagername', 'warehouse_add'=>'Lager hinzufügen',
        'warehouse_exists'=>'Ein Lager mit diesem Namen existiert bereits.',
    ],
    'es' => [
        'product_barcode'=>'Código de barras', 'product_search'=>'Nombre, SKU o código de barras', 'product_unassigned'=>'Existencias sin almacén',
        'warehouses'=>'Almacenes', 'warehouse_name'=>'Nombre del almacén', 'warehouse_add'=>'Añadir almacén',
        'warehouse_exists'=>'Ya existe un almacén con ese nombre.',
    ],
    'fr' => [
        'product_barcode'=>'Code-barres', 'product_search'=>'Nom, référence ou code-barres', 'product_unassigned'=>'Stock hors entrepôt',
        'warehouses'=>'Entrepôts', 'warehouse_name'=>'Nom de l’entrepôt', 'warehouse_add'=>'Ajouter un entrepôt',
        'warehouse_exists'=>'Un entrepôt porte déjà ce nom.',
    ],
    'ja' => [
        'product_barcode'=>'バーコード', 'product_search'=>'商品名、SKU、バーコード', 'product_unassigned'=>'倉庫未割当の在庫',
        'warehouses'=>'倉庫', 'warehouse_name'=>'倉庫名', 'warehouse_add'=>'倉庫を追加',
        'warehouse_exists'=>'この名前の倉庫は既にあります。',
    ],
    'ko' => [
        'product_barcode'=>'바코드', 'product_search'=>'상품명, SKU 또는 바코드', 'product_unassigned'=>'창고 미지정 재고',
        'warehouses'=>'창고', 'warehouse_name'=>'창고 이름', 'warehouse_add'=>'창고 추가',
        'warehouse_exists'=>'이 이름의 창고가 이미 있습니다.',
    ],
    'pt' => [
        'product_barcode'=>'Código de barras', 'product_search'=>'Nome, SKU ou código de barras', 'product_unassigned'=>'Estoque sem depósito',
        'warehouses'=>'Depósitos', 'warehouse_name'=>'Nome do depósito', 'warehouse_add'=>'Adicionar depósito',
        'warehouse_exists'=>'Já existe um depósito com esse nome.',
    ],
    'uk' => [
        'product_barcode'=>'Штрихкод', 'product_search'=>'Назва, артикул або штрихкод', 'product_unassigned'=>'Залишок без складу',
        'warehouses'=>'Склади', 'warehouse_name'=>'Назва складу', 'warehouse_add'=>'Додати склад',
        'warehouse_exists'=>'Склад із такою назвою вже існує.',
    ],
    'vi' => [
        'product_barcode'=>'Mã vạch', 'product_search'=>'Tên, SKU hoặc mã vạch', 'product_unassigned'=>'Tồn kho chưa phân kho',
        'warehouses'=>'Kho hàng', 'warehouse_name'=>'Tên kho', 'warehouse_add'=>'Thêm kho',
        'warehouse_exists'=>'Tên kho này đã được dùng.',
    ],
    'zh' => [
        'product_barcode'=>'条形码', 'product_search'=>'名称、SKU 或条形码', 'product_unassigned'=>'未分配仓库的库存',
        'warehouses'=>'仓库', 'warehouse_name'=>'仓库名称', 'warehouse_add'=>'添加仓库',
        'warehouse_exists'=>'已存在同名仓库。',
    ],
];
$listLabels = [
    'de' => [
        'products'=>'Produkte', 'filters'=>'Filter', 'apply'=>'Anwenden',
        'pages'=>'Seiten', 'price'=>'Preis', 'remove'=>'Löschen',
        'save'=>'Speichern', 'status'=>'Status', 'offers'=>'Angebote und Varianten',
        'summary'=>'Kurzbeschreibung', 'description'=>'Vollständige Beschreibung', 'no_products'=>'Keine Produkte gefunden.',
        'kits'=>'Pakete',
    ],
    'es' => [
        'products'=>'Productos', 'filters'=>'Filtros', 'apply'=>'Aplicar',
        'pages'=>'Páginas', 'price'=>'Precio', 'remove'=>'Eliminar',
        'save'=>'Guardar', 'status'=>'Estado', 'offers'=>'Ofertas y variantes',
        'summary'=>'Descripción breve', 'description'=>'Descripción completa', 'no_products'=>'No se encontraron productos.',
        'kits'=>'Paquetes',
    ],
    'fr' => [
        'products'=>'Produits', 'filters'=>'Filtres', 'apply'=>'Appliquer',
        'pages'=>'Pages', 'price'=>'Prix', 'remove'=>'Supprimer',
        'save'=>'Enregistrer', 'status'=>'Statut', 'offers'=>'Offres et variantes',
        'summary'=>'Description courte', 'description'=>'Description complète', 'no_products'=>'Aucun produit trouvé.',
        'kits'=>'Lots',
    ],
    'ja' => [
        'products'=>'商品', 'filters'=>'絞り込み', 'apply'=>'適用',
        'pages'=>'ページ', 'price'=>'価格', 'remove'=>'削除',
        'save'=>'保存', 'status'=>'状態', 'offers'=>'オファーとバリエーション',
        'summary'=>'短い説明', 'description'=>'詳しい説明', 'no_products'=>'商品が見つかりません。',
        'kits'=>'セット',
    ],
    'ko' => [
        'products'=>'상품', 'filters'=>'필터', 'apply'=>'적용',
        'pages'=>'페이지', 'price'=>'가격', 'remove'=>'삭제',
        'save'=>'저장', 'status'=>'상태', 'offers'=>'판매 옵션과 변형',
        'summary'=>'간단한 설명', 'description'=>'상세 설명', 'no_products'=>'상품을 찾을 수 없습니다.',
        'kits'=>'묶음 상품',
    ],
    'pt' => [
        'products'=>'Produtos', 'filters'=>'Filtros', 'apply'=>'Aplicar',
        'pages'=>'Páginas', 'price'=>'Preço', 'remove'=>'Excluir',
        'save'=>'Salvar', 'status'=>'Estado', 'offers'=>'Ofertas e variantes',
        'summary'=>'Descrição breve', 'description'=>'Descrição completa', 'no_products'=>'Nenhum produto encontrado.',
        'kits'=>'Conjuntos',
    ],
    'uk' => [
        'products'=>'Товари', 'filters'=>'Фільтри', 'apply'=>'Застосувати',
        'pages'=>'Сторінки', 'price'=>'Ціна', 'remove'=>'Видалити',
        'save'=>'Зберегти', 'status'=>'Статус', 'offers'=>'Пропозиції та варіанти',
        'summary'=>'Короткий опис', 'description'=>'Повний опис', 'no_products'=>'Товарів не знайдено.',
        'kits'=>'Комплекти',
    ],
    'vi' => [
        'products'=>'Sản phẩm', 'filters'=>'Bộ lọc', 'apply'=>'Áp dụng',
        'pages'=>'Trang', 'price'=>'Giá', 'remove'=>'Xóa',
        'save'=>'Lưu', 'status'=>'Trạng thái', 'offers'=>'Lựa chọn bán hàng và biến thể',
        'summary'=>'Mô tả ngắn', 'description'=>'Mô tả đầy đủ', 'no_products'=>'Không tìm thấy sản phẩm.',
        'kits'=>'Gói sản phẩm',
    ],
    'zh' => [
        'products'=>'商品', 'filters'=>'筛选', 'apply'=>'应用',
        'pages'=>'页码', 'price'=>'价格', 'remove'=>'删除',
        'save'=>'保存', 'status'=>'状态', 'offers'=>'报价与规格',
        'summary'=>'简短说明', 'description'=>'完整说明', 'no_products'=>'未找到商品。',
        'kits'=>'套装',
    ],
];
$sortLabels = [
    'en' => ['product_sort_asc'=>'Ascending', 'product_sort_desc'=>'Descending'],
    'ru' => ['product_sort_asc'=>'По возрастанию', 'product_sort_desc'=>'По убыванию'],
    'de' => ['product_sort_asc'=>'Aufsteigend', 'product_sort_desc'=>'Absteigend'],
    'es' => ['product_sort_asc'=>'Ascendente', 'product_sort_desc'=>'Descendente'],
    'fr' => ['product_sort_asc'=>'Croissant', 'product_sort_desc'=>'Décroissant'],
    'ja' => ['product_sort_asc'=>'昇順', 'product_sort_desc'=>'降順'],
    'ko' => ['product_sort_asc'=>'오름차순', 'product_sort_desc'=>'내림차순'],
    'pt' => ['product_sort_asc'=>'Crescente', 'product_sort_desc'=>'Decrescente'],
    'uk' => ['product_sort_asc'=>'За зростанням', 'product_sort_desc'=>'За спаданням'],
    'vi' => ['product_sort_asc'=>'Tăng dần', 'product_sort_desc'=>'Giảm dần'],
    'zh' => ['product_sort_asc'=>'升序', 'product_sort_desc'=>'降序'],
];
$filterKeys = ['filter_position','filter_off','filter_top','filter_left','filter_price','filter_fields','filter_search','filter_sort','filter_live'];
$filterLabels = [
    'ru'=>'Расположение фильтров|Не использовать|Сверху|Слева|Подбор по цене|Характеристики товаров|Поиск по каталогу|Сортировка товаров|Живой фильтр без кнопки «Применить»',
    'en'=>'Filter position|Disabled|Above products|Left of products|Filter by price|Product attributes|Catalog search|Product sorting|Live filters without Apply',
    'de'=>'Filterposition|Deaktiviert|Über den Produkten|Links neben den Produkten|Preisfilter|Produktmerkmale|Katalogsuche|Produktsortierung|Live-Filter ohne Anwenden',
    'es'=>'Posición de filtros|Desactivados|Encima de los productos|A la izquierda|Filtrar por precio|Atributos del producto|Búsqueda en el catálogo|Orden de productos|Filtros en vivo sin Aplicar',
    'fr'=>'Position des filtres|Désactivés|Au-dessus des produits|À gauche|Filtrer par prix|Caractéristiques des produits|Recherche dans le catalogue|Tri des produits|Filtres en direct sans Appliquer',
    'ja'=>'フィルターの位置|無効|商品の上|商品の左|価格で絞り込み|商品属性|カタログ検索|商品並べ替え|適用ボタンなしで絞り込み',
    'ko'=>'필터 위치|사용 안 함|상품 위|상품 왼쪽|가격 필터|상품 속성|카탈로그 검색|상품 정렬|적용 버튼 없는 실시간 필터',
    'pt'=>'Posição dos filtros|Desativados|Acima dos produtos|À esquerda|Filtrar por preço|Atributos do produto|Pesquisa no catálogo|Ordenação de produtos|Filtros ao vivo sem Aplicar',
    'uk'=>'Розташування фільтрів|Не використовувати|Зверху|Ліворуч|Добір за ціною|Характеристики товарів|Пошук у каталозі|Сортування товарів|Живий фільтр без кнопки «Застосувати»',
    'vi'=>'Vị trí bộ lọc|Tắt|Phía trên sản phẩm|Bên trái sản phẩm|Lọc theo giá|Thuộc tính sản phẩm|Tìm trong danh mục|Sắp xếp sản phẩm|Lọc trực tiếp không cần Áp dụng',
    'zh'=>'筛选器位置|停用|商品上方|商品左侧|按价格筛选|商品属性|目录搜索|商品排序|实时筛选，无需点击应用',
];
$brandKeys = ['brands','brand_add','brand_website','brand_logo','brand_upload'];
$brandLabels = [
    'ru'=>'Бренды|Добавить бренд|Сайт бренда|Логотип|Загрузить логотип',
    'en'=>'Brands|Add brand|Brand website|Logo|Upload logo',
    'de'=>'Marken|Marke hinzufügen|Website der Marke|Logo|Logo hochladen',
    'es'=>'Marcas|Añadir marca|Sitio de la marca|Logotipo|Subir logotipo',
    'fr'=>'Marques|Ajouter une marque|Site de la marque|Logo|Téléverser un logo',
    'ja'=>'ブランド|ブランドを追加|ブランドサイト|ロゴ|ロゴをアップロード',
    'ko'=>'브랜드|브랜드 추가|브랜드 웹사이트|로고|로고 업로드',
    'pt'=>'Marcas|Adicionar marca|Site da marca|Logotipo|Carregar logotipo',
    'uk'=>'Бренди|Додати бренд|Сайт бренду|Логотип|Завантажити логотип',
    'vi'=>'Thương hiệu|Thêm thương hiệu|Trang thương hiệu|Logo|Tải logo',
    'zh'=>'品牌|添加品牌|品牌网站|标志|上传标志',
];
$viewLabels = [
    'ru'=>'Плитка|Список', 'en'=>'Grid|List', 'de'=>'Raster|Liste', 'es'=>'Cuadrícula|Lista',
    'fr'=>'Grille|Liste', 'ja'=>'グリッド|リスト', 'ko'=>'격자|목록', 'pt'=>'Grelha|Lista',
    'uk'=>'Плитка|Список', 'vi'=>'Lưới|Danh sách', 'zh'=>'网格|列表',
];
$resizeLabels = ['ru'=>'Изменить ширину столбца', 'en'=>'Resize column', 'de'=>'Spaltenbreite ändern',
    'es'=>'Cambiar el ancho de la columna', 'fr'=>'Redimensionner la colonne', 'ja'=>'列幅を変更', 'ko'=>'열 너비 조정',
    'pt'=>'Redimensionar coluna', 'uk'=>'Змінити ширину стовпця', 'vi'=>'Đổi độ rộng cột', 'zh'=>'调整列宽'];
$moveLabels = ['ru'=>'Переместить столбец', 'en'=>'Move column', 'de'=>'Spalte verschieben',
    'es'=>'Mover columna', 'fr'=>'Déplacer la colonne', 'ja'=>'列を移動', 'ko'=>'열 이동',
    'pt'=>'Mover coluna', 'uk'=>'Перемістити стовпець', 'vi'=>'Di chuyển cột', 'zh'=>'移动列'];
$bulkLabels = [
    'ru'=>'Выбрать все товары на странице|Выбрать товар|Активировать выбранные|Деактивировать выбранные|Удалить выбранные|Удалить выбранные товары?',
    'en'=>'Select all products on this page|Select product|Activate selected|Deactivate selected|Delete selected|Delete the selected products?',
    'de'=>'Alle Produkte auf dieser Seite auswählen|Produkt auswählen|Ausgewählte aktivieren|Ausgewählte deaktivieren|Ausgewählte löschen|Ausgewählte Produkte löschen?',
    'es'=>'Seleccionar todos los productos de esta página|Seleccionar producto|Activar selección|Desactivar selección|Eliminar selección|¿Eliminar los productos seleccionados?',
    'fr'=>'Sélectionner tous les produits de cette page|Sélectionner le produit|Activer la sélection|Désactiver la sélection|Supprimer la sélection|Supprimer les produits sélectionnés ?',
    'ja'=>'このページの商品をすべて選択|商品を選択|選択した商品を有効化|選択した商品を無効化|選択した商品を削除|選択した商品を削除しますか？',
    'ko'=>'이 페이지의 모든 상품 선택|상품 선택|선택 항목 활성화|선택 항목 비활성화|선택 항목 삭제|선택한 상품을 삭제할까요?',
    'pt'=>'Selecionar todos os produtos desta página|Selecionar produto|Ativar selecionados|Desativar selecionados|Excluir selecionados|Excluir os produtos selecionados?',
    'uk'=>'Вибрати всі товари на сторінці|Вибрати товар|Активувати вибрані|Деактивувати вибрані|Видалити вибрані|Видалити вибрані товари?',
    'vi'=>'Chọn tất cả sản phẩm trên trang|Chọn sản phẩm|Bật mục đã chọn|Tắt mục đã chọn|Xóa mục đã chọn|Xóa các sản phẩm đã chọn?',
    'zh'=>'选择本页所有商品|选择商品|启用所选商品|停用所选商品|删除所选商品|删除所选商品？',
];
$characteristicLabels = [
    'ru'=>'Добавить характеристику раздела|Единица измерения|Значение|Добавить ещё',
    'en'=>'Add category attribute|Unit of measure|Value|Add another',
    'de'=>'Kategorieattribut hinzufügen|Maßeinheit|Wert|Weiteres hinzufügen',
    'es'=>'Añadir atributo de categoría|Unidad de medida|Valor|Añadir otro',
    'fr'=>'Ajouter un attribut de catégorie|Unité de mesure|Valeur|Ajouter un autre',
    'ja'=>'カテゴリ属性を追加|単位|値|さらに追加',
    'ko'=>'카테고리 속성 추가|단위|값|다른 속성 추가',
    'pt'=>'Adicionar atributo da categoria|Unidade de medida|Valor|Adicionar outro',
    'uk'=>'Додати характеристику розділу|Одиниця вимірювання|Значення|Додати ще',
    'vi'=>'Thêm thuộc tính danh mục|Đơn vị đo|Giá trị|Thêm mục khác',
    'zh'=>'添加分类属性|计量单位|值|再添加一项',
];
$productLinks = ['ru'=>'Открыть товар в магазине', 'en'=>'Open product in the shop', 'de'=>'Produkt im Shop öffnen',
    'es'=>'Abrir producto en la tienda', 'fr'=>'Ouvrir le produit dans la boutique', 'ja'=>'ショップで商品を開く', 'ko'=>'상점에서 상품 열기',
    'pt'=>'Abrir produto na loja', 'uk'=>'Відкрити товар у магазині', 'vi'=>'Mở sản phẩm trong cửa hàng', 'zh'=>'在商城中打开商品'];
$positivePrices = ['ru'=>'Цена товара должна быть больше нуля.', 'en'=>'Product price must be greater than zero.',
    'de'=>'Der Produktpreis muss größer als null sein.', 'es'=>'El precio del producto debe ser mayor que cero.',
    'fr'=>'Le prix du produit doit être supérieur à zéro.', 'ja'=>'商品価格は0より大きくしてください。',
    'ko'=>'상품 가격은 0보다 커야 합니다.', 'pt'=>'O preço do produto deve ser maior que zero.',
    'uk'=>'Ціна товару має бути більшою за нуль.', 'vi'=>'Giá sản phẩm phải lớn hơn 0.', 'zh'=>'商品价格必须大于零。'];
$roundingLabels = [
    'ru'=>'Округление цен|До сотен|До десятков|До единиц|До десятых|До сотых|Введите число с точностью до двух знаков после точки.',
    'en'=>'Price rounding|To hundreds|To tens|To units|To tenths|To hundredths|Enter a number with up to two decimal places.',
    'de'=>'Preisrundung|Auf Hunderter|Auf Zehner|Auf Einer|Auf Zehntel|Auf Hundertstel|Geben Sie eine Zahl mit bis zu zwei Nachkommastellen ein.',
    'es'=>'Redondeo de precios|A centenas|A decenas|A unidades|A décimas|A centésimas|Introduce un número con hasta dos decimales.',
    'fr'=>'Arrondi des prix|À la centaine|À la dizaine|À l’unité|Au dixième|Au centième|Saisissez un nombre comportant au plus deux décimales.',
    'ja'=>'価格の丸め|百の位|十の位|一の位|小数第1位|小数第2位|小数第2位までの数値を入力してください。',
    'ko'=>'가격 반올림|백 단위|십 단위|일 단위|소수 첫째 자리|소수 둘째 자리|소수점 아래 두 자리까지 입력하세요.',
    'pt'=>'Arredondamento de preços|Às centenas|Às dezenas|Às unidades|Aos décimos|Aos centésimos|Introduza um número com até duas casas decimais.',
    'uk'=>'Округлення цін|До сотень|До десятків|До одиниць|До десятих|До сотих|Введіть число з точністю до двох знаків після крапки.',
    'vi'=>'Làm tròn giá|Đến hàng trăm|Đến hàng chục|Đến hàng đơn vị|Đến phần mười|Đến phần trăm|Nhập số có tối đa hai chữ số thập phân.',
    'zh'=>'价格取整|取整到百位|取整到十位|取整到个位|保留一位小数|保留两位小数|请输入最多两位小数的数字。',
];
$numberLabels = [
    'ru'=>'Введите число.|Допустимы только цифры.', 'en'=>'Enter a number.|Use digits only.',
    'de'=>'Geben Sie eine Zahl ein.|Verwenden Sie nur Ziffern.', 'es'=>'Introduce un número.|Usa solo dígitos.',
    'fr'=>'Saisissez un nombre.|Utilisez uniquement des chiffres.', 'ja'=>'数値を入力してください。|数字のみ入力してください。',
    'ko'=>'숫자를 입력하세요.|숫자만 입력하세요.', 'pt'=>'Introduza um número.|Use apenas dígitos.',
    'uk'=>'Введіть число.|Дозволені лише цифри.', 'vi'=>'Nhập một số.|Chỉ dùng chữ số.', 'zh'=>'请输入数字。|仅允许输入数字字符。',
];
$categoryLabels = [
    'ru'=>'Добавить раздел|Добавить вложенный раздел|Редактировать раздел|Верхний уровень|Родительский раздел|Порядок|Учитывать товары подразделов|Открыть раздел на витрине|Удалить раздел?|Перед удалением переместите товары, подразделы и поля.|Раздел с таким адресом уже существует.|Очистить фильтры',
    'en'=>'Add category|Add subcategory|Edit category|Top level|Parent category|Order|Include products from subcategories|View category in storefront|Delete category?|Move products, subcategories and fields before deleting.|This category address is already in use.|Clear filters',
    'de'=>'Kategorie hinzufügen|Unterkategorie hinzufügen|Kategorie bearbeiten|Oberste Ebene|Übergeordnete Kategorie|Reihenfolge|Produkte aus Unterkategorien einschließen|Kategorie im Shop öffnen|Kategorie löschen?|Verschieben Sie zuerst Produkte, Unterkategorien und Felder.|Diese Kategorieadresse ist bereits vergeben.|Filter löschen',
    'es'=>'Añadir categoría|Añadir subcategoría|Editar categoría|Nivel superior|Categoría principal|Orden|Incluir productos de subcategorías|Abrir categoría en la tienda|¿Eliminar categoría?|Mueve los productos, subcategorías y campos antes de eliminar.|Esta dirección de categoría ya se usa.|Limpiar filtros',
    'fr'=>'Ajouter une catégorie|Ajouter une sous-catégorie|Modifier la catégorie|Niveau supérieur|Catégorie parente|Ordre|Inclure les produits des sous-catégories|Ouvrir la catégorie dans la boutique|Supprimer la catégorie ?|Déplacez les produits, sous-catégories et champs avant de supprimer.|Cette adresse de catégorie est déjà utilisée.|Effacer les filtres',
    'ja'=>'カテゴリを追加|サブカテゴリを追加|カテゴリを編集|最上位|親カテゴリ|並び順|サブカテゴリの商品を含める|ショップでカテゴリを開く|カテゴリを削除しますか？|削除前に商品、サブカテゴリ、項目を移動してください。|このカテゴリのアドレスは使用済みです。|フィルターをクリア',
    'ko'=>'카테고리 추가|하위 카테고리 추가|카테고리 편집|최상위|상위 카테고리|순서|하위 카테고리 상품 포함|상점에서 카테고리 열기|카테고리를 삭제할까요?|삭제하기 전에 상품, 하위 카테고리, 필드를 이동하세요.|이미 사용 중인 카테고리 주소입니다.|필터 지우기',
    'pt'=>'Adicionar categoria|Adicionar subcategoria|Editar categoria|Nível superior|Categoria principal|Ordem|Incluir produtos das subcategorias|Abrir categoria na loja|Excluir categoria?|Mova produtos, subcategorias e campos antes de excluir.|Este endereço de categoria já está em uso.|Limpar filtros',
    'uk'=>'Додати розділ|Додати підрозділ|Редагувати розділ|Верхній рівень|Батьківський розділ|Порядок|Враховувати товари підрозділів|Відкрити розділ на вітрині|Видалити розділ?|Перед видаленням перемістіть товари, підрозділи та поля.|Розділ із такою адресою вже існує.|Очистити фільтри',
    'vi'=>'Thêm danh mục|Thêm danh mục con|Sửa danh mục|Cấp cao nhất|Danh mục cha|Thứ tự|Gồm sản phẩm từ danh mục con|Mở danh mục trong cửa hàng|Xóa danh mục?|Di chuyển sản phẩm, danh mục con và trường trước khi xóa.|Địa chỉ danh mục đã được sử dụng.|Xóa bộ lọc',
    'zh'=>'添加分类|添加子分类|编辑分类|顶级|父分类|顺序|包含子分类商品|在商城打开分类|删除分类？|请先移走商品、子分类和字段。|此分类地址已被使用。|清除筛选',
];
$seoLabels = [
    'ru'=>'Править товар|Править бренд|Метатеги и SEO|Закрыть от индексации',
    'en'=>'Edit product|Edit brand|Metadata and SEO|Prevent indexing',
    'de'=>'Produkt bearbeiten|Marke bearbeiten|Metadaten und SEO|Indexierung verhindern',
    'es'=>'Editar producto|Editar marca|Metadatos y SEO|Impedir la indexación',
    'fr'=>'Modifier le produit|Modifier la marque|Métadonnées et SEO|Empêcher l’indexation',
    'ja'=>'商品を編集|ブランドを編集|メタデータとSEO|インデックス登録を無効にする',
    'ko'=>'상품 편집|브랜드 편집|메타데이터 및 SEO|색인 생성 방지',
    'pt'=>'Editar produto|Editar marca|Metadados e SEO|Impedir a indexação',
    'uk'=>'Редагувати товар|Редагувати бренд|Метатеги й SEO|Закрити від індексації',
    'vi'=>'Sửa sản phẩm|Sửa thương hiệu|Thẻ meta và SEO|Không lập chỉ mục',
    'zh'=>'编辑商品|编辑品牌|元数据与SEO|禁止索引',
];
$categoryMediaLabels = [
    'ru'=>'Адрес раздела|Иконка или картинка|Иконка|Переместить раздел',
    'en'=>'Category address|Icon or image|Icon|Move category',
    'de'=>'Kategorieadresse|Symbol oder Bild|Symbol|Kategorie verschieben',
    'es'=>'Dirección de categoría|Icono o imagen|Icono|Mover categoría',
    'fr'=>'Adresse de la catégorie|Icône ou image|Icône|Déplacer la catégorie',
    'ja'=>'カテゴリのアドレス|アイコンまたは画像|アイコン|カテゴリを移動',
    'ko'=>'카테고리 주소|아이콘 또는 이미지|아이콘|카테고리 이동',
    'pt'=>'Endereço da categoria|Ícone ou imagem|Ícone|Mover categoria',
    'uk'=>'Адреса розділу|Іконка або зображення|Іконка|Перемістити розділ',
    'vi'=>'Địa chỉ danh mục|Biểu tượng hoặc ảnh|Biểu tượng|Di chuyển danh mục',
    'zh'=>'分类地址|图标或图片|图标|移动分类',
];
$galleryLabels = [
    'ru'=>'Назад|Вперёд|Варианты|Фото варианта',
    'en'=>'Previous|Next|Variants|Variant photo',
    'de'=>'Zurück|Weiter|Varianten|Variantenfoto',
    'es'=>'Anterior|Siguiente|Variantes|Foto de la variante',
    'fr'=>'Précédent|Suivant|Variantes|Photo de la variante',
    'ja'=>'前へ|次へ|バリエーション|バリエーションの写真',
    'ko'=>'이전|다음|옵션|옵션 사진',
    'pt'=>'Anterior|Seguinte|Variantes|Foto da variante',
    'uk'=>'Назад|Далі|Варіанти|Фото варіанта',
    'vi'=>'Trước|Tiếp|Biến thể|Ảnh biến thể',
    'zh'=>'上一张|下一张|规格|规格图片',
];
$orderKeys = explode(' ', 'checkout_contact checkout_summary checkout_edit_cart checkout_subtotal print_pdf packing_slip recipient empty_orders order_history add_order_status status_phase phase_pending phase_reserved phase_complete phase_cancelled awaiting_payment packing ready_pickup in_transit sales_report report_period report_days report_orders report_sales report_amount report_by_day report_date');
$orderLabels = [
    'ru'=>'Контактные данные|Ваш заказ|Изменить корзину|Товары|Печать / PDF|Накладная|Получатель|Заказов за этот период нет.|История статусов|Добавить статус|Что происходит с остатком|Без резерва|Резервируется|Списан · заказ завершён|Возвращается · заказ отменён|Ожидает оплаты|Собирается|Готов к выдаче|В доставке|Статистика продаж|Период|дн.|Создано заказов|Завершено заказов|Сумма завершённых заказов|По дням|Дата',
    'en'=>'Contact details|Your order|Edit basket|Items|Print / PDF|Packing slip|Recipient|No orders in this period.|Status history|Add status|Stock behavior|Unreserved|Reserved|Consumed · completed|Released · cancelled|Awaiting payment|Packing|Ready for pickup|In transit|Sales statistics|Period|days|Orders placed|Orders completed|Completed order value|Daily breakdown|Date',
    'de'=>'Kontaktdaten|Ihre Bestellung|Warenkorb ändern|Artikel|Drucken / PDF|Lieferschein|Empfänger|Keine Bestellungen in diesem Zeitraum.|Statusverlauf|Status hinzufügen|Lagerbestand|Nicht reserviert|Reserviert|Abgebucht · abgeschlossen|Freigegeben · storniert|Zahlung ausstehend|Wird gepackt|Abholbereit|Unterwegs|Verkaufsstatistik|Zeitraum|Tage|Bestellungen eingegangen|Bestellungen abgeschlossen|Wert abgeschlossener Bestellungen|Tagesübersicht|Datum',
    'es'=>'Datos de contacto|Tu pedido|Editar cesta|Artículos|Imprimir / PDF|Albarán|Destinatario|No hay pedidos en este periodo.|Historial de estados|Añadir estado|Efecto en las existencias|Sin reserva|Reservadas|Descontadas · completado|Liberadas · cancelado|Pendiente de pago|Preparando|Listo para recoger|En tránsito|Estadísticas de ventas|Periodo|días|Pedidos recibidos|Pedidos completados|Importe de pedidos completados|Detalle diario|Fecha',
    'fr'=>'Coordonnées|Votre commande|Modifier le panier|Articles|Imprimer / PDF|Bon de livraison|Destinataire|Aucune commande pour cette période.|Historique des statuts|Ajouter un statut|Effet sur le stock|Non réservé|Réservé|Déduit · terminé|Libéré · annulé|En attente de paiement|En préparation|Prêt à retirer|En transit|Statistiques des ventes|Période|jours|Commandes reçues|Commandes terminées|Montant des commandes terminées|Détail quotidien|Date',
    'ja'=>'連絡先|ご注文|カートを編集|商品|印刷 / PDF|納品書|受取人|この期間の注文はありません。|ステータス履歴|ステータスを追加|在庫の扱い|未確保|確保済み|引き落とし · 完了|確保を解除 · キャンセル|支払い待ち|梱包中|受け取り可能|配送中|売上統計|期間|日|受付件数|完了件数|完了注文の金額|日別内訳|日付',
    'ko'=>'연락처|주문 내역|장바구니 수정|상품|인쇄 / PDF|납품서|수령인|이 기간에는 주문이 없습니다.|상태 기록|상태 추가|재고 처리|미예약|예약됨|차감 · 완료|예약 취소 · 주문 취소|결제 대기|포장 중|수령 가능|배송 중|판매 통계|기간|일|접수 주문|완료 주문|완료 주문 금액|일별 내역|날짜',
    'pt'=>'Dados de contacto|A sua encomenda|Editar carrinho|Artigos|Imprimir / PDF|Guia de remessa|Destinatário|Sem encomendas neste período.|Histórico de estados|Adicionar estado|Efeito no stock|Não reservado|Reservado|Deduzido · concluído|Libertado · cancelado|A aguardar pagamento|Em preparação|Pronto para recolha|Em trânsito|Estatísticas de vendas|Período|dias|Encomendas recebidas|Encomendas concluídas|Valor das encomendas concluídas|Detalhe diário|Data',
    'uk'=>'Контактні дані|Ваше замовлення|Змінити кошик|Товари|Друк / PDF|Накладна|Отримувач|За цей період замовлень немає.|Історія статусів|Додати статус|Зміна залишку|Без резерву|Резервується|Списано · завершено|Повертається · скасовано|Очікує оплати|Комплектується|Готове до видачі|У доставці|Статистика продажів|Період|дн.|Створено замовлень|Завершено замовлень|Сума завершених замовлень|За днями|Дата',
    'vi'=>'Thông tin liên hệ|Đơn hàng của bạn|Sửa giỏ hàng|Sản phẩm|In / PDF|Phiếu giao hàng|Người nhận|Không có đơn trong khoảng thời gian này.|Lịch sử trạng thái|Thêm trạng thái|Xử lý tồn kho|Chưa giữ hàng|Giữ hàng|Đã xuất kho · hoàn tất|Hoàn lại · đã hủy|Chờ thanh toán|Đang đóng gói|Sẵn sàng nhận|Đang giao|Thống kê bán hàng|Khoảng thời gian|ngày|Đơn đã đặt|Đơn hoàn tất|Giá trị đơn hoàn tất|Chi tiết theo ngày|Ngày',
    'zh'=>'联系信息|您的订单|修改购物车|商品|打印 / PDF|送货单|收件人|此期间没有订单。|状态记录|添加状态|库存处理|未预留|已预留|已扣减 · 已完成|已释放 · 已取消|待付款|打包中|可自提|配送中|销售统计|时间范围|天|已下订单数|已完成订单数|已完成订单金额|每日明细|日期',
];
$integrationKeys = explode(' ', 'integrations marketplace_api delivery_api payment_api integration_catalog integration_refresh integration_current integration_install integration_update integration_enabled integration_disabled integration_docs integration_on integration_check integration_connected integration_connection_only integration_secret_saved integration_unavailable payment_pending payment_check_dashboard');
$integrationLabels = [
    'ru'=>'Интеграции|Маркетплейсы|Службы доставки|Способы оплаты|Каталог модулей GitHub|Проверить каталог|Все доступные модули установлены.|Установить|Обновить|Включён|Выключен|Документация|Включить модуль|Проверить сохранённые настройки|Подключение подтверждено.|Настройка и проверка подключения. Обмен товарами и отправка заказов выполняются в кабинете сервиса.|Ключ сохранён|Сервис недоступен или отклонил запрос.|Счёт уже запрашивали. Проверьте его в кабинете платёжного сервиса.|Проверьте оплату в кабинете сервиса перед подтверждением заказа.',
    'en'=>'Integrations|Marketplaces|Delivery services|Payment methods|GitHub module catalog|Check catalog|All available modules are installed.|Install|Update|Enabled|Disabled|Documentation|Enable module|Test saved settings|Connection confirmed.|Configure and test the connection. Manage listings and shipments in the provider dashboard.|Key saved|The service is unavailable or rejected the request.|An invoice was already requested. Check it in your payment provider dashboard.|Check payment in your provider dashboard before confirming this order.',
    'de'=>'Integrationen|Marktplätze|Lieferdienste|Zahlungsarten|GitHub-Modulkatalog|Katalog prüfen|Alle verfügbaren Module sind installiert.|Installieren|Aktualisieren|Aktiviert|Deaktiviert|Dokumentation|Modul aktivieren|Gespeicherte Einstellungen testen|Verbindung bestätigt.|Verbindung einrichten und testen. Angebote und Sendungen im Anbieterportal verwalten.|Schlüssel gespeichert|Der Dienst ist nicht verfügbar oder hat die Anfrage abgelehnt.|Eine Rechnung wurde bereits angefordert. Prüfen Sie das Zahlungsportal.|Prüfen Sie die Zahlung im Anbieterportal vor der Bestätigung.',
    'es'=>'Integraciones|Mercados|Servicios de entrega|Formas de pago|Catálogo de módulos de GitHub|Consultar catálogo|Todos los módulos disponibles están instalados.|Instalar|Actualizar|Activado|Desactivado|Documentación|Activar módulo|Probar ajustes guardados|Conexión confirmada.|Configura y prueba la conexión. Gestiona los anuncios y envíos en el panel del proveedor.|Clave guardada|El servicio no está disponible o rechazó la solicitud.|Ya se solicitó una factura. Consúltala en el panel de pagos.|Verifica el pago en el panel del proveedor antes de confirmar.',
    'fr'=>'Intégrations|Places de marché|Services de livraison|Moyens de paiement|Catalogue des modules GitHub|Vérifier le catalogue|Tous les modules disponibles sont installés.|Installer|Mettre à jour|Activé|Désactivé|Documentation|Activer le module|Tester les paramètres enregistrés|Connexion confirmée.|Configurez et testez la connexion. Gérez les offres et expéditions dans le tableau de bord du prestataire.|Clé enregistrée|Le service est indisponible ou a refusé la demande.|Une facture a déjà été demandée. Consultez le tableau de bord de paiement.|Vérifiez le paiement chez le prestataire avant de confirmer.',
    'ja'=>'連携|マーケットプレイス|配送サービス|支払い方法|GitHub モジュールカタログ|カタログを確認|利用可能なモジュールはすべてインストール済みです。|インストール|更新|有効|無効|ドキュメント|モジュールを有効にする|保存済みの設定をテスト|接続を確認しました。|接続を設定してテストします。出品と発送はサービスの管理画面で行います。|キーを保存済み|サービスが利用できないか、リクエストが拒否されました。|請求は依頼済みです。決済サービスの管理画面をご確認ください。|注文確定前に決済サービスで支払いをご確認ください。',
    'ko'=>'연동|마켓플레이스|배송 서비스|결제 수단|GitHub 모듈 카탈로그|카탈로그 확인|사용 가능한 모듈이 모두 설치되었습니다.|설치|업데이트|활성|비활성|문서|모듈 활성화|저장된 설정 테스트|연결이 확인되었습니다.|연결을 구성하고 테스트합니다. 상품 등록과 발송은 서비스 관리자에서 처리하세요.|키 저장됨|서비스가 응답하지 않거나 요청을 거부했습니다.|청구서를 이미 요청했습니다. 결제사 관리 화면에서 확인하세요.|주문 확정 전에 결제사 관리 화면에서 결제를 확인하세요.',
    'pt'=>'Integrações|Marketplaces|Serviços de entrega|Métodos de pagamento|Catálogo de módulos do GitHub|Verificar catálogo|Todos os módulos disponíveis estão instalados.|Instalar|Atualizar|Ativo|Inativo|Documentação|Ativar módulo|Testar configurações guardadas|Ligação confirmada.|Configure e teste a ligação. Gira anúncios e envios no painel do prestador.|Chave guardada|O serviço está indisponível ou recusou o pedido.|Já foi pedido um pagamento. Consulte o painel do prestador.|Verifique o pagamento no painel do prestador antes de confirmar.',
    'uk'=>'Інтеграції|Маркетплейси|Служби доставки|Способи оплати|Каталог модулів GitHub|Перевірити каталог|Усі доступні модулі встановлено.|Встановити|Оновити|Увімкнено|Вимкнено|Документація|Увімкнути модуль|Перевірити збережені налаштування|Підключення підтверджено.|Налаштуйте та перевірте підключення. Керуйте товарами й відправленнями в кабінеті сервісу.|Ключ збережено|Сервіс недоступний або відхилив запит.|Рахунок уже запитано. Перевірте його в кабінеті платіжного сервісу.|Перевірте оплату в кабінеті сервісу перед підтвердженням.',
    'vi'=>'Tích hợp|Sàn thương mại|Dịch vụ giao hàng|Phương thức thanh toán|Danh mục mô-đun GitHub|Kiểm tra danh mục|Đã cài tất cả mô-đun hiện có.|Cài đặt|Cập nhật|Đã bật|Đã tắt|Tài liệu|Bật mô-đun|Thử cài đặt đã lưu|Đã xác nhận kết nối.|Cấu hình và kiểm tra kết nối. Quản lý sản phẩm và vận chuyển trong bảng điều khiển dịch vụ.|Đã lưu khóa|Dịch vụ không khả dụng hoặc từ chối yêu cầu.|Đã yêu cầu hóa đơn. Hãy kiểm tra trong dịch vụ thanh toán.|Kiểm tra thanh toán trong bảng điều khiển trước khi xác nhận.',
    'zh'=>'集成|电商平台|配送服务|支付方式|GitHub 模块目录|检查目录|已安装所有可用模块。|安装|更新|已启用|已停用|文档|启用模块|测试已保存的设置|连接成功。|配置并测试连接。请在服务后台管理商品和发货。|密钥已保存|服务不可用或拒绝了请求。|已请求账单。请在支付服务后台查看。|确认订单前，请在服务后台核对支付结果。',
];
$integrationFields = [
    'campaign_id'=>'Campaign ID','client_id'=>'Client ID','api_key'=>'API Key','app_token'=>'Access Token','user_key'=>'User Authorization Key',
    'source_address'=>'Origin address','client_secret'=>'Client Secret','shop_id'=>'Shop ID','secret_key'=>'Secret Key',
    'terminal_key'=>'TerminalKey','terminal_password'=>'Terminal Password','public_id'=>'Public ID','api_secret'=>'API Secret',
    'merchant_login'=>'MerchantLogin','password1'=>'Password #1',
];
foreach ($productLabels as $locale => $messages) {
    $labels[$locale] = ($labels[$locale] ?? []) + $inventoryLabels[$locale] + $messages + $sortLabels[$locale] + ($listLabels[$locale] ?? [])
        + array_combine($filterKeys, explode('|', $filterLabels[$locale]))
        + array_combine($brandKeys, explode('|', $brandLabels[$locale]))
        + array_combine(['view_grid','view_list'], explode('|', $viewLabels[$locale]))
        + ['resize_column'=>$resizeLabels[$locale], 'move_column'=>$moveLabels[$locale], 'product_open_store'=>$productLinks[$locale],
            'positive_price'=>$positivePrices[$locale]]
        + array_combine(['bulk_select_page','bulk_select_product','bulk_activate','bulk_deactivate','bulk_delete','bulk_confirm'], explode('|', $bulkLabels[$locale]))
        + array_combine(['characteristic_add','characteristic_unit','characteristic_value','characteristic_more'], explode('|', $characteristicLabels[$locale]))
        + array_combine(['price_rounding','round_hundreds','round_tens','round_units','round_tenths','round_hundredths','price_format'], explode('|', $roundingLabels[$locale]))
        + array_combine(['number_format','digits_format'], explode('|', $numberLabels[$locale]))
        + array_combine(['category_add','category_child','category_edit','category_root','category_parent','category_order',
            'category_nested','category_open','category_delete','category_not_empty','category_slug_used','filter_reset'], explode('|', $categoryLabels[$locale]))
        + array_combine(['product_edit_context','brand_edit_context','seo_edit_context','seo_noindex'], explode('|', $seoLabels[$locale]))
        + array_combine(['category_slug','category_visual','category_icon','category_move'], explode('|', $categoryMediaLabels[$locale]))
        + array_combine(['gallery_prev','gallery_next','variants','variant_photo'], explode('|', $galleryLabels[$locale]))
        + array_combine($orderKeys, explode('|', $orderLabels[$locale]))
        + array_combine($integrationKeys, explode('|', $integrationLabels[$locale]))
        + ($locale === 'ru' ? ['source_address'=>'Адрес отправления', 'terminal_password'=>'Пароль терминала'] : [])
        + $integrationFields;
}
$workspaceLabels = [
    'ru'=>['Как пользоваться|Медиатека|Выбрать в менеджере файлов|Использовать фото|Добавить запись|Редактировать|Найти|Подходящих записей нет.|Все разделы|К чему относится|Товар в комплекте|Цена до скидки|Дополнительно|Массовое редактирование CSV|Характеристики|Галерея товаров|Дополнительные цены|Перенаправления|Значения характеристик|Такая запись уже есть.|Номер заказа|Все статусы|Адрес доставки|Адрес не указан.|Покупатель забирает заказ самостоятельно.|Адрес, оставленный покупателем', 'Найдите товар по названию или артикулу. Колонки и ширину таблицы можно настроить под задачу.|Вложенные разделы задают меню витрины. Перетащите раздел за ручку, чтобы изменить порядок или родителя.|У каждой комплектации свой артикул, цена и остаток. Например, дрель с аккумулятором и без него — два варианта одной карточки.|Характеристика задаёт название, тип и единицу измерения. Например, «Напряжение» — число в вольтах. Поле раздела наследуют подразделы. Сортировка доступна для числовых полей.|Покажите товар сразу в нескольких разделах. Например, дрель остаётся в «Инструментах» и появляется в «Акциях».|Добавьте дополнительные ракурсы к карточке товара. Главное фото и фото вариантов выбираются в редакторе товара.|Дополнительные цены задаются отдельно для каждого варианта. Текущую и старую цены меняйте в карточке товара.|Укажите, какие товары и в каком количестве входят в набор. Например, один шуруповёрт и два аккумулятора. Состав учитывается при расчёте остатков.|Старый адрес перенаправляет покупателя на текущую карточку. При смене адреса в редакторе запись создаётся автоматически.|Здесь задаются конкретные значения: например, бренд Bosch для товара или напряжение 18 В для варианта. Сначала создайте характеристику.|Добавьте места хранения. Количество по каждому складу задаётся в редакторе варианта.|Загрузите CSV или YML и проверьте, сколько позиций добавится и обновится. Используйте одно название для повторных загрузок от одного поставщика.|Загрузите файлы или откройте менеджер. Чтобы привязать фото, выберите его из карточки товара или галереи.|Ищите заказ по номеру или статусу. В карточке виден способ получения и доступна накладная для печати.|Выберите период. Сумма продаж учитывает завершённые заказы на дату завершения.|Добавьте логотип, описание и сайт. На витрине данные показываются при выборе бренда.|Выберите расположение и состав фильтров. Живой фильтр обновляет каталог при изменении значений.|Выберите сервис, сохраните ключи и проверьте подключение. Новые профили доступны через каталог GitHub.|Настройте страницу витрины, валюту, доставку и оформление заказа. Шаблоны редактируются отдельно внутри этой вкладки.'],
    'en'=>['How to use|Media library|Choose in file manager|Use image|Add record|Edit|Find|No matching records.|All categories|Applies to|Bundle item|Before discount|More|Bulk CSV editing|Characteristics|Product gallery|Additional prices|Redirects|Characteristic values|This record already exists.|Order number|All statuses|Delivery address|Address missing.|The customer collects the order.|Address provided by the customer', 'Find products by name or SKU. Adjust the visible columns and widths to suit your work.|Categories organize the storefront. Drag a category handle to reorder it or change its parent.|Each variant has its own SKU, price, and stock. A drill with or without a battery can share one product page.|A characteristic defines a name, type, and unit, such as voltage in volts. Subcategories inherit fields. Numeric fields can be sorted.|Show a product in multiple categories. A drill can appear in both Tools and Deals.|Add extra views to the product gallery. Choose the main and variant photos in the product editor.|Set additional prices for each variant. Edit the current and old prices in the product editor.|Choose items and quantities for a bundle, such as one drill and two batteries. The contents affect stock.|Old URLs redirect customers to the current product. Changing the URL in the editor creates a redirect automatically.|Enter values such as Bosch for a product or 18 V for a variant. Create the characteristic first.|Add storage locations. Set each warehouse quantity in the variant editor.|Upload CSV or YML and review the estimated additions and updates. Reuse the same name for each supplier.|Upload files or open the manager. Assign photos in the product editor or product gallery.|Find orders by number or status. Open the order to see how it will be received and print a packing slip.|Choose a period. Sales count completed orders as of their completion date.|Add a logo, description, and website. Shoppers see them when they select the brand.|Choose where filters appear and what they include. Live filters update the catalog as selections change.|Choose a service, save its keys, and test the connection. Get new profiles from the GitHub catalog.|Set the storefront page, currency, delivery, and checkout. Editable templates are in a separate section of this tab.'],
    'de'=>['So geht’s|Mediathek|Im Dateimanager auswählen|Bild verwenden|Eintrag hinzufügen|Bearbeiten|Suchen|Keine passenden Einträge.|Alle Kategorien|Gilt für|Bestandteil|Vor Rabatt|Mehr|CSV-Sammelbearbeitung|Merkmale|Produktgalerie|Zusätzliche Preise|Weiterleitungen|Merkmalswerte|Der Eintrag besteht bereits.|Bestellnummer|Alle Status|Lieferadresse|Adresse fehlt.|Der Kunde holt die Bestellung ab.|Vom Kunden angegebene Adresse', 'Produkte nach Name oder Artikelnummer suchen.|Kategorien ziehen, um Reihenfolge oder Ebene zu ändern.|Jede Variante hat Artikelnummer, Preis und Bestand.|Merkmale wie Spannung oder Marke definieren. Unterkategorien erben Merkmale.|Ein Produkt kann in mehreren Kategorien erscheinen, etwa Werkzeuge und Angebote.|Zusätzliche Produktfotos zur Galerie hinzufügen.|Legen Sie Zusatzpreise je Variante fest. Bearbeiten Sie den aktuellen und alten Preis im Produkteditor.|Bestandteile und Mengen eines Sets festlegen.|Frühere Adressen leiten zum aktuellen Produkt weiter.|Merkmalwerte eingeben, etwa 18 V für eine Variante.|Lager anlegen. Bestände stehen bei der Variante.|CSV oder YML hochladen und Vorschau prüfen.|Dateien hochladen und im Produkt oder in der Galerie zuordnen.|Nach Nummer oder Status suchen und Lieferscheine drucken.|Abgeschlossene Bestellungen im gewählten Zeitraum sehen.|Logo, Beschreibung und Website pflegen.|Position und Inhalt der Filter wählen.|Schlüssel speichern, Verbindung prüfen und GitHub-Profile installieren.|Shopseite, Währung, Lieferung und Vorlagen einstellen.'],
    'es'=>['Cómo usar|Biblioteca multimedia|Elegir en el gestor de archivos|Usar imagen|Añadir registro|Editar|Buscar|No hay resultados.|Todas las categorías|Se aplica a|Componente del lote|Antes del descuento|Más|Edición masiva CSV|Características|Galería de productos|Precios adicionales|Redirecciones|Valores de características|El registro ya existe.|Número de pedido|Todos los estados|Dirección de entrega|Falta la dirección.|El cliente recoge el pedido.|Dirección facilitada por el cliente', 'Busca productos por nombre o SKU.|Arrastra categorías para ordenarlas o moverlas.|Cada variante tiene su SKU, precio y existencias.|Define características como voltaje o marca. Las subcategorías las heredan.|Muestra un producto en varias categorías, por ejemplo Herramientas y Ofertas.|Añade fotos adicionales a la galería.|Define precios adicionales por variante. Edita los precios actual y anterior en la ficha del producto.|Define los componentes y cantidades de un lote.|Las direcciones antiguas redirigen al producto actual.|Introduce valores como 18 V para una variante.|Añade almacenes. Edita las cantidades en la variante.|Carga CSV o YML y revisa la vista previa.|Carga archivos y asígnalos desde el producto o la galería.|Busca pedidos por número o estado e imprime albaranes.|Consulta los pedidos completados en el periodo.|Añade un logotipo, descripción y sitio web.|Elige la posición y el contenido de los filtros.|Guarda claves, prueba conexiones e instala perfiles de GitHub.|Configura página, moneda, entrega y plantillas.'],
    'fr'=>['Mode d’emploi|Médiathèque|Choisir dans le gestionnaire de fichiers|Utiliser la photo|Ajouter une entrée|Modifier|Rechercher|Aucun résultat.|Toutes les catégories|S’applique à|Article du lot|Avant remise|Plus|Édition CSV en lot|Caractéristiques|Galerie produits|Prix supplémentaires|Redirections|Valeurs des caractéristiques|Cette entrée existe déjà.|Numéro de commande|Tous les statuts|Adresse de livraison|Adresse manquante.|Le client retire sa commande.|Adresse fournie par le client', 'Recherchez par nom ou référence.|Déplacez les catégories pour modifier leur ordre ou leur parent.|Chaque variante a sa référence, son prix et son stock.|Définissez des caractéristiques comme la tension ou la marque. Les sous-catégories en héritent.|Affichez un produit dans plusieurs catégories, par exemple Outils et Promotions.|Ajoutez des photos à la galerie du produit.|Définissez les prix supplémentaires par variante. Modifiez les prix actuel et précédent dans la fiche produit.|Indiquez les composants et quantités d’un lot.|Les anciennes adresses redirigent vers le produit actuel.|Saisissez des valeurs comme 18 V pour une variante.|Ajoutez des entrepôts. Réglez les stocks dans la variante.|Importez un CSV ou YML et vérifiez l’aperçu.|Chargez des fichiers et associez-les dans le produit ou la galerie.|Cherchez par numéro ou statut et imprimez les bons de livraison.|Consultez les commandes terminées sur la période.|Ajoutez logo, description et site web.|Choisissez la position et le contenu des filtres.|Enregistrez les clés, testez la connexion et installez des profils GitHub.|Configurez la page, la devise, la livraison et les modèles.'],
    'ja'=>['使い方|メディアライブラリ|ファイルマネージャーで選択|画像を使う|項目を追加|編集|検索|該当する項目はありません。|すべてのカテゴリ|対象|セット構成品|割引前価格|その他|CSV一括編集|商品属性|商品ギャラリー|追加価格|リダイレクト|属性値|この項目は登録済みです。|注文番号|すべてのステータス|配送先住所|住所が未入力です。|購入者が注文を受け取ります。|購入者が入力した住所', '商品名または SKU で検索します。|カテゴリをドラッグして順序や親を変更します。|仕様ごとに SKU、価格、在庫があります。|電圧やブランドなどの属性を定義します。サブカテゴリは属性を引き継ぎます。|工具とセールなど、複数のカテゴリに商品を表示します。|商品ギャラリーに追加の写真を設定します。|仕様ごとに追加価格を設定できます。現在価格と旧価格は商品編集画面で編集します。|セットの構成品と数量を設定します。|古い URL は現在の商品ページに転送します。|仕様の電圧 18 V など、属性値を入力します。|倉庫を追加し、仕様画面で倉庫ごとの在庫を設定します。|CSV または YML をアップロードして内容を確認します。|ファイルを登録し、商品やギャラリーで選びます。|番号や状態で注文を探し、納品書を印刷します。|選んだ期間の完了注文を確認します。|ロゴ、説明、ウェブサイトを追加します。|フィルターの位置と内容を選択します。|キーを保存して接続を確認し、GitHub からプロファイルを追加します。|ショップページ、通貨、配送、テンプレートを設定します。'],
    'ko'=>['사용 방법|미디어 라이브러리|파일 관리자에서 선택|이미지 사용|항목 추가|편집|검색|일치하는 항목이 없습니다.|모든 카테고리|적용 대상|묶음 구성품|할인 전 가격|더 보기|CSV 일괄 편집|특성|상품 갤러리|추가 가격|리디렉션|특성 값|이미 있는 항목입니다.|주문 번호|모든 상태|배송 주소|주소가 없습니다.|고객이 주문을 직접 수령합니다.|고객이 제공한 주소', '상품명이나 SKU로 검색합니다.|카테고리를 끌어 순서나 상위 카테고리를 바꿉니다.|옵션마다 SKU, 가격, 재고가 있습니다.|전압이나 브랜드 같은 특성을 만듭니다. 하위 카테고리는 특성을 상속합니다.|공구와 할인 행사처럼 한 상품을 여러 카테고리에 표시합니다.|상품 갤러리에 사진을 추가합니다.|옵션별 추가 가격을 설정하세요. 현재 가격과 이전 가격은 상품 편집 화면에서 수정합니다.|세트의 구성품과 수량을 정합니다.|이전 주소를 현재 상품 주소로 연결합니다.|옵션의 전압 18 V 같은 값을 입력합니다.|창고를 추가하고 옵션에서 창고별 재고를 설정합니다.|CSV 또는 YML을 업로드하고 미리 봅니다.|파일을 올린 후 상품이나 갤러리에서 선택합니다.|번호 또는 상태로 주문을 찾고 납품서를 인쇄합니다.|선택 기간의 완료 주문을 확인합니다.|로고, 설명, 웹사이트를 추가합니다.|필터 위치와 항목을 선택합니다.|키를 저장하고 연결을 확인한 뒤 GitHub 프로필을 설치합니다.|상점 페이지, 통화, 배송, 템플릿을 설정합니다.'],
    'pt'=>['Como usar|Biblioteca multimédia|Escolher no gestor de ficheiros|Usar imagem|Adicionar registo|Editar|Pesquisar|Sem resultados.|Todas as categorias|Aplica-se a|Componente do conjunto|Antes do desconto|Mais|Edição CSV em massa|Características|Galeria de produtos|Preços adicionais|Redirecionamentos|Valores das características|Este registo já existe.|Número da encomenda|Todos os estados|Morada de entrega|Falta a morada.|O cliente recolhe a encomenda.|Morada indicada pelo cliente', 'Pesquise por nome ou SKU.|Arraste categorias para alterar a ordem ou o nível.|Cada variante tem SKU, preço e stock próprios.|Defina características como tensão ou marca. As subcategorias herdam-nas.|Mostre um produto em várias categorias, como Ferramentas e Promoções.|Adicione fotos à galeria do produto.|Defina preços adicionais por variante. Edite o preço atual e o anterior na ficha do produto.|Defina os componentes e quantidades de um conjunto.|Os endereços antigos redirecionam para o produto atual.|Introduza valores como 18 V numa variante.|Adicione armazéns e defina o stock na variante.|Carregue CSV ou YML e reveja a pré-visualização.|Carregue ficheiros e associe-os no produto ou na galeria.|Encontre encomendas por número ou estado e imprima guias.|Consulte encomendas concluídas no período.|Adicione logótipo, descrição e página web.|Escolha a posição e o conteúdo dos filtros.|Guarde chaves, teste ligações e instale perfis do GitHub.|Defina página, moeda, entrega e modelos.'],
    'uk'=>['Як користуватися|Медіатека|Вибрати в менеджері файлів|Використати фото|Додати запис|Редагувати|Знайти|Відповідних записів немає.|Усі розділи|До чого належить|Товар у комплекті|Ціна до знижки|Додатково|Масове редагування CSV|Характеристики|Галерея товарів|Додаткові ціни|Перенаправлення|Значення характеристик|Такий запис уже є.|Номер замовлення|Усі статуси|Адреса доставки|Адресу не вказано.|Покупець забирає замовлення самостійно.|Адреса, яку залишив покупець', 'Знайдіть товар за назвою або артикулом.|Перетягуйте розділи, щоб змінити порядок або батьківський розділ.|Кожен варіант має артикул, ціну та залишок.|Налаштуйте характеристики, наприклад напругу або бренд. Підрозділи успадковують їх.|Покажіть товар у кількох розділах, наприклад «Інструменти» та «Акції».|Додайте фото до галереї товару.|Задавайте додаткові ціни для кожного варіанта. Поточну та стару ціни змінюйте в картці товару.|Задайте склад і кількість товарів у комплекті.|Старі адреси переспрямовують на актуальний товар.|Задайте значення, наприклад 18 В для варіанта.|Додайте склади й задайте залишки у варіантах.|Завантажте CSV або YML і перевірте попередній перегляд.|Завантажте файли й виберіть фото у товарі або галереї.|Знайдіть замовлення за номером чи статусом і надрукуйте накладну.|Перегляньте завершені замовлення за вибраний період.|Додайте логотип, опис і сайт.|Оберіть місце та склад фільтрів.|Збережіть ключі, перевірте підключення та встановіть профілі з GitHub.|Налаштуйте сторінку, валюту, доставку й шаблони.'],
    'vi'=>['Cách sử dụng|Thư viện media|Chọn trong trình quản lý tệp|Dùng ảnh|Thêm bản ghi|Sửa|Tìm|Không có kết quả phù hợp.|Tất cả danh mục|Áp dụng cho|Món trong bộ|Giá trước giảm|Thêm|Sửa hàng loạt bằng CSV|Đặc tính|Bộ ảnh sản phẩm|Giá bổ sung|Chuyển hướng|Giá trị đặc tính|Bản ghi này đã tồn tại.|Mã đơn hàng|Tất cả trạng thái|Địa chỉ giao|Thiếu địa chỉ.|Khách hàng tự nhận hàng.|Địa chỉ khách hàng đã cung cấp', 'Tìm sản phẩm theo tên hoặc SKU.|Kéo danh mục để đổi thứ tự hoặc cấp cha.|Mỗi biến thể có SKU, giá và tồn kho riêng.|Tạo đặc tính như điện áp hoặc thương hiệu. Danh mục con kế thừa chúng.|Hiện sản phẩm trong nhiều danh mục, như Công cụ và Khuyến mãi.|Thêm ảnh vào bộ ảnh sản phẩm.|Đặt giá bổ sung theo biến thể. Sửa giá hiện tại và giá cũ trong trình chỉnh sửa sản phẩm.|Chọn món và số lượng trong bộ.|Địa chỉ cũ chuyển đến trang sản phẩm hiện tại.|Nhập giá trị như 18 V cho biến thể.|Thêm kho và thiết lập tồn kho trong biến thể.|Tải CSV hoặc YML và xem trước.|Tải tệp lên và gắn ảnh trong sản phẩm hoặc bộ ảnh.|Tìm theo mã hoặc trạng thái và in phiếu giao.|Xem đơn hoàn thành theo kỳ.|Thêm logo, mô tả và trang web.|Chọn vị trí và nội dung bộ lọc.|Lưu khóa, kiểm tra kết nối và cài hồ sơ GitHub.|Cài đặt trang, tiền tệ, giao hàng và mẫu.'],
    'zh'=>['使用说明|媒体库|在文件管理器中选择|使用图片|添加记录|编辑|查找|没有匹配的记录。|所有分类|适用于|套装商品|折扣前价格|更多|CSV 批量编辑|商品属性|商品相册|附加价格|重定向|属性值|此记录已存在。|订单号|所有状态|配送地址|缺少地址。|顾客自行取货。|顾客填写的地址', '按名称或 SKU 查找商品。|拖动分类可调整顺序或更改上级。|每个规格都有独立的 SKU、价格和库存。|设置电压或品牌等属性，子分类继承这些属性。|同一商品可显示在多个分类，例如工具和促销。|为商品相册添加额外照片。|可为每个规格设置附加价格。当前价格和原价在商品编辑器中修改。|设置套装内商品及数量。|旧地址会跳转到当前商品页。|填写具体属性值，例如规格的电压 18 V。|添加仓库并在规格中设置库存。|上传 CSV 或 YML 后先预览。|上传文件，并在商品或相册中选择照片。|按订单号或状态搜索，打印送货单。|查看指定时段内完成的订单。|添加标志、简介和网站。|选择筛选器位置和内容。|保存密钥并测试连接，从 GitHub 安装配置。|设置店铺页面、币种、配送和模板。'],
];
$workspaceKeys = 'shop_help media_library media_pick media_use record_add record_edit record_find record_empty record_all_categories record_target record_component record_old_price record_more record_csv record_properties record_gallery record_extra_prices record_redirects record_property_values record_duplicate record_order_id record_all_statuses record_delivery_address record_address_missing record_pickup_info record_customer_address';
$workspaceHelpKeys = 'help_products help_categories help_offers help_fields help_placements help_images help_prices help_kits help_aliases help_values help_warehouses help_tab_import help_tab_images help_tab_orders help_tab_sales help_tab_brands help_tab_filters help_tab_integrations help_tab_settings';
foreach ($workspaceLabels as $locale => [$messages, $guidance]) {
    $labels[$locale] += array_combine(explode(' ', $workspaceKeys), explode('|', $messages))
        + array_combine(explode(' ', $workspaceHelpKeys), explode('|', $guidance));
}
$exports = [
    'ru'=>'Экспорт|Скачать каталог|Внешние ссылки|Включить ссылки|Перевыпустить ключи|Отключить ссылки|Скопировать|Ссылка скопирована|Для XLSX требуется расширение PHP Zip.|Файл можно скачать целиком или по разделу с подразделами. CSV использует точку с запятой. YML содержит активные товарные предложения.|Ссылки выгружают актуальный каталог активных товаров. Передавайте их только получателям выгрузки. Перевыпуск ключей отключает прежние ссылки.',
    'en'=>'Export|Download catalog|External links|Enable links|Rotate keys|Disable links|Copy|Link copied|XLSX requires the PHP Zip extension.|Download the full catalog or a category with its subcategories. CSV uses semicolons. YML contains active product offers.|Links serve the current catalog of active products. Share them only with intended recipients. Rotating keys revokes the previous links.',
    'de'=>'Export|Katalog herunterladen|Externe Links|Links aktivieren|Schlüssel erneuern|Links deaktivieren|Kopieren|Link kopiert|XLSX benötigt die PHP-Erweiterung Zip.|Laden Sie den gesamten Katalog oder eine Kategorie mit Unterkategorien herunter. CSV verwendet Semikolons. YML enthält aktive Angebote.|Links liefern den aktuellen Katalog aktiver Produkte. Teilen Sie sie nur mit den vorgesehenen Empfängern. Neue Schlüssel widerrufen alte Links.',
    'es'=>'Exportación|Descargar catálogo|Enlaces externos|Activar enlaces|Renovar claves|Desactivar enlaces|Copiar|Enlace copiado|XLSX requiere la extensión Zip de PHP.|Descarga todo el catálogo o una categoría con subcategorías. CSV usa punto y coma. YML incluye ofertas activas.|Los enlaces sirven el catálogo actualizado de productos activos. Compártelos solo con los destinatarios. Renovar las claves revoca los enlaces anteriores.',
    'fr'=>'Export|Télécharger le catalogue|Liens externes|Activer les liens|Renouveler les clés|Désactiver les liens|Copier|Lien copié|XLSX nécessite l’extension PHP Zip.|Téléchargez le catalogue ou une catégorie avec ses sous-catégories. Le CSV utilise le point-virgule. Le YML contient les offres actives.|Les liens donnent accès au catalogue actif à jour. Partagez-les uniquement avec les destinataires prévus. Le renouvellement révoque les liens précédents.',
    'ja'=>'エクスポート|カタログをダウンロード|外部リンク|リンクを有効にする|キーを再発行|リンクを無効にする|コピー|リンクをコピーしました|XLSX には PHP Zip 拡張が必要です。|全カタログまたはサブカテゴリを含むカテゴリを取得できます。CSV はセミコロン区切りです。YML は有効な商品を含みます。|リンクから最新の有効な商品を取得できます。必要な相手にだけ共有してください。キーを再発行すると古いリンクは無効になります。',
    'ko'=>'내보내기|카탈로그 다운로드|외부 링크|링크 켜기|키 재발급|링크 끄기|복사|링크를 복사했습니다|XLSX에는 PHP Zip 확장이 필요합니다.|전체 카탈로그 또는 하위 카테고리를 포함한 카테고리를 받으세요. CSV는 세미콜론을 사용합니다. YML에는 활성 상품이 포함됩니다.|링크로 최신 활성 상품을 받습니다. 필요한 사람에게만 공유하세요. 키를 재발급하면 이전 링크는 만료됩니다.',
    'pt'=>'Exportar|Descarregar catálogo|Ligações externas|Ativar ligações|Renovar chaves|Desativar ligações|Copiar|Ligação copiada|XLSX requer a extensão PHP Zip.|Descarregue o catálogo ou uma categoria com subcategorias. O CSV usa ponto e vírgula. O YML inclui ofertas ativas.|As ligações dão acesso ao catálogo ativo atualizado. Partilhe apenas com os destinatários. Renovar chaves revoga ligações anteriores.',
    'uk'=>'Експорт|Завантажити каталог|Зовнішні посилання|Увімкнути посилання|Перевидати ключі|Вимкнути посилання|Скопіювати|Посилання скопійовано|Для XLSX потрібне розширення PHP Zip.|Завантажте каталог або розділ із підрозділами. CSV використовує крапку з комою. YML містить активні пропозиції.|Посилання відкривають актуальні активні товари. Передавайте їх лише одержувачам. Перевидання ключів відкликає старі посилання.',
    'vi'=>'Xuất dữ liệu|Tải danh mục|Liên kết bên ngoài|Bật liên kết|Đổi khóa|Tắt liên kết|Sao chép|Đã sao chép liên kết|XLSX cần phần mở rộng PHP Zip.|Tải toàn bộ danh mục hoặc một danh mục cùng các danh mục con. CSV dùng dấu chấm phẩy. YML chứa mặt hàng đang hoạt động.|Liên kết cung cấp danh mục hiện tại. Chỉ chia sẻ với người nhận dự kiến. Đổi khóa sẽ hủy các liên kết cũ.',
    'zh'=>'导出|下载商品目录|外部链接|启用链接|重新生成密钥|停用链接|复制|已复制链接|XLSX 需要 PHP Zip 扩展。|下载完整目录或包含子分类的目录。CSV 使用分号分隔。YML 包含上架商品。|链接实时提供上架商品目录。仅与预期接收方共享。重新生成密钥会使旧链接失效。',
];
$exportKeys = explode(' ', 'export export_download export_links export_enable export_rotate export_disable export_copy export_copied export_zip help_tab_export export_link_help');
foreach ($exports as $locale => $content) $labels[$locale] += array_combine($exportKeys, explode('|', $content));
$checkoutLabels = [
    'ru'=>'Оформление и доставка|Поля из формы сайта|Стандартные контакты|Создать форму|Добавляйте поля и задавайте их типы в обычном редакторе форм. Имя, email и телефон обязательны. Если их нет в выбранной форме, магазин добавит их.|Правила доставки и самовывоза|Стоимость доставки|Минимальная сумма для доставки|Бесплатная доставка от|Адрес самовывоза|Условия для покупателя|Пороги считаются от суммы товаров. Ноль отключает соответствующий порог.|Выберите доступный способ получения.',
    'en'=>'Checkout and delivery|Fields from a site form|Default contacts|Create a form|Add fields and choose their types in the regular form editor. Name, email and phone are required and added if absent.|Delivery and pickup rules|Delivery fee|Minimum subtotal for delivery|Free delivery from|Pickup address|Customer instructions|Thresholds use the item subtotal. Zero disables the threshold.|Choose an available fulfillment method.',
    'de'=>'Bestellung und Lieferung|Felder eines Formulars|Standardkontakte|Formular erstellen|Felder und Typen im Formulareditor bearbeiten. Name, E-Mail und Telefon sind erforderlich.|Liefer- und Abholregeln|Liefergebühr|Mindestsumme für Lieferung|Kostenlose Lieferung ab|Abholadresse|Hinweise für Kunden|Grenzen beziehen sich auf die Warensumme. Null deaktiviert die Grenze.|Wählen Sie eine verfügbare Empfangsart.',
    'es'=>'Pedido y entrega|Campos de un formulario|Contacto predeterminado|Crear formulario|Añade campos y tipos en el editor habitual. Nombre, correo y teléfono son obligatorios.|Reglas de entrega y recogida|Coste de entrega|Importe mínimo para entrega|Entrega gratuita desde|Dirección de recogida|Instrucciones al cliente|Los límites usan el subtotal. Cero desactiva el límite.|Elige una opción de recepción disponible.',
    'fr'=>'Commande et livraison|Champs d’un formulaire|Contacts par défaut|Créer un formulaire|Ajoutez les champs et leurs types dans l’éditeur habituel. Nom, courriel et téléphone sont obligatoires.|Règles de livraison et de retrait|Frais de livraison|Minimum pour la livraison|Livraison gratuite à partir de|Adresse de retrait|Consignes au client|Les seuils portent sur le sous-total. Zéro désactive le seuil.|Choisissez un mode de réception disponible.',
    'ja'=>'注文と配送|サイトフォームの項目|標準の連絡先|フォームを作成|通常のフォームエディターで項目を追加し、型を設定します。氏名、メール、電話は必須です。|配送と店頭受取のルール|配送料|配送の最低商品合計|送料無料の最低金額|受取場所|購入者への案内|金額の判定には商品小計を使います。0 で無効になります。|利用可能な受け取り方法を選んでください。',
    'ko'=>'주문과 배송|사이트 양식 필드|기본 연락처|양식 만들기|일반 양식 편집기에서 항목과 유형을 지정합니다. 이름, 이메일, 전화는 필수입니다.|배송과 수령 규칙|배송비|배송 최소 상품 금액|무료 배송 기준|수령 주소|고객 안내|상품 소계로 계산하며 0은 기준을 끕니다.|사용 가능한 수령 방법을 선택하세요.',
    'pt'=>'Encomenda e entrega|Campos de um formulário|Contactos predefinidos|Criar formulário|Adicione campos e tipos no editor habitual. Nome, e-mail e telefone são obrigatórios.|Regras de entrega e recolha|Taxa de entrega|Subtotal mínimo para entrega|Entrega grátis a partir de|Morada de recolha|Instruções ao cliente|Os limites usam o subtotal dos artigos. Zero desativa o limite.|Escolha um método de receção disponível.',
    'uk'=>'Оформлення й доставка|Поля з форми сайту|Стандартні контакти|Створити форму|Додавайте поля та типи у звичайному редакторі форм. Ім’я, email і телефон обов’язкові.|Правила доставки й самовивозу|Вартість доставки|Мінімальна сума для доставки|Безкоштовна доставка від|Адреса самовивозу|Умови для покупця|Пороги рахуються від суми товарів. Нуль вимикає поріг.|Виберіть доступний спосіб отримання.',
    'vi'=>'Đặt hàng và giao hàng|Trường từ biểu mẫu|Liên hệ mặc định|Tạo biểu mẫu|Thêm trường và kiểu trong trình chỉnh sửa biểu mẫu. Tên, email và điện thoại là bắt buộc.|Quy tắc giao và nhận|Phí giao hàng|Tiền hàng tối thiểu để giao|Miễn phí giao từ|Địa chỉ nhận hàng|Hướng dẫn cho khách|Ngưỡng tính theo tiền hàng. Số 0 tắt ngưỡng.|Chọn một cách nhận có sẵn.',
    'zh'=>'结账和配送|网站表单字段|默认联系信息|创建表单|在普通表单编辑器中添加字段和类型。姓名、邮箱和电话为必填项。|配送与自提规则|配送费|配送最低商品小计|免配送费起点|自提地址|买家须知|按商品小计计算门槛。0 表示停用。|请选择可用的收货方式。',
];
$checkoutKeys = explode(' ', 'checkout_settings checkout_select_form checkout_default_form checkout_new_form checkout_form_help delivery_rules delivery_fee delivery_minimum delivery_free delivery_pickup_address delivery_rules_text delivery_threshold_help delivery_unavailable');
foreach ($checkoutLabels as $locale => $content) {
    $labels[$locale] += array_combine($checkoutKeys, explode('|', $content));
    $labels[$locale]['help_tab_checkout'] = $labels[$locale]['delivery_threshold_help'];
}
$feedLabels = [
    'ru'=>'Проверка YML|Проверить YML|Без фото|Без штрихкода|Без бренда|Что заполнить',
    'en'=>'YML feed check|Check YML feed|Missing photo|Missing barcode|Missing brand|Fields to fill in',
    'de'=>'YML-Feed prüfen|YML prüfen|Ohne Foto|Ohne Barcode|Ohne Marke|Auszufüllende Felder',
    'es'=>'Revisión del feed YML|Revisar YML|Sin foto|Sin código de barras|Sin marca|Campos por completar',
    'fr'=>'Vérification du flux YML|Vérifier le flux YML|Sans photo|Sans code-barres|Sans marque|Champs à renseigner',
    'ja'=>'YML フィードの確認|YML を確認|写真なし|バーコードなし|ブランドなし|入力が必要な項目',
    'ko'=>'YML 피드 검사|YML 검사|사진 없음|바코드 없음|브랜드 없음|입력할 항목',
    'pt'=>'Verificação do feed YML|Verificar YML|Sem foto|Sem código de barras|Sem marca|Campos a preencher',
    'uk'=>'Перевірка YML|Перевірити YML|Без фото|Без штрихкоду|Без бренду|Що заповнити',
    'vi'=>'Kiểm tra nguồn cấp YML|Kiểm tra YML|Thiếu ảnh|Thiếu mã vạch|Thiếu thương hiệu|Trường cần điền',
    'zh'=>'YML 数据源检查|检查 YML|缺少图片|缺少条码|缺少品牌|待填写字段',
];
$feedKeys = explode(' ', 'feed_check feed_check_action feed_missing_image feed_missing_barcode feed_missing_brand feed_issues');
$importLabels = [
    'ru'=>'Цена в копейках|Определить автоматически|Без изменений|Сопоставление столбцов|Примеры изменений',
    'en'=>'Price in minor currency units|Detect automatically|Unchanged|Column mapping|Examples of changes',
    'de'=>'Preis in kleinsten Währungseinheiten|Automatisch erkennen|Unverändert|Spaltenzuordnung|Beispiele für Änderungen',
    'es'=>'Precio en céntimos|Detectar automáticamente|Sin cambios|Correspondencia de columnas|Ejemplos de cambios',
    'fr'=>'Prix en unité monétaire minimale|Détecter automatiquement|Inchangé|Correspondance des colonnes|Exemples de modifications',
    'ja'=>'最小通貨単位の価格|自動認識|変更なし|列の対応付け|変更例',
    'ko'=>'최소 통화 단위 가격|자동 감지|변경 없음|열 매핑|변경 예시',
    'pt'=>'Preço na unidade monetária mínima|Detetar automaticamente|Sem alterações|Mapeamento de colunas|Exemplos de alterações',
    'uk'=>'Ціна в копійках|Визначити автоматично|Без змін|Зіставлення стовпців|Приклади змін',
    'vi'=>'Giá theo đơn vị tiền tệ nhỏ nhất|Tự động nhận diện|Không thay đổi|Ánh xạ cột|Ví dụ thay đổi',
    'zh'=>'最小货币单位价格|自动识别|未更改|列映射|更改示例',
];
$importKeys = explode(' ', 'import_minor import_auto import_unchanged import_match import_sample');
foreach ($feedLabels as $locale => $content) {
    $labels[$locale] += array_combine($feedKeys, explode('|', $content))
        + array_combine($importKeys, explode('|', $importLabels[$locale]));
}
$widgetKeys = explode(' ', 'widget_items widget_carousel widget_mobile widget_desktop widget_auto widget_no_filters widget_limit widget_search');
$widgets = [
    'ru'=>'Список товаров|Карусель товаров|Столбцы на телефоне|Столбцы на компьютере|Автоматически|В разделе нет доступных фильтров.|Товаров на странице|Поисковое слово',
    'en'=>'Product list|Product carousel|Columns on mobile|Columns on desktop|Automatic|No filters are available for this category.|Products per page|Search term',
    'uk'=>'Список товарів|Карусель товарів|Стовпці на телефоні|Стовпці на комп’ютері|Автоматично|Для цього розділу немає доступних фільтрів.|Товарів на сторінці|Пошукове слово',
    'de'=>'Produktliste|Produktkarussell|Spalten auf Mobilgeräten|Spalten auf dem Desktop|Automatisch|Für diese Kategorie sind keine Filter verfügbar.|Produkte pro Seite|Suchwort',
    'fr'=>'Liste de produits|Carrousel de produits|Colonnes sur mobile|Colonnes sur ordinateur|Automatique|Aucun filtre disponible pour cette catégorie.|Produits par page|Terme de recherche',
    'es'=>'Lista de productos|Carrusel de productos|Columnas en móvil|Columnas en ordenador|Automático|No hay filtros para esta categoría.|Productos por página|Término de búsqueda',
    'pt'=>'Lista de produtos|Carrossel de produtos|Colunas no telemóvel|Colunas no computador|Automático|Esta categoria não tem filtros disponíveis.|Produtos por página|Termo de pesquisa',
    'ja'=>'商品一覧|商品カルーセル|モバイルの列数|デスクトップの列数|自動|このカテゴリで使用できる絞り込みはありません。|ページあたりの商品数|検索キーワード',
    'ko'=>'상품 목록|상품 캐러셀|모바일 열 수|데스크톱 열 수|자동|이 카테고리에 사용할 수 있는 필터가 없습니다.|페이지당 상품 수|검색어',
    'vi'=>'Danh sách sản phẩm|Băng chuyền sản phẩm|Số cột trên điện thoại|Số cột trên máy tính|Tự động|Danh mục này không có bộ lọc khả dụng.|Sản phẩm mỗi trang|Từ khóa tìm kiếm',
    'zh'=>'商品列表|商品轮播|移动端列数|桌面端列数|自动|此分类没有可用筛选项。|每页商品数|搜索关键词',
];
foreach ($widgets as $locale => $content) $labels[$locale] += array_combine($widgetKeys, explode('|', $content));
$catalogFolderLabels = [
    'ru'=>'Папка каталога',
    'en'=>'Catalog folder',
    'uk'=>'Папка каталогу',
    'de'=>'Katalogordner',
    'fr'=>'Dossier du catalogue',
    'es'=>'Carpeta del catálogo',
    'pt'=>'Pasta do catálogo',
    'ja'=>'カタログのフォルダー',
    'ko'=>'카탈로그 폴더',
    'vi'=>'Thư mục danh mục',
    'zh'=>'目录路径',
];
foreach ($catalogFolderLabels as $locale => $title) $labels[$locale]['catalog_folder'] = $title;
$pageTemplateLabels = [
    'ru'=>'Постоянные ссылки на товары|Карточка товара|Раздел с товарами|Править шаблон',
    'en'=>'Permanent product URLs|Product page|Category with products|Edit template',
    'uk'=>'Постійні посилання на товари|Картка товару|Розділ із товарами|Редагувати шаблон',
    'de'=>'Dauerhafte Produkt-URLs|Produktseite|Kategorie mit Produkten|Vorlage bearbeiten',
    'fr'=>'URL permanentes des produits|Fiche produit|Catégorie avec produits|Modifier le modèle',
    'es'=>'Enlaces permanentes de productos|Ficha de producto|Categoría con productos|Editar plantilla',
    'pt'=>'URLs permanentes dos produtos|Página de produto|Categoria com produtos|Editar modelo',
    'ja'=>'商品 URL を固定する|商品ページ|商品カテゴリーページ|テンプレートを編集',
    'ko'=>'상품 고유 URL 사용|상품 상세 페이지|상품 카테고리 페이지|템플릿 편집',
    'vi'=>'Liên kết sản phẩm cố định|Trang sản phẩm|Danh mục kèm sản phẩm|Sửa mẫu',
    'zh'=>'使用固定商品链接|商品详情页|商品分类页|编辑模板',
];
foreach ($pageTemplateLabels as $locale => $copy) {
    $labels[$locale] += array_combine(['short_product_urls','shop_product_template','shop_category_template','shop_edit_template'], explode('|', $copy));
    $labels[$locale]['help_tab_templates'] = $labels[$locale]['shop_edit_template'];
}
$templateParts = [
    'ru'=>'Фрагменты оформления','en'=>'Layout fragments','uk'=>'Фрагменти оформлення','de'=>'Layout-Fragmente',
    'fr'=>'Fragments de mise en page','es'=>'Fragmentos de diseño','pt'=>'Fragmentos do layout','ja'=>'レイアウト断片',
    'ko'=>'레이아웃 조각','vi'=>'Đoạn bố cục','zh'=>'布局片段',
];
foreach ($templateParts as $locale => $title) $labels[$locale]['template_parts'] = $title;
$templateGroups = [
    'ru'=>'Шаблоны маркетплейса|Страницы витрины|Строка раздела|Карточка в каталоге|Значок корзины|Блок оформления заказа|Заказы в кабинете|Страница маркетплейса',
    'en'=>'Marketplace templates|Storefront pages|Category row|Catalog card|Basket indicator|Checkout block|Account orders|Marketplace page',
    'uk'=>'Шаблони маркетплейсу|Сторінки вітрини|Рядок розділу|Картка в каталозі|Значок кошика|Блок оформлення замовлення|Замовлення в кабінеті|Сторінка маркетплейсу',
    'de'=>'Marketplace-Vorlagen|Shopseiten|Kategoriezeile|Katalogkarte|Warenkorbanzeige|Bestellblock|Bestellungen im Konto|Marketplace-Seite',
    'fr'=>'Modèles de la marketplace|Pages de la boutique|Ligne de catégorie|Fiche du catalogue|Indicateur du panier|Bloc de commande|Commandes du compte|Page de la marketplace',
    'es'=>'Plantillas del marketplace|Páginas de la tienda|Fila de categoría|Tarjeta del catálogo|Indicador del carrito|Bloque de compra|Pedidos de la cuenta|Página del marketplace',
    'pt'=>'Modelos do Marketplace|Páginas da loja|Linha de categoria|Cartão do catálogo|Indicador do carrinho|Bloco de finalização|Pedidos da conta|Página do Marketplace',
    'ja'=>'マーケットプレイスのテンプレート|ストアページ|カテゴリー行|カタログカード|カート表示|注文手続きブロック|アカウントの注文|マーケットプレイスページ',
    'ko'=>'마켓플레이스 템플릿|스토어 페이지|카테고리 행|카탈로그 카드|장바구니 표시|주문 블록|계정 주문|마켓플레이스 페이지',
    'vi'=>'Mẫu Marketplace|Trang cửa hàng|Dòng danh mục|Thẻ danh mục|Biểu tượng giỏ hàng|Khối đặt hàng|Đơn hàng trong tài khoản|Trang Marketplace',
    'zh'=>'商城模板|店铺页面|分类行|目录卡片|购物车标记|结账区块|账户订单|商城页面',
];
foreach ($templateGroups as $locale => $copy) {
    $labels[$locale] = array_combine(['templates','template_pages','template_category','template_item','template_basket',
        'template_checkout','template_account','template_page'], explode('|', $copy)) + $labels[$locale];
}
$settingsGroups = [
    'ru'=>'Витрина|Цены', 'en'=>'Storefront|Pricing', 'uk'=>'Вітрина|Ціни', 'de'=>'Shop|Preise', 'fr'=>'Vitrine|Prix',
    'es'=>'Tienda|Precios', 'pt'=>'Vitrine|Preços', 'ja'=>'ストアフロント|価格', 'ko'=>'스토어|가격',
    'vi'=>'Gian hàng|Giá', 'zh'=>'商城前台|价格',
];
foreach ($settingsGroups as $locale => $copy) {
    $labels[$locale] += array_combine(['shop_settings_catalog','shop_settings_prices'], explode('|', $copy));
}
$tokenLabels = [
    'ru'=>'Пагинация|Адрес ссылки|Изображение или иконка раздела|Вложенные разделы|Подпись элемента|Количество товаров|Содержимое блока|Сообщение магазина|Ссылки на страницы|Разметка Product в формате JSON-LD добавляется в HTML готовой карточки автоматически.',
    'en'=>'Pagination|Link URL|Category image or icon|Subcategories|Element label|Product count|Block content|Store notice|Page links|Product JSON-LD is added automatically to the rendered product page.',
    'uk'=>'Пагінація|Адреса посилання|Зображення або іконка розділу|Вкладені розділи|Підпис елемента|Кількість товарів|Вміст блока|Повідомлення магазину|Посилання на сторінки|Розмітка Product у форматі JSON-LD автоматично додається до HTML картки.',
    'de'=>'Seitennavigation|Linkadresse|Kategoriebild oder Symbol|Unterkategorien|Beschriftung|Produktanzahl|Blockinhalt|Shop-Hinweis|Seitenlinks|Product-JSON-LD wird automatisch zur fertigen Produktseite hinzugefügt.',
    'fr'=>'Pagination|Adresse du lien|Image ou icône de catégorie|Sous-catégories|Libellé|Nombre de produits|Contenu du bloc|Message de la boutique|Liens des pages|Le balisage Product JSON-LD est ajouté automatiquement à la fiche affichée.',
    'es'=>'Paginación|Dirección del enlace|Imagen o icono de categoría|Subcategorías|Etiqueta|Número de productos|Contenido del bloque|Aviso de la tienda|Enlaces de páginas|El marcado Product JSON-LD se añade automáticamente a la ficha generada.',
    'pt'=>'Paginação|Endereço do link|Imagem ou ícone da categoria|Subcategorias|Rótulo|Número de produtos|Conteúdo do bloco|Aviso da loja|Links de páginas|A marcação Product em JSON-LD é adicionada automaticamente à página do produto.',
    'ja'=>'ページ送り|リンク URL|カテゴリー画像またはアイコン|サブカテゴリー|ラベル|商品数|ブロックの内容|ストアのお知らせ|ページリンク|生成された商品ページには Product JSON-LD が自動で追加されます。',
    'ko'=>'페이지 탐색|링크 주소|카테고리 이미지 또는 아이콘|하위 카테고리|항목 이름표|상품 수|블록 내용|스토어 알림|페이지 링크|표시된 상품 페이지에 Product JSON-LD가 자동으로 추가됩니다.',
    'vi'=>'Phân trang|Địa chỉ liên kết|Ảnh hoặc biểu tượng danh mục|Danh mục con|Nhãn|Số sản phẩm|Nội dung khối|Thông báo cửa hàng|Liên kết trang|Đánh dấu Product JSON-LD tự động được thêm vào trang sản phẩm.',
    'zh'=>'分页|链接地址|分类图片或图标|子分类|元素标签|商品数量|区块内容|商城提示|页面链接|Product JSON-LD 会自动添加到生成的商品页中。',
];
foreach ($tokenLabels as $locale => $copy) {
    $labels[$locale] += array_combine(['template_pagination','template_token_url','template_token_visual','template_token_children',
        'template_token_label','template_token_count','template_token_content','template_token_notice','template_token_links','template_schema_help'], explode('|', $copy));
}
$galleryLabels = [
    'ru'=>['Подпись фото','Добавить фото'],
    'en'=>['Photo caption','Add photo'],
    'uk'=>['Підпис фото','Додати фото'],
    'de'=>['Bildbeschriftung','Foto hinzufügen'],
    'fr'=>['Légende de la photo','Ajouter une photo'],
    'es'=>['Pie de foto','Añadir foto'],
    'pt'=>['Legenda da foto','Adicionar foto'],
    'ja'=>['写真キャプション','写真を追加'],
    'ko'=>['사진 설명','사진 추가'],
    'vi'=>['Chú thích ảnh','Thêm ảnh'],
    'zh'=>['照片说明','添加照片'],
];
foreach ($galleryLabels as $locale => $copy) [$labels[$locale]['photo_caption'], $labels[$locale]['photo_add']] = $copy;
$oldPriceLabels = [
    'ru'=>'Старая цена', 'en'=>'Old price', 'uk'=>'Стара ціна', 'de'=>'Alter Preis',
    'fr'=>'Ancien prix', 'es'=>'Precio anterior', 'pt'=>'Preço anterior', 'ja'=>'旧価格',
    'ko'=>'이전 가격', 'vi'=>'Giá cũ', 'zh'=>'原价',
];
foreach ($oldPriceLabels as $locale => $copy) $labels[$locale]['old_price'] = $copy;
$quickOrderLabels = [
    'ru'=>['Быстрый заказ самовывозом по телефону','Заказать','Укажите действительный номер телефона.'],
    'en'=>['Quick pickup order by phone','Order','Enter a valid phone number.'],
    'uk'=>['Швидке замовлення із самовивозом за телефоном','Замовити','Введіть дійсний номер телефону.'],
    'de'=>['Schnelle Abholbestellung mit Telefonnummer','Bestellen','Geben Sie eine gültige Telefonnummer ein.'],
    'fr'=>['Commande rapide à retirer avec numéro de téléphone','Commander','Saisissez un numéro de téléphone valide.'],
    'es'=>['Pedido rápido para recogida con teléfono','Pedir','Introduce un número de teléfono válido.'],
    'pt'=>['Encomenda rápida para recolha com telefone','Encomendar','Introduza um número de telefone válido.'],
    'ja'=>['電話番号だけで店頭受け取りを注文','注文する','有効な電話番号を入力してください。'],
    'ko'=>['전화번호로 빠른 방문 수령 주문','주문하기','올바른 전화번호를 입력하세요.'],
    'vi'=>['Đặt nhanh để nhận tại cửa hàng bằng số điện thoại','Đặt hàng','Nhập số điện thoại hợp lệ.'],
    'zh'=>['仅用手机号快速下单自提','下单','请输入有效的电话号码。'],
];
foreach ($quickOrderLabels as $locale => $copy) {
    $labels[$locale] += array_combine(['quick_pickup','quick_order','quick_phone_error'], $copy);
}
$stockLabels = [
    'ru'=>['Продано','В резерве','Резерв до'],
    'en'=>['Sold','Reserved','Reserved until'],
    'uk'=>['Продано','Зарезервовано','Резерв до'],
    'de'=>['Verkauft','Reserviert','Reserviert bis'],
    'fr'=>['Vendu','Réservé','Réservé jusqu’au'],
    'es'=>['Vendido','Reservado','Reservado hasta'],
    'pt'=>['Vendido','Reservado','Reservado até'],
    'ja'=>['売約済み','取り置き中','取り置き期限'],
    'ko'=>['판매 완료','예약 중','예약 기한'],
    'vi'=>['Đã bán','Đang giữ hàng','Giữ hàng đến'],
    'zh'=>['已售出','已预留','预留至'],
];
foreach ($stockLabels as $locale => $copy) $labels[$locale] += array_combine(['sold','reserved','reserve_until'], $copy);
$doorCardLabels = [
    'ru'=>['Иконка характеристики','Снаружи','Изнутри','Комплектация и салон'],
    'en'=>['Characteristic icon','Exterior','Interior','Specifications and showroom'],
    'uk'=>['Іконка характеристики','Зовні','Зсередини','Комплектація та салон'],
    'de'=>['Merkmalssymbol','Außen','Innen','Ausstattung und Ausstellung'],
    'fr'=>['Icône de caractéristique','Extérieur','Intérieur','Équipement et salle d’exposition'],
    'es'=>['Icono de característica','Exterior','Interior','Equipamiento y exposición'],
    'pt'=>['Ícone da característica','Exterior','Interior','Equipamento e showroom'],
    'ja'=>['仕様アイコン','外側','内側','仕様と展示場所'],
    'ko'=>['특성 아이콘','바깥쪽','안쪽','사양 및 전시장'],
    'vi'=>['Biểu tượng thuộc tính','Mặt ngoài','Mặt trong','Trang bị và nơi trưng bày'],
    'zh'=>['属性图标','外侧','内侧','配置与展厅'],
];
foreach ($doorCardLabels as $locale => $copy) {
    $labels[$locale] += array_combine(['field_icon','door_exterior','door_interior','door_details'], $copy);
}
$listingLabels = [
    'ru'=>['Элемент списка · Плитка','Элемент списка · Простой список','Модель','Размеры','Открывание','Скидка','Видео'],
    'en'=>['List item · Grid','List item · Simple list','Model','Dimensions','Opening','Discount','Video'],
    'uk'=>['Елемент списку · Плитка','Елемент списку · Простий список','Модель','Розміри','Відчинення','Знижка','Відео'],
    'de'=>['Listenelement · Raster','Listenelement · Einfache Liste','Modell','Maße','Öffnung','Rabatt','Video'],
    'fr'=>['Élément · Grille','Élément · Liste simple','Modèle','Dimensions','Ouverture','Remise','Vidéo'],
    'es'=>['Elemento · Cuadrícula','Elemento · Lista simple','Modelo','Medidas','Apertura','Descuento','Vídeo'],
    'pt'=>['Item · Grelha','Item · Lista simples','Modelo','Dimensões','Abertura','Desconto','Vídeo'],
    'ja'=>['一覧項目 · グリッド','一覧項目 · シンプルリスト','モデル','寸法','開閉方向','割引','動画'],
    'ko'=>['목록 항목 · 그리드','목록 항목 · 기본 목록','모델','크기','열림 방향','할인','동영상'],
    'vi'=>['Mục · Dạng lưới','Mục · Danh sách đơn giản','Mẫu','Kích thước','Hướng mở','Giảm giá','Video'],
    'zh'=>['列表项 · 网格','列表项 · 简洁列表','型号','尺寸','开启方向','折扣','视频'],
];
foreach ($listingLabels as $locale => $copy) {
    $labels[$locale] = array_combine(['template_item','template_item-list','door_model','door_size','door_opening','door_discount','door_video'], $copy)
        + $labels[$locale];
}
return $labels;
