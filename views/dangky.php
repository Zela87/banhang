<?php
$error_register = dangky(); // Gọi hàm để xử lý đăng ký và lấy thông báo lỗi nếu có
?>
<div class="contain_form">
        <h2>Đăng ký tài khoản</h2>
        <div class="box_form">
            <form action="index.php?act=dangky" method="post">
                <label for="hoten">HỌ TÊN</label>
                <input type="text" name="hoten" id="hoten">
                <label for="email">EMAIL</label>
                <input type="email" name="email" id="email">
                <label for="matkhau">MẬT KHẨU*</label>
                <input type="password" name="pw" id="pw">
                <label for="matkhau">NHẬP LẠI MẬT KHẨU*</label>
                <input type="password" name="re_pw" id="re_pw">
                <?php if (!empty($error_register)): ?>
        <p style="color:red;"><?php echo $error_register; ?></p>
    <?php endif; ?>
                <input type="submit" name="dangky" value="Tạo tài khoản">
                <p>* Độ mạnh của mật khẩu:<br>
                    Hãy sử dụng ít nhất 8 ký tự. Không sử dụng mật khẩu cho trang web khác hoặc nội dung quá rõ ràng</p>
            </form>
        </div>
    </div>

