# Tiếng Việt · Eurowebcart CMS Lite

## Bắt đầu với 1.6.58

- Kích hoạt trang web hoàn chỉnh trong Mẫu thiết kế → Giao diện. Màu và phông chữ có thẻ riêng. Có thể bật nhiều phông chữ và lưu bản sao cục bộ.
- Bật thư viện trong Cài đặt → Thư viện và lưu tài nguyên cần thiết cục bộ. Font Awesome được đóng gói sẵn.
- Trong chế độ chỉnh sửa, nhấp đúp vào nội dung để mở trình chỉnh sửa trong hộp thoại. Nhấp đúp vào H1 mặc định để mở trường trong thẻ SEO.
- Định nghĩa trường được dùng chung, giá trị lưu riêng theo trang. Khi tạo trường có thể nhập giá trị cho trang đang mở. Ô nhập thay đổi theo kiểu trường.
- Với tên miền mới, tạo ZIP đầy đủ có giao diện hoạt động rồi thử cài đặt sạch. Với trang web hiện có, sao lưu và dùng bản cập nhật đã ký.

- [Sự quản lý](vi/readme.md)
- [Câu hỏi thường gặp của quản trị viên](vi/faq.md)
- [Mẫu và thành phần](vi/templates.md)

## Trình đơn di động · 1.6.7

- Hiện trong menu chính
- Hiện trong menu di động
- Ẩn trong menu di động


## Sửa dữ liệu ngay trên trang · 1.6.6

Trong chế độ sửa, khối dữ liệu, biểu mẫu [formX], đoạn mã [tel] và các thành phần có trình sửa cho phép mở hộp thoại hoặc toàn màn hình. Danh sách kiểu hiển thị chứa các mẫu đã đăng ký. Chọn thư mục gốc để chuyển mục lên cấp cao nhất. Dùng [list-pages] để liệt kê các trang đã xuất bản trong mục và các thư mục con. Đóng menu ngôn ngữ bằng cách bấm bên ngoài hoặc nhấn Escape.


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


## Các nút trên thanh quản trị · 1.6.4

Biểu tượng quả địa cầu mở trang web, còn mũi tên dùng để đăng xuất. Trên điện thoại chỉ hiện biểu tượng. Di chuột hoặc dùng bàn phím để xem nhãn.

Nội dung, Biểu mẫu và yêu cầu, và Trang web và dịch vụ mở trực tiếp. Các thẻ của mục đã chọn xuất hiện bên dưới menu chính.

Chèn [bread-crumbs] hoặc [search] vào HTML của trang chủ (index.php) hoặc mẫu. Mỗi khối xuất hiện một lần tại vị trí đã chọn. Cài đặt đường dẫn điều hướng và tìm kiếm vẫn được áp dụng. [privacy] liên kết đến /privacy/. Chỉnh sửa chính sách trong mục Trang.

```text
[bread-crumbs]
[search]
[privacy]
```


## Mới trong phiên bản 1.6.3

### Trang chủ và chính sách bảo mật

Mở “Mẫu thiết kế” để chỉnh sửa trang chủ và trang /privacy.php. Mã [page-content] hiển thị nội dung của trang chính. Trong chính sách [site-name] và [site-url] lấy tên và địa chỉ công khai từ “Cài đặt → Chung”. Hoàn thành văn bản ban đầu với thông tin về trang web của bạn và xử lý dữ liệu.

### Cookie và quay lại đầu trang

Ở phần chân trang, [conf-policy] cung cấp liên kết đến chính sách, [cookies] mở cài đặt và [top] cuộn lên đầu trang. Phân tích và bản đồ CMS tích hợp đang chờ thỏa thuận. Các tập lệnh được thêm vào mẫu hoặc tiêu đề dịch vụ theo cách thủ công yêu cầu cấu hình riêng.

### Trình chỉnh sửa functions.php

Mở “Mẫu thiết kế → Chức năng” sau tab “Tiêu đề dịch vụ”. Thêm các bao đóng vào mảng được trả về. Trong mẫu PHP của bạn, hãy gọi `translit("Москва")` hoặc `ew_call("translit", "München")`. Trong HTML chèn [func=translit value="Москва и München"]. Ví dụ tạo ra `Moskva i Muenchen`.

### Chỉnh sửa mẫu chuẩn

Trong trường “Mẫu thiết kế” đã chứa mã nguồn PHP và HTML được đánh dấu. Khi chỉnh sửa một mẫu tiêu chuẩn, hãy chọn “PHP tùy chỉnh”. Việc lưu sẽ tạo một phiên bản tùy chỉnh. Sử dụng các nút bên cạnh mẫu, bạn có thể hoàn tác chỉnh sửa cuối cùng hoặc khôi phục bản gốc.

### Kiểm tra PHP trước khi lưu

