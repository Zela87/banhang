<?php
// Kiểm tra nếu có sản phẩm để đánh giá
$productID = $_GET['productID'] ?? null;
$orderID = $_GET['orderID'] ?? null;

if (!$productID || !$orderID) {
    echo "Sản phẩm không hợp lệ.";
    exit;
}

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$product = getProductDetails($productID);
?>

<div class="review-container">
    <h2>Đánh giá sản phẩm: <?= htmlspecialchars($product['Name']); ?></h2>
    <img src="<?= htmlspecialchars($img_path . $product['PrimaryImageURL']); ?>" alt="<?= htmlspecialchars($product['Name']); ?>">
    
    <form method="post" action="index.php?act=submit_review">
        <input type="hidden" name="productID" value="<?= $productID; ?>">
        <input type="hidden" name="orderID" value="<?= $orderID; ?>">
        
        <div class="rating">
            <label>Đánh giá:</label>
            <select name="rating" required>
                <option value="5">★★★★★ - 5 sao</option>
                <option value="4">★★★★☆ - 4 sao</option>
                <option value="3">★★★☆☆ - 3 sao</option>
                <option value="2">★★☆☆☆ - 2 sao</option>
                <option value="1">★☆☆☆☆ - 1 sao</option>
            </select>
        </div>

        <div class="comment">
            <label for="comment">Nhận xét:</label>
            <textarea name="comment" id="comment" rows="5" placeholder="Viết nhận xét của bạn..." required></textarea>
        </div>

        <button type="submit" class="btn-submit-review">Gửi đánh giá</button>
    </form>
</div>
