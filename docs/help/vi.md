# Tiếng Việt · Eurowebcart CMS Light

- [Sự quản lý](vi/readme.md)
- [Câu hỏi thường gặp của quản trị viên](vi/faq.md)
- [Mẫu và thành phần](vi/templates.md)

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

