# 模板和组件

## 设计模板

### 页眉、菜单和页脚

整页自动连接这些块。通用版本在“设计模板”中编辑，对于单个域，则在“域”部分中编辑。

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### 插入另一页

插入已发布页面的内容。最大嵌套为十层。跳过同一链中重复的页面。

```text
[include]/contacts/[/include]
```

### 菜单中的链接

“在菜单中显示”复选框添加到标准菜单的链接。如果名称为空，则使用标题。如果取消选中文件夹页面的复选框，其子页面也将从菜单中消失。

```text
MenuVisible: 1
Menu: Контакты
```

### CSS 和 JavaScript

通用代码在“CSS和JS”选项卡中编辑。页面代码在“CSS 和 JavaScript 页面”块中编辑，并包含在常规页面之后。将 CSS 和 JavaScript 内容粘贴到字段中。

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### 服务标头

在“设计模板→服务标题”部分中，您可以在标题内或结束正文之前插入通用元标记和计数器代码。 Canonical 会自动添加到整页上。

```text
<meta name="theme-color" content="#065f63">
```

### 字体真棒图标

Font Awesome Free 7.3.1 已连接。简短的代码适用于内容和 HTML 设计块。有纯色、常规和品牌款式可供选择。如果图标代表操作，则在图标旁边添加文本。

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Связаться
```

### 库和字体

连接在“设置→库和字体”中管理。对于 Material UI，请使用模块化 JavaScript。所选的 Google 字体将替换网站的系统字体。

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## 变量、子模板和日期

### 站点变量

将条目添加到“设置→常规”。将密钥插入文本、页眉、站点标题或页脚中。

```text
[city] = Москва
[spb] = Санкт-Петербург
```

```text
Сервис в городе [city]
```

### 子模式

在子模板部分中，指定名称、描述和值。对于域，首先取其值，然后取其一般值。子模板可以包含 HTML。标头中的标记已被删除。

```text
[tel]
```

```text
[title]
```

### 日期和时间

时区取自 PHP。 [year] - 年、[month] - 月、[day] - 月中的某天、[time] - 小时和分钟、[dmY] 和 [date] - 日期 DD.MM.YYYY、[datetime] - 日期和时间。

```text
© [year] [city]
Дата [dmY], время [time]
```

### CMS 名称和作者

代码 [author] 输出 Eurowebcart CMS Light、作者姓名 Бандалетов Павел 以及 GitHub 的链接。在标准页脚中，签名会自动出现。在您自己的 HTML 页脚中，将代码粘贴到您想要的位置。

```text
[author]
```

### 主页模板和政策

代码 [page-content] 将正文插入模板 [home] 中。在 Pages 中，转至 /privacy/ 编辑策略文本。替换 [site-name] 和 [site-url] 取自主设置。

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### 页脚中的链接和设置

代码 [privacy] 显示指向 /privacy/ 的链接，[top] 将页面滚动到顶部，[cookies] 打开同意设置。

```text
[privacy]
[cookies]
[top]
```

### 常用 PHP 函数

在“设计模板→功能”部分中，编辑functions.php。在 PHP 代码中，translit() 或 ew_call() 调用已注册的函数。短代码 [func=translit] 可在内容和“自定义 HTML”字段中找到。

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("Москва");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## 轮播、照片库和文件

### 转盘 [gal]

显示文件夹中的照片。箭头和数字接受打开或关闭，效果 - 滑动、淡入淡出或无，适合 - 包含或覆盖。

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### 照片库 [fotos]

显示响应式网格。列 — 自动或 1 到 6 之间的数字，比率 — 4x3、1x1、16x9 或原始，适合 — 覆盖或包含。标题和链接可通过打开或关闭启用。

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### 文件列表 [files]

列出支持的文件。图标、大小和下载接受打开或关闭、顺序 - 升序或降序。允许使用 Zip、pdf、xls、xlsx、doc、docx、png、gif、jpg、jpeg、mp4、mov 和 mp3。

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## 表格和回电

### 表格 [formN]

在“表单”部分中，配置字段、最大长度、按钮文本和收件人。 “使用验证码”启用 Google reCAPTCHA v2 小部件，并在服务器上进行响应验证。请求通过 AJAX 发送，并在“消息”部分中可见。

```text
[form1]
[form2]
```

### 邮件表格模板

每个表单的主题和文本分别存储。在您的电子邮件模板中，在花括号中插入字段键。收件人列表和副本列表以逗号分隔。

```text
Привет {name}!
Телефон: {phone}
Сообщение: {message}
```

### 回拨 [callback]

打开包含电话号码的表单。该应用程序保存在“回调”部分。按钮标签可以在代码中设置。对于“否”提供商，将禁用发送到电话。

```text
[callback]
[callback=Вызвать мастера]
```

## 信息块

### 电话表和清单

在“Infoblocks”部分创建一个数据集。信息块名称和列键中允许使用 A–Z、a–z、0–9、连字符和下划线。不允许有空格。表格模板显示表格，电话显示带有呼叫链接的电话列。支持关闭代码 [iblock] 和 [/iblock]。

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## 联系人和社交网络

### 通用块和图标

[contacts] 显示联系人块，[phones] 显示电话号码，[soc-icons] 收集所有社交网络的图标以及自定义链接。

```text
[contacts]
[phones]
[soc-icons]
```

### 个人社交网络

每个代码都会显示其社交网络的链接。地址和签名在“设置→联系人和社交网络”中设置。

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
```

