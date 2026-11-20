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

## Sửa dữ liệu ngay trên trang · 1.6.6

Trong chế độ sửa, khối dữ liệu, biểu mẫu [formX], đoạn mã [tel] và các thành phần có trình sửa cho phép mở hộp thoại hoặc toàn màn hình. Danh sách kiểu hiển thị chứa các mẫu đã đăng ký. Chọn thư mục gốc để chuyển mục lên cấp cao nhất. Dùng [list-pages] để liệt kê các trang đã xuất bản trong mục và các thư mục con. Đóng menu ngôn ngữ bằng cách bấm bên ngoài hoặc nhấn Escape.

## Cài đặt trang · 1.6.8

Soạn nội dung trong Chỉnh sửa. Đặt địa chỉ, menu và lịch xuất bản trong Cài đặt trang. SEO gồm tiêu đề, mô tả, từ khóa và mục đưa vào sitemap. Bản đồ có tọa độ và ví dụ về thẻ.

Để trống ngày nếu không giới hạn. Ngày giờ dùng múi giờ của trang web. Trang đã ẩn phải được xuất bản riêng.

- `[map]` — bản đồ với vị trí của trang hiện tại.
- `[maps]` — bản đồ với vị trí của tất cả trang đã xuất bản.

## Banner và trình sửa ảnh · 1.6.8

Quản lý bộ và slide trong Thành phần. Dùng ảnh từ thư mục, thêm chú thích và nút mở liên kết, biểu mẫu hoặc gọi lại, rồi đặt lịch hiển thị. Chế độ chỉnh sửa hiển thị thao tác cho từng slide. Bộ chọn ảnh hỗ trợ duyệt thư mục, tải lên, xóa và tạo bản sao xoay hoặc đổi kích thước bằng GD. Có thể tìm biểu tượng Font Awesome theo tên.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## Ngôn ngữ · 1.6.9

Thẻ Ngôn ngữ liệt kê các ngôn ngữ của trang. Mở đưa bạn đến bản đã lưu. Tạo bản dịch mở trình soạn thảo trống.

Mục con nằm trong mục hiện tại, mục cùng cấp nằm cạnh mục đó, còn mục gốc nằm ở cấp cao nhất của trang web.

## Tương thích PHP và điều hướng · 1.6.10

Phiên bản PHP tối thiểu là 7.2. Trang web và khu vực quản trị hỗ trợ PHP 8.6. Hãy dùng cú pháp của phiên bản trên máy chủ cho mô-đun và mẫu riêng.

Mục hiện tại của [bread-crumbs] mở danh sách các mục cùng cấp đã xuất bản và có trong menu chính. URL đã cấu hình được dùng trong menu, danh sách mục, đường dẫn điều hướng, kết quả và gợi ý tìm kiếm.

Biểu tượng thùng rác xóa một hàng trong trình sửa khối dữ liệu. Xóa một cột sẽ bỏ toàn bộ giá trị của cột đó. Các thay đổi được ghi khi lưu, kể cả khi tự động lưu sau khi sắp xếp lại.

### Sao lưu trang web

ZIP riêng chứa trang, phương tiện, khối dữ liệu, cài đặt, mẫu và mô-đun PHP. Tài khoản quản trị và yêu cầu đến được lưu riêng. Cần PHP Zip.

Thay thế nội dung và cài đặt. Hệ thống sao lưu trạng thái hiện tại trước.

### Dọn tệp không sử dụng

Quá trình quét tìm phương tiện chưa được tham chiếu trong nội dung đã lưu. Kiểm tra danh sách và chọn tệp cần xóa. Hệ thống sao lưu trước. Xóa mã thành phần vẫn giữ thư mục và các tệp của nó.

### Chọn một điểm trên bản đồ

Tạo khóa «JavaScript API» trong bảng điều khiển Yandex Maps và giới hạn theo HTTP Referer. Trong thẻ Bản đồ của trang, hãy chọn điểm và áp dụng tọa độ.

Chọn văn bản để căn lề hoặc xóa định dạng. Nhấp vào ảnh để sửa thuộc tính rồi kéo ảnh để di chuyển hoặc kéo góc để đổi kích cỡ. Liên kết hỗ trợ URL, email, điện thoại và điểm neo. Nút mở URL, biểu mẫu, gọi lại hoặc liên kết thanh toán.

Dùng thẻ HTML và Trình sửa trực quan để đổi chế độ. SUP, SUB và Đoạn văn chèn thẻ tương ứng. Mã hiển thị cạnh tên thành phần. Thư mục thiếu được tạo khi chèn. Tên thư mục cho phép chữ Latin, số, dấu gạch nối và gạch dưới. Thẻ Ngôn ngữ liên kết đến cài đặt ngôn ngữ.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## Bán hàng · 1.6.11

Đặt giá và cách thanh toán trong thẻ Bán hàng, có thể thay riêng từng ngôn ngữ. Chèn [item-price] và [item-buy-button]. Đơn được ghi trước khi thanh toán. Thông báo trạng thái có thể gửi qua email hoặc SMS Zadarma.

