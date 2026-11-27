<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function admin_mail_labels(string $locale): array
{
    $keys = ['title','add','name','event','unassigned','system','forms','custom','welcome','reset','order','status','conflict','invalid','assigned'];
    $labels = [
        'ru'=>'Шаблоны писем|Добавить шаблон|Название|Событие|Без привязки|Письма сайта и магазина|Письма из форм|Пользовательские шаблоны|Регистрация пользователя|Сброс пароля пользователя|Подтверждение заказа|Изменение статуса заказа|Шаблон изменился. Откройте его заново.|Заполните название, тему и текст письма.|Используется шаблон',
        'en'=>'Email templates|Add template|Name|Event|Unassigned|Site and shop emails|Form emails|Custom templates|User registration|User password reset|Order confirmation|Order status has changed|The template has changed. Open it again.|Enter a name, subject and message.|Template in use',
        'de'=>'E-Mail-Vorlagen|Vorlage hinzufügen|Name|Ereignis|Nicht zugewiesen|E-Mails von Website und Shop|Formular-E-Mails|Eigene Vorlagen|Registrierung|Passwort zurücksetzen|Bestellbestätigung|Bestellstatus geändert|Die Vorlage wurde geändert. Öffnen Sie sie erneut.|Geben Sie Name, Betreff und Text ein.|Verwendete Vorlage',
        'es'=>'Plantillas de correo|Añadir plantilla|Nombre|Evento|Sin asignar|Correos del sitio y la tienda|Correos de formularios|Plantillas personalizadas|Registro|Restablecimiento de contraseña|Confirmación de pedido|Cambio de estado del pedido|La plantilla ha cambiado. Ábrela de nuevo.|Introduce el nombre, asunto y mensaje.|Plantilla en uso',
        'fr'=>'Modèles de courriel|Ajouter un modèle|Nom|Événement|Non attribué|Courriels du site et de la boutique|Courriels de formulaire|Modèles personnalisés|Inscription|Réinitialisation du mot de passe|Confirmation de commande|Changement de statut de commande|Le modèle a changé. Ouvrez-le à nouveau.|Saisissez un nom, un objet et un message.|Modèle utilisé',
        'ja'=>'メールテンプレート|テンプレートを追加|名前|イベント|未割当|サイトとショップのメール|フォームのメール|カスタムテンプレート|ユーザー登録|パスワードのリセット|注文確認|注文状況の変更|テンプレートが変更されました。開き直してください。|名前、件名、本文を入力してください。|使用中のテンプレート',
        'ko'=>'이메일 템플릿|템플릿 추가|이름|이벤트|미지정|사이트 및 쇼핑몰 메일|양식 메일|사용자 템플릿|사용자 등록|비밀번호 재설정|주문 확인|주문 상태 변경|템플릿이 변경되었습니다. 다시 열어 주세요.|이름, 제목, 본문을 입력하세요.|사용 중인 템플릿',
        'pt'=>'Modelos de e-mail|Adicionar modelo|Nome|Evento|Sem vínculo|E-mails do site e da loja|E-mails de formulários|Modelos personalizados|Cadastro de usuário|Redefinição de senha|Confirmação do pedido|Alteração do estado do pedido|O modelo mudou. Abra-o novamente.|Informe nome, assunto e mensagem.|Modelo em uso',
        'uk'=>'Шаблони листів|Додати шаблон|Назва|Подія|Без прив’язки|Листи сайту й магазину|Листи з форм|Власні шаблони|Реєстрація користувача|Скидання пароля|Підтвердження замовлення|Зміна статусу замовлення|Шаблон змінено. Відкрийте його знову.|Заповніть назву, тему й текст листа.|Використовується шаблон',
        'vi'=>'Mẫu email|Thêm mẫu|Tên|Sự kiện|Chưa gán|Email của trang và cửa hàng|Email biểu mẫu|Mẫu tùy chỉnh|Đăng ký tài khoản|Đặt lại mật khẩu|Xác nhận đơn hàng|Thay đổi trạng thái đơn hàng|Mẫu đã thay đổi. Hãy mở lại.|Nhập tên, chủ đề và nội dung.|Mẫu đang dùng',
        'zh'=>'邮件模板|添加模板|名称|事件|未分配|网站和商城邮件|表单邮件|自定义模板|用户注册|重置密码|订单确认|订单状态变更|模板已更改，请重新打开。|输入名称、主题和正文。|当前使用的模板',
    ];
    return array_combine($keys, explode('|', $labels[$locale] ?? $labels['en']));
}
