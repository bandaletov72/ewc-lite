# 日本語 · 1.6.0

## 検索とキャッシュ

公開後や管理画面・FTP で更新した後は、メンテナンスで検索インデックスを再作成します。入力候補には閲覧者の言語で公開ページが表示されます。キャッシュは初期状態で無効です。フォームや動的処理はリクエストごとに実行します。

## サイト上での編集

ログイン後、ヘッダー上部の右端のスイッチでサイト全体の編集モードを切り替えます。コンポーネントの近くに編集リンクが表示されます。ページの CSS・JS はライトボックスで開きます。管理画面では HTML 編集、ビジュアル編集、コード挿入、AJAX による一括アップロードと並べ替えが使えます。

## コンポーネントとナビゲーション

指定のない値には共通設定を使用します。width は 100・50・33・25 に対応し、% も付けられます。スマートフォンでは一列になります。numbers=off でスライド番号を非表示にできます。左右のスワイプに対応し、モバイルメニューは右から開きます。パンくずリストは基本設定で無効にできます。

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## 言語・外観・価格

地域設定では管理画面とサイトの言語、通貨、数値・日付・時刻の形式、タイムゾーンを選べます。翻訳は index.php の隣に index_en.php として保存し、翻訳がない場合は元の内容が開きます。価格は選択した通貨で表示されます。テーマの配色とモードをプレビューし、明暗別のロゴや Roboto Condensed の例文を設定できます。

```text
/contacts/index.php → /contacts/index_en.php
```

## フォーム・依頼・連携

CAPTCHA の共通設定に Google reCAPTCHA v2 のキーを入力します。フォームごとの設定も可能です。メッセージと折り返し依頼は未読順で一ページに20件表示されます。連携では Zadarma、Telegram、分析、Google または Yandex の地図とクラスタリングを設定できます。ライブラリはライトボックスでテストし、挿入コードを確認できます。CodeMirror は自動、HTML、CSS、JS、PHP、RAW に対応します。

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## PHP モジュール

PHP モジュールの名前、説明、有効状態を管理し、ページに挿入します。server-date は地域設定の日付形式を使います。実行ファイルは開発者が保護されたディレクトリに配置します。

```text
[php]server-date[/php]
```

