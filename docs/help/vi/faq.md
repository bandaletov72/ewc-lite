# Câu hỏi thường gặp của quản trị viên

## Cài đặt và đăng nhập

### Làm cách nào để khởi chạy một trang web và mở bảng quản trị?

Để khởi chạy cục bộ, hãy chạy lệnh php -S 127.0.0.1:8000 router.php trong thư mục dự án và mở http://127.0.0.1:8000/admin/. Trên máy chủ lưu trữ, trước tiên hãy chạy php admin/setup.php, sau đó nhập mã một lần nhận được vào bước đầu tiên của trình hướng dẫn trong /admin/.

### Bạn có thể cấu hình những gì khi mới bắt đầu?

Đầu tiên tạo tên người dùng và mật khẩu. Ở bước thứ hai, đặt tên trang web, mô tả meta mặc định, văn bản chân trang, bảng màu và chế độ chủ đề. Nếu bạn có PHP GD, bạn có thể tải xuống logo ngay lập tức và tạo một favicon từ nó. Bạn có thể bỏ qua bước thứ hai hoặc tiếp tục vào lần đăng nhập tiếp theo.

## Trang và menu

### Làm thế nào để tạo một trang và nhập địa chỉ nào?

Đi tới Trang → Trang mới. Nhập địa chỉ, ví dụ /Services-1/Repair/ hoặc /Services-1/Price_2.php, điền tiêu đề và lưu. Đối với địa chỉ thư mục, chính công cụ sẽ tạo các thư mục và index.php. Cho phép sử dụng AZ, a-z, 0-9, dấu gạch ngang và dấu gạch dưới trong tên. Trang chính được dành riêng ở thư mục gốc và việc tạo các trang trong thư mục dịch vụ sẽ bị đóng.

### Sự khác biệt giữa hộp kiểm “Trong Menu” và nút “Ẩn” là gì?

Việc bỏ chọn hộp “Trong menu” sẽ xóa liên kết khỏi các menu thông thường, trong khi vẫn duy trì quyền truy cập vào địa chỉ trực tiếp. Trên trang có địa chỉ thư mục, hộp kiểm cũng kiểm soát các mục trên trang con. Nút “Ẩn” sẽ xóa liên kết và đóng địa chỉ công khai của trang cho đến khi nó được xuất bản lại.

### Làm cách nào để thay đổi thứ tự các trang hoặc di chuyển toàn bộ thư mục?

Kéo biểu tượng ⠿ để sắp xếp lại các trang trong cùng cấp độ. Để di chuyển một trang, hãy mở “Di chuyển trang” trong trình chỉnh sửa của trang đó. Để chuyển một thư mục cùng với các trang và tập tin đính kèm, hãy nhấp vào ↪ bên cạnh tên thư mục. Sau khi chuyển, hãy kiểm tra các liên kết thủ công và mã [include] trỏ đến địa chỉ cũ.

### Tại sao các thay đổi trên trang không hiển thị ngay lập tức?

Nhấp vào "Lưu" trong trình chỉnh sửa. Sau đó sử dụng liên kết "Mở trang" hoặc "Đi". Nếu liên kết đã biến mất, hãy kiểm tra xem trang đã được xuất bản chưa. Để biết những thay đổi trên tên miền phụ, hãy kiểm tra tên miền trong thanh địa chỉ.

## Thiết kế và tên miền phụ

### Tôi có thể thay đổi tiêu đề, chân trang, logo và favicon ở đâu?

Mở Cài đặt → Chung. Trường Văn bản Chân trang hỗ trợ [year] cho năm hiện tại. Trong khối “Biểu tượng và biểu tượng trang web”, bạn có thể tải hình ảnh lên hoặc nhấp vào “Tạo biểu tượng yêu thích từ biểu tượng”. Tải và chuyển đổi yêu cầu PHP GD.

### Làm cách nào để thay đổi chủ đề hoặc phông chữ rồi quay lại chủ đề hoặc phông chữ cũ?

