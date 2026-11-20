# Tiếng Việt · Eurowebcart CMS Light

- [Sự quản lý](vi/readme.md)
- [Câu hỏi thường gặp của quản trị viên](vi/faq.md)
- [Mẫu và thành phần](vi/templates.md)

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

Mã [author] hiển thị chữ ký Eurowebcart CMS Light, tên tác giả Бандалетов Павел và liên kết tới GitHub. Để chọn vị trí đặt chữ ký của bạn trong chân trang HTML tùy chỉnh, hãy dán [author] vào dòng thích hợp.

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
