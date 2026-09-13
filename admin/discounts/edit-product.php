<?php
require_once __DIR__ . '/../quanlygiamgia.php';
require_once __DIR__ . '../../../models_admin/discounts.php';
require_once __DIR__ . '../../../partials/header-admin.php';

$pdo = connect_db();
$controller = new DiscountsController($pdo);
try {
    if (isset($_GET['id'])) {
        $discountsId = $_GET['id'];
        $discounts = $controller->edit($discountsId);
        $products = $controller->getAllProducts();
    } else {
        header('Location: /greenplanet_copy/admin/discounts/list-san-pham.php');
        exit;
    }
} finally {
    $pdo = disconnect_db($pdo);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = connect_db();
        $controller = new DiscountsController($pdo);
        $discountsId = $_GET['id'];
        $controller->edit($discountsId);
    } finally {
        $pdo = disconnect_db($pdo);
    }
}
?>

<div class="head-page">
    <h2>Quản lý giảm giá</h2>

</div>
<main class="main-content">
    <div class="khuyen-mai">
        <div class="head-form">

            <h1 class="">Sửa giảm giá</h1>
        </div>
        <div class="form">
            <div class="style-khuyen-mai">
                <label for="product-id-main" class="">Tên sản phẩm </label>
                <select id="product-id-main" onchange="fillHiddenInput()">
                    <option value="" disabled selected>Chọn sản phẩm</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['ProductID']) ?>"
                            <?= $discounts['ProductID'] == $product['ProductID'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($product['ProductName']) ?>
                        </option>

                    <?php endforeach; ?>
                </select>

            </div>
        </div>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="ProductID" id="hidden-product-id" value="<?= !empty($discounts) ? htmlspecialchars($discounts['ProductID']) : '' ?>" />
            <div class="form-group">
                <label for="DiscountPercentage" class="">Giá trị giảm (theo phần trăm)</label>
                <input type="number" id="DiscountPercentage" name="DiscountPercentage" value="<?php echo intval($discounts['DiscountPercentage']); ?>" class=""/>
            </div>
            <div class="form-group">
                <label for="DiscountAmount" class="">Giá trị giảm (theo đơn vị)</label>
                <input type="number" id="DiscountAmount" name="DiscountAmount" value="<?php echo $discounts['DiscountAmount']; ?>" class="" />
            </div>
            <div class="form-group">
                <label for="StartDate" class="">Ngày bắt đầu</label>
                <input type="date" id="StartDate" name="StartDate" class="" value="<?php echo $discounts['StartDate']; ?>" required />
            </div>
            <div class="form-group">
                <label for="EndDate" class="">Ngày kết thúc</label>
                <input type="date" id="EndDate" name="EndDate" class="" value="<?php echo $discounts['EndDate']; ?>" required />
            </div>
            <div class="form-group">
                <div class="flex-0"></div>
                <button type="submit" class="btn-succes">Lưu</button>

            </div>

        </form>
    </div>
</main>
<script>
    function fillHiddenInput() {
        var selectElement = document.getElementById("product-id-main");
        var hiddenInput = document.getElementById("hidden-product-id");

        hiddenInput.value = selectElement.value;
    }

    function validateForm() {
        const selectElement = document.getElementById('product-id-main');
        const hiddenInput = document.getElementById('hidden-product-id');

        if (selectElement.value === "") {
            alert("Vui lòng chọn sản phẩm trước khi gửi!");
            hiddenInput.value = "";
            return false;
        } else {
            hiddenInput.value = selectElement.value;
            return true;
        }
    }
</script>
</div>
</div>
</body>

</html>