-- HỆ THỐNG QUẢN LÝ SỰ KIỆN / CÂU LẠC BỘ SINH VIÊN
-- Database: MySQL

CREATE DATABASE IF NOT EXISTS qlclb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qlclb;

-- Bảng người dùng (Users)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('guest', 'member', 'organizer', 'admin') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng câu lạc bộ (Clubs)
CREATE TABLE clubs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    logo_url VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Bảng thành viên ban tổ chức (Club Organizers)
CREATE TABLE club_organizers (
    club_id INT NOT NULL,
    user_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (club_id, user_id),
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng sự kiện (Events)
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    registration_deadline DATETIME NOT NULL,
    max_participants INT NOT NULL DEFAULT 0, -- 0 = unlimited
    current_participants INT NOT NULL DEFAULT 0,
    status ENUM('open', 'closed', 'full', 'cancelled') NOT NULL DEFAULT 'open',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng đăng ký sự kiện (Registrations)
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered', 'cancelled', 'attended') NOT NULL DEFAULT 'registered',
    attendance_checked BOOLEAN NOT NULL DEFAULT FALSE,
    attendance_time DATETIME NULL,
    UNIQUE KEY unique_registration (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng thông báo (Notifications)
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_events_club ON events(club_id);
CREATE INDEX idx_events_start ON events(start_time);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_registrations_event ON registrations(event_id);
CREATE INDEX idx_registrations_user ON registrations(user_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);

-- Insert default admin user (password: admin123 - hashed with password_hash)
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@qlclb.com', 'admin');

-- Dữ liệu mẫu: Người dùng
INSERT INTO users (username, password, full_name, email, role) VALUES
('organizer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'vana@qlclb.com', 'organizer'),
('organizer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 'thib@qlclb.com', 'organizer'),
('member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', 'vanc@qlclb.com', 'member'),
('member2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị D', 'thid@qlclb.com', 'member'),
('member3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Văn E', 'vane@qlclb.com', 'member');

-- Dữ liệu mẫu: Câu lạc bộ
INSERT INTO clubs (name, description, created_by) VALUES
('CLB Lập trình', 'Nơi chia sẻ kiến thức và kỹ năng lập trình', 2),
('CLB Tiếng Anh', 'Rèn luyện kỹ năng giao tiếp tiếng Anh', 3),
('CLB Thể thao', 'Các hoạt động thể thao và rèn luyện sức khỏe', 2);

-- Gán organizer cho CLB
INSERT INTO club_organizers (club_id, user_id) VALUES
(1, 2),
(2, 3),
(3, 2);

-- Dữ liệu mẫu: Sự kiện
INSERT INTO events (club_id, title, description, location, start_time, end_time, registration_deadline, max_participants, current_participants, status, created_by) VALUES
(1, 'Workshop Python Cơ Bản', 'Học lập trình Python từ con số 0', 'Phòng A101', '2026-08-20 14:00:00', '2026-08-20 17:00:00', '2026-08-19 23:59:59', 30, 2, 'open', 2),
(1, 'Hackathon Mùa Hè 2026', 'Cuộc thi lập trình kéo dài 24h', 'Hội trường lớn', '2026-09-01 08:00:00', '2026-09-02 08:00:00', '2026-08-30 23:59:59', 50, 0, 'open', 2),
(2, 'Giao lưu Tiếng Anh với người bản xứ', 'Thực hành nói chuyện với khách mời nước ngoài', 'Sân trường', '2026-08-25 18:00:00', '2026-08-25 20:30:00', '2026-08-24 12:00:00', 20, 1, 'open', 3),
(3, 'Giải bóng đá sinh viên', 'Thi đấu giao hữu giữa các khoa', 'Sân vận động', '2026-08-18 15:00:00', '2026-08-18 18:00:00', '2026-08-17 23:59:59', 40, 40, 'full', 2),
(1, 'Seminar AI & Machine Learning', 'Giới thiệu về trí tuệ nhân tạo', 'Phòng B202', '2026-08-10 09:00:00', '2026-08-10 11:30:00', '2026-08-09 23:59:59', 25, 10, 'closed', 2);

-- Dữ liệu mẫu: Đăng ký sự kiện
INSERT INTO registrations (event_id, user_id, status, attendance_checked) VALUES
(1, 4, 'registered', FALSE),
(1, 5, 'registered', FALSE),
(3, 4, 'registered', FALSE),
(4, 4, 'registered', TRUE),
(4, 5, 'registered', TRUE),
(4, 6, 'registered', FALSE),
(5, 5, 'attended', TRUE);