Mẫu thiết kế, mô-đun và trình soạn thảo functions.php kiểm tra cú pháp bằng lệnh `php -l`. Nếu có lỗi thì vẫn giữ nguyên phiên bản trước đó. Để chọn PHP CLI trên máy chủ, hãy sử dụng biến `SITE_PHP_LINT_BINARY`.

### Tìm kiếm ngược sáng và nhỏ gọn

CodeMirror được bật theo mặc định trên các bản cài đặt mới. Cài đặt này nằm trong Thư viện và Phông chữ. Trong tìm kiếm thu gọn, trường mở rộng sang trái, dấu thập xóa dòng và phím Enter bắt đầu tìm kiếm.

### Menu quản trị và bảng chỉnh sửa

Menu bên dưới tiêu đề bảng quản trị và bảng chỉnh sửa trên trang web hiển thị các biểu tượng có chú thích. Các biểu tượng vẫn còn trên màn hình hẹp. Chú giải công cụ mở khi di chuột và tập trung vào bàn phím trên màn hình hẹp. “Ẩn” nằm bên cạnh các hành động còn lại của trang. Công tắc Chế độ chỉnh sửa nằm ở bên phải.

### Mô tả và mã của mô-đun PHP

Trong "Mô-đun PHP", mô tả ngắn gọn dành cho văn bản thuần túy. Dán phần thân của hàm vào một trường riêng “Mã nguồn · PHP”.

## Mới trong phiên bản 1.6.2

### Ngôn ngữ của quản trị viên và trợ giúp

Trong cài đặt khu vực, hãy bật ngôn ngữ mong muốn. Menu chọn ngôn ngữ trong tiêu đề thay đổi giao diện và trợ giúp, trong khi vẫn giữ nguyên phần hiện tại. Văn bản trang và các giá trị cài đặt của bạn được lưu trữ ở dạng ban đầu.

### Trang trí tầng hầm

Mã [author] hiển thị chữ ký Eurowebcart CMS Lite, tên tác giả Бандалетов Павел và liên kết tới GitHub. Để chọn vị trí đặt chữ ký của bạn trong chân trang HTML tùy chỉnh, hãy dán [author] vào dòng thích hợp.

### Đèn nền ở chế độ chỉnh sửa

Di chuột qua liên kết Chỉnh sửa Thành phần hoặc đặt tiêu điểm bàn phím cho liên kết đó. Khung sẽ làm nổi bật đầu ra của phần chèn cụ thể này.

### Nơi thay đổi tìm kiếm

Trong “Cài đặt → Chung” chọn loại tìm kiếm. Trong “Bảo trì”, bạn có thể lập chỉ mục lại các trang và trường trong tiêu đề quản trị viên sẽ tìm kiếm theo cài đặt.

### Kết quả và gợi ý

Mỗi trang hiển thị 20 kết quả được đánh số. Ô tìm kiếm thu gọn mở sang bên trái biểu tượng kính lúp và có biểu tượng Enter bên trong. Gợi ý trong khu vực quản trị liên kết trực tiếp đến cài đặt.

## Tính năng mới trong phiên bản 1.6.1

### Tìm kiếm

Chọn Tắt, Thu gọn hoặc Mở rộng trong cài đặt chung. Thanh quản trị tìm tên cài đặt. Chỉ mục tự cập nhật sau khi lưu; dùng lịch cho thay đổi qua FTP. Nội dung các trang được chèn và khối dữ liệu được lập chỉ mục. PHP động không chạy.

### Mô-đun PHP

Mở mô-đun để xem cài đặt và trang sử dụng. Tạo bằng mã, tên tệp và thân hàm PHP. Đổi tên tệp vẫn giữ mã [php]id[/php]. Có thể bật tô sáng CodeMirror ở mục thư viện.

### Mẫu và bản dịch

Hoàn tác tối đa tám thay đổi cho mỗi mẫu hoặc khôi phục mẫu PHP gốc. Mỗi miền có lịch sử riêng. HTML tùy chỉnh sửa trong quản trị, PHP tùy chỉnh đặt tại templates/local/. Nút chọn ngôn ngữ có cả khi tạo bản dịch; bản dịch đã lưu di chuyển cùng trang.

### Tích hợp và bảo trì

Các liên kết trợ giúp trong mục CAPTCHA, bản đồ, điện thoại và phân tích dẫn tới nhà cung cấp. Có thể nhập URL và mã thông báo HTTP API. Thiết lập miền phụ vẫn có hiệu lực khi chuyển sang miền chính. Lên lịch php update.php --scheduled sau mỗi 15 phút.

## Tính năng mới trong phiên bản 1.6.0

### Tìm kiếm và bộ nhớ đệm

