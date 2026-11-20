# テンプレートとコンポーネント

## レイアウトテンプレート

### ヘッダー、メニュー、フッター

ページ全体では、これらのブロックが自動的に接続されます。一般バージョンは「デザイン テンプレート」で編集し、個々のドメインの場合は「ドメイン」セクションで編集します。

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### 別のページを挿入する

公開されたページのコンテンツを挿入します。最大ネストは 10 レベルです。同じチェーン内のページを繰り返すとスキップされます。

```text
[include]/contacts/[/include]
```

### メニュー内のリンク

「メニューに表示」チェックボックスをオンにすると、標準メニューへのリンクが追加されます。名前が空の場合は、タイトルが使用されます。フォルダー ページのチェックボックスをオフにすると、そのサブページもメニューから消えます。

```text
メニュー表示: 1
メニュー: 連絡先
```

### CSSとJavaScript

一般的なコードは「CSSとJS」タブで編集します。ページ コードは「CSS および JavaScript ページ」ブロックで編集され、一般的なコードの後に​​組み込まれます。 CSS および JavaScript コンテンツをフィールドに貼り付けます。

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### サービスヘッダー

「デザインテンプレート→サービスヘッダー」セクションでは、ヘッド内または終了本文の前に一般的なメタタグとカウンターコードを挿入できます。 Canonical はページ全体に自動的に追加されます。

```text
<meta name="theme-color" content="#065f63">
```

### 素晴らしいフォントのアイコン

Font Awesome Free 7.3.1 が接続されました。短いコードは、コンテンツおよび HTML デザイン ブロックで機能します。ソリッド、レギュラー、ブランドスタイルをご用意しております。アクションを表す場合は、アイコンの横にテキストを追加します。

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> 連絡先
```

### ライブラリとフォント

接続は「設定 → ライブラリとフォント」で管理します。マテリアル UI には、モジュール型 JavaScript を使用します。選択した Google フォントは、サイトのシステム フォントを置き換えます。

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## 変数、サブテンプレート、日付

### サイト変数

「設定→一般」にエントリを追加します。テキスト、ヘッダー、サイトのタイトル、またはフッターにキーを挿入します。

```text
[city] = モスクワ
[spb] = サンクトペテルブルク
```

```text
市内のサービス[city]
```

### スニペット

[サブテンプレート] セクションで、名前、説明、値を指定します。ドメインの場合、最初にその値が取得され、次に一般的な値が取得されます。サブテンプレートには HTML が含まれる場合があります。ヘッダー内のマークアップは削除されます。

```text
[tel]
```

```text
[title]
```

### 日時

タイムゾーンはPHPから取得されます。 [year] - 年、[month] - 月、[day] - 日、[time] - 時と分、[dmY] および [date] - 日付 DD.MM.YYYY、[datetime] - 日付と時刻。

```text
© [year] [city]
日付 [dmY]、時刻 [time]
```

### CMS 名と作成者

コード [author] は、Eurowebcart CMS Light、作成者名 Бандалетов Павел、および GitHub へのリンクを出力します。標準のフッターでは、署名は自動的に表示されます。独自の HTML フッター内の、必要な場所にコードを貼り付けます。

```text
[author]
```

### ホームテンプレートとポリシー

[page-content] はホームページの内容を [home] に挿入します。ポリシーは「ページ」の /privacy/ で編集できます。[site-name] と [site-url] は基本設定から取得します。

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### フッターのリンクと設定

[privacy] は /privacy/ へのリンク、[top] は先頭へのスクロール、[cookies] は同意設定を開きます。

```text
[privacy]
[cookies]
[top]
```

### 一般的な PHP 関数

「デザイン テンプレート → 関数」セクションで、functions.php を編集します。 PHP コードでは、translit() または ew_call() は登録された関数を呼び出します。ショート コード [func=translit] は、コンテンツと [カスタム HTML] フィールドで使用できます。

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("モスクワ");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## カルーセル、フォトギャラリー、ファイル

### カルーセル [gal]

フォルダー内の写真を表示します。矢印と数字はオンまたはオフ、効果 - スライド、フェードまたはなし、フィット - 含むかカバーを受け入れます。

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### フォトギャラリー [fotos]

レスポンシブなグリッドを表示します。列 - 自動または 1 ～ 6 の数値、比率 - 4x3、1x1、16x9 またはオリジナル、フィット - カバーまたは含む。キャプションとリンクはオンまたはオフで有効になります。

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### ファイルのリスト [files]

サポートされているファイルをリストします。アイコン、サイズ、ダウンロードのオンまたはオフ、順序 (昇順または降順) を選択します。 Zip、pdf、xls、xlsx、doc、docx、png、gif、jpg、jpeg、mp4、mov、mp3 が使用できます。

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## フォームとコールバック

### フォーム[formN]

「フォーム」セクションで、フィールド、その最大長、ボタンのテキスト、受信者を設定します。 「Use CAPTCHA」は、サーバー上で応答検証を行う Google reCAPTCHA v2 ウィジェットを有効にします。リクエストは AJAX 経由で送信され、[メッセージ] セクションに表示されます。

```text
[form1]
[form2]
```

### メールフォームテンプレート

件名とテキストはフォームごとに個別に保存されます。電子メール テンプレートで、中括弧内にフィールド キーを挿入します。受信者とコピーのリストはカンマで区切られます。

```text
{name}さん、こんにちは！
電話番号: {phone}
メッセージ: {message}
```

### [callback] に折り返し電話してください

電話番号を入力したフォームを開きます。アプリケーションは「コールバック」セクションに保存されます。ボタンのラベルはコードで設定できます。 「いいえ」プロバイダーを使用すると、テレフォニーへの送信は無効になります。

```text
[callback]
[callback=折り返しを依頼]
```

## データブロック

### 電話機の表とリスト

「情報ブロック」セクションでデータセットを作成します。情報ブロック名と列キーには、A ～ Z、a ～ z、0 ～ 9、ハイフン、およびアンダースコアを使用できます。スペースは使用できません。テーブル テンプレートにはテーブルが表示され、電話には通話へのリンクを含む電話列が表示されます。終了コード [iblock] および [/iblock] がサポートされています。

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## 連絡先とSNS

### 一般的なブロックとアイコン

[contacts] は連絡先のブロックを表示し、[phones] は電話番号を表示し、[soc-icons] はカスタマイズされたリンクを持つすべてのソーシャル ネットワークのアイコンを収集します。

```text
[contacts]
[phones]
[soc-icons]
```

### 個人のソーシャルネットワーク

各コードには、そのソーシャル ネットワークへのリンクが表示されます。アドレスと署名は「設定→連絡先とソーシャルネットワーク」で設定します。

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
```