## 管理标题中的按钮 · 1.6.4

地球仪打开站点，箭头结束管理员会话。手机上仅显示图标。当您将鼠标悬停或使用键盘聚焦时，会出现工具提示。

“内容”、“表格和应用程序”以及“网站和服务”立即开放。其选项卡位于所选部分的标题下。

将 [bread-crumbs] 或 [search] 插入主页 (index.php) 或模板的 HTML 中。该块将在指定位置出现一次。面包屑复选框和搜索模式也适用于插入的标签。 [privacy] 显示指向 /privacy/ 的链接。政策文本在“页面”部分进行编辑。

```text
[bread-crumbs]
[search]
[privacy]
```

## 服务文件、地图和模板 · 1.6.5

打开“维护→服务文件”。这里编辑.htaccess、robots.txt和sitemap.xml。生成 sitemap.xml 会使用“在站点地图中显示”复选框获取已发布的页面。以前的版本可用于 .htaccess。

在页面上填写纬度和经度。 [map]将显示其积分，[maps]将收集已发布页面的积分。在这两个标签中，center 指定中心，zoom 指定比例。交互式地图使用 Integrations 的提供程序。

带有下拉小节的 [main-menu] 显示在标题下方。在设计模板中，创建您自己的模板并配置移动设备和桌面设备上的可见性。在“子模板”中，“+ 添加域”按钮会导致添加表单。要参考该策略，请使用 [privacy]。

在“集成”中，指定 JivoSite、Microsoft Clarity 和 Umami Cloud ID、Botfaqtor 代码或 Slack 传入 Webhook。说明链接位于字段旁边。在扎达玛，检查您的余额、发送测试短信或拨打国际号码进行测试呼叫。短信和通话可能需要付费。对于 [payment]，插入现成的付款链接 YuMoney、PayPal 和 SBP。付款状态在提供商的帐户中检查。

在 HTML 和可视化编辑器中，图标格式化文本、添加 H1–H5 和列表。定位光标并单独选择模板、组件、图像或图标。保存的代码可用于表单，其参数可用于组件。启用库后，Font Awesome 插入即可使用。

单击左侧的文件夹名称可管理右侧的部分。您可以在其中选择父部分、配置常规菜单和移动菜单，或指定转换 URL。空白部分页面显示小节。在“维护”中，设置 /contact.html => /contacts/ 等规则，并在 /error/ 中编辑错误页面。手机上的搜索和语言可通过标题中的图标进行。

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

## 编辑页面数据·1.6.6

在编辑模式下，信息块、表格[formX]、子模板[tel]等带有单独数据的组件有“数据”和“全屏”动作。 “信息块类型”列出了已注册的输出模板。在父节列表中，“根文件夹”将该节移动到顶层。使用 [list-pages] 列出节及其子文件夹的已发布页面。可以通过单击列表外部或单击“退出”来关闭管理面板中的语言菜单。

## 页面设置·1.6.8

在“编辑”中写入内容。地址、菜单和发布时间表在“页面设置”中配置。标题、元描述、关键字和站点地图包含在 SEO 中。坐标和示例标签可以在地图中找到。

空白日期消除了限制。使用该网站的时区。隐藏页面必须单独发布。

- `[map]` - 当前页面上有一个点的地图。
- `[maps]` - 包含已发布页面的所有点的地图。

## 横幅和图像编辑器·1.6.8

在组件中设置集和框架。您可以从文件夹中拍摄照片，设置标题、带有链接的按钮、表单或回调，并选择显示时间。在编辑模式下，框架按钮可打开其设置。插入图像会打开文件夹、下载、删除并创建旋转或调整大小的副本。要创建副本，您需要 GD。Font Awesome 选择显示图标并支持按名称搜索。

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## 语言1.6.9

“语言”选项卡列出了页面的语言。 “打开”会打开已保存的版本，“创建翻译”会打开编辑器以进行新翻译。

对于新部分，选择所需的级别。在开放部分内创建子部分，在其旁边创建相邻部分，并在站点的顶层创建根部分。

## PHP 兼容性和导航·1.6.10

最低 PHP 版本为 7.2。该网站和管理面板支持 PHP 8.6。对于您的 PHP 模块和模板，请使用服务器上安装的版本的语法。

当前项目 [bread-crumbs] 扩展了同级别的其他已发布部分（如果它们包含在主菜单中）。页面设置中的 URL 用于菜单、部分列表、面包屑、结果和搜索建议。