Trong mục Bảo trì, tạo lại chỉ mục văn bản sau khi xuất bản hoặc sửa trang qua quản trị hay FTP. Gợi ý dùng các trang công khai theo ngôn ngữ của khách. Bộ nhớ đệm mặc định tắt; có thể bật hoặc xóa ở cùng mục. Biểu mẫu và nội dung động được xử lý theo từng yêu cầu.

### Chỉnh sửa trên trang

Sau khi đăng nhập, công tắc bên phải thanh công cụ bật chế độ sửa trên toàn trang và hiển thị liên kết cạnh các thành phần. Mở CSS hoặc JS của trang trong hộp thoại. Trình quản trị có chế độ HTML, trực quan, chèn mã, tải hàng loạt qua AJAX và sắp xếp tệp.

### Thành phần và điều hướng

Khi thiếu tham số, thành phần dùng cấu hình chung. width nhận 100, 50, 33 hoặc 25, có thể thêm dấu %. Trên điện thoại các khối xếp thành một cột. numbers=off ẩn số ảnh; vuốt trái hoặc phải để chuyển. Menu di động mở từ bên phải. Có thể tắt đường dẫn phân cấp trong cài đặt cơ bản.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

### Ngôn ngữ, giao diện và giá

Trong cài đặt khu vực, chọn ngôn ngữ quản trị và trang web, tiền tệ, múi giờ, định dạng số và thời gian. Đặt index_en.php cạnh index.php để dịch trang; nếu thiếu bản dịch, nội dung gốc vẫn hiển thị. Kiểu giá dùng tiền tệ đã chọn. Có thể xem màu chủ đề, đổi chế độ tức thì, tải hai logo sáng/tối và thử Roboto Condensed bằng câu tự chọn.

```text
/contacts/index.php → /contacts/index_en.php
```

### Biểu mẫu, yêu cầu và tích hợp

Cài đặt CAPTCHA chung nhận khóa Google reCAPTCHA v2 và có thể áp dụng cho mọi biểu mẫu thường; vẫn có cấu hình riêng. Tin nhắn và yêu cầu gọi lại hiện 20 mục mỗi trang, chưa đọc ở trên. Tích hợp gồm Zadarma, Telegram, phân tích và bản đồ Google hoặc Yandex với cụm điểm tùy chọn. Hộp thoại thư viện thử phiên bản đang dùng và hiển thị mã chèn. CodeMirror có Tự động, HTML, CSS, JS, PHP và RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

### Mô-đun PHP

Trong mô-đun PHP, sửa tên, mô tả và quyền chèn. server-date hiện ngày theo định dạng khu vực. Nhà phát triển đặt các tệp thực thi trong thư mục được bảo vệ.

```text
[php]server-date[/php]
```

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

Dùng thẻ HTML và Trình sửa trực quan để đổi chế độ. SUP, SUB và Đoạn văn chèn thẻ tương ứng. Mã hiển thị cạnh tên thành phần. Thư mục thiếu được tạo khi chèn. Tên thư mục cho phép chữ Latin, số, dấu gạch nối và gạch dưới. Thẻ Ngôn ngữ liên kết đến cài đặt ngôn ngữ. Bảng chèn mở phía trên vùng chỉnh sửa. Nhấn «Đóng» ở đầu bảng hoặc phím Esc để đóng bảng và bỏ chọn nút. Các nút mũi tên trên thanh công cụ cho phép hoàn tác và làm lại tối đa 10 lần sửa văn bản gần đây qua máy chủ. Biểu tượng đồng hồ tải văn bản đã lưu gần nhất. Khi rời trang có thay đổi chưa lưu, bạn sẽ được yêu cầu xác nhận. Biểu tượng Lưu trước các nút mũi tên sẽ sáng khi có thay đổi chưa lưu. Chọn đoạn văn bản rồi mở Định dạng văn bản. Chọn cỡ chữ, màu chữ, màu nền hoặc phông chữ đã bật trong cài đặt trang.

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

Chọn “Chỉnh sửa” để sửa mẫu. “Lưu mẫu” áp dụng thay đổi cho các trang mới. “Tạo bản sao” tạo một mẫu riêng. Trong trình chỉnh sửa, bạn có thể mở mẫu đã chọn hoặc tạo mẫu mới từ trang đã lưu. Trong biểu mẫu mẫu trang, chọn “Chèn biểu tượng” và tìm theo tên. Bạn cũng có thể nhập mã như `[fa-icon=house]`.

Các mẫu bố cục được thu gọn lúc đầu. Bấm tiêu đề hoặc Mở rộng tất cả. Bấm lần nữa để thu gọn danh sách.

Để đổi chiều rộng toàn bộ băng chuyền, bật chế độ sửa, mở Chỉnh sửa thành phần cạnh banner và chọn chiều rộng khối.

## Thành phố · Điện thoại · 1.6.18

