<?php
require_once __DIR__ . '/../quanlysp.php';
require_once __DIR__ . '../../../models_admin/product.php';
require_once __DIR__ . '../../../partials/header-admin.php';
require_once __DIR__ . '../../../partials/global.php';


$pdo = connect_db();
$controller = new ProductController($pdo);
$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    if (!empty($search)) {
        $products = $controller->searchProductsByName($search);
    } else {
        $products = $controller->index();
    }
} finally {
    $pdo = disconnect_db($pdo);
}
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $pdo = connect_db();
        $controller = new ProductController($pdo);

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
    <h2>Quản lý sản phẩm</h2>

</div>
<main class="main-content">
    <div class="d-flex btn-add ">
        <a href="/greenplanet_copy/admin/product/add-san-pham.php" class="btn-succes">Thêm sản phẩm</a>
        <div class="others">
            <li class="search">
                <input type="search" id="search-input" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <i class="fa fa-search" id="search-icon" style="cursor: pointer;"></i>
            </li>

        </div>

    </div>
    <table class="styled-table">
        <tr>
            <th>Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng</th>
            <th>Giá</th>
            <th>Danh mục</th>
            <th>Sửa</th>
            <th>Xóa</th>
        </tr>

        <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?=  htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" style="max-width: 100px; max-height: 100px;" />
                    <?php else: ?>
                        <span>Chưa có ảnh</span>
                    <?php endif; ?>
                </td>
                <td><?= $product['Name'] ?></td>
                <td><?= $product['StockQuantity'] ?></td>
                <td><?= format_cash((int)$product['Price'])  ?>đ</td>
                <td><?= isset($product['category_name']) ? $product['category_name'] : 'Chưa có danh mục' ?></td>

                <td>
                    <a title="Sửa" href="/greenplanet_copy/admin/product/edit-product.php?id=<?= $product['ProductID'] ?>">
                        <img src="/greenplanet_copy/assets/images/icons/edit.svg" title="Sửa" />
                    </a>
                </td>
                <td>
                    <a title="Xóa" href="?action=delete&id=<?= $product['ProductID'] ?>" onclick="return confirm('Chắc chắn xóa sản phẩm?');">
                        <img src="/greenplanet_copy/assets/images/icons/delete.svg" title="Xóa" />
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
<script>
    function performSearch() {
        var searchQuery = document.getElementById('search-input').value.trim();
        if (searchQuery) {
            window.location.href = '?search=' + encodeURIComponent(searchQuery);
        } else {
            window.location.href = window.location.pathname;
        }
    }

    document.getElementById('search-input').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
    });

    document.getElementById('search-icon').addEventListener('click', function() {
        performSearch();
    });
</script>


</div>
</div>

</body>

</html>