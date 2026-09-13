<?php
require_once __DIR__ . '/../hosoadmin.php';
require_once __DIR__ . '../../../models/user.php';
require_once __DIR__ . '../../../models/db-connect.php';
require_once __DIR__ . '../../../partials/header-admin.php';
$pdo = connect_db();
$controller = new UserController($pdo);
$id = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : '';

try {
    $user = $controller->index($id);
} finally {
    $pdo = disconnect_db($pdo);
}
$result = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = connect_db();
        $controller = new UserController($pdo);
        $result = $controller->changePassword($id);
        if ($result['status'] === true) {
            $_SESSION['change_password_message'] = $result['message'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = $result['message']; 
        }
    } finally {
        $pdo = disconnect_db($pdo);
    }
}
?>

<div class="head-page">
    <h2>Quản lý tài khoản</h2>

</div>

<main class="main-content change-password">
    <div class="">
        <div class="head-form">
            <h1 class="">Đổi mật khẩu </h1>
        </div>
        <form id="user-form" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <div class="main-form">
                    <?php if (isset($_SESSION['change_password_message'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['change_password_message']; ?>
                            <?php unset($_SESSION['change_password_message']); ?>
                        </div>
                    <?php elseif (isset($message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <div class="main-form">
                    <label for="old-password" class="">Mật khẩu hiện tại</label>
                    <input type="password" id="old-password" name="oldPassword" class="" required />
                </div>

            </div>
            <div class="form-group">
                <div class="main-form">

                    <label for="new-password" class="">Mật khẩu mới</label>
                    <input type="password" id="new-password" name="newPassword" class="" required minlength="8" />
                </div>

            </div>
            <div class="form-group">
                <div class="main-form">

                    <label for="re-password" class="">Nhập lại mật khẩu mới</label>
                    <input type="password" id="re-password" name="rePassword" class="" minlength="8" required />
                </div>

            </div>

            <div class="form-group">
                <div class="main-form">
                    <button type="submit" class="btn-succes">Xác nhận</button>
                </div>

            </div>
            <div class="form-group">
                <div class="main-form">

                    *Độ mạnh của mật khẩu:
                    Hãy sử dụng ít nhất 8 ký tự. Không sử dụng mật khẩu cho trang web khác hoặc nội dung quá rõ ràng.
                </div>

            </div>
        </form>


    </div>
</main>

</div>
</div>

</body>

</html>