Trong Cài đặt → Chủ đề & Màu sắc, chọn chế độ và bảng màu. Lưu bảng màu hiện tại vào danh sách để bạn có thể sử dụng nó sau này. Nút "Sử dụng làm mặc định" ghi nhớ bảng màu cho lệnh "Trả chủ đề về mặc định". Phông chữ có thể được thay đổi trong phần "Thư viện và Phông chữ". Tên và mẫu trong danh sách được hiển thị bằng phông chữ thích hợp. Giá trị "Hệ thống" trả về phông chữ bình thường.

### Thay đổi tiêu đề ở đâu và chèn bộ đếm, CSS hoặc JavaScript ở đâu?

Các khối tiêu đề, menu di động, menu và chân trang chung được chỉnh sửa trong tab “Mẫu thiết kế”. Ở cùng một vị trí, “Tiêu đề dịch vụ” chèn HTML vào phần đầu hoặc trước thẻ nội dung đóng. Đối với CSS và JavaScript thông thường, có một tab “CSS và JS” riêng. Mã cho một trang được thêm vào trình soạn thảo của nó.

### Làm cách nào để đặt số điện thoại và tiêu đề khác nhau cho tên miền phụ?

Thêm tên miền phụ vào phần "Miền". Sau đó đặt một giá trị mẫu con riêng trong phần Mẫu con bằng cách chọn miền bạn muốn. Thay đổi tiêu đề và các khối khác bằng nút “Mẫu” bên cạnh tên miền. Phiên bản miền được ưu tiên hơn phiên bản chung. DNS và máy chủ web phải trỏ tên miền phụ vào thư mục của trang web đó.

### Tôi có thể thay đổi liên kết đến mạng xã hội và chú thích bên cạnh chúng ở đâu?

Mở “Cài đặt → Danh bạ và mạng xã hội”. Điền địa chỉ, chữ ký và số điện thoại. Mã [soc-icons] hiển thị biểu tượng của tất cả các mạng được định cấu hình. Đối với một liên kết đơn, hãy sử dụng [tg] hoặc [vk] chẳng hạn.

## Các biểu mẫu và ứng dụng

### Làm cách nào để thêm biểu mẫu vào một trang?

Mở Biểu mẫu, định cấu hình các trường, bắt buộc, độ dài tối đa và văn bản nút. Lưu biểu mẫu và dán mã của nó, ví dụ [form1], vào HTML của trang.

### Tôi có thể xem đơn đăng ký ở đâu nếu chưa nhận được thông báo qua email?

Mở Tin nhắn. Các ứng dụng được lưu ở đó sau khi gửi biểu mẫu thành công. Để biết thông báo, hãy kiểm tra các địa chỉ trong trường “Tới” trong mẫu thư biểu mẫu và chức năng của chức năng mail() trên máy chủ lưu trữ.

### Làm thế nào để thiết lập một lá thư và gửi bản sao?

Mở biểu mẫu mong muốn và tìm khối “Mẫu thư”. Nhập địa chỉ vào trường “Tới” và “Bản sao (Cc), cách nhau bằng dấu phẩy. Trong chủ đề và nội dung, hãy sử dụng các phím trường trong dấu ngoặc nhọn, ví dụ Hello {name}! Các nút bên dưới văn bản sẽ chèn biến đã chọn. Chủ đề hoặc văn bản trống sử dụng thông báo tiêu chuẩn.

### Tại sao biểu mẫu từ chối tin nhắn hoặc hỏi "Tôi không phải là người máy"?

Kiểm tra các trường bắt buộc, độ dài, định dạng điện thoại và email. Các trường nhập miễn phí sử dụng văn bản thuần túy không có địa chỉ HTML hoặc trang web. Danh sách các từ và IP bị cấm nằm trong phần Chống thư rác. Hộp kiểm “Sử dụng CAPTCHA” yêu cầu khóa trang web và khóa bí mật Google reCAPTCHA v2 cho miền mong muốn.

### Tôi có thể xem yêu cầu gọi lại ở đâu?

Trong phần "Gọi lại". Thành phần này được thêm vào trang với mã [callback] hoặc [callback=Вызвать мастера]. Với nhà cung cấp “Không”, ứng dụng sẽ được lưu trong bảng quản trị. Khi điện thoại được cấu hình, hộp số tay hoặc tự động sẽ khả dụng.

