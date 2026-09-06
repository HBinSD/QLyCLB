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
Điểm danh người tham gia.
Cập nhật trạng thái điểm danh.
Xem thống kê số lượng đăng ký và tham gia.
Quản lý thông báo của câu lạc bộ.
#### Quản trị viên
Đăng nhập hệ thống.
Quản lý tài khoản người dùng.
Thêm, sửa, xóa câu lạc bộ.
Quản lý thông tin câu lạc bộ.
Quản lý thành viên.
Thêm, sửa, xóa và quản lý sự kiện.
Quản lý danh sách người đăng ký.
Quản lý hoạt động của các câu lạc bộ.
Quản lý thông báo.
Quản lý và kiểm soát quyền người dùng.
Xem thống kê số lượng đăng ký và tham gia.
Xem báo cáo tổng quan về hoạt động của hệ thống.
## 3. Công nghệ
  - PHP, MySQL, PDO, MVC, JavaScript

## 4. Cài đặt
## 5. Tài khoản demo
## 6. Chức năng đã hoàn thành
## 7. Cấu trúc thư mục
```text
QLyCLB/
├── admin/             # Giao diện admin + các chức năng
├── organizer/         # Giao diện người tổ chức + các chức năng
├── member/            # Giao diện thành viên + các chức năng
├── database/          # Cơ sở dữ liệu
├── Models/            # Các file xử lí luồng dữ liệu
├── README.md          # Tài liệu dự án
├── about.php          # Trang about
├── login.php          # Trang đăng nhập
└── logout.php         # Trang chính
```

## 8. Test checklist
## 9. Hạn chế và hướng phát triển
