<?php
if (isset($_SESSION['review_message'])) {
    $message = $_SESSION['review_message'];
    echo "<script>alert('" . addslashes($message) . "');</script>";
    unset($_SESSION['review_message']);
}
?>

<main>
    <div class="product-page">
        <div class="product-images">
            <?php if (!empty($product['PrimaryImageURL'])) : ?>
                <img id="mainImage" src="<?= $img_path . $product['PrimaryImageURL'] ?>" alt="<?= htmlspecialchars($product['Name']) ?>">
                <div class="thumbnail-images">
                    <?php
                    if (!empty($images) && is_array($images)) {
                        foreach ($images as $image) : ?>
                            <?php if ($image['IsPrimary'] != 1) : ?>
                                <img class="thumbnail" src="<?= $img_path . $image['ImageURL']; ?>" alt="Thumbnail" onclick="changeMainImage('<?= $img_path . $image['ImageURL']; ?>')">
                            <?php endif; ?>
                    <?php endforeach;
                    } ?>
                </div>
            <?php else : ?>
                <p>Không có hình ảnh sản phẩm.</p>
            <?php endif; ?>
        </div>
        <div class="product-details">
            <h1><?= htmlspecialchars($product['Name'] ?? 'Sản phẩm không tồn tại'); ?></h1>
            <p class="price">
                <?php if (!empty($product['DiscountPercentage']) || !empty($product['DiscountAmount'])) : ?>
                    <span class="old-price"><?= number_format($product['Price'], 0, ',', '.'); ?>đ</span>
                    <span class="new-price"><?= number_format($product['CurrentPrice'], 0, ',', '.'); ?>đ</span>
                <?php else : ?>
                    <span class="new-price"><?= number_format($product['Price'], 0, ',', '.'); ?>đ</span>
                <?php endif; ?>
            </p>
            <div class="stock-info">
                <?php if ($product['StockQuantity'] > 0) : ?>
                    <p>Còn lại: <?= htmlspecialchars($product['StockQuantity']); ?> sản phẩm</p>
                <?php endif; ?>
            </div>

            <div class="rating">
                <span>★★★★★</span> (<?= !empty($feedbacks) ? count($feedbacks) : 0; ?> đánh giá)
            </div>

            <div class="quantity-and-buttons">
                <?php if ($product['StockQuantity'] > 0) : ?>
                    <div class="quantity">
                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['StockQuantity'] ?>" oninput="updateQuantityForForms()">
                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>

                    <form method="POST" action="index.php?act=addToCart" id="addToCartForm">
                        <input type="hidden" name="ProductID" value="<?= $product['ProductID']; ?>">
                        <input type="hidden" name="Name" value="<?= htmlspecialchars($product['Name']); ?>">
                        <?php if (!empty($product['DiscountPercentage']) || !empty($product['DiscountAmount'])) : ?>
                            <input type="hidden" name="Price" value="<?= $product['CurrentPrice']; ?>">
                        <?php else : ?>
                            <input type="hidden" name="Price" value="<?= $product['Price']; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="Quantity" id="addQuantity" value="1">
                        <button type="submit" class="add-to-cart">Thêm vào giỏ</button>
                    </form>

                    <form method="POST" action="index.php?act=dathang" id="buyNowForm">
                        <input type="hidden" name="ProductID" value="<?= $product['ProductID']; ?>">
                        <input type="hidden" name="Name" value="<?= htmlspecialchars($product['Name']); ?>">
                        <?php if (!empty($product['DiscountPercentage']) || !empty($product['DiscountAmount'])) : ?>
                            <input type="hidden" name="Price" value="<?= $product['CurrentPrice']; ?>">
                        <?php else : ?>
                            <input type="hidden" name="Price" value="<?= $product['Price']; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="Quantity" id="buyNowQuantity" value="1">
                        <button type="submit" class="buy-now" name="buy-now">Mua ngay</button>
                    </form>
                <?php else : ?>
                    <p class="out-of-stock">Hết hàng</p>
                <?php endif; ?>
            </div>

            <p class="category">Danh mục: <?= htmlspecialchars($product['CategoryName'] ?? 'Không xác định'); ?></p>
        </div>
    </div>
    <?php
    if (!empty($result_info) && is_array($result_info)) {
        echo "<h2>Mô tả</h2>";
        foreach ($result_info as $info) : ?>
            <section class="product-description">
                <p><?php echo htmlspecialchars($info['Content']); ?></p>
                <?php if (!empty($secondaryImages)) : ?>
                    <div class="product-description-img">
                        <img src="<?php echo array_shift($secondaryImages); ?>" alt="<?php echo htmlspecialchars($info['SectionTitle']); ?>">
                    </div>
                <?php endif; ?>
            </section>
    <?php endforeach;
    } else {
        echo "<p>Không có thông tin chi tiết về sản phẩm.</p>";
    }
    ?>

    <section class="reviews_sp">
        <h3>Đánh giá sản phẩm:</h3>
        <?php if (!empty($feedbacks)) : ?>
            <?php foreach ($feedbacks as $feedback) : ?>
                <div class="reviewsp">
                    <div class="review-item">
                        <?php if (!empty($feedback['Avatar'])) : ?>
                            <img src="../assets/images/<?php echo htmlspecialchars($feedback['Avatar']); ?>" alt="Profile Picture">
                        <?php else : ?>
                            <img src="../assets/images/user.jpg" alt="Profile Picture">
                        <?php endif; ?>
                    </div>
                    <div class="review-content">
                        <p><?php echo htmlspecialchars($feedback['hoten']); ?></p>
                        <span><?php echo str_repeat('★', $feedback['ProductRating']) . str_repeat('☆', 5 - $feedback['ProductRating']); ?></span>
                        <p><?php echo date('d/m/Y H:i', strtotime($feedback['CreatedAt'])); ?></p>
                        <p><?php echo htmlspecialchars($feedback['Comment']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        <?php endif; ?>
    </section>


    <h2>Có thể bạn cũng thích:</h2>
    <section class="related-products">
        <?php if (!empty($relatedProducts)) : ?>
            <?php foreach ($relatedProducts as $related) : ?>
                <form action="index.php?act=addToCart" method="post">
                    <div class="related-item">
                        <?php if (!empty($related['DiscountPercentage'])) : ?>
                            <div class="sale">
                                <p>-<?= htmlspecialchars($related['DiscountPercentage']) ?>%</p>
                            </div>
                        <?php endif; ?>
                        <a href="index.php?act=chitietsp&ProductID=<?= htmlspecialchars($related['ProductID']) ?>">
                            <img src="<?= htmlspecialchars($img_path . $related['ImageURL']) ?>" alt="<?= htmlspecialchars($related['Name']) ?>">
                            <h3><?= htmlspecialchars($related['Name']) ?></h3>
                            <?php if ($related['StockQuantity'] > 0) : ?>
                                <div class="price">
                                    <!-- Hiển thị giá trước và sau giảm giá nếu có khuyến mãi -->
                                    <?php if (!empty($related['DiscountPercentage']) || !empty($related['DiscountAmount'])) : ?>
                                        <p class="original_price"><?= number_format($related['Price'], 0, ',', '.') ?>đ</p>
                                        <p class="current_price"><?= number_format($related['CurrentPrice'], 0, ',', '.') ?>đ</p>
                                    <?php else : ?>
                                        <p class="current_price"><?= number_format($related['Price'], 0, ',', '.') ?>đ</p>
                                    <?php endif; ?>
                                </div>
                        </a>

                        <input type="hidden" name="ProductID" value="<?= $product['ProductID'] ?>">
                        <input type="hidden" name="Name" value="<?= htmlspecialchars($product['Name']) ?>">
                        <input type="hidden" name="Quantity" value="1">

                        <input type="submit" value="Thêm vào giỏ" name="addToCart">
                    <?php else : ?>
                        <p class="out-of-stock">Hết hàng</p>
                    <?php endif; ?>
                </form>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Không có sản phẩm gợi ý.</p>
        <?php endif; ?>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');

        // Tăng số lượng
        function increaseQuantity() {
            const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);
            let currentQuantity = parseInt(quantityInput.value, 10);

            if (currentQuantity < maxQuantity) {
                quantityInput.value = currentQuantity + 1;
                updateQuantityForForms();
            } else {
                alert("Sản phẩm này chỉ còn " + maxQuantity + " sản phẩm trong kho.");
            }
        }

        // Giảm số lượng
        function decreaseQuantity() {
            let currentQuantity = parseInt(quantityInput.value, 10);

            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
                updateQuantityForForms();
            } else {
                alert("Số lượng không thể nhỏ hơn 1.");
            }
        }

        // Cập nhật số lượng cho các form
        function updateQuantityForForms() {
            const currentQuantity = quantityInput.value;

            document.getElementById('addQuantity').value = currentQuantity;
            document.getElementById('buyNowQuantity').value = currentQuantity;
        }

        // Xử lý khi người dùng nhập tay số lượng
        quantityInput.addEventListener('input', function() {
            const maxQuantity = parseInt(this.getAttribute('max'), 10);
            let currentQuantity = parseInt(this.value, 10);

            // Kiểm tra và điều chỉnh nếu vượt quá tồn kho
            if (currentQuantity > maxQuantity) {
                alert("Sản phẩm này chỉ còn " + maxQuantity + " sản phẩm trong kho.");
                this.value = maxQuantity;
            }

            // Kiểm tra nếu số lượng nhỏ hơn 1
            if (currentQuantity < 1) {
                alert("Số lượng không thể nhỏ hơn 1.");
                this.value = 1;
            }

            updateQuantityForForms(); // Cập nhật số lượng trong form
        });

        // Gán hàm tăng/giảm cho các nút tương ứng
        document.querySelector('.quantity-btn-increase').addEventListener('click', increaseQuantity);
        document.querySelector('.quantity-btn-decrease').addEventListener('click', decreaseQuantity);
    });
</script>