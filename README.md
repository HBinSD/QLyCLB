# Hệ thống quản lý câu lạc bộ sinh viên

## 1. Thành viên
- Chu Xuân Minh Đức - 224001782
- Nguyễn Tùng Dương - 224001781
- Đinh Gia Hưng - 224001798
- Phạm Thị Hoàng Lan - 224001802
- Vũ Mai Phương -224001823

## 2. Mô tả bài toán

### Mục tiêu

- Quản lý thông tin 1 câu lạc bộ.
- Quản lý danh sách thành viên.
- Quản lý các hoạt động/sự kiện.
- Cho phép sinh viên đăng ký tham gia câu lạc bộ.
- Theo dõi hoạt động và thành viên của câu lạc bộ.
- Giúp việc quản lý thuận tiện và giảm thao tác thủ công.

### Chức năng

#### Sinh viên
Đăng ký tài khoản.
Đăng nhập/đăng xuất.
Xem thông tin cá nhân.
Xem danh sách câu lạc bộ.
Xem thông tin chi tiết câu lạc bộ.
Xem danh sách sự kiện/hoạt động.
Tìm kiếm và lọc sự kiện theo thời gian, câu lạc bộ và trạng thái.
Xem chi tiết sự kiện.
Đăng ký tham gia sự kiện còn chỗ.
Hủy đăng ký sự kiện trước thời hạn.
Xem danh sách các sự kiện đã đăng ký.
Xem lịch sử tham gia hoạt động.
Nhận và xem thông báo.
#### Ban tổ chức
Đăng nhập hệ thống.
Xem thông tin câu lạc bộ được phân công.
Thêm, sửa, xóa sự kiện.
Quản lý thông tin và thời gian đăng ký sự kiện.
Đóng/mở đăng ký sự kiện.
Quản lý số lượng người tham gia.
Xem danh sách người đăng ký.
Quản lý người tham gia sự kiện.
Xem thống kê số lượng đăng ký và tham gia.
Quản lý thông báo của câu lạc bộ.
#### Quản trị viên
Đăng nhập hệ thống.
Quản lý tài khoản người dùng.
Sửa thông tin câu lạc bộ.
Quản lý thông tin câu lạc bộ.
Quản lý thành viên.
Quản lý thông báo.
Quản lý và kiểm soát quyền người dùng.
## 3. Công nghệ
  - PHP, MySQL, PDO, JavaScript

## 4. Cài đặt
## 5. Tài khoản demo
- Member:
  Username: member01, member02
  Pass: 123456

- Organizer: 
  Username: organizer01
  Pass: 123456

- Admin
  Username: admin01
  Pass: 123456

