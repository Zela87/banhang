<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo isset($title) ? $title . " - Green Planet" : "Green Planet"; ?></title>
    <link rel="stylesheet" href="../assets/css/header-footer.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="../assets/images/logo.svg" alt="">
                <div class="name">
                    <p>GREEN PLANET</p>
                </div>
            </div>
            <div class="others">
    <!-- Tìm kiếm (không cần class active ở đây) -->
    <li class="search">
        <form action="index.php" method="GET">
            <input type="search" name="search" id="search-input" placeholder="Tìm kiếm...">
            <input type="hidden" name="act" value="search">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </li>

    <!-- Dropdown tài khoản -->
    <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn <?php echo (isset($_GET['act']) && ($_GET['act'] == 'dangnhap' || $_GET['act'] == 'dangky' || $_GET['act'] == 'hoso')) ? 'active' : ''; ?>">
            <i class="fa fa-user"></i>
        </a>
        <div class="dropdown-content">
            <?php if (isset($_SESSION['user'])) : ?>
                <a href="index.php?act=hoso">Xin chào, <?php echo htmlspecialchars($_SESSION['user']['hoten']); ?></a>
                <a href="index.php?act=dangxuat">Đăng xuất</a>
            <?php else : ?>
                <a href="index.php?act=dangnhap">Đăng nhập</a>
                <a href="index.php?act=dangky">Đăng ký</a>
            <?php endif; ?>
        </div>
    </li>

    <a href="index.php?act=giohang" class="cart-icon <?php echo (isset($_GET['act']) && $_GET['act'] == 'giohang') ? 'active' : ''; ?>">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        <span id="cart-count" class="cart-count"><?= $cart_count; ?></span>
    </a>
</div>

        </div>
    </header>
    <nav>
    <div class="container">
        <div class="menu">
            <a href="index.php" class="<?php echo (!isset($_GET['act']) || $_GET['act'] == 'trangchu') ? 'active' : ''; ?>">Trang chủ</a>
            <a href="index.php?act=sanpham" class="<?php echo (isset($_GET['act']) && $_GET['act'] == 'sanpham') ? 'active' : ''; ?>">Sản phẩm</a>
            <a href="index.php?act=khuyenmai" class="<?php echo (isset($_GET['act']) && $_GET['act'] == 'khuyenmai') ? 'active' : ''; ?>">Khuyến mại</a>
            <a href="index.php?act=gioithieu" class="<?php echo (isset($_GET['act']) && $_GET['act'] == 'gioithieu') ? 'active' : ''; ?>">Giới thiệu</a>
            <a href="index.php?act=lienhe" class="<?php echo (isset($_GET['act']) && $_GET['act'] == 'lienhe') ? 'active' : ''; ?>">Liên hệ</a>
        </div>
    </div>
</nav>
