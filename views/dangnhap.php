<?php
$error_login = dangnhap();
?>
<div class="contain_form">
    <h2>Đăng nhập tài khoản</h2>
    <div class="box_form">
        <form action="index.php?act=dangnhap" method="post">
            <label for="email">EMAIL</label>
            <input type="email" name="email" id="email">
            <label for="matkhau">MẬT KHẨU</label>
            <input type="password" name="pw" id="pw">
            <?php if (!empty($error_login)): ?>
        <p style="color:red;"><?php echo $error_login; ?></p>
    <?php endif; ?>
            <input type="submit" name="dangnhap" value="ĐĂNG NHẬP">
            <a href="index.php?act=dangky" class="ac">Tạo tài khoản mới</a>
        </form>
    </div>
</div>