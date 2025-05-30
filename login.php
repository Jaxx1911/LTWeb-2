<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
session_start();

// Debug connection
if (!$pdo) {
    die("Database connection failed");
}

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin.php");
    } elseif ($_SESSION['role'] == 'shipper') {
        header("Location: shipper-dashboard.php");
    } else {
        header("Location: user.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $login_error = "Vui lòng nhập đầy đủ thông tin";
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $username]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['code'] = $user['code'];
                        
                        if ($user['role'] == 'admin') {
                            header("Location: admin.php");
                        } elseif ($user['role'] == 'shipper') {
                            header("Location: shipper-dashboard.php");
                        } else {
                            header("Location: user.php");
                        }
                        exit();
                    } else {
                        $login_error = "Thông tin đăng nhập không chính xác";
                    }
                } catch (PDOException $e) {
                    $login_error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        } else if ($_POST['action'] === 'register') {
            $username = $_POST['reg_username'];
            $email = $_POST['reg_email'];
            $password = $_POST['reg_password'];
            $confirm_password = $_POST['reg_confirm_password'];

            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $register_error = "Vui lòng nhập đầy đủ thông tin";
            } else if ($password !== $confirm_password) {
                $register_error = "Mật khẩu xác nhận không khớp";
            } else {
                try {
                    // Check if username/email already exists
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetch()) {
                        $register_error = "Tên đăng nhập hoặc email đã tồn tại";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                        $stmt->execute([$username, $email, $hashed_password]);
                        $register_success = "Đăng ký thành công! Vui lòng đăng nhập.";
                    }
                } catch (PDOException $e) {
                    $register_error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập/Đăng ký - J&T Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/login.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <header class="login-header">
            <div class="logo-wrapper">
                <img src="/assets/images/logo.png" alt="J&T Express" class="logo">
            </div>
        </header>
        
        <main class="login-main">
            <div class="main-cont">
                <section class="column-center">
                    <div class="login-bt">
                        <div class="auth-tabs">
                            <button class="tab-btn active" data-tab="login">Đăng nhập</button>
                            <button class="tab-btn" data-tab="register">Đăng ký</button>
                        </div>

                        <!-- Login Form -->
                        <div class="tab-content" id="login-tab">
                            <h1 class="title">Đăng nhập</h1>
                            
                            <?php if (isset($login_error)): ?>
                                <div class="alert alert-danger"><?php echo $login_error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="login">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại, mã khách hàng hoặc email</label>
                                    <input type="text" class="form-control" name="username" 
                                        placeholder="Số điện thoại/ Mã khách hàng/ Email" required>
                                </div>
                                
                                <div class="form-group">
                                    <div class="password-label">
                                        <label class="form-label">Mật khẩu:</label>
                                        <a href="#" class="forgot-password">Quên mật khẩu</a>
                                    </div>
                                    <div class="password-group">
                                        <input type="password" class="form-control" name="password" 
                                            placeholder="Nhập mật khẩu" maxlength="50" required>
                                        <span class="password-toggle material-icons">visibility_off</span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn-login">Đăng nhập</button>
                            </form>
                        </div>

                        <!-- Register Form -->
                        <div class="tab-content" id="register-tab" style="display: none;">
                            <h1 class="title">Đăng ký</h1>
                            
                            <?php if (isset($register_error)): ?>
                                <div class="alert alert-danger"><?php echo $register_error; ?></div>
                            <?php endif; ?>
                            
                            <?php if (isset($register_success)): ?>
                                <div class="alert alert-success"><?php echo $register_success; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <input type="hidden" name="action" value="register">
                                <div class="form-group">
                                    <label class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" name="reg_username" 
                                        placeholder="Nhập tên đăng nhập" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="reg_email" 
                                        placeholder="Nhập địa chỉ email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Mật khẩu</label>
                                    <div class="password-group">
                                        <input type="password" class="form-control" name="reg_password" 
                                            placeholder="Nhập mật khẩu" maxlength="50" required>
                                        <span class="password-toggle material-icons">visibility_off</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Xác nhận mật khẩu</label>
                                    <div class="password-group">
                                        <input type="password" class="form-control" name="reg_confirm_password" 
                                            placeholder="Nhập lại mật khẩu" maxlength="50" required>
                                        <span class="password-toggle material-icons">visibility_off</span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn-login">Đăng ký</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Update active tab button
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                // Show selected tab content
                document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
                document.getElementById(button.dataset.tab + '-tab').style.display = 'block';
            });
        });

        // Toggle password visibility
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.textContent = type === 'password' ? 'visibility_off' : 'visibility';
            });
        });
    </script>
</body>
</html> 