回收站从信息块编辑器中删除该行。删除列会删除该列的所有值。保存信息块时会记录编辑，包括拖动后自动保存时。

### 站点备份

ZIP 存储在一个封闭的文件夹中，包括页面、媒体、信息块、设置、自定义模板和 PHP 模块。管理员帐户和传入请求单独保存。为了存档，您需要 PHP Zip 扩展。

将内容和设置替换为所选副本。在替换之前，将创建当前状态的新备份。

### 清理未使用的文件

扫描会查找存储内容中没有链接的媒体。检查列表并标记您需要的文件。清理之前会创建备份。删除组件的代码会保留其文件夹和文件。

### 在地图上选择一个点

在您的 Yandex 地图帐户中，为“地图”部分中的“JavaScript API”产品创建密钥。将密钥限制为 HTTP Referer 域。打开提供程序后，在页面的“地图”选项卡上，单击“在地图上选择”，放置一个点并将其坐标传输到字段。

在可视化编辑器中，选择要对齐或删除格式的文本。单击图像打开其选项。拖动一个角来调整大小或将图像本身拖动到另一个位置。该链接提供 URL、电子邮件、电话和锚点。该按钮可以打开 URL、表单、回调或付款。要付款，请使用提供商的链接支付指定金额。

HTML 和可视化编辑器模式通过选项卡切换。 SUP、SUB 和段落插入适当的元素。这些代码在组件名称旁边可见。如果路径中的段仅包含拉丁字母、数字、连字符和下划线，则在插入时创建媒体文件夹。“语言”选项卡可以选择其他站点语言。

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## 销售 · 1.6.11

在“销售”选项卡设置价格和付款方式，也可以按语言单独设置。插入 [item-price] 和 [item-buy-button]。客户前往支付服务前，系统会记录订单。订单状态可通过电子邮件和 Zadarma 短信通知客户。

已受理 · 已确认付款 · 已发货 · 已完成 · 已取消

```text
[item-price]
[item-buy-button]
```

插入图像需要选定的文件或 URL。单击图像即可显示其尺寸、对齐方式、四个边距和 CSS。所选图像的对齐方式与文本分开应用。切换到 HTML 时，块标记会逐行显示。 “[list-pages] 部分的页面列表”位于组件列表中。管理页眉和页脚中的 CMS 徽标使用当前主题的颜色。

## 数据块图表

在页面编辑器中打开“插入组件”，选择表格或图表。绘制图表时，请启用 Chart.js，选择标签列及“数字”或“价格”类型的数值列。饼图、环形图和极区图使用一组非负数据。停用图表库时仍会显示表格。

重新打开编辑器后，已保存的表单、模板、片段和已注册的组件会自动出现在插入列表中。新横幅组的 ID 只允许 A–Z、a–z、连字符和下划线。将图片上传到 [banners] 文件夹后，每个文件旁边都会出现编辑链接。 在网站编辑模式中，“编辑幻灯片”会在弹窗中打开选中的幻灯片，另有链接可查看组件默认设置。轮播、相册和文件列表的保存按钮旁有文件管理器，支持上传、删除、排序和新建子文件夹（A–Z、a–z、0–9、-、_）。幻灯片颜色以方形色样显示。

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## 后台页眉和页脚 · 1.6.12

桌面端的标志旁显示 Eurowebcart CMS、Light 和已安装的版本。手机端的标志与操作按钮排在同一行。页脚显示仓库地址，可在“设置 → 更新”中检查累积版本。

## 后台页眉和页脚 · 1.6.13

桌面端的标志旁显示 Eurowebcart CMS、Light 和已安装的版本。手机端的标志与操作按钮排在同一行。页脚显示仓库地址，可在“设置 → 更新”中检查累积版本。 页面列表中的默认首页名称随后台语言显示。页脚标识分两行显示 Eurowebcart CMS 和 LIGHT。

## PHP 信息

“维护”中的 phpinfo() 仅向已登录的管理员显示 PHP 版本、模块和配置，隐藏环境变量和请求变量。在“设置 → 常规”中可设置 error_reporting，默认采用服务器设置。

## 隐藏页面树 · 1.6.14

在桌面端，点击箭头可折叠页面树并加宽编辑器。编辑器上方的按钮可恢复页面树。浏览器会记住选择。

## 隐藏页面树 · 1.6.15

在桌面端，点击箭头可折叠页面树并加宽编辑器。编辑器上方的按钮可恢复页面树。浏览器会记住选择。

## 在地图上选点

在 Yandex Maps 控制台为 JavaScript API 产品创建密钥，并通过 HTTP Referer 限制访问域名。在页面的地图标签中选择地点并应用坐标。 本地测试时在 HTTP Referer 中填写不带端口的 localhost。密钥激活后，使用 localhost 打开后台。

## 菜单中的搜索 · 1.6.16

[search] 在桌面端与 [main-menu] 的链接并排显示，在手机端显示在 [mobile-menu] 内。搜索模式在常规设置中选择。页面正文中的 [search] 优先。

`[search]`
