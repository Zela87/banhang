<?php
require_once __DIR__ . '/../quanlysp.php';
require_once __DIR__ . '../../../models_admin/product.php';
require_once __DIR__ . '../../../partials/header-admin.php';


$pdo = connect_db();
$controller = new ProductController($pdo);
try {
    if (isset($_GET['id'])) {
        $productId = $_GET['id'];
        $product = $controller->edit($productId);
        $categories = $controller->getCategories();
    } else {
        header('Location: /greenplanet_copy/admin/product/list-san-pham.php');
        exit;
    }
} finally {
    $pdo = disconnect_db($pdo);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = connect_db();
        $controller = new ProductController($pdo);
        $productId = $_GET['id'];
        $controller->edit($productId);
    } finally {
        $pdo = disconnect_db($pdo);
    }
}
?>
<div class="head-page">
    <h2>Quản lý sản phẩm</h2>

</div>
<main class="main-content">
    <div class="">
        <div class="head-form">

            <h1 class="">Quản lý sản phẩm / Sửa sản phẩm</h1>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product-name" class="">Tên sản phẩm:</label>
                <input type="text" id="product-name" name="name" class="" value="<?php echo $product['Name']; ?>" />
            </div>

            <div class="form-group">
                <label for="category_id" class="">Danh mục sản phẩm:</label>
                <select id="categorySelect" name="category_id">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= htmlspecialchars($category['CategoryID']) ?>" <?= $category['CategoryID'] == $product['CategoryID'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="product-price" class="">Giá bán:</label>
                <input type="number" id="product-price" class="" name="Price" value="<?php echo htmlspecialchars((int)$product['Price']); ?>" />
            </div>

            <div class="form-group">
                <label for="product-quantity" class="">Số lượng:</label>
                <input type="number" id="product-quantity" class="" name="StockQuantity" value="<?php echo htmlspecialchars($product['StockQuantity']); ?>" />
            </div>

            <div class="form-group">
                <label for="product-description" class="">Mô tả sản phẩm:</label>
                <textarea id="product-description" name="Description" class="" rows="8"><?php echo $product['Description']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="Content" class="">Mô tả chi tiết:</label>
                <textarea id="Content" name="Content" class="" rows="8"><?php echo $product['Content']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="product-image" class="">Hình ảnh sản phẩm:</label>
                <div class="image-upload">
                    <input type="file" id="product-image" class="file-input" accept="'image/jpeg', 'image/png', 'image/gif', 'image/webp'" name="uploaded_file" style="display: none;" />
                    <?php if (!empty($product['image_url'])) : ?>
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" id="imgMain" />
                    <?php else : ?>
                        <label for="product-image" class="file-label" id="iconUpload">
                            <img src="/greenplanet_copy/assets/images/icons/Upload.png" alt="Upload Icon" class="upload-icon" />
                        </label>
                    <?php endif; ?>
                    <img id="image-preview" alt="Image Preview" class="image-preview" style="display: none;" />
                </div>
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

    if (imgMain) {
        imgMain.style.display = 'block';
    }

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                if (imgMain) {
                    imgMain.style.display = 'none';
                }

                if (iconUpload) {
                    iconUpload.style.display = 'none';
                }

                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(file);
        }
    });

    preview.addEventListener('click', function () {
        fileInput.click();
    });

    if (imgMain) {
        imgMain.addEventListener('click', function () {
            fileInput.click();
        });
    }
});

</script>

</body>

</html>