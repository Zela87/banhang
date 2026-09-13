<?php
$sessionLifetime = 300;

// Cấu hình thời gian sống của session (cookie)
session_set_cookie_params($sessionLifetime);

// Cấu hình thời gian sống của session dữ liệu trên server
ini_set('session.gc_maxlifetime', $sessionLifetime);

session_start();

// Cập nhật thời gian hết hạn của cookie mỗi khi người dùng có tương tác
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $sessionLifetime) {
    session_unset(); 
    session_destroy();
    header('location: /greenplanet_copy/views/index.php');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
?>
<?php
require_once __DIR__ . '/../models/user.php';

$pdo = connect_db();
$controller = new Users($pdo);

$id = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : '';

if (!$id) {
    echo "Không tìm thấy hồ sơ người dùng.";
    exit;
}

try {
    $user = $controller->getProfileAdmin($id);
    if (!$user) {
        echo "Không tìm thấy hồ sơ người dùng.";
        exit;
    }
    $_SESSION['user'] = $user;
} finally {
    $pdo = disconnect_db($pdo);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/greenplanet_copy/assets/css/style-admin.css">
    <title><?php echo isset($title) ? $title." - Green Planet" : "Green Planet"; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="/greenplanet_copy/assets/images/logo-main.png" />
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/greenplanet_copy/admin/product/list-san-pham.php">
                            <img src="/greenplanet_copy/assets/images/icons/Layers.svg" alt="">
                            Quản lý sản phẩm
                        </a>
                    </li>
                    <li>
                        <a href="/greenplanet_copy/admin/promotions/list-san-pham.php">
                            <img src="/greenplanet_copy/assets/images/icons/Chrome.svg" alt="">
                            Quản lý khuyến mãi
                        </a>
                    </li>
                    <li>
                        <a href="/greenplanet_copy/admin/discounts/list-san-pham.php">
                            <img src="/greenplanet_copy/assets/images/icons/Vector.svg" alt="">
                            Quản lý giảm giá
                        </a>
                    </li>
                    <li>
                        <a href="/greenplanet_copy/admin/feedback/list-feed-back.php">
                            <img src="/greenplanet_copy/assets/images/icons/Eye.svg" alt="">
                            Phản hồi khách hàng
                        </a>
                    </li>


                    <li><a href="/greenplanet_copy/views/dangxuat.php">
                            <img src="/greenplanet_copy/assets/images/icons/Group 50.svg" alt="">
                            Đăng xuất</a></li>
                </ul>
            </nav>
        </aside>

        <div class="content">
            <header class="header">
                <div class="right-side">
                    <div class="text-user">
                        Xin chào, <?php echo $_SESSION['user']['hoten']?>
                    </div>
                    <div class="icon others">
                        <li class="dropdown">
                            <a href="/greenplanet_copy/admin/profile/ho-so.php" class="dropbtn"><i class="fa fa-user"></i></a>
                           
                        </li>
                    </div>
                </div>
            </header>
           