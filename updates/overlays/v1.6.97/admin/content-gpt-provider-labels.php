<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return [
    'ru' => 'Для Gemini и Claude используйте клиент EWC. Он выполняет REST-запросы, показывает предпросмотр и сохраняет правки только после подтверждения в терминале. Ключи задаются в окружении клиента.',
    'en' => 'Use the EWC client with Gemini or Claude. It calls REST, shows the preview and applies edits only after terminal confirmation. Set keys in the client environment.',
    'uk' => 'Використовуйте клієнт EWC для Gemini або Claude. Він викликає REST, показує попередній перегляд і зберігає зміни після підтвердження в терміналі. Ключі задаються в оточенні клієнта.',
    'de' => 'Verwenden Sie für Gemini oder Claude den EWC-Client. Er ruft REST auf, zeigt die Vorschau und speichert nach Bestätigung im Terminal. Setzen Sie die Schlüssel in der Client-Umgebung.',
    'fr' => 'Utilisez le client EWC avec Gemini ou Claude. Il appelle REST, affiche l’aperçu et enregistre après confirmation dans le terminal. Configurez les clés dans son environnement.',
    'es' => 'Usa el cliente EWC con Gemini o Claude. Llama a REST, muestra la vista previa y guarda tras confirmación en el terminal. Configura las claves en su entorno.',
    'pt' => 'Use o cliente EWC com Gemini ou Claude. O cliente chama REST, mostra a pré-visualização e guarda após confirmação no terminal. Configure as chaves no seu ambiente.',
    'ja' => 'Gemini または Claude では EWC クライアントを使います。REST のプレビューを表示し、ターミナルで確認後に保存します。キーはクライアントの環境変数に設定します。',
    'ko' => 'Gemini 또는 Claude에서는 EWC 클라이언트를 사용하세요. REST로 미리 보기를 표시하고 터미널 확인 후 저장합니다. 키는 클라이언트 환경에 설정하세요.',
    'vi' => 'Dùng ứng dụng EWC với Gemini hoặc Claude. Ứng dụng gọi REST, hiện bản xem trước và lưu sau khi xác nhận trong terminal. Đặt khóa trong môi trường của ứng dụng.',
    'zh' => '使用 EWC 客户端连接 Gemini 或 Claude。客户端通过 REST 显示预览，并在终端确认后保存。请在客户端环境变量中设置密钥。',
];
