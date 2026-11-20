# Mẫu và thành phần

## Mẫu bố cục

### Đầu trang, menu và chân trang

Toàn bộ trang kết nối các khối này một cách tự động. Các phiên bản chung được chỉnh sửa trong “Mẫu thiết kế” và cho một miền riêng lẻ - trong phần “Miền”.

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### Chèn một trang khác

Chèn nội dung của một trang được xuất bản. Việc lồng tối đa là mười cấp độ. Việc lặp lại một trang trong cùng một chuỗi sẽ bị bỏ qua.

```text
[include]/contacts/[/include]
```

### Liên kết trong menu

Hộp kiểm “Hiển thị trong menu” sẽ thêm liên kết vào các menu tiêu chuẩn. Nếu tên trống, tiêu đề sẽ được lấy. Nếu bạn bỏ chọn hộp kiểm cho một trang thư mục, các trang con của nó cũng sẽ biến mất khỏi menu.

```text
Thực đơnHiển thị: 1
Thực đơn: Danh bạ
```

### CSS và JavaScript

Mã chung được chỉnh sửa trong tab “CSS và JS”. Mã trang được chỉnh sửa trong khối “trang CSS và JavaScript” và được bao gồm sau khối chung. Dán nội dung CSS và JavaScript vào các trường.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### Tiêu đề dịch vụ

Trong phần “Mẫu thiết kế → Tiêu đề dịch vụ”, bạn có thể chèn thẻ meta chung và mã bộ đếm bên trong phần đầu hoặc trước phần thân đóng. Canonical được thêm tự động vào các trang đầy đủ.

```text
<meta name="theme-color" content="#065f63">
```

### Phông chữ Biểu tượng tuyệt vời

Font Awesome Free 7.3.1 đã được kết nối. Mã ngắn hoạt động trong các khối thiết kế nội dung và HTML. Phong cách rắn, thường xuyên và thương hiệu có sẵn. Thêm văn bản bên cạnh biểu tượng nếu nó đại diện cho một hành động.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Liên hệ
```

### Thư viện và phông chữ

Các kết nối được quản lý trong “Cài đặt → Thư viện và phông chữ”. Đối với Material UI, hãy sử dụng JavaScript mô-đun. Phông chữ Google đã chọn sẽ thay thế phông chữ hệ thống của trang web.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## Các biến, mẫu con và ngày tháng

### Biến trang web

Thêm các mục vào “Cài đặt → Chung”. Chèn khóa vào văn bản, tiêu đề, tiêu đề trang web hoặc chân trang.

```text
[city] = Mátxcơva
[spb] = St. Petersburg
```

```text
Dịch vụ trong thành phố [city]
```

### Đoạn mẫu

Trong phần Mẫu con, chỉ định tên, mô tả và giá trị. Đối với một miền, giá trị của nó được lấy trước tiên, sau đó là giá trị chung của nó. Mẫu con có thể chứa HTML. Đánh dấu trong tiêu đề bị xóa.

```text
[tel]
```

```text
[title]
```

### Ngày và giờ

Múi giờ được lấy từ PHP. [year] - năm, [month] - tháng, [day] - ngày trong tháng, [time] - giờ và phút, [dmY] và [date] - ngày DD.MM.YYYY, [datetime] - ngày và giờ.

```text
© [year] [city]
Ngày [dmY], thời gian [time]
```

### Tên và tác giả CMS

Mã [author] xuất ra Eurowebcart CMS Light, tên tác giả Бандалетов Павел và liên kết tới GitHub. Trong chân trang tiêu chuẩn, chữ ký sẽ tự động xuất hiện. Trong phần chân trang HTML của riêng bạn, hãy dán mã vào nơi bạn muốn.

```text
[author]
```

### Mẫu trang chủ và chính sách

[page-content] chèn nội dung trang chủ vào [home]. Mở /privacy/ trong mục Trang để sửa chính sách. [site-name] và [site-url] lấy từ cài đặt chung.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### Liên kết và cài đặt ở chân trang

[privacy] liên kết đến /privacy/, [top] cuộn lên đầu trang, và [cookies] mở cài đặt về sự đồng ý.

```text
[privacy]
[cookies]
[top]
```

### Các hàm PHP phổ biến

Trong phần “Mẫu thiết kế → Chức năng”, chỉnh sửa functions.php. Trong mã PHP, translit() hoặc ew_call() gọi một hàm đã đăng ký. Mã ngắn [func=translit] có sẵn trong nội dung và trong trường “HTML tùy chỉnh”.

```text
[func=translit value="Москва и München"]
```

```text
$var = dịch("Moscow");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## Băng chuyền, thư viện ảnh và tập tin

### Băng chuyền [gal]

Hiển thị ảnh từ một thư mục. mũi tên và số chấp nhận bật hoặc tắt, hiệu ứng – trượt, làm mờ hoặc không, vừa – chứa hoặc che.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### Thư viện ảnh [fotos]

Hiển thị một lưới đáp ứng. cột — tự động hoặc một số từ 1 đến 6, tỷ lệ — 4x3, 1x1, 16x9 hoặc gốc, vừa — che hoặc chứa. chú thích và liên kết được bật hoặc tắt.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### Danh sách file [files]

