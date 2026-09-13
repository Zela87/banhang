<div class="contain-pro">
    <h2>Hồ sơ của tôi</h2>
    <div class="profile-container">
        <div class="profile-details">
            <div class="info-row">
                <label>Họ tên:</label>
                <?php if (empty($hoten)) : ?>
                    <a href="index.php?act=suahoso">Cập nhật</a>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($hoten); ?></p>
                <?php endif; ?>
            </div>
            <div class="info-row">
                <label>Email:</label>
                <?php if (empty($email)) : ?>
                    <a href="index.php?act=suahoso">Cập nhật</a>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($email); ?></p>
                <?php endif; ?>
            </div>
            <div class="info-row">
                <label>SDT:</label>
                <?php if (empty($sdt)) : ?>
                    <a href="index.php?act=suahoso">Cập nhật</a>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($sdt); ?></p>
                <?php endif; ?>
            </div>
            <div class="info-row">
                <label>Địa chỉ:</label>
                <?php if (empty($diachi)) : ?>
                    <a href="index.php?act=suahoso">Cập nhật</a>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($diachi); ?></p>
                <?php endif; ?>
            </div>
            <div class="info-row">
                <label>Giới tính:</label>
                <?php if (empty($gioitinh)) : ?>
                    <a href="index.php?act=suahoso">Cập nhật</a>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($gioitinh); ?></p>
                <?php endif; ?>
            </div>
            <div class="info-row">
                <label>Ngày sinh:</label>
                <?php if (empty($ngaysinh)) : ?>
                    <a href="index.php?act=suahoso">Cập nhật</a>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($ngaysinh); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-picture">
            <?php if (!empty($avatar)) : ?>
                <img src="../assets/images/<?php echo htmlspecialchars($avatar); ?>" alt="Profile Picture">
            <?php else : ?>
                <img src="../assets/images/user.jpg" alt="Profile Picture">
            <?php endif; ?>
            <p><?php echo htmlspecialchars($hoten); ?></p>
        </div>
    </div>

    <div class="profile-actions">
        <a href="index.php?act=suahoso">Cập nhật hồ sơ</a>
        <a href="index.php?act=lsdonhang">Lịch sử đơn hàng</a>
        <a href="index.php?act=baomat">Đổi mật khẩu</a>
    </div>
</div>