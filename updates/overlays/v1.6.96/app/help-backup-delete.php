<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return [
    'ru' => ['Удалить бэкап', 'Подтверждаю удаление выбранного архива.', 'Бэкап удалён.', 'Не удалось удалить бэкап. Проверьте, что архив существует и удаление подтверждено.', 'В «Бэкапах» раскройте архив, выберите «Удалить бэкап» и подтвердите удаление.'],
    'en' => ['Delete backup', 'Confirm deletion of the selected archive.', 'Backup deleted.', 'Could not delete the backup. Check that it exists and deletion is confirmed.', 'In Backups, expand an archive, select Delete backup, and confirm deletion.'],
    'uk' => ['Видалити резервну копію', 'Підтверджую видалення вибраного архіву.', 'Резервну копію видалено.', 'Не вдалося видалити копію. Перевірте, чи існує архів і чи підтверджено видалення.', 'У розділі резервних копій розгорніть архів, виберіть видалення й підтвердьте дію.'],
    'de' => ['Sicherung löschen', 'Löschen des ausgewählten Archivs bestätigen.', 'Sicherung gelöscht.', 'Die Sicherung konnte nicht gelöscht werden. Prüfen Sie, ob das Archiv vorhanden ist und die Löschung bestätigt wurde.', 'Öffnen Sie in den Sicherungen ein Archiv, wählen Sie „Sicherung löschen“ und bestätigen Sie den Vorgang.'],
    'fr' => ['Supprimer la sauvegarde', 'Confirmer la suppression de l’archive sélectionnée.', 'Sauvegarde supprimée.', 'Impossible de supprimer la sauvegarde. Vérifiez que l’archive existe et que la suppression a été confirmée.', 'Dans les sauvegardes, développez une archive, choisissez « Supprimer la sauvegarde » et confirmez.'],
    'es' => ['Eliminar copia de seguridad', 'Confirmar la eliminación del archivo seleccionado.', 'Copia eliminada.', 'No se pudo eliminar la copia. Comprueba que exista y que hayas confirmado la eliminación.', 'En Copias de seguridad, abre un archivo, elige Eliminar copia de seguridad y confirma.'],
    'pt' => ['Eliminar cópia de segurança', 'Confirmar a eliminação do arquivo selecionado.', 'Cópia eliminada.', 'Não foi possível eliminar a cópia. Verifique se existe e se confirmou a eliminação.', 'Nas cópias de segurança, expanda um arquivo, escolha Eliminar cópia de segurança e confirme.'],
    'ja' => ['バックアップを削除', '選択したアーカイブの削除を確認する。', 'バックアップを削除しました。', 'バックアップを削除できませんでした。アーカイブの存在と削除の確認を確認してください。', 'バックアップ一覧でアーカイブを開き、「バックアップを削除」を選んで確定します。'],
    'ko' => ['백업 삭제', '선택한 아카이브 삭제를 확인합니다.', '백업을 삭제했습니다.', '백업을 삭제할 수 없습니다. 아카이브가 존재하며 삭제가 확인되었는지 확인하세요.', '백업 목록에서 아카이브를 펼쳐 백업 삭제를 선택한 후 확인하세요.'],
    'vi' => ['Xóa bản sao lưu', 'Xác nhận xóa tệp lưu trữ đã chọn.', 'Đã xóa bản sao lưu.', 'Không thể xóa bản sao lưu. Hãy kiểm tra tệp còn tồn tại và thao tác đã được xác nhận.', 'Trong mục Sao lưu, mở tệp lưu trữ, chọn Xóa bản sao lưu và xác nhận.'],
    'zh' => ['删除备份', '确认删除所选存档。', '备份已删除。', '无法删除备份。请检查存档是否存在并确认删除操作。', '在备份列表中展开存档，选择“删除备份”并确认。'],
];