Đã tiếp nhận · Đã nhận thanh toán · Đã gửi · Đã đóng · Đã hủy

```text
[item-price]
[item-buy-button]
```

Chọn tệp hoặc URL để chèn ảnh. Nhấp ảnh để chỉnh kích thước, căn lề, bốn lề ngoài và CSS. Căn ảnh độc lập với văn bản. HTML hiển thị các khối trên từng dòng. [list-pages] nằm trong danh sách thành phần. Logo CMS dùng màu của chủ đề.

## Biểu đồ từ khối dữ liệu

Trong trình sửa trang, mở mục chèn thành phần và chọn bảng hoặc biểu đồ. Để vẽ biểu đồ, bật Chart.js, chọn cột nhãn và các cột kiểu số hoặc giá. Biểu đồ tròn, vành khuyên và vùng cực dùng một chuỗi không âm. Khi thư viện bị tắt, trang hiển thị bảng.

Biểu mẫu, mẫu, đoạn mã đã lưu và thành phần đã đăng ký tự xuất hiện khi mở lại trình sửa. ID bộ banner mới dùng A–Z, a–z, dấu gạch nối và gạch dưới. Sau khi tải ảnh vào thư mục [banners], liên kết sửa slide xuất hiện cạnh mỗi tệp. Ở chế độ sửa, cửa sổ chỉ mở slide đã chọn và có liên kết riêng đến cài đặt mặc định. Trình quản lý tệp cạnh nút Lưu của băng chuyền, thư viện ảnh và danh sách tệp cho phép tải lên, xóa, sắp xếp và tạo thư mục con (A–Z, a–z, 0–9, - và _). Màu slide hiển thị trong ô vuông.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## Đầu trang và chân trang quản trị · 1.6.12

Trên máy tính, bên cạnh logo có Eurowebcart CMS và Light kèm phiên bản đã cài. Trên điện thoại, biểu tượng nằm cùng hàng với các nút. Chân trang liên kết đến kho mã. Kiểm tra bản cập nhật tích lũy trong phần Cài đặt.

## Đầu trang và chân trang quản trị · 1.6.13

Trên máy tính, bên cạnh logo có Eurowebcart CMS và Light kèm phiên bản đã cài. Trên điện thoại, biểu tượng nằm cùng hàng với các nút. Chân trang liên kết đến kho mã. Kiểm tra bản cập nhật tích lũy trong phần Cài đặt. Tên Trang chủ mặc định trong danh sách trang theo ngôn ngữ quản trị. Logo chân trang có hai dòng: Eurowebcart CMS và LIGHT.

## Thông tin PHP

Trong Bảo trì, phpinfo() hiển thị phiên bản, mô-đun và cấu hình PHP cho quản trị viên đã đăng nhập. Biến môi trường và yêu cầu được ẩn. Đặt error_reporting ở cài đặt chung. Mặc định dùng mức của máy chủ.

## Ẩn cây trang · 1.6.14

Trên máy tính, mũi tên thu gọn cây trang và mở rộng trình sửa. Nút phía trên trình sửa khôi phục cây trang. Trình duyệt ghi nhớ lựa chọn.

## Ẩn cây trang · 1.6.15

Trên máy tính, mũi tên thu gọn cây trang và mở rộng trình sửa. Nút phía trên trình sửa khôi phục cây trang. Trình duyệt ghi nhớ lựa chọn.

## Chọn một điểm trên bản đồ

Tạo khóa «JavaScript API» trong bảng điều khiển Yandex Maps và giới hạn theo HTTP Referer. Trong thẻ Bản đồ của trang, hãy chọn điểm và áp dụng tọa độ. Khi thử nghiệm cục bộ, nhập localhost không kèm cổng vào HTTP Referer, chờ khóa hoạt động rồi mở quản trị qua localhost.

## Tìm kiếm trong menu · 1.6.16

[search] đặt ô tìm kiếm cạnh các mục [main-menu] trên máy tính và trong [mobile-menu] trên điện thoại. Chọn chế độ trong cài đặt chung. [search] trong nội dung trang được ưu tiên.

`[search]`

## Tìm kiếm trong menu · 1.6.17

[search] đặt ô tìm kiếm cạnh các mục [main-menu] trên máy tính và trong [mobile-menu] trên điện thoại. Chọn chế độ trong cài đặt chung. [search] trong nội dung trang được ưu tiên. Ô tìm kiếm luôn hiển thị khi mở menu di động. [lang] đặt bộ chọn ngôn ngữ trong menu di động và phần đầu trang trên máy tính.

`[search]` · `[lang]`

Cuối menu di động hiển thị các kênh liên hệ và mạng xã hội đã cấu hình.

## Phụ đề trang web