Chỉ bắt buộc tên thành phố. Tên miền và số điện thoại là tùy chọn. Nhập dấu + và mã quốc gia, ví dụ +7 (900) 000-00-00. Có thể dùng mã như [tel] thay cho số.

Ưu tiên thành phố đã lưu, thành phố của tên miền, rồi thành phố đầu tiên. Khi không có thành phố, trang dùng số điện thoại chung.

Biểu mẫu, yêu cầu gọi lại và đơn mới dùng email thành phố hoặc email chung nếu bỏ trống. Khi cả hai trống, biểu mẫu dùng người nhận riêng. Liên kết mạng xã hội trống kế thừa liên kết chung.

Trong chế độ sửa, di chuột lên bố cục và chọn Sửa mẫu. Với mọi thành phần [php] đã cài đặt, Sửa mô-đun mở mã và cài đặt trong hộp thoại. Trình sửa khung hiện tên và mã biểu mẫu. [banners width=100] phủ chiều rộng cửa sổ nếu là khối riêng trên trang không có thanh bên. Trình sửa bố cục mở trong hộp thoại trên trang. Trong chế độ sửa, nhấp đúp vào vùng trống của mẫu. Khi rê chuột, gợi ý hiện dưới nút sửa. Nếu các khối chồng lên nhau, mẫu đứng sau được ưu tiên. Nút Sửa slide mở slide banner đã chọn. Thanh công cụ hiển thị thành phần, bố cục và mẫu con của trang hiện tại. Trong trình sửa trang, mở Thao tác với thành phần trang bên cạnh Ngôn ngữ để chỉnh sửa tham số và dữ liệu. Nhấp đúp vào nội dung để mở trình chỉnh sửa trang hiện tại. Trình chỉnh sửa mở trong hộp thoại trên trang web. Sau khi lưu, hãy đóng hộp thoại để xem thay đổi trên trang. Nhấp đúp vào tiêu đề H1 để mở ngay trường tương ứng trong thẻ SEO.

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

## Giao diện thiết kế · КОНТУР · 1.6.19

Giao diện trọn bộ chuyển cả thiết kế, trang, khối dữ liệu và cài đặt. Các giao diện khác dùng trang chung của trang web.

Dấu tích chỉ tệp giao diện được ưu tiên hơn tệp CMS mặc định. Bạn có thể chỉnh sửa hoặc khôi phục tệp.

**Khôi phục bản gốc**

### Danh mục dự án

Mở kontur-works trong Nội dung → Khối dữ liệu. Nhập tiêu đề, nhóm, mô tả, ảnh phân tách bằng dấu phẩy và liên kết tùy chọn. Ảnh đầu tiên dùng làm ảnh bìa. Dùng cùng các cột cho bộ sưu tập khác.

```text
[portfolio]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]
[maps source=kontur-map]
```

«Тихий сад» là trang web trọn bộ cho studio thiết kế cảnh quan. Chọn trong Mẫu bố cục → Giao diện thiết kế rồi áp dụng. Sửa hai khối dữ liệu garden-projects và garden-prices và bộ banner garden-cover. Thêm thông tin liên hệ và người nhận cho [form85].

Giao diện Dezinfektori có 32 dịch vụ, biểu mẫu yêu cầu [form86] và đánh giá [form87]. Mở phản hồi trong Tin nhắn. Nếu được tác giả đồng ý, chép tên, nội dung và điểm vào dez-reviews rồi bật xuất bản. Các trang dịch vụ có mô tả tác hại, hình minh họa trước và sau và truyện hư cấu. Chỉnh sửa các khối này trong Trang.

```text
[iblock=reviews limit=6]/data/dez-reviews.txt[/iblock]
```

Mỗi trang danh mục có biểu mẫu đánh giá. Sau khi duyệt trong mục Đánh giá, nội dung sẽ xuất hiện trên trang gửi đánh giá.

### Màu trang web · Phông chữ

Tích chọn phông chữ rồi gán cho văn bản, tiêu đề hoặc nút. Tệp cục bộ do chính trang web cung cấp.

Thư viện → Lưu cục bộ

Mẫu trang → Tạo trang

## Trường tùy chỉnh

Nhập giá trị cho trang hiện tại trong dòng mới và lưu cùng thiết lập trường.

## Đánh giá

Chèn mã trong trình sửa trang. Khách nhập tên, viết đánh giá và chọn từ 0 đến 5 sao. Trong mục Đánh giá, bạn có thể duyệt, từ chối, sửa hoặc xóa đánh giá. Tải thêm sẽ hiển thị 10 đánh giá tiếp theo.

Nhập đường dẫn như /contacts/ hoặc để trống để hiện tất cả. Biểu mẫu lưu trang gửi đánh giá. Trang web chỉ hiện đánh giá đã duyệt. CAPTCHA dùng cài đặt chung của CMS.

```text
[reviews]
```
