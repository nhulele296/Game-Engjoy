# Memory Game Project

## 📝 Mô tả
Game trí nhớ (Memory Game) là một trò chơi giúp người chơi rèn luyện khả năng ghi nhớ thông qua việc tìm các cặp thẻ giống nhau. Người chơi sẽ lật các thẻ và cần nhớ vị trí của chúng để tìm ra các cặp thẻ trùng khớp.

## 🎮 Tính năng chính
- Đăng ký và đăng nhập tài khoản
- Đăng nhập bằng Google
- Chơi game với nhiều cấp độ khó khác nhau
- Tính điểm và thời gian chơi
- Bảng xếp hạng người chơi
- Khôi phục mật khẩu qua email

## 📁 Cấu trúc thư mục và chức năng

### 🔷 Frontend (app1/)
- `index.html` - Trang chủ của game
- `login.html` - Giao diện đăng nhập
- `register.html` - Giao diện đăng ký tài khoản
- `game.html` - Giao diện chính của game
- `profile.html` - Trang thông tin người chơi
- `leaderboard.html` - Bảng xếp hạng

#### Components (app1/components/)
- `Card.js` - Component thẻ bài trong game
- `Timer.js` - Component đếm thời gian chơi
- `Score.js` - Component hiển thị và tính điểm
- `GameBoard.js` - Component quản lý bàn chơi
- `Modal.js` - Component popup thông báo

#### CSS (app1/css/)
- `style.css` - Style chung cho toàn bộ game
- `game.css` - Style riêng cho giao diện game
- `auth.css` - Style cho các trang đăng nhập/đăng ký

### 🔷 Backend (php/)
#### Authentication
- `login.php` - Xử lý đăng nhập
- `register.php` - Xử lý đăng ký tài khoản
- `google_login.php` - Xử lý đăng nhập bằng Google
- `forgot_password.php` - Xử lý quên mật khẩu
- `reset_password.php` - Xử lý đặt lại mật khẩu

#### Game Logic
- `game_state.php` - Quản lý trạng thái game
- `score_handler.php` - Xử lý tính điểm
- `leaderboard.php` - Xử lý bảng xếp hạng
- `save_progress.php` - Lưu tiến độ game

#### Database
- `config.php` - Cấu hình kết nối database
- `db_functions.php` - Các hàm thao tác với database

#### API
- `api/user.php` - API xử lý thông tin người dùng
- `api/game.php` - API xử lý logic game
- `api/scores.php` - API xử lý điểm số

### 🔷 Database Schema
```sql
-- Bảng Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255),
    created_at TIMESTAMP
);

-- Bảng Scores
CREATE TABLE scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    score INT,
    time_taken INT,
    level VARCHAR(20),
    completed_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bảng Game_Progress
CREATE TABLE game_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    current_level VARCHAR(20),
    saved_state JSON,
    last_played TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 🛠️ Công nghệ sử dụng
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Authentication:** Google OAuth 2.0
- **Server:** Apache (XAMPP)
- **Libraries:** 
  - PHPMailer (gửi email)
  - JWT (xác thực token)
  - Google API Client

## 🔧 Yêu cầu hệ thống
- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Apache Server
- Web browser hiện đại (Chrome, Firefox, Edge...)
- XAMPP (recommended)

## ⚙️ Hướng dẫn cài đặt
1. Clone repository về máy:
```bash
git clone https://github.com/nhulele296/Game-Engjoy.git
```

2. Copy thư mục project vào htdocs của XAMPP

3. Import database:
   - Mở phpMyAdmin
   - Tạo database mới tên "memory_game"
   - Import file `database/memory_game.sql`

4. Cấu hình:
   - Chỉnh sửa thông tin kết nối database trong `php/config.php`
   - Cấu hình Google OAuth trong `php/google_config.php`
   - Cấu hình email trong `php/mail_config.php`

5. Khởi động XAMPP:
   - Start Apache
   - Start MySQL

6. Truy cập website:
   - Mở browser
   - Truy cập `http://localhost/Game`

## 👥 Tác giả
- Lê Như (@nhulele296)

## 📄 License
Project này được phân phối dưới giấy phép MIT License. 