<div class="container">
    <div class="breadcrumb">
        <a href="index.php?act=hoso">Hồ sơ</a> /
        <span>Cập nhật hồ sơ</span>
    </div>
</div>

<div class="contain-pro">
    <h2>Cập nhật hồ sơ</h2>
    <form action="index.php?act=suahoso" method="POST" enctype="multipart/form-data">
        <div class="profile-container">
            <div class="profile-details">
                <div class="info-row">
                    <label for="hoten">Họ tên</label>
                    <input type="text" id="hoten" name="hoten" value="<?php echo htmlspecialchars($hoten); ?>">
                </div>
                <div class="info-row">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="info-row">
                    <label for="sdt">SDT</label>
                    <input type="tel" id="sdt" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>">
                </div>
                <div class="info-row">
                    <label for="diachi">Địa chỉ</label>
                    <input type="text" id="diachi" name="diachi" value="<?php echo htmlspecialchars($diachi); ?>">
                </div>
                <div class="info-row gender-row">
                    <label>Giới tính</label>
                    <input type="radio" id="male" name="gender" value="Nam" <?php if ($gioitinh == 'Nam') echo 'checked'; ?>>
                    <label for="male">Nam</label>
                    <input type="radio" id="female" name="gender" value="Nữ" <?php if ($gioitinh == 'Nữ') echo 'checked'; ?>>
                    <label for="female">Nữ</label>
                    <input type="radio" id="other" name="gender" value="Khác" <?php if ($gioitinh == 'Khác') echo 'checked'; ?>>
                    <label for="other">Khác</label>
                </div>
                <div class="info-row">
                    <label for="birthday">Ngày sinh</label>
                    <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($ngaysinh); ?>">
                </div>
            </div>
            <div class="profile-picture">
                <img id="profileImage" src="../assets/images/<?php echo htmlspecialchars($avatar ?: 'user.jpg'); ?>" alt="Profile Picture">
                <label for="Avatar">Chọn Ảnh</label>
                <input type="file" id="Avatar" name="Avatar" accept="image/*" onchange="previewImage(event)">
                <p>Dung lượng tối đa 1 MB<br>Định dạng: PNG, JPEG, JPG</p>
            </div>
        </div>

        <div class="profile-actions">
            <input type="submit" value="Cập nhật" name="capnhat">
        </div>
    </form>
</div>