Đặt phụ đề trong cài đặt chung. Phụ đề hiển thị nhỏ bên dưới tên trang trong phần đầu. Tên trang nhỏ hơn trên điện thoại.

## Thành phố

Trong mục Thành phố, gán tên miền đã đăng ký và nhập tối đa năm số điện thoại. Trường số điện thoại chấp nhận mã như [tel]. [cities] hiển thị bộ chọn, [phones] liệt kê các số đã nhập và [tel], [tel2]–[tel5] hiển thị từng số. Trình duyệt ghi nhớ lựa chọn và cập nhật số mà không tải lại trang.

`[cities]` · `[phones]` · `[tel]`

## Hiệu ứng khi cuộn

Trong cài đặt chung, chọn hiệu ứng xuất hiện của các khối khi cuộn. Hiệu ứng mặc định tắt và tuân theo tùy chọn giảm chuyển động của thiết bị.

## Mẫu trang

Chọn mẫu khi tạo trang. Mẫu điền nội dung và các trường SEO để bạn chỉnh sửa. Thêm mẫu của riêng bạn trong mục Mẫu trang. Bản đồ, biểu mẫu và mua hàng dùng cài đặt của từng thành phần.

CSS và JavaScript của mẫu được sao chép vào trang. Thêm lớp riêng trong HTML và định kiểu trong CSS của mẫu.

### Lớp dùng chung

Khung bố cục, lưới thích ứng, phần nổi bật, thẻ và giá nhấn mạnh.

`.page-preset` · `.page-preset-grid` · `.page-preset-hero` · `.page-preset-card` · `.page-preset-price`

Các mẫu bố cục được thu gọn lúc đầu. Bấm tiêu đề hoặc Mở rộng tất cả. Bấm lần nữa để thu gọn danh sách.

Để đổi chiều rộng toàn bộ băng chuyền, bật chế độ sửa, mở Chỉnh sửa thành phần cạnh banner và chọn chiều rộng khối.

## Thành phố · Điện thoại · 1.6.18

Chỉ bắt buộc tên thành phố. Tên miền và số điện thoại là tùy chọn. Nhập dấu + và mã quốc gia, ví dụ +7 (900) 000-00-00. Có thể dùng mã như [tel] thay cho số.

Ưu tiên thành phố đã lưu, thành phố của tên miền, rồi thành phố đầu tiên. Khi không có thành phố, trang dùng số điện thoại chung.

Biểu mẫu, yêu cầu gọi lại và đơn mới dùng email thành phố hoặc email chung nếu bỏ trống. Khi cả hai trống, biểu mẫu dùng người nhận riêng. Liên kết mạng xã hội trống kế thừa liên kết chung.

Trong chế độ sửa, di chuột lên bố cục và chọn Sửa mẫu. Với mọi thành phần [php] đã cài đặt, Sửa mô-đun mở mã và cài đặt trong hộp thoại. Trình sửa khung hiện tên và mã biểu mẫu. [banners width=100] phủ chiều rộng cửa sổ nếu là khối riêng trên trang không có thanh bên. Trình sửa bố cục mở trong hộp thoại trên trang.

Trình quản lý kiểm tra thư mục và tệp trước khi xóa, đồng thời sao lưu thư mục không trống. Slide banner có thể có tiêu đề và tiêu đề phụ.

Số điện thoại được kiểm tra khi nhập. Số Nga bắt đầu bằng 8 được đổi thành +7. Sau khi gửi thành công, biểu mẫu được ẩn và khi mở lại sẽ thấy xác nhận.

Mã quốc gia đã chọn được thêm vào số không có +. Số bắt đầu bằng + giữ mã quốc tế đã nhập.

## Giao diện thiết kế

Sao chép tệp vào giao diện và lưu. Tệp còn thiếu dùng bản mặc định. Thay đổi, mã bố cục mới, lịch sử và cài đặt tên miền thuộc giao diện đang dùng. Dùng css/theme.css và js/theme.js để thêm kiểu và mã.

`templates/themes/demo/header.php` · `templates/themes/demo/components/banners.php`

Ngôn ngữ nằm cuối menu di động. Trên máy tính, nút quả địa cầu nằm cạnh tìm kiếm trong menu dưới đầu trang.

Thư viện ảnh, banner và danh sách tệp ưu tiên thư mục giao diện đang dùng. Nếu không có thì dùng tệp chung. Thư mục mới, tệp tải lên, logo và favicon được lưu trong giao diện. Bản sao lưu bao gồm giao diện và cài đặt.

Có thể dùng chiều rộng cửa sổ (width=page), nội dung (width=content) hoặc khối cha (width=parent). height dành cho máy tính và mobile-height dành cho điện thoại. auto giữ kích thước tự nhiên.

`[banners width=page height=520 mobile-height=240]` · `[form1 width=parent]`

Mục cuối của đường dẫn hiển thị các chuyên mục cùng cấp đã xuất bản, kể cả mục ẩn khỏi menu chính. Liên kết dùng URL đã cấu hình.
