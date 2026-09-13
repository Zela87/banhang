<?php
require_once __DIR__ . '/../hosoadmin.php';
require_once __DIR__ . '../../../models/user.php';
require_once __DIR__ . '../../../models/db-connect.php';
require_once __DIR__ . '../../../partials/header-admin.php';

$pdo = connect_db();
$controller = new UserController($pdo);
$id = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : '';

try {
    $user = $_SESSION['user'];
    if (!$user) {
        echo "Không tìm thấy hồ sơ người dùng.";
        exit;
    }
} finally {
    $pdo = disconnect_db($pdo);
}
?>

<div class="head-page">
    <h2>Quản lý tài khoản</h2>
</div>
<main class="main-content">
    <div class="">
        <div class="head-form">
            <h1 class="">Hồ sơ admin </h1>
        </div>
        <div class="content-ho-so">
            <div class="form-admin">
                <div class="left-side">
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <p>
                            <?php echo isset($user) && $user ? ($_SESSION['user']['hoten'] ?? "Chưa có họ tên") : "Chưa có họ tên"; ?>
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <p>
                            <?php echo isset($user) && $user ? ($_SESSION['user']['email'] ?? "Chưa có email") : "Chưa có email"; ?>
                        </p>
                    </div>
                    <div class="form-group">
                        <label>SĐT</label>
                        <p>
                            <?php echo isset($user) && $user ? ($_SESSION['user']['sdt'] ?? "Chưa có sdt") : "Chưa có sdt"; ?>
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <p>
                            <?php echo isset($user) && $user ? ($_SESSION['user']['diachi'] ?? "Chưa có địa chỉ") : "Chưa có địa chỉ"; ?>
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <p>
                            <?php echo isset($user) && $user ? ($_SESSION['user']['gioitinh'] ?? "Chưa có giới tính") : "Chưa có giới tính"; ?>
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <p>
                            <?php
                            if (isset($user) && !empty($_SESSION['user']['ngaysinh'])) {
                                echo date("d-m-Y", strtotime($_SESSION['user']['ngaysinh']));
                            } else {
                                echo "Chưa có ngày sinh";
                            }
                            ?>
                        </p>
                    </div>
                </div>
                <div class="right-side-admin">
                    <div class="img-ava">
                        <?php if (isset($_SESSION['user']['Avatar']) && !empty($_SESSION['user']['Avatar'])): ?>
                            <img src="<?php echo $_SESSION['user']['Avatar']; ?>" alt="">
                        <?php else: ?>
                            <img src="/greenplanet_copy/assets/images/user.jpg" alt="Hình ảnh mặc định">
                        <?php endif; ?>
                    </div>
                    <div class="name-admin">
                        <?php echo isset($user) && $user ? ($_SESSION['user']['hoten'] ?? "Chưa có họ tên") : "Chưa có họ tên"; ?>
                    </div>
                </div>
            </div>
            <div class="btn-form">
                <a href="/greenplanet_copy/admin/profile/edit-ho-so.php" class="btn-succes">Cập nhật hồ sơ</a>
                <a href="/greenplanet_copy/admin/profile/change-password.php" class="btn-succes">Đổi mật khẩu</a>
            </div>
        </div>
    </div>
</main>
</div>
</div>
</body>
</html>
