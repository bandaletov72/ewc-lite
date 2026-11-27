<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$formats = 'PNG, WebP, JPG, GIF, AVIF, MP4, MOV, MP3, PDF, XLS, DOC, DOCX, XLSX, ZIP, 7Z';
$labels = [
    'ru' => ['Сортировка','Имя','Дата изменения','Размер','Порядок','По возрастанию','По убыванию','Ход загрузки'],
    'en' => ['Sort by','Name','Last modified','Size','Order','Ascending','Descending','Upload progress'],
    'uk' => ['Сортування','Ім’я','Дата зміни','Розмір','Порядок','За зростанням','За спаданням','Хід завантаження'],
    'de' => ['Sortierung','Name','Änderungsdatum','Größe','Reihenfolge','Aufsteigend','Absteigend','Upload-Fortschritt'],
    'fr' => ['Trier par','Nom','Date de modification','Taille','Ordre','Croissant','Décroissant','Progression du téléversement'],
    'es' => ['Ordenar por','Nombre','Última modificación','Tamaño','Orden','Ascendente','Descendente','Progreso de carga'],
    'pt' => ['Ordenar por','Nome','Última modificação','Tamanho','Ordem','Ascendente','Descendente','Progresso do envio'],
    'ja' => ['並べ替え','名前','更新日時','サイズ','順序','昇順','降順','アップロードの進行状況'],
    'ko' => ['정렬','이름','수정 날짜','크기','순서','오름차순','내림차순','업로드 진행률'],
    'vi' => ['Sắp xếp','Tên','Ngày sửa','Kích thước','Thứ tự','Tăng dần','Giảm dần','Tiến độ tải lên'],
    'zh' => ['排序方式','名称','修改日期','大小','顺序','升序','降序','上传进度'],
];
foreach ($labels as &$locale) $locale[] = $formats;
unset($locale);
return $labels;
