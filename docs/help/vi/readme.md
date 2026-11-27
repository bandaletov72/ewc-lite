# Eurowebcart CMS Lite

Tác giả - Бандалетов Павел. [Dự án gốc và cập nhật](https://github.com/bandaletov72/ewc-lite).

Phiên bản PHP tối thiểu là 7.2. Trang web và khu vực quản trị hỗ trợ PHP 8.6. Hãy dùng cú pháp của phiên bản trên máy chủ cho mô-đun và mẫu riêng.

[Câu hỏi thường gặp của quản trị viên](faq.md) cũng có sẵn trong tab “Trợ giúp”.

[Trợ giúp cho phiên bản hiện tại bằng mười ngôn ngữ](../README.md) mô tả tìm kiếm, chỉnh sửa trang web, bản địa hóa và bảo trì. [Admin FAQ in English](../en/faq.md).

Để bảo trì theo lịch trình, hãy thêm lệnh `php update.php --scheduled` vào bộ lập lịch lưu trữ trong khoảng thời gian 15 phút. Trong phần Bảo trì, bạn có thể kích hoạt lập chỉ mục lại một cách độc lập và đặt khoảng thời gian kiểm tra cho các bản cập nhật GitHub đã ký.

## Ra mắt cục bộ

Mở thư mục EWC Lite đã giải nén chứa `router.php` và khởi chạy máy chủ PHP tích hợp.

```sh
php -S 127.0.0.1:8000 router.php
```

Trang web sẽ mở tại địa chỉ `http://127.0.0.1:8000/`, bảng quản trị đặt tại `http://127.0.0.1:8000/admin/`.

## Cài đặt và đăng nhập lần đầu

Trên máy chủ cục bộ của bạn, hãy mở `/admin/` và tạo một tài khoản. Ở bước thứ hai, đặt tên trang web, mô tả meta, văn bản chân trang, bảng màu và chế độ chủ đề. Logo có thể được tải xuống ngay lập tức nếu bạn có PHP GD. Trình hướng dẫn sẽ tạo một favicon từ nó. Nhấp vào "Lưu và mở quản trị viên" hoặc tiếp tục với cài đặt hiện tại. Nếu bạn thoát khỏi trình hướng dẫn, nó sẽ tiếp tục vào lần đăng nhập tiếp theo.

Trên máy chủ lưu trữ, chạy `php admin/setup.php` từ thư mục dự án và nhập mã một lần vào bước đầu tiên của trình hướng dẫn. Mã được đổi khi bạn tạo tài khoản. Chạy lại lệnh trước khi cài đặt sẽ thay thế mã trước đó. Cài đặt bản cập nhật vẫn giữ lại tài khoản và cài đặt trước đó. Nếu phiên bản trước khi cập nhật sử dụng `SITE_ADMIN_PASSWORD_HASH`, thông tin đăng nhập sẽ được lưu bằng thông tin đăng nhập `admin` và mật khẩu trước đó.

Để ứng dụng hoạt động, bạn cần có phiên PHP và quyền ghi vào `admin/`, `admin/write-lock.php`, `admin/inbox/`, `data/` và thư mục của các trang có thể chỉnh sửa. Để tạo các trang và thư mục ở thư mục gốc, hãy cấp cho quy trình PHP quyền ghi vào thư mục gốc của dự án. Sử dụng máy chủ web có HTTPS, xử lý `.php` và `index.php` trong danh sách tệp chỉ mục. Vô hiệu hóa danh sách thư mục. Đối với HTTPS đằng sau proxy ngược, hãy chuyển thuộc tính PHP `HTTPS` thông qua cấu hình máy chủ đáng tin cậy.

## Trang và menu

Trong bảng quản trị, trang chính được liệt kê là `/`, các trang còn lại trong thư mục được liệt kê là `/contacts/`. Để tạo một trang con, hãy nhập, ví dụ: `/Services-1/Repair/`. Bảng quản trị sẽ tạo các danh mục và chính `index.php`. Đối với một tệp riêng biệt, hãy nhập địa chỉ như `/Services-1/About_2.php`. Cho phép sử dụng `A–Z`, `a–z`, `0–9`, dấu gạch ngang và dấu gạch dưới trong tên tệp và thư mục. Tên được nhập không có dấu cách. Tại thư mục gốc, chỉ cho phép trang chính. Các thư mục dịch vụ bao gồm `data`, `admin`, `templates`, `css` và `js` được bảo lưu.

Trong danh sách các trang, các thư mục có thể được mở rộng. Trong hộp kiểm Menu, ẩn, xuất bản và xóa được lưu qua AJAX. Ẩn, xuất bản và xóa yêu cầu xác nhận. Kéo biểu tượng ⠿ để sắp xếp các trang liền kề. Trang ẩn ngừng mở và biến mất khỏi menu trước khi xuất bản.

Trong trình chỉnh sửa trang, hãy mở khối “Di chuyển trang” và chỉ định thư mục đích. Ví dụ: nếu bạn chuyển `/contacts/` sang `/company/`, địa chỉ mới sẽ là `/company/contacts/`. Các trang lồng nhau vẫn ở cùng một địa chỉ. Cập nhật các liên kết và mã `[include]` của riêng bạn dẫn đến trang được di chuyển.

Kéo tay nắm ⠿ để sắp xếp các trang cùng cấp. Di chuyển một trang trong cài đặt của trang. Biểu tượng cạnh thư mục mở bộ chọn mục cha. Chọn mục khác hoặc cấp gốc rồi bấm Di chuyển thư mục. Các trang và tệp được chuyển cùng thư mục. Kiểm tra liên kết cũ và mã [include].

Nội dung có thể được chỉnh sửa trong trình soạn thảo văn bản và được thêm qua FTP hoặc SFTP. Tệp phải ở dạng UTF-8 không có BOM. Lưu khối dịch vụ PHP đến dòng `__halt_compiler();`. Trong tệp trang mới, chèn khối này từ ví dụ ở độ sâu mong muốn và kiểm tra đường dẫn tương đối đến thư mục gốc `index.php`. Tiếp theo là các tiêu đề, một dòng trống và một đoạn HTML của trang.

```php
<?php
$pageFile = __FILE__;
require __DIR__ . '/../index.php';
__halt_compiler();
Title: Контакты
Heading: Как с нами связаться
Description: Контактная информация
Menu: Контакты
MenuVisible: 1
Order: 20

<p>Укажите здесь контактную информацию.</p>
```

`Title` là bắt buộc. `Heading` trống được lấy từ `Title`. `Description` điền vào mô tả meta của trang. `MenuVisible: 1` hiển thị liên kết trong menu, `MenuVisible: 0` ẩn nó. Đối với thư mục `index.php`, việc bỏ chọn hộp kiểm cũng sẽ ẩn tất cả các trang con khỏi menu. Nếu thiếu `MenuVisible`, hộp kiểm sẽ kế thừa quy tắc trước đó và được coi là bật khi `Menu` được điền. Khi hộp kiểm này được bật, trường trống `Menu` sẽ được thay thế bằng tiêu đề `Title`. `Order` chỉ định trình tự trong một cấp độ và mặc định là 100.

Tab CSS và JS lưu trữ CSS và JavaScript phổ biến cho các trang công khai. Trình chỉnh sửa trang có một khối riêng biệt “Các trang CSS và JavaScript”. Mã trang được tải sau mã chung. Chỉ chỉ định nội dung của tệp mà không có thẻ `<style>` và `<script>`. Kích thước cho phép của mỗi trường lên tới 64 KiB. Để liên kết tới hình ảnh từ CSS, bạn có thể sử dụng đường dẫn từ thư mục gốc của trang. Tab Trợ giúp chứa các ví dụ về các mẫu và thành phần có sẵn.

Trong phần “Mẫu thiết kế”, bạn có thể xem các nguồn của `header.php`, `footer.php`, `menu.php` và `mobile-menu.php` và đặt HTML chung cho từng nguồn. "Tiêu đề dịch vụ" chèn các thẻ meta, mã truy cập và tập lệnh phổ biến vào `<head>` hoặc trước `</body>`. Trên mỗi trang đầy đủ được xuất bản, `rel="canonical"` được hình thành từ địa chỉ thông thường của nó mà không có tham số truy vấn.

## Cài đặt, mẫu phụ và tên miền

Trong phần “Cài đặt”, bạn đặt tên trang web, mô tả meta mặc định, văn bản chân trang và các biến. Trong văn bản chân trang, `[year]` tự động hiển thị năm hiện tại. Các biến được viết một biến trên mỗi dòng, ví dụ `[city] = Москва` và `[spb] = Санкт-Петербург`. Để hiển thị một giá trị trong tiêu đề trang, nội dung trang, đầu trang hoặc chân trang, hãy chèn `[city]`. Trong các mẫu PHP, bạn có thể sử dụng `site_escape($settings['variables']['city'] ?? '')`.

Các thay thế hệ thống bao gồm `[year]`, `[month]`, `[day]`, `[time]` (giờ và phút), `[dmY]` và `[date]` (ngày DD.MM.YYYY), `[datetime]` (ngày và giờ). Múi giờ PHP được sử dụng.

Trong “Cơ bản → Logo và biểu tượng trang web”, bạn có thể tải lên PNG, JPEG, GIF hoặc WebP. Việc tải xuống được xử lý bởi PHP GD. Nút tạo favicon tạo PNG trong suốt 64×64 từ logo đã chọn hoặc đã lưu. Hình ảnh được lưu trữ trong `templates/images/branding/` và `templates/icons/branding/`.

"Mẫu con" lưu trữ tên, mô tả và ý nghĩa của từng mã. Đối với điện thoại, tạo `[tel]`. Để đặt tiêu đề tab thông qua mẫu phụ, hãy tạo `[title]` và chỉ định nó trong trường `Title` trên trang. Sau tiêu đề tab, tên của trang web sẽ được hiển thị. Các thay thế hoạt động trong văn bản trang và siêu dữ liệu văn bản. Đối với liên kết, hãy chèn toàn bộ thẻ `<a>` làm giá trị mẫu phụ và đặt mã vào văn bản giữa các thẻ. Trong siêu dữ liệu, HTML được chuyển đổi từ giá trị thành văn bản.

Thêm tên miền phụ vào phần “Tên miền”. Trong bảng quản trị của tên miền phụ cùng tên, chọn “Miền hiện tại” hoặc mở miền mong muốn từ danh sách chung. Ý nghĩa địa phương được ưu tiên hơn ý nghĩa chung. Trang công khai chọn các giá trị theo tên miền trong địa chỉ của nó và các liên kết nội bộ sẽ lưu nó. Để kết nối một miền phụ, hãy định cấu hình DNS và máy chủ web trên dịch vụ lưu trữ của bạn để miền phục vụ cùng một thư mục dự án.

Nút "Mẫu" bên cạnh miền sẽ mở trình soạn thảo `[header]`, `[mobile-menu]`, `[menu]` và `[footer]`. Đối với mỗi khối, bạn có thể đặt HTML của riêng mình hoặc sử dụng phiên bản chung. Các mẫu chung được cấu hình trong cùng một phần.

## Các thành phần trong HTML

| Mã số | Kết quả |
| --- | --- |
| `[gal]/images/myfotos/[/gal]` | Băng chuyền ảnh với tính năng cuộn, mũi tên và vuốt mượt mà |
| `[fotos]/images/myfotos/[/fotos]` | Thư viện ảnh có liên kết đến bản gốc |
| `[files]/files/myfiles[/files]` | Danh sách các tập tin có biểu tượng theo phần mở rộng |
| `[include]/contacts/[/include]` | HTML của một trang khác với các mẫu con và thành phần của nó |
| `[form1]` | Biểu mẫu được định cấu hình trong phần Biểu mẫu |
| `[callback=Вызвать мастера]` | Nút và biểu mẫu yêu cầu gọi lại |
| `[iblock=table]/data/myiblock.txt[iblock]` | Bảng khối thông tin |
| `[iblock=phones]/data/myiblock.txt[iblock]` | Danh sách điện thoại khối thông tin |
| `[phones]`, `[contacts]` | Điện thoại hoặc toàn bộ khối liên lạc được cấu hình |
| `[soc-icons]` | Biểu tượng của tất cả các mạng xã hội có liên kết tùy chỉnh |
| `[tg]`, `[vk]`, `[whatsapp]`, `[youtube]`, `[instagram]`, `[max]` | Liên kết mạng xã hội có biểu tượng và chữ ký |

Đặt ảnh vào `templates/images/myfotos/` hoặc `images/myfotos/`. Danh sách tệp hiển thị `zip`, `pdf`, `xls`, `xlsx`, `doc`, `docx`, `png`, `gif`, `jpg`, `jpeg`, `mp4`, `mov` và `mp3`. Đối với `[include]`, hỗ trợ tối đa mười cấp độ lồng nhau. Việc lặp lại trang trong chủ đề hiện tại sẽ bị bỏ qua. Đặt mã thành phần vào phần nội dung của trang, trên một dòng riêng biệt giữa các khối HTML.

Trong “Cài đặt → Danh bạ và mạng xã hội”, đặt liên kết, số, chữ ký và tiêu đề khối. Trong “Cài đặt → Chủ đề và Màu sắc”, hệ thống, chế độ tối và sáng cũng như các bảng màu riêng lẻ đều có sẵn. Trong “Cài đặt → Thành phần”, bạn đặt các thông số chung của băng chuyền và thư viện ảnh. Trong cài đặt có các ví dụ về cách chèn với các giá trị đã chọn. Các tham số trong mã trang ghi đè các giá trị chung.

Trong Chủ đề & Màu sắc, bạn có thể lưu bản sao của cài đặt đã chọn dưới tên của chính mình hoặc để tên tự động. “Áp dụng” thay đổi thiết kế hiện tại. Sử dụng Mặc định áp dụng chủ đề đã lưu và ghi nhớ chủ đề đó cho lệnh Đặt lại chủ đề về mặc định. Việc xóa khỏi danh sách sẽ giữ nguyên thiết kế hiện tại của trang web.

```text
[gal arrows=off numbers=off effect=fade fit=cover]/images/myfotos/[/gal]
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off]/images/myfotos/[/fotos]
[files icons=on size=on order=desc download=on]/files/myfiles/[/files]
```

Đối với `[fotos]`, số lượng cột có thể được đặt thủ công cho màn hình rộng hoặc được chọn tự động. Trên màn hình hẹp, lưới làm giảm số lượng cột. Tỷ lệ khung hình có sẵn là 4:3, 1:1, 16:9 và chế độ gốc, `cover` và `contain`, chữ ký từ tên tệp và liên kết đến bản gốc.

## Các hình thức và chống thư rác

Trong phần “Biểu mẫu”, bạn có thể chỉnh sửa `[form1]`, tạo `[form2]` và các biểu mẫu sau. Đối với biểu mẫu, địa chỉ của người nhận, chữ ký nút và các trường được chỉ định. Trường có thể có độ dài tối đa. Giá trị trống sử dụng giới hạn mặc định cho loại trường. Khi bạn nhập `89031234567`, trường điện thoại sẽ định dạng nó là `+7(903)123-45-67`. Máy chủ kiểm tra độ dài, loại, lựa chọn hợp lệ và giới hạn tổng kích thước của yêu cầu. Các trường nhập miễn phí chấp nhận văn bản thuần túy. HTML trong tin nhắn bị thoát khi đầu ra và thẻ HTML trong tin nhắn mới bị từ chối.

Hộp kiểm "Sử dụng CAPTCHA" bật Google reCAPTCHA v2 với tùy chọn kiểm tra "Tôi không phải là rô-bốt" cho biểu mẫu đã chọn. Nhập khóa trang web và khóa bí mật cho tên miền. Bí mật được lưu trữ trong tệp cài đặt được bảo vệ và chỉ được sử dụng để xác minh máy chủ. [Tài liệu xác minh máy chủ](https://developers.google.com/recaptcha/docs/verify).

Các biểu mẫu được gửi qua AJAX. Các ứng dụng được lưu trong phần “Tin nhắn”. Nếu người nhận được chỉ định, `mail()` sẽ được sử dụng cho các thông báo yêu cầu thư được định cấu hình trên máy chủ. Trong phần Chống thư rác, bạn có thể chỉnh sửa danh sách từ, biểu thức, địa chỉ IPv4 và IPv6. Các liên kết bị chặn trên máy chủ. Địa chỉ người gửi được lấy từ `REMOTE_ADDR`. Đằng sau proxy ngược, hãy định cấu hình khôi phục IP gốc trong máy chủ web cho các proxy đáng tin cậy.

Mỗi biểu mẫu có một “Mẫu thư” với các trường “Tới”, “Cc (Cc)”, “Chủ đề email” và “Nội dung email”. Các địa chỉ được chỉ định cách nhau bằng dấu phẩy. Bản sao sẽ được gửi nếu ít nhất một người nhận chính được chỉ định. Các phím trường được chèn vào chủ đề và văn bản trong dấu ngoặc nhọn, ví dụ `Привет {name}!`. Giá trị trường trống được thay thế bằng một chuỗi trống, khóa không xác định vẫn còn trong chữ cái. Nếu chủ đề hoặc văn bản trống thì phần tương ứng của chữ cái tiêu chuẩn sẽ được sử dụng.

## Yêu cầu gọi lại

Chèn `[callback]` hoặc `[callback=Вызвать мастера]` vào trang HTML. Nút này sẽ mở một biểu mẫu có số điện thoại. Các yêu cầu được lưu trong phần “Gọi lại”. Chữ ký nút và chế độ chuyển yêu cầu sang điện thoại cũng được đặt ở đó. Theo mặc định, nhà cung cấp “Không” được chọn và các yêu cầu chỉ được lưu. Điện thoại có thể được kích hoạt để gửi thủ công từ danh sách hoặc gửi ngay sau khi nhận được số.

- **Zadarma.** Đặt số, SIP hoặc số máy nhánh của nhà điều hành trong bảng quản trị và chuyển khóa sang PHP qua `SITE_ZADARMA_KEY` và `SITE_ZADARMA_SECRET`. [Tài liệu API Zadarma](https://zadarma.com/en/support/api/)
- **API HTTP.** Chỉ định `SITE_CALLBACK_WEBHOOK_URL` bằng địa chỉ HTTPS và `SITE_CALLBACK_WEBHOOK_TOKEN` tùy chọn. Trang web gửi một POST với các trường JSON `id`, `phone`, `host`, `page`, `requested_at` và tiêu đề `Idempotency-Key`. Mã thông báo được chuyển dưới dạng Người mang. Mã phản hồi 2xx chuyển yêu cầu sang trạng thái “Được điện thoại chấp nhận”.

Cấp quyền ghi quy trình PHP cho `admin/callbacks/`. Ứng dụng sử dụng hình thức cài đặt chống thư rác. Việc lặp lại cùng một số được giới hạn trong một phút; tối đa ba yêu cầu mỗi 15 phút được chấp nhận qua IP.

## Khối thông tin và thư mục dữ liệu đóng

Trong phần “Infoblocks”, bạn có thể tạo tập dữ liệu, chỉnh sửa và kéo các hàng. `A–Z`, `a–z`, `0–9`, dấu gạch ngang và dấu gạch dưới được cho phép trong tên khối thông tin và các phím cột. Tên và khóa được nhập không có dấu cách. Tên cột và giá trị ô có thể được viết bằng tiếng Nga. Các điều khiển tương ứng với các loại cột và việc lưu được thực hiện thông qua AJAX. Nút ☰ “Chỉnh sửa dưới dạng văn bản” chuyển toàn bộ khối thông tin sang CSV bằng dấu phân cách `|` và nút sao chép. Ba dòng đầu tiên chứa khóa, tên và loại cột. Sau đó là các dòng dữ liệu. Ngày trong CSV là `ГГГГ-ММ-ДД`, có/không được lưu trữ dưới dạng `1` hoặc `0`.

```text
name|phone|is_open
Название|Телефон|Открыто
text|tel|bool
Справочная|+7(903)123-45-67|1
```

Mẫu `phones` sử dụng các cột loại `tel` và một cột văn bản có tiêu đề. Bạn có thể đặt mẫu của mình trong `templates/local/iblock/имя.php`, nơi có sẵn `$block['columns']` và `$block['rows']`.

Đường dẫn `/data/myiblock.txt` bên trong mã tương ứng với tệp `data/myiblock.txt.php`. Đây là văn bản UTF-8 có gốc PHP trả về 404 khi được gọi trực tiếp. `router.php` đóng toàn bộ thư mục khi chạy cục bộ. `data/` có các quy tắc dành cho Apache và IIS. Đối với Apache, hãy bật `data/.htaccess`. Trên Nginx, định cấu hình thư mục sẽ được đóng trong cấu hình trang web, có tính đến tiền tố khi cài đặt trong thư mục con.

```nginx
location = /data { return 404; }
location ^~ /data/ { return 404; }
```

Sau khi cài đặt, hãy kiểm tra xem yêu cầu trực tiếp tới `/data/` và tệp infoblock có nhận được 403 hoặc 404 hay không.

## Đang tải các đoạn HTML

Địa chỉ thông thường `/contacts/` sẽ mở toàn bộ trang. Yêu cầu `?ajax=1` trả về `<main>` với H1 và nội dung được xử lý nếu có khóa phiên trong tiêu đề `X-Site-Fragment`. Các mảnh vỡ được liên kết với nguồn gốc hiện tại. Các trang và đoạn có khóa được cung cấp mà không cần lưu vào bộ nhớ đệm.

Trong JavaScript trên một trang của trang web, hãy sử dụng hàm dựng sẵn.

```js
const html = await window.siteFetchContent('/contacts/');
document.getElementById('content').outerHTML = html;
```

Khi cài đặt trong thư mục con, hãy chuyển URL có tiền tố trang web. Tập lệnh và kiểu được lấy từ trang chèn đoạn. Các trang công khai vẫn có thể truy cập được tại các địa chỉ thông thường.

## Mẫu và tài nguyên

Trong “Cài đặt → Thư viện và phông chữ”, jQuery, Bootstrap, Font Awesome, Material UI, Alpine.js, htmx, Chart.js và GLightbox được bật riêng. Font Awesome được lưu trữ cục bộ. Các thư viện còn lại được trình duyệt tải xuống từ CDN khi được bật. Giao diện người dùng Material có sẵn trong các tập lệnh `type="module"` đến `import { Button } from '@mui/material'`. Đối với các biểu tượng, `[fa-icon=house]` hoặc đánh dấu thông thường `<i class="fa-solid fa-house"></i>` là phù hợp.

Có sẵn 10 phông chữ Google với bảng chữ cái Cyrillic. Lựa chọn này sẽ thay thế phông chữ hệ thống của trang web. Giá trị "Hệ thống" trả về thiết kế trước đó. Trong “Cài đặt → Chủ đề & Màu sắc → Chủ đề đã lưu” có năm bảng màu được tạo sẵn với các tùy chọn sáng và tối.

| Con đường | Mục đích |
| --- | --- |
| `index.php` | Trang chủ và điểm vào chung |
| `contacts/index.php` | Ví dụ về một trang trong một thư mục riêng |
| `admin/` | Đăng nhập và chỉnh sửa trang |
| `app/site.php` | Đọc trang, xây dựng menu và tiêu đề website trong `SITE_NAME` |
| `templates/layout.php` | Bố cục chung, tiêu đề tab, siêu dữ liệu và khu vực nội dung |
| `templates/header.php`, `menu.php`, `footer.php` | Đầu trang, menu và chân trang |
| `templates/css/`, `js/`, `icons/`, `images/` | Kiểu, chữ viết, biểu tượng và hình ảnh |
| `templates/components/`, `templates/iblock/` | Mẫu đánh dấu thành phần và khối thông tin |
| `data/` | Các tệp khối thông tin được bảo vệ khỏi truy cập HTTP trực tiếp |

Công cụ này tập hợp một trang hoàn chỉnh từ `[header]`, `[mobile-menu]`, `[menu]` và `[footer]`. Đây là thanh trên cùng, menu di động, menu bên phải và chân trang. Chúng có thể được xác định lại bằng các mẫu con cùng tên trong bảng quản trị, bao gồm các tùy chọn riêng cho tên miền phụ. Lưu trữ phần ghi đè mẫu PHP trong `templates/local/` với cùng một đường dẫn tương đối, ví dụ `templates/local/footer.php`.

## Cập nhật động cơ

Cần có kho lưu trữ đầy đủ để cài đặt mới. Đối với trang web trực tiếp, hãy sử dụng `ewc-lite-update.zip`. Nó chỉ chứa động cơ và các mẫu tiêu chuẩn. Các trang đã lưu, khối thông tin, cài đặt, mật khẩu, tin nhắn và tiện ích mở rộng người dùng không được đưa vào kho lưu trữ.

Nhấp vào "Cập nhật công cụ" trong "Cài đặt → Cập nhật" hoặc chạy kiểm tra tương tự từ bộ lập lịch lưu trữ.

```sh
php update.php --github
```

Nguồn mặc định là `bandaletov72/ewc-lite`. Đối với kho lưu trữ của riêng bạn, hãy đặt nó thành `SITE_GITHUB_REPO`. Kho lưu trữ công cộng không yêu cầu mã thông báo. Đối với kho lưu trữ riêng, chuyển biến `SITE_GITHUB_TOKEN` cho quy trình PHP với quyền truy cập đọc nội dung. Máy chủ yêu cầu các tiện ích mở rộng Zip và OpenSSL PHP cũng như khả năng thực hiện các yêu cầu HTTPS tới GitHub.

Để cài đặt thủ công, hãy sử dụng ZIP.

```sh
php update.php /path/to/ewc-lite-update.zip
```

Trên phiên bản cũ không có `update.php`, trước tiên hãy giải nén gói phía trên dự án, giữ lại `.htaccess` hiện có và các tệp từ `maintenance/`, nếu có. Nếu bạn đã chỉnh sửa các mẫu tiêu chuẩn, hãy chuyển các chỉnh sửa sang `templates/local/` trước khi cập nhật. Trình cài đặt xác minh danh sách và tổng kiểm tra các tệp và đối với GitHub, nó cũng xác minh chữ ký bằng khóa chung đáng tin cậy `maintenance/release-public.pem`.

Đối với bản phát hành tiếp theo, hãy thay đổi phiên bản thành `app/version.php` và xây dựng gói thông qua `php update.php --build ewc-lite-update.zip`. Ký ZIP bằng khóa riêng RSA của bạn với SHA‑256 và lưu trữ khóa riêng tư này khỏi trang web. Sau đó đẩy ZIP và `.sig` lên GitHub Release với thẻ `vX.Y.Z` hoặc `updates/vX.Y.Z/` và cập nhật `updates/latest.json` thành `{"version":"X.Y.Z"}`.

## Tiện ích mở rộng

Các mô-đun động cơ có thể được thêm vào `app/extensions/` cùng với các bản cập nhật. Lưu trữ các mô-đun của riêng bạn trong `extensions/`, bên ngoài gói cập nhật. Tệp PHP trả về một mảng định nghĩa. Một thành phần duy nhất có `kind` bằng `single` và một thành phần có đối số bằng `wrapped`. Công cụ nhận dạng mã mới trên các trang hiện có. Một thành phần có thể khai báo menu các tùy chọn trong `settings`, đặt từng tùy chọn thành `label`, `default` và mảng `options`. Các giá trị được nhận trong `$context['settings']`.

```php
<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return ['notice' => [
    'kind' => 'wrapped',
    'render' => static function (string $text, array $context): string {
        return '<strong>' . site_escape($text) . '</strong>';
    },
]];
```

Thành phần ví dụ được chèn dưới dạng `[notice]Важное сообщение[/notice]`.

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

### Màu trang web · Phông chữ

Tích chọn phông chữ rồi gán cho văn bản, tiêu đề hoặc nút. Tệp cục bộ do chính trang web cung cấp.

Thư viện → Lưu cục bộ

Mẫu trang → Tạo trang

Trong cửa hàng giao diện, dùng các mũi tên để chuyển giữa ảnh trang chủ và cửa hàng. Ảnh thứ hai của Flor Atelier hiển thị bộ sưu tập bó hoa. Để nhập mục giảm giá cửa STAL, hãy chọn tải sản phẩm mẫu.