## 管理画面のヘッダーボタン · 1.6.4

地球のアイコンでサイトを開き、矢印のアイコンでログアウトします。スマートフォンではアイコンのみ表示されます。マウスを乗せるか、キーボードでフォーカスするとラベルを確認できます。

「コンテンツ」「フォームとリクエスト」「サイトとサービス」を選ぶと、各セクションが直接開きます。メインメニューの下に選択したセクションのタブが表示されます。

ホームページ（index.php）またはテンプレートのHTMLに [bread-crumbs] や [search] を追加すると、指定した位置に一度だけ表示されます。パンくずリストと検索の設定も適用されます。[privacy] は /privacy/ へのリンクです。ポリシーは「ページ」で編集できます。

```text
[bread-crumbs]
[search]
[privacy]
```

## 設定ファイル、地図、テンプレート · 1.6.5

メンテナンスの設定ファイルで .htaccess、robots.txt、sitemap.xml を編集できます。生成時にはサイトマップに含める設定の公開ページが対象となります。.htaccess の前の版を復元できます。

ページに緯度と経度を入力します。[map] はそのページの地点を表示し、[maps] は座標のある公開ページ全体を表示します。center と zoom で中心と倍率を指定し、連携設定で地図サービスを選びます。

[main-menu] はヘッダー直下のドロップダウンメニューです。独自テンプレートを追加し、端末別の表示を設定できます。スニペット画面からドメイン追加フォームを開けます。ポリシーへのリンクは [privacy] です。

連携設定で JivoSite、Microsoft Clarity、Umami Cloud の ID、Botfaqtor のコード、Slack の受信用 Webhook を設定できます。各欄の案内を参照してください。Zadarma では残高照会、SMS と通話のテストができます。テストの際に料金が発生する場合があります。[payment] には YooMoney、PayPal、SBP の決済リンクを設定し、支払い状況はサービス側で確認します。

HTML とビジュアル編集の各アイコンで文字装飾、見出し H1〜H5、リストを追加できます。カーソル位置を決め、テンプレート、コンポーネント、画像、アイコンを選びます。フォーム一覧とコンポーネントの設定が表示されます。Font Awesome はライブラリ有効時に使えます。

フォルダー名を選ぶと右側でセクションを管理できます。親セクション、モバイルと通常のメニュー、転送先 URL を設定できます。空のページには子セクションが表示されます。メンテナンスで /contact.html => /contacts/ のようなルールを追加し、/error/ のページを編集できます。モバイルの言語選択と検索はヘッダーのアイコンにあります。

```text
[map]
[map center="55.7558,37.6173" zoom=14]
[maps center="55.7558,37.6173" zoom=11]
[main-menu]
[lang]
[privacy]
[payment]
```

```php
$var = translit("Москва"); // Moskva
```

## ページ上でデータを編集 · 1.6.6

編集モードでは情報ブロック、[formX]、[tel] などのデータをダイアログや全画面で編集できます。表示形式の一覧には登録済みのテンプレートが表示されます。ルートフォルダーを選ぶと、セクションが最上位に移動します。 [list-pages] はセクションと子フォルダーの公開ページを一覧にします。言語メニューは外側をクリックするか Escape で閉じます。
