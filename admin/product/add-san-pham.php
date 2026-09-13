<?php
require_once __DIR__ . '/../quanlysp.php';
require_once __DIR__ . '../../../models_admin/product.php';
require_once __DIR__ . '../../../partials/header-admin.php';


$pdo = connect_db();
$controller = new ProductController($pdo);
try {
    $categories = $controller->getCategories();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->add();
    }
} finally {
    $pdo = disconnect_db($pdo);
}
?>

<div class="head-page">
    <h2>Quản lý sản phẩm</h2>

</div>
<main class="main-content">
    <div class="">
        <div class="head-form">

            <h1 class="">Quản lý sản phẩm / Thêm sản phẩm</h1>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product-name" class="">Tên sản phẩm:</label>
                <input type="text" id="product-name" name="name" class="" required />
            </div>

            <div class="form-group">
                <label for="category_id" class="">Danh mục sản phẩm:</label>
                <select id="categorySelect" name="category_id">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= htmlspecialchars($category['CategoryID']) ?>">
                            <?= htmlspecialchars($category['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="product-price" class="">Giá bán:</label>
                <input type="number" id="product-price" class="" name="Price" required />
            </div>

            <div class="form-group">
                <label for="product-quantity" class="">Số lượng:</label>
                <input type="number" id="product-quantity" class="" name="StockQuantity" required />
            </div>

            <div class="form-group">
                <label for="product-description" class="">Mô tả sản phẩm:</label>
                <textarea id="product-description" name="Description" class="" rows="8" required></textarea>
            </div>
            <div class="form-group">
                <label for="Content" class="">Mô tả chi tiết:</label>
                <textarea id="Content" name="Content" class="" rows="8"></textarea>
            </div>
            <div class="form-group">
                <label for="product-image" class="">Hình ảnh sản phẩm:</label>
                <div class="image-upload">
                    <input type="file" id="product-image" class="file-input" accept="image/*" name="uploaded_file"/>
                    <label for="product-image" class="file-label" id="iconUpload">
                        <img src="/greenplanet_copy/assets/images/icons/Upload.png" alt="Upload Icon" class="upload-icon" />
                    </label>
                    <img id="image-preview" alt="Image Preview" class="image-preview" style="display: none;" />
                </div>
                <button type="button" id="changeImageButton" class="btn-change-image" style="display: none;">Thay đổi ảnh</button>
            </div>

            <div class="form-group">
                <div class="flex-0"></div>
                <button type="submit" class="btn-succes">Lưu</button>

            </div>
        </form>
    </div>
</main>

</div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById('product-image');
    const imgMain = document.getElementById('imgMain');
    const preview = document.getElementById('image-preview');
    const iconUpload = document.getElementById('iconUpload');

    // Nếu đã có ảnh từ trước (ảnh cũ), hiển thị nó
    if (imgMain) {
        imgMain.style.display = 'block';
    }

    // Khi người dùng chọn một ảnh
    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                // Nếu đã có ảnh cũ, ẩn nó đi
                if (imgMain) {
                    imgMain.style.display = 'none';
                }

                // Ẩn icon tải lên và hiển thị ảnh preview
                if (iconUpload) {
                    iconUpload.style.display = 'none';
                }

                // Hiển thị ảnh preview mới
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(file);
        }
    });

    // Khi nhấp vào ảnh preview, cho phép chọn lại ảnh
    preview.addEventListener('click', function () {
        fileInput.click();
    });

    // Nếu ảnh cũ được nhấp, cho phép chọn lại ảnh
    if (imgMain) {
        imgMain.addEventListener('click', function () {
            fileInput.click();
        });
    }
});

</script>

</body>

</html>