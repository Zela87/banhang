<?php
 require_once __DIR__ . '/../quanlykm.php';
 require_once __DIR__ . '../../../models_admin/promotions.php';
 require_once __DIR__ . '../../../partials/header-admin.php';
$pdo = connect_db();
$controller = new PromotionsController($pdo);
try {
    $promotions  = $controller->index();
} finally {
    $pdo = disconnect_db($pdo);
}
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $pdo = connect_db();
        $controller = new PromotionsController($pdo);

        $productId = $_GET['id'];
        $controller->delete($productId);
    } finally {
        $pdo = disconnect_db($pdo);
    }
}
function format_cash($price)
{
    return str_replace(",", ".", number_format($price));
}
?>
<div class="head-page">
    <h2>Quản lý khuyến mãi</h2>

</div>
<main class="main-content">
    <div class="d-flex btn-add ">
        <div>
        <a href="/greenplanet_copy/admin/promotions/add-san-pham.php" class="btn-succes">Thêm sản phẩm</a>
        </div>
    </div>
    <table class="styled-table">

        <tr>
            <th>Tên </th>
            <th>Giảm giá </th>
            <th>Giới hạn sử dụng</th>
            <th>Đơn tối thiểu</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Sửa</th>
            <th>Xóa</th>
        </tr>

        <?php foreach ($promotions as $promotion): ?>
            <tr>

                <td><?= $promotion['Title'] ?></td>
                <td>
                    <?php
                    if (!empty($promotion['DiscountPercentage']) && intval($promotion['DiscountPercentage']) != 0) {
                        echo intval($promotion['DiscountPercentage']) . '%';
                    } elseif (!empty($promotion['DiscountAmount']) && intval($promotion['DiscountAmount']) != 0) {
                        echo format_cash($promotion['DiscountAmount']) . 'đ';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td><?= $promotion['UsageLimit'] ?></td>
                <td><?= format_cash($promotion['MinOrderValue']) ?>đ</td>
                <td><?= (new DateTime($promotion['StartDate']))->format('d/m/Y') ?></td>
                <td><?= (new DateTime($promotion['EndDate']))->format('d/m/Y') ?></td>


                <td>
                    <a title="Sửa" href="/greenplanet_copy/admin/promotions/edit-product.php?id=<?= $promotion['PromotionID'] ?>">
                        <img src="/greenplanet_copy/assets/images/icons/edit.svg" title="Sửa" />
                    </a>
                </td>
                <td>
                    <a title="Xóa" href="?action=delete&id=<?= $promotion['PromotionID'] ?>" onclick="return confirm('Chắc chắn xóa?');">
                        <img src="/greenplanet_copy/assets/images/icons/delete.svg" title="Xóa" />
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
</div>
</div>

</body>

</html>