<?php
include '../models/db-connect.php'; // Kết nối cơ sở dữ liệu
include '../models/product.php'; // Bao gồm các chức năng truy vấn sản phẩm

if (isset($_POST['query'])) {
    $keyword = htmlspecialchars(trim($_POST['query']));
    $products = searchProducts($keyword);

    if (!empty($products)) {
        foreach ($products as $product) {
            echo "<a href='index.php?act=chitietsp&ProductID=" . $product['id'] . "'>Xem chi tiết</a>";
        }
    } else {
        echo "<p>Không tìm thấy sản phẩm nào</p>";
    }
} else {
    echo "<p>Không có từ khóa tìm kiếm.</p>";
}
?>
