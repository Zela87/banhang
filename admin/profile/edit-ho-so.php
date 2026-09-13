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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = connect_db();
        $controller = new UserController($pdo);
        $controller->update($id);
    } finally {
        $pdo = disconnect_db($pdo);
    }
}
?>

<div class="head-page">
    <h2>Quản lý tài khoản</h2>

</div>
<main class="main-content edit-ho-so-admin">
    <div class="head-form">
        <h1 class="">Cập nhật hồ sơ </h1>
    </div>
    <div class="content-ho-so">
        <form class="form-admin" id="adminForm" action="" method="POST" enctype="multipart/form-data">
            <div class="left-side">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <?php if (!empty($user) && isset($user['hoten'])) : ?>
                        <input type="text" id="hoten" name="hoten" class="" value="<?php echo htmlspecialchars($user['hoten']); ?>" required />
                    <?php else : ?>
                        <input type="text" id="hoten" name="hoten" class="" value="" required />
                    <?php endif; ?>

                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" id="email" name="email" class="" value="<?php echo $user['email']; ?>" required />


                </div>
                <div class="form-group">
                    <label>SĐT</label>
                    <input type="text" id="sdt" name="sdt" class="" value="<?php echo $user['sdt']; ?>" required />


                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" id="diachi" name="diachi" class="" value="<?php echo $user['diachi']; ?>" required />


                </div>
                <div class="form-group">
                    <label>Giới tính</label>
                    <div>
                        <input type="radio" id="nam" name="gioitinh" value="Nam" <?php if ($user['gioitinh'] === 'Nam') echo 'checked'; ?>>
                        <label for="nam">Nam</label>
                    </div>
                    <div>
                        <input type="radio" id="nu" name="gioitinh" value="Nữ" <?php if ($user['gioitinh'] === 'Nữ') echo 'checked'; ?>>
                        <label for="nu">Nữ</label>
                    </div>
                    <div>
                        <input type="radio" id="khac" name="gioitinh" value="Khác" <?php if ($user['gioitinh'] === 'Khác') echo 'checked'; ?>>
                        <label for="khac">Khác</label>
                    </div>

                </div>

                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" id="ngaysinh" name="ngaysinh" class="" value="<?php echo $user['ngaysinh']; ?>" required />

                </div>

            </div>
            <div class="right-side-admin">
                <div class="img-ava">
                <img id="previewImg" src="<?php echo htmlspecialchars(!empty($_SESSION['user']['Avatar']) ? $_SESSION['user']['Avatar'] : '/greenplanet_copy/assets/images/user.jpg'); ?>" alt="Profile Picture">
                </div>
                <div class="name-admin" style="cursor:pointer" onclick="document.getElementById('avatarInput').click()">
                    Chọn ảnh
                    <input type="file" id="avatarInput" name="uploaded_file" style="display: none;" accept="image/*" onchange="previewImage(event)" />
                </div>
                <div class="text-main">
                    Dung lượng tối đa 1 MB <br>
                    Định dạng: PNG, JPEG,...
                </div>
            </div>


        </form>
        <div class="btn-form">
            <a href="javascript:void(0)" onclick="submitForm()" class="btn-succes">Cập nhật</button>

        </div>
    </div>


</main>
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('previewImg');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    function submitForm() {
        document.getElementById('adminForm').submit();
    }
</script>
</div>
</div>

</body>

</html>