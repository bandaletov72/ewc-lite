<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return [
    'help' => [
        'ru' => 'Откройте папку в медиатеке, выберите плитку или список. Отметьте нужные фото для удаления, поворота или изменения размера. Изменения сохраняются в тех же файлах. Для обработки фото необходимо расширение GD.',
        'en' => 'Open a media folder and choose grid or list. Select photos to delete, rotate or resize. Image edits update the original files. Image processing requires GD.',
        'uk' => 'Відкрийте папку медіатеки та виберіть плитку або список. Позначте фото для видалення, повороту чи зміни розміру. Правки зберігаються в оригіналах. Для обробки потрібне GD.',
        'de' => 'Öffnen Sie einen Medienordner und wählen Sie Raster oder Liste. Wählen Sie Fotos zum Löschen, Drehen oder Skalieren. Änderungen ersetzen die Originaldateien. Für die Verarbeitung ist GD erforderlich.',
        'fr' => 'Ouvrez un dossier, puis choisissez la grille ou la liste. Sélectionnez les photos à supprimer, pivoter ou redimensionner. Les modifications s’appliquent aux fichiers originaux. Le traitement requiert GD.',
        'es' => 'Abre una carpeta y elige cuadrícula o lista. Selecciona fotos para borrar, girar o cambiar de tamaño. Los cambios se aplican a los archivos originales. Se requiere GD.',
        'pt' => 'Abra uma pasta e escolha grelha ou lista. Selecione fotos para eliminar, rodar ou redimensionar. As alterações são guardadas nos originais. O processamento requer GD.',
        'ja' => 'フォルダーを開き、グリッドまたは一覧を選択します。写真を選んで削除、回転、サイズ変更を行います。編集は元のファイルに保存されます。処理には GD が必要です。',
        'ko' => '폴더를 열고 격자나 목록을 선택하세요. 사진을 선택해 삭제, 회전 또는 크기 변경을 합니다. 변경 사항은 원본 파일에 저장됩니다. 처리에는 GD가 필요합니다.',
        'vi' => 'Mở thư mục và chọn dạng lưới hoặc danh sách. Chọn ảnh để xóa, xoay hoặc đổi kích thước. Thay đổi lưu vào tệp gốc. Cần GD để xử lý ảnh.',
        'zh' => '打开媒体文件夹并选择网格或列表。选择照片后可删除、旋转或调整大小。图像编辑会保存到原文件。处理需要 GD 扩展。',
    ],
    'ru' => ['Медиатека','Плитка','Список','Выбрать все на странице','Удалить выбранные','Изменить исходные фото','Применить поворот или размер к выбранным оригиналам?','Удалить выбранные изображения?'],
    'en' => ['Media library','Grid','List','Select all on this page','Delete selected','Update original images','Apply the rotation or size to the selected originals?','Delete the selected images?'],
    'uk' => ['Медіатека','Плитка','Список','Вибрати все на сторінці','Видалити вибране','Змінити оригінали','Застосувати поворот або розмір до вибраних оригіналів?','Видалити вибрані зображення?'],
    'de' => ['Mediathek','Raster','Liste','Alle auf dieser Seite auswählen','Auswahl löschen','Originale ändern','Drehung oder Größe auf ausgewählte Originale anwenden?','Ausgewählte Bilder löschen?'],
    'fr' => ['Médiathèque','Grille','Liste','Tout sélectionner sur cette page','Supprimer la sélection','Modifier les originaux','Appliquer la rotation ou la taille aux originaux sélectionnés ?','Supprimer les images sélectionnées ?'],
    'es' => ['Biblioteca multimedia','Cuadrícula','Lista','Seleccionar todo en esta página','Eliminar selección','Modificar originales','¿Aplicar la rotación o el tamaño a los originales seleccionados?','¿Eliminar las imágenes seleccionadas?'],
    'pt' => ['Biblioteca multimédia','Grelha','Lista','Selecionar tudo nesta página','Eliminar selecionadas','Alterar originais','Aplicar rotação ou tamanho aos originais selecionados?','Eliminar as imagens selecionadas?'],
    'ja' => ['メディアライブラリ','グリッド','一覧','このページをすべて選択','選択した画像を削除','元の画像を変更','選択した元画像に回転やサイズを適用しますか？','選択した画像を削除しますか？'],
    'ko' => ['미디어 라이브러리','격자','목록','이 페이지 전체 선택','선택 항목 삭제','원본 이미지 변경','선택한 원본에 회전 또는 크기를 적용할까요?','선택한 이미지를 삭제할까요?'],
    'vi' => ['Thư viện ảnh','Lưới','Danh sách','Chọn tất cả trên trang này','Xóa mục đã chọn','Sửa ảnh gốc','Áp dụng góc xoay hoặc kích thước cho ảnh gốc đã chọn?','Xóa các ảnh đã chọn?'],
    'zh' => ['媒体库','网格','列表','选择本页全部','删除所选','修改原图','对选中的原图应用旋转或尺寸吗？','删除选中的图片吗？'],
];