Liệt kê các tập tin được hỗ trợ. biểu tượng, kích thước và tải xuống chấp nhận bật hoặc tắt, đặt hàng – asc hoặc desc. Cho phép zip, pdf, xls, xlsx, doc, docx, png, gif, jpg, jpeg, mp4, Mov và mp3.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## Biểu mẫu và gọi lại

### Mẫu [formN]

Trong phần “Biểu mẫu”, hãy định cấu hình các trường, độ dài tối đa của chúng, văn bản nút và người nhận. “Sử dụng CAPTCHA” sẽ bật tiện ích Google reCAPTCHA v2 xác minh phản hồi trên máy chủ. Yêu cầu được gửi qua AJAX và hiển thị trong phần Tin nhắn.

```text
[form1]
[form2]
```

### Mẫu biểu mẫu thư

Chủ đề và văn bản được lưu trữ riêng biệt cho từng biểu mẫu. Trong mẫu email của bạn, hãy chèn các khóa trường trong dấu ngoặc nhọn. Danh sách người nhận và bản sao được phân tách bằng dấu phẩy.

```text
Xin chào {name}!
Điện thoại: {phone}
Tin nhắn: {message}
```

### Gọi lại [callback]

Mở một biểu mẫu có số điện thoại. Ứng dụng được lưu trong phần “Gọi lại”. Nhãn nút có thể được đặt bằng mã. Với nhà cung cấp “Không”, việc gửi tới điện thoại bị vô hiệu hóa.

```text
[callback]
[callback=Вызвать мастера]
```

## Khối dữ liệu

### Bảng và danh sách điện thoại

Tạo tập dữ liệu trong phần "Infoblocks". Cho phép A–Z, a–z, 0–9, dấu gạch ngang và dấu gạch dưới trong tên khối thông tin và các khóa cột. Không gian không được phép. Mẫu bảng hiển thị bảng, điện thoại hiển thị cột điện thoại kèm link gọi. Mã đóng [iblock] và [/iblock] được hỗ trợ.

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## Liên hệ và mạng xã hội

### Khối và biểu tượng chung

[contacts] hiển thị một khối danh bạ, [phones] hiển thị số điện thoại, [soc-icons] thu thập các biểu tượng của tất cả các mạng xã hội với các liên kết tùy chỉnh.

```text
[contacts]
[phones]
[soc-icons]
```

### Mạng xã hội cá nhân

Mỗi mã hiển thị một liên kết đến mạng xã hội của nó. Địa chỉ và chữ ký được đặt trong “Cài đặt → Danh bạ và mạng xã hội”.

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
```

## Các nút trên thanh quản trị · 1.6.4

Biểu tượng quả địa cầu mở trang web, còn mũi tên dùng để đăng xuất. Trên điện thoại chỉ hiện biểu tượng. Di chuột hoặc dùng bàn phím để xem nhãn.

Nội dung, Biểu mẫu và yêu cầu, và Trang web và dịch vụ mở trực tiếp. Các thẻ của mục đã chọn xuất hiện bên dưới menu chính.

Chèn [bread-crumbs] hoặc [search] vào HTML của trang chủ (index.php) hoặc mẫu. Mỗi khối xuất hiện một lần tại vị trí đã chọn. Cài đặt đường dẫn điều hướng và tìm kiếm vẫn được áp dụng. [privacy] liên kết đến /privacy/. Chỉnh sửa chính sách trong mục Trang.

```text
[bread-crumbs]
[search]
[privacy]
```

## Tệp cấu hình, bản đồ và mẫu · 1.6.5

Vào Bảo trì → Tệp cấu hình để sửa .htaccess, robots.txt và sitemap.xml. Sơ đồ XML chỉ dùng các trang đã xuất bản và được đánh dấu. Có thể khôi phục phiên bản .htaccess trước đó.

Nhập vĩ độ và kinh độ. [map] hiển thị điểm của trang, còn [maps] thu thập các điểm của trang đã xuất bản. center và zoom đặt tâm và mức thu phóng. Chọn nhà cung cấp bản đồ trong phần Tích hợp.

[main-menu] là menu dưới đầu trang có mục con thả xuống. Thêm mẫu tùy chỉnh và chọn hiển thị theo thiết bị trong phần Mẫu. Nút thêm tên miền trong phần đoạn mã mở biểu mẫu. Dùng [privacy] để liên kết đến chính sách.

Trong Tích hợp, nhập ID JivoSite, Microsoft Clarity và Umami Cloud, mã Botfaqtor hoặc webhook Slack. Hướng dẫn nằm cạnh các trường. Trong Zadarma có thể xem số dư, thử SMS hoặc cuộc gọi có thể tính phí. Với [payment], nhập liên kết YooMoney, PayPal và SBP. Kiểm tra trạng thái tại nhà cung cấp.

Các nút biểu tượng trong trình soạn HTML và trực quan định dạng văn bản, tiêu đề H1–H5 và danh sách. Đặt con trỏ rồi chọn mẫu, thành phần, ảnh hoặc biểu tượng. Có danh sách biểu mẫu và tùy chọn thành phần. Font Awesome khả dụng khi bật thư viện.

Bấm tên thư mục để quản lý ở bên phải. Chọn mục cha, cấu hình menu máy tính và di động, hoặc điền URL chuyển hướng. Trang mục trống sẽ liệt kê các mục con. Trong Bảo trì, thêm quy tắc như /contact.html => /contacts/ và sửa các trang trong /error/. Trên di động, ngôn ngữ và tìm kiếm có nút biểu tượng.

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
