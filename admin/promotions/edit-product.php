<?php
 require_once __DIR__ . '/../quanlykm.php';
 require_once __DIR__ . '../../../models_admin/promotions.php';
 require_once __DIR__ . '../../../partials/header-admin.php';
 
$pdo = connect_db();
$controller = new PromotionsController($pdo);
$categories = $controller->getTypeAll();

try {
    if (isset($_GET['id'])) {
        $promotionsId = $_GET['id'];
        $promotions = $controller->edit($promotionsId);
    } else {
        header('Location: /greenplanet_copy/admin/promotions/list-san-pham.php');
        exit;
    }
} finally {
    $pdo = disconnect_db($pdo);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = connect_db();
        $controller = new PromotionsController($pdo);
        $promotionsId = $_GET['id'];
        $controller->edit($promotionsId);
    } finally {
        $pdo = disconnect_db($pdo);
    }
}
?>
<div class="head-page">
    <h2>Quản lý khuyến mãi</h2>

</div>
<main class="main-content">
    <div class="khuyen-mai">
        <div class="head-form">

            <h1 class="">Quản lý khuyến mãi / Sửa khuyến mãi</h1>
        </div>
        <div class="form">
            <div class="style-khuyen-mai">
                <label for="product-name-main" class="">Tên khuyến mãi</label>
                <input type="text" id="product-name-main" class="" value="<?php echo $promotions['Title']; ?>" required placeholder="Nhập tên khuyến mãi" />

            </div>
        </div>
        <form id="promotion-form" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="Title" id="hidden-title" required />
            <div class="form-group">
                <label for="PromotionTypeID" class="">Loại khuyến mãi</label>
                <select id="PromotionTypeID" name="PromotionTypeID">
                    <option value="">-- Chọn loại --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['PromotionTypeID']) ?>"
                            <?= $category['PromotionTypeID'] == $promotions['PromotionTypeID'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['TypeName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>


            </div>

            <div class="form-group">
                <label for="PromoCode" class="">Mã Code</label>
                <input type="text" id="PromoCode" name="PromoCode" class="" required value="<?= htmlspecialchars($promotions['PromoCode']) ?>"/>
            </div>
            <div class="form-group">
                <label for="Description" class="">Mô tả</label>
                <input type="text" id="Description" name="Description" class="" value="<?= htmlspecialchars($promotions['Description']) ?>"/>
            </div>
            <div class="form-group">
                <label for="DiscountPercentage" class="">Giá trị giảm</label>
                <input type="number" id="DiscountPercentage" name="DiscountPercentage" class="" value="<?= htmlspecialchars( intval($promotions['DiscountPercentage']))  ?>"/>
            </div>
            <div class="form-group">
                <label for="DiscountAmount" class="">Số tiền giảm</label>
                <input type="number" id="DiscountAmount" name="DiscountAmount" class="" value="<?= htmlspecialchars(intval($promotions['DiscountAmount'])) ?>"/>
            </div>
            <div class="form-group">
                <label for="MinOrderValue" class="">Giá trị đơn hàng tối thiểu</label>
                <input type="number" id="MinOrderValue" name="MinOrderValue" class="" value="<?= htmlspecialchars(intval($promotions['MinOrderValue'])) ?>"/>
            </div>
            <div class="form-group">
                <label for="UsageLimit" class="">Số lượng dùng tối thiểu</label>
                <input type="number" id="UsageLimit" name="UsageLimit" class="" required value="<?= htmlspecialchars($promotions['UsageLimit']) ?>"/>
            </div>
            <div class="form-group">
                <label for="StartDate" class="">Ngày bắt đầu</label>
                <input type="date" id="StartDate" name="StartDate" class="" required value="<?= htmlspecialchars($promotions['StartDate']) ?>"/>
            </div>
            <div class="form-group">
                <label for="EndDate" class="">Ngày kết thúc</label>
                <input type="date" id="EndDate" name="EndDate" class="" required value="<?= htmlspecialchars($promotions['EndDate']) ?>"/>
            </div>
            <!-- <div class="form-group">
                <label for="product-image" class="">Hình ảnh khuyến mãi:</label>
                <div class="image-upload" id="onChangeImage" style="cursor: pointer;">
                    <input type="file" id="product-image" class="file-input" accept="image/*" name="uploaded_file" />
                    <?php if (!empty($promotions['ImageURL'])): ?>
                        <img src="/<?= htmlspecialchars($promotions['ImageURL']) ?>" alt="<?= htmlspecialchars($promotions['Title']) ?>" id="imgMain" />
                    <?php else: ?>
                        <label for="product-image" class="file-label" id="iconUpload">
                            <img src="/../assets/images/Upload.png" alt="Upload Icon" class="upload-icon" />
                        </label>
                    <?php endif; ?>
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
        // document.addEventListener("DOMContentLoaded", function() {
        //     const imgMain = document.getElementById('imgMain');
        //     const imageUploadDiv = document.querySelector('.image-upload');
        //     document.getElementById('onChangeImage').onclick = function() {
        //         document.getElementById('product-image').click();
        //     };
        //     if (imageUploadDiv && imgMain) {
        //         imageUploadDiv.style.height = '200px';
        //         imageUploadDiv.style.padding = '0';
        //     }
        // });
        // document.getElementById('product-image').addEventListener('change', function(event) {
        //     const file = event.target.files[0];
        //     const preview = document.getElementById('image-preview');
        //     const imgMain = document.getElementById('imgMain');
        //     const iconUpload = document.getElementById('iconUpload');
        //     const imageUploadDiv = document.querySelector('.image-upload');

        //     if (file) {
        //         const reader = new FileReader();

        //         reader.onload = function(e) {
        //             if (iconUpload) {
        //                 iconUpload.style.display = 'none';
        //             }
        //             if (imageUploadDiv) {
        //                 imageUploadDiv.style.height = '200px';
        //                 imageUploadDiv.style.padding = '0';

        //             }
        //             if (imgMain) {
        //                 imgMain.style.display = 'none';

        //             }
        //             preview.src = e.target.result;
        //             preview.style.display = 'block';

        //         }

        //         reader.readAsDataURL(file);
        //     }
        // });
    </script>
</main>

</div>
</div>
</body>

</html>