## Khối thông tin và các thành phần

### Làm cách nào để tạo khối thông tin và hiển thị nó trên trang?

Trong phần “Infoblocks”, nhấp vào “Infoblock mới”, định cấu hình các cột và thêm hàng. Trong tên khối thông tin và các phím cột, sử dụng A–Z, a–z, 0–9, dấu gạch ngang hoặc dấu gạch dưới không có dấu cách. Lưu và sao chép mã nhúng từ trình chỉnh sửa khối thông tin. Để biết danh sách điện thoại, hãy sử dụng mẫu điện thoại.

### Làm cách nào để sao chép hoặc thay thế tất cả các hàng của khối thông tin?

Mở khối thông tin và nhấp vào “Chỉnh sửa dưới dạng văn bản”. CSV sử dụng | dấu phân cách. Ba dòng đầu tiên chứa khóa, tên và loại cột. Tiếp theo là dữ liệu. Đặt trường bằng dấu phân cách, dấu ngoặc kép hoặc dấu ngắt dòng trong dấu ngoặc kép và dấu ngoặc kép bên trong.

### Cần kiểm tra xem thư viện hoặc danh sách tập tin có trống không?

Kiểm tra đường dẫn trong mã thành phần với thư mục trên máy chủ. Ví dụ: [fotos]/images/myfotos/[/fotos] sử dụng ảnh trong images/myfotos/ hoặc templates/images/myfotos/.. Đối với [files], hãy kiểm tra phần mở rộng của tệp. Bạn có thể tìm thấy danh sách các tiện ích mở rộng và tham số được hỗ trợ trong phần trợ giúp thành phần.

### Tại sao yêu cầu có ?ajax=1 lại trả về 403?

Đoạn trang yêu cầu phiên công khai và mã thông báo hợp lệ trên cùng một trang. Để tải bên trong trang web, hãy sử dụng window.siteFetchContent() để chuyển mã thông báo. Để chuyển đổi bình thường, hãy mở địa chỉ trang không có tham số ajax.

## Cập nhật và sao lưu

### Làm cách nào để cập nhật công cụ của một trang web đã cài đặt?

Trong Cài đặt → Cập nhật, so sánh phiên bản đã cài đặt với phiên bản mới nhất từ ​​GitHub, sau đó nhấp vào Cập nhật công cụ. Nếu GitHub không khả dụng, bảng quản trị sẽ hiển thị phiên bản được kiểm tra lần cuối. Để cập nhật thủ công, hãy chuyển kho lưu trữ php-site-update.zip sang lệnh php update.php. Gói cập nhật thay thế các tệp công cụ và mẫu tiêu chuẩn. Lưu trữ các mẫu PHP của bạn trong templates/local/.

### Tôi nên sao chép những gì trước khi nâng cấp hoặc di chuyển?

Tạo một bản sao của toàn bộ thư mục trang web cùng với các tập tin ẩn. Các trang được đặt trong thư mục gốc và các thư mục của chúng, các khối thông tin trong tệp dữ liệu/, cài đặt và tài khoản trong admin/.. Đồng thời lưu các mẫu, hình ảnh đã tải lên, tin nhắn và yêu cầu của bạn. Bản sao bao gồm các bí mật về cấu hình, vì vậy hãy giữ nó bên ngoài thư mục công cộng.

### Cần kiểm tra điều gì nếu bảng quản trị không thể lưu thay đổi?

Đầu tiên hãy đọc tin nhắn bên cạnh biểu mẫu. Nếu các thay đổi xung đột, hãy sao chép các chỉnh sửa của bạn và mở lại trình chỉnh sửa. Nếu có lỗi ghi, hãy yêu cầu máy chủ lưu trữ kiểm tra quyền ghi của quy trình PHP trong thư mục và tệp admin/write-lock.php. mong muốn. Nếu cập nhật không thành công, hãy kiểm tra quyền truy cập của máy chủ vào GitHub cũng như tính khả dụng của PHP Zip và OpenSSL.

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

## Trình đơn di động · 1.6.7

- Hiện trong menu chính
- Hiện trong menu di động
- Ẩn trong menu di động

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
