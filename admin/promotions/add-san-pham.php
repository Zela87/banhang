<?php
 require_once __DIR__ . '/../quanlykm.php';
 require_once __DIR__ . '../../../models_admin/promotions.php';
 require_once __DIR__ . '../../../partials/header-admin.php';
$pdo = connect_db();
$controller = new PromotionsController($pdo);
$categories = $controller->getTypeAll();
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->add(); 
    }
} finally {
    $pdo = disconnect_db($pdo);
}
?>
<div class="head-page">
    <h2>Quản lý khuyến mãi</h2>

</div>
<main class="main-content">
    <div class="khuyen-mai">
        <div class="head-form">

            <h1 class="">Quản lý khuyến mãi / Thêm khuyến mãi</h1>
        </div>
        <div class="form">
            <div class="style-khuyen-mai">
                <label for="product-name-main" class="">Tên khuyến mãi</label>
                <input type="text" id="product-name-main" class="" required placeholder="Nhập tên khuyến mãi" />

            </div>
        </div>

        <form id="promotion-form" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="Title" id="hidden-title" required />
            <div class="form-group">
                <label for="PromotionTypeID" class="">Loại khuyến mãi</label>
                <select id="PromotionTypeID" name="PromotionTypeID">
                    <option value="">-- Chọn loại --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['PromotionTypeID']) ?>">
                            <?= htmlspecialchars($category['TypeName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
             
            </div>
            <div class="form-group">
                <label for="PromoCode" class="">Mã Code</label>
                <input type="text" id="PromoCode" name="PromoCode" class="" required />
            </div>
            <div class="form-group">
                <label for="Description" class="">Mô tả</label>
                <input type="text" id="Description" name="Description" class="" required />
            </div>
            <div class="form-group">
                <label for="DiscountPercentage" class="">Giá trị giảm</label>
                <input type="number" id="DiscountPercentage" name="DiscountPercentage" class="" />
            </div>
            <div class="form-group">
                <label for="DiscountAmount" class="">Số tiền giảm</label>
                <input type="number" id="DiscountAmount" name="DiscountAmount" class="" />
            </div>
            <div class="form-group">
                <label for="MinOrderValue" class="">Giá trị đơn hàng tối thiểu</label>
                <input type="number" id="MinOrderValue" name="MinOrderValue" class=""  />
            </div>
            <div class="form-group">
                <label for="UsageLimit" class="">Số lượng dùng tối thiểu</label>
                <input type="number" id="UsageLimit" name="UsageLimit" class="" required />
            </div>
            <div class="form-group">
                <label for="StartDate" class="">Ngày bắt đầu</label>
                <input type="date" id="StartDate" name="StartDate" class="" required />
            </div>
            <div class="form-group">
                <label for="EndDate" class="">Ngày kết thúc</label>
                <input type="date" id="EndDate" name="EndDate" class="" required />
            </div>
            <!-- <div class="form-group">
                <label for="product-image" class="">Hình ảnh khuyến mãi:</label>
                <div class="image-upload">
                    <input type="file" id="product-image" class="file-input" accept="image/*" name="uploaded_file" required/>
                    <label for="product-image" class="file-label" id="iconUpload">
                        <img src="/../assets/images/Upload.png" alt="Upload Icon" class="upload-icon" />
                    </label>
                    <img id="image-preview" alt="Image Preview" class="image-preview"
                        style="display: none;" />
                </div>
            </div> -->
            <div class="form-group">
                <div class="flex-0"></div>
                <button type="submit" class="btn-succes">Lưu</button>

            </div>

        </form>
    </div>
    <script>
        document.getElementById('promotion-form').addEventListener('submit', function(event) {
            var promotionName = document.getElementById('product-name-main').value;
            if (promotionName.trim() === '') {
                alert('Tên khuyến mãi không được để trống.');
                event.preventDefault();
            } else {
                document.getElementById('hidden-title').value = promotionName;
            }
        });
        // document.getElementById('product-image').addEventListener('change', function(event) {
        // const file = event.target.files[0];
        // const preview = document.getElementById('image-preview');
        // const iconUpload = document.getElementById('iconUpload');
        // const imageUploadDiv = document.querySelector('.image-upload');

        // if (file) {
        //     const reader = new FileReader();

        //     reader.onload = function(e) {
        //         iconUpload.style.display = 'none';

        //         if (imageUploadDiv) {
        //             imageUploadDiv.style.height = '200px';
        //             imageUploadDiv.style.padding = '0';

        //         }
        //         preview.src = e.target.result;
        //         preview.style.display = 'block';

        //     }

        //     reader.readAsDataURL(file);
        // }
    // });
    </script>
</main>

</div>
</div>
</body>

</html>