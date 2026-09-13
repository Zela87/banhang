<div class="container">
    <div class="breadcrumb">
        <a href="index.php?act=hoso">Hồ sơ</a> /
        <span>Cập nhật mật khẩu</span>
    </div>
</div>

<div class="contain_form">
    <h2>Đổi mật khẩu</h2>
    <div class="box_form">


        <form action="index.php?act=baomat" method="POST">
            <label for="current_password">Mật khẩu hiện tại</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">Mật khẩu mới*</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Nhập lại mật khẩu mới*</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <?php if (!empty($error)) : ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <p style="color: green;"><?php echo $success; ?></p>
            <?php endif; ?>

            <input type="submit" value="Xác nhận">

            <p>* Độ mạnh của mật khẩu:<br>
                Hãy sử dụng ít nhất 8 ký tự. Không sử dụng mật khẩu cho trang web khác hoặc nội dung quá rõ ràng.</p>
        </form>
    </div>
</div>