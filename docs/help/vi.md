# Tiếng Việt · 1.6.0

## Tìm kiếm và bộ nhớ đệm

Trong mục Bảo trì, tạo lại chỉ mục văn bản sau khi xuất bản hoặc sửa trang qua quản trị hay FTP. Gợi ý dùng các trang công khai theo ngôn ngữ của khách. Bộ nhớ đệm mặc định tắt; có thể bật hoặc xóa ở cùng mục. Biểu mẫu và nội dung động được xử lý theo từng yêu cầu.

## Chỉnh sửa trên trang

Sau khi đăng nhập, công tắc bên phải thanh công cụ bật chế độ sửa trên toàn trang và hiển thị liên kết cạnh các thành phần. Mở CSS hoặc JS của trang trong hộp thoại. Trình quản trị có chế độ HTML, trực quan, chèn mã, tải hàng loạt qua AJAX và sắp xếp tệp.

## Thành phần và điều hướng

Khi thiếu tham số, thành phần dùng cấu hình chung. width nhận 100, 50, 33 hoặc 25, có thể thêm dấu %. Trên điện thoại các khối xếp thành một cột. numbers=off ẩn số ảnh; vuốt trái hoặc phải để chuyển. Menu di động mở từ bên phải. Có thể tắt đường dẫn phân cấp trong cài đặt cơ bản.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## Ngôn ngữ, giao diện và giá

Trong cài đặt khu vực, chọn ngôn ngữ quản trị và trang web, tiền tệ, múi giờ, định dạng số và thời gian. Đặt index_en.php cạnh index.php để dịch trang; nếu thiếu bản dịch, nội dung gốc vẫn hiển thị. Kiểu giá dùng tiền tệ đã chọn. Có thể xem màu chủ đề, đổi chế độ tức thì, tải hai logo sáng/tối và thử Roboto Condensed bằng câu tự chọn.

```text
/contacts/index.php → /contacts/index_en.php
```

## Biểu mẫu, yêu cầu và tích hợp

Cài đặt CAPTCHA chung nhận khóa Google reCAPTCHA v2 và có thể áp dụng cho mọi biểu mẫu thường; vẫn có cấu hình riêng. Tin nhắn và yêu cầu gọi lại hiện 20 mục mỗi trang, chưa đọc ở trên. Tích hợp gồm Zadarma, Telegram, phân tích và bản đồ Google hoặc Yandex với cụm điểm tùy chọn. Hộp thoại thư viện thử phiên bản đang dùng và hiển thị mã chèn. CodeMirror có Tự động, HTML, CSS, JS, PHP và RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## Mô-đun PHP

Trong mô-đun PHP, sửa tên, mô tả và quyền chèn. server-date hiện ngày theo định dạng khu vực. Nhà phát triển đặt các tệp thực thi trong thư mục được bảo vệ.

```text
[php]server-date[/php]
```

