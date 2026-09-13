<?php
require_once __DIR__ . '/../quanlygiamgia.php';
require_once __DIR__ . '../../../models_admin/discounts.php';
require_once __DIR__ . '../../../partials/header-admin.php';


$pdo = connect_db();
$controller = new DiscountsController($pdo);
try {
    $discounts  = $controller->index();
} finally {
    $pdo = disconnect_db($pdo);
}
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $pdo = connect_db();
        $controller = new DiscountsController($pdo);

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
    <h2>Quản lý giảm giá</h2>

</div>
<main class="main-content">
    <div class="d-flex btn-add ">
        <a href="/greenplanet_copy/admin/discounts/add-san-pham.php" class="btn-succes">Thêm sản phẩm</a>
        <div class="others">
            <li class="search">
                <input type="search" name="" id=""><i class="fa fa-search"></i>
            </li>
        </div>

    </div>

    <table class="styled-table">
        <tr>

            <th>Tên</th>
            <th>Giảm giá</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Sửa</th>
            <th>Xóa</th>
        </tr>

        <?php foreach ($discounts as $discount) : ?>
            <tr>

                <td><?= $discount['ProductName'] ?></td>

                <td>
                    <?php
                    if (!empty($discount['DiscountPercentage'])) {
                        echo intval($discount['DiscountPercentage']) . '%';
                    } elseif (!empty($discount['DiscountAmount'])) {
                        echo format_cash((int)$discount['DiscountAmount']) . 'đ';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td><?= (new DateTime($discount['StartDate']))->format('d/m/Y') ?></td>
                <td><?= (new DateTime($discount['EndDate']))->format('d/m/Y') ?></td>


                <td>
                    <a title="Sửa" href="/greenplanet_copy/admin/discounts/edit-product.php?id=<?= $discount['DiscountID'] ?>">
                        <img src="/greenplanet_copy/assets/images/icons/edit.svg" title="Sửa" />
                    </a>
                </td>
                <td>
                    <a title="Xóa" href="?action=delete&id=<?= $discount['DiscountID'] ?>" onclick="return confirm('Chắc chắn xóa?');">
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