## 6. Chức năng đã hoàn thành
Xác thực & Phân quyền: Đăng ký 2 bước, Đăng nhập, Đăng xuất, Phân quyền truy cập qua middleware auth.php.
Quản lý Thành viên: Xem thông tin cá nhân, chỉnh sửa hồ sơ, tải lên ảnh đại diện, duyệt/từ chối thành viên mới.
Quản lý Câu lạc bộ & Ban: Xem thông tin CLB, quản lý danh sách thành viên, thêm/xóa các Ban/Bộ phận chuyên môn.
Quản lý Sự kiện: Tạo mới, chỉnh sửa, xóa sự kiện, giới hạn số chỗ (slots), lọc sự kiện theo điều kiện Ban.
Luồng Đăng ký Sự kiện: Đăng ký tham gia, hủy đơn đăng ký đang chờ (pending), hỗ trợ đăng ký lại sau khi hủy, duyệt/từ chối đơn đăng ký.
Truyền thông & Phản hồi: Gửi/nhận thông báo câu lạc bộ, form liên hệ và tiếp nhận phản hồi từ sinh viên.
## 7. Cấu trúc thư mục
```text
├── admin/                    # Phân hệ Quản trị viên (Admin)
│   ├── css/                  # File định dạng giao diện admin
│   ├── Thongbao/             # Thư mục quản lý/lưu trữ thông báo
│   ├── accept_member.php     # Xử lý chấp nhận thành viên
│   ├── add_band.php          # Thêm Ban/Bộ phận cho CLB
│   ├── approve_event.php     # Phê duyệt đơn đăng ký sự kiện
│   ├── approve_member.php    # Phê duyệt hồ sơ thành viên
│   ├── clubs.php             # Quản lý danh sách & thông tin CLB
│   ├── contact.php           # Quản lý tin nhắn / phản hồi liên hệ
│   ├── create_event.php      # Tạo mới sự kiện
│   ├── dashboard.php         # Bảng điều khiển tổng quan Admin
│   ├── delete_band.php       # Xóa Ban/Bộ phận khỏi CLB
│   ├── edit_event.php        # Chỉnh sửa thông tin sự kiện
│   ├── event_detail.php      # Xem chi tiết sự kiện & danh sách đăng ký
│   ├── event_registrations.php # Quản lý các đơn đăng ký sự kiện
│   ├── events.php            # Danh sách toàn bộ sự kiện
│   ├── member_detail.php     # Xem chi tiết hồ sơ thành viên
│   ├── member_edit.php       # Chỉnh sửa thông tin thành viên
│   ├── members.php           # Quản lý danh sách thành viên toàn hệ thống
│   ├── notification.php      # Quản lý & tạo thông báo
│   ├── reject_event.php      # Từ chối đơn đăng ký sự kiện
│   └── reject_member.php     # Từ chối đơn xin gia nhập của thành viên
├── database/
│   └── database.php          # Class kết nối CSDL PDO
├── includes/
│   ├── auth.php              # Middleware kiểm tra xác thực & phân quyền
│   ├── headers.php           # Layout Header dùng chung
│   └── footer.php            # Layout Footer dùng chung
├── member/                   # Phân hệ Thành viên
│   ├── css/                  # File định dạng giao diện member
│   ├── uploads/avatars/      # Thư mục lưu ảnh đại diện người dùng
│   ├── club.php              # Trang giới thiệu CLB
│   ├── club_member.php       # Danh sách thành viên CLB
│   ├── contact.php           # Form liên hệ / phản hồi
│   ├── dashboard.php         # Bảng điều khiển thành viên
│   ├── edit_profile.php      # Form cập nhật thông tin cá nhân
│   ├── events.php            # Danh sách sự kiện dành cho member
│   ├── event_detail.php      # Xem chi tiết & xử lý hủy/đăng ký lại
│   ├── news.php              # Tin tức & thông báo CLB
│   ├── profile.php           # Hồ sơ cá nhân
│   ├── register_event.php    # Backend xử lý nộp đơn đăng ký sự kiện
│   └── registered_events.php # Danh sách sự kiện cá nhân đã đăng ký
├── organizer/                # Phân hệ Ban tổ chức
│   ├── css/                  # File định dạng giao diện organizer
│   ├── approve_event.php     # Xử lý duyệt đơn đăng ký
│   ├── create_event.php      # Form & xử lý tạo mới sự kiện
│   ├── dashboard.php         # Bảng điều khiển tổng quan Organizer
│   ├── edit_event.php        # Chỉnh sửa thông tin sự kiện
│   ├── events.php            # Quản lý danh sách sự kiện
│   ├── event_detail.php      # Chi tiết sự kiện & danh sách đăng ký
│   ├── event_registrations.php # Danh sách đơn đăng ký chờ duyệt
│   └── reject_event.php      # Xử lý từ chối đơn đăng ký
├── about.php                 # Trang giới thiệu nhóm/đề tài
├── login.php                 # Trang đăng nhập hệ thống
├── loginsuccess.php          # Trang trung gian sau đăng nhập
├── logout.php                # Xử lý đăng xuất & hủy session
├── register.php              # Form đăng ký tài khoản (2 bước)
├── register_success.php      # Trang thông báo đăng ký thành công
└── README.md                 # Tài liệu hướng dẫn dự án

## 8. Test checklist
Đăng ký & Đăng nhập:
Đăng ký tài khoản mới thành công (chuyển hướng đếm ngược 5s).
Đăng nhập đúng role (member, organizer, admin) chuyển hướng chính xác đến trang Dashboard tương ứng.
Đăng nhập sai username/password hiển thị lỗi.
Đăng xuất làm mới và hủy hoàn toàn Session.
Phân hệ Thành viên (Member):
Cập nhật hồ sơ cá nhân và tải lên ảnh đại diện (.png, .jpg).
Đăng ký tham gia sự kiện còn slot trống và đúng Ban/Bộ phận yêu cầu.
Báo lỗi/chặn đăng ký khi sự kiện đã hết chỗ hoặc không thuộc Ban quy định.
Hủy đăng ký sự kiện khi đơn đang ở trạng thái pending.
Đăng ký lại sự kiện sau khi đã hủy thành công.
Phân hệ Ban tổ chức (Organizer):
Tạo sự kiện mới với giới hạn số chỗ và chọn các Ban được phép tham gia.
Chỉnh sửa thông tin, thời gian, địa điểm sự kiện đã tạo.
Phê duyệt đơn đăng ký sự kiện (tự động trừ slot còn trống).
Từ chối đơn đăng ký sự kiện kèm nhập lý do.
Phân hệ Quản trị viên (Admin):
Tạo mới CLB và gán ban quản lý phụ trách.
Thêm mới (add_band) và Xóa (delete_band) các Ban/Bộ phận trong CLB.
Duyệt đơn gia nhập CLB của thành viên mới (approve_member).
Xem danh sách và xử lý các phản hồi liên hệ (contact.php).
## 9. Hạn chế và hướng phát triển
Cơ chế thông báo mới dừng ở mức hiển thị thông tin trên hệ thống, chưa tích hợp các phương thức thông báo tự động như email hoặc thông báo theo thời gian thực. Chức năng điểm danh cũng đang được thực hiện thông qua thao tác của ban tổ chức, chưa hỗ trợ các phương thức tự động hóa như mã QR.
Trong tương lai, hệ thống có thể được mở rộng theo hướng tự động hóa, tăng tính bảo mật và nâng cao khả năng khai thác dữ liệu.
Trước hết, chức năng điểm danh có thể được phát triển bằng mã QR. Mỗi sự kiện có thể tạo một mã điểm danh riêng, sinh viên thực hiện quét mã để xác nhận tham gia. Cách tiếp cận này giúp giảm thao tác thủ công, nâng cao tốc độ điểm danh và hạn chế sai sót trong quá trình tổng hợp dữ liệu.
