<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return [
    'ru' => ['Список', 'Настройки доступа', 'Разрешить вход в личный кабинет', 'Отключение регистрации оставляет вход открытым для существующих пользователей. Отключение личного кабинета закрывает вход для покупателей и сотрудников. Вход администратора работает отдельно.', 'Личный кабинет отключён.'],
    'en' => ['List', 'Access settings', 'Allow account sign-in', 'Turning off registration still allows existing users to sign in. Turning off account sign-in blocks customers and staff. Administrator sign-in is separate.', 'Account sign-in is disabled.'],
    'uk' => ['Список', 'Налаштування доступу', 'Дозволити вхід в особистий кабінет', 'Вимкнення реєстрації залишає вхід доступним для наявних користувачів. Вимкнення кабінету закриває вхід покупцям і працівникам. Вхід адміністратора працює окремо.', 'Особистий кабінет вимкнено.'],
    'de' => ['Liste', 'Zugriffseinstellungen', 'Kontoanmeldung erlauben', 'Bei deaktivierter Registrierung können sich bestehende Nutzer weiterhin anmelden. Bei deaktivierter Kontoanmeldung ist der Zugang für Kunden und Mitarbeitende gesperrt. Die Administratoranmeldung ist separat.', 'Die Kontoanmeldung ist deaktiviert.'],
    'fr' => ['Liste', 'Paramètres d’accès', 'Autoriser la connexion au compte', 'La fermeture des inscriptions laisse les comptes existants accessibles. La désactivation de la connexion bloque les clients et le personnel. La connexion administrateur est séparée.', 'La connexion au compte est désactivée.'],
    'es' => ['Lista', 'Configuración de acceso', 'Permitir el acceso a la cuenta', 'Desactivar el registro permite a los usuarios existentes seguir entrando. Desactivar el acceso bloquea a clientes y personal. El acceso de administrador es independiente.', 'El acceso a la cuenta está desactivado.'],
    'pt' => ['Lista', 'Definições de acesso', 'Permitir acesso à conta', 'Desativar o registo mantém o acesso dos utilizadores existentes. Desativar o acesso bloqueia clientes e colaboradores. O acesso de administrador é independente.', 'O acesso à conta está desativado.'],
    'ja' => ['一覧', 'アクセス設定', 'アカウントへのログインを許可', '登録を無効にしても既存の利用者はログインできます。ログインを無効にすると購入者とスタッフはアクセスできません。管理者のログインは別です。', 'アカウントへのログインは無効です。'],
    'ko' => ['목록', '접근 설정', '계정 로그인 허용', '가입을 끄면 기존 사용자는 계속 로그인할 수 있습니다. 계정 로그인을 끄면 고객과 직원의 접근이 차단됩니다. 관리자 로그인은 별도입니다.', '계정 로그인이 비활성화되었습니다.'],
    'vi' => ['Danh sách', 'Cài đặt truy cập', 'Cho phép đăng nhập tài khoản', 'Tắt đăng ký vẫn cho phép người dùng hiện có đăng nhập. Tắt đăng nhập sẽ chặn khách hàng và nhân viên. Đăng nhập quản trị hoạt động riêng.', 'Đăng nhập tài khoản đã bị tắt.'],
    'zh' => ['列表', '访问设置', '允许账户登录', '关闭注册后，现有用户仍可登录。关闭账户登录会阻止顾客和员工访问。管理员登录单独运行。', '账户登录已停用。'],
];
