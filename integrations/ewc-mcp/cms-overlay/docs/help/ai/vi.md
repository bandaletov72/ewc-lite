# AI: Kết nối ChatGPT

Chọn cách kết nối trình chỉnh sửa. Bạn có thể quay lại từ thẻ AI.

## ChatGPT qua Secure MCP Tunnel

1. Trong OpenAI Platform, tạo đường hầm riêng và khóa Tunnels Read + Use cho trang. Chỉ nhập ID đường hầm tại đây.
2. Cài gói MCP và ứng dụng khách OpenAI chính thức. Chọn thư mục nháp riêng và lưu khóa trong kho bí mật của dịch vụ. Chạy: `instance-from-cms.sh`.
3. Thêm máy chủ MCP tùy chỉnh trong ChatGPT với tên trang, chế độ Tunnel và cùng ID. Kiểm tra bằng list_pages.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## API EWC trực tiếp (không cần đường hầm)

Ứng dụng khách riêng dùng /mcp qua HTTPS và gửi mã EWC riêng qua Authorization: Bearer. Mặc định chỉ đọc.

Không thể nhập mã EWC này vào ChatGPT. Kết nối trực tiếp tới trình chỉnh sửa riêng tư cần OAuth 2.1 tương thích, chưa có trong gói. [OAuth](https://developers.openai.com/plugins/build/auth)

## Kiểm tra chỉnh sửa

`list_pages` → `draft_page` → `preview_draft` → xác nhận rõ ràng → `publish`.

Mở trang trong khu vực quản trị và so sánh bản xem trước. Chế độ chỉ đọc không cho chỉnh sửa.
