<section class="cart-container">
    <h2>Giỏ hàng (<?= $cart_count; ?> sản phẩm)</h2>

    <?php if ($cart_count > 0) : ?>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng cộng</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grandTotal = 0;
                foreach ($cart_items as $item) :
                    $quantity = $item['Quantity'];
                    $price = $item['CurrentPrice'];
                    $isOutOfStock = $item['StockQuantity'] == 0;
                    $totalPrice = $price * $quantity;
                    $grandTotal += $totalPrice;
                ?>
                    <tr data-product-id="<?= htmlspecialchars($item['ProductID']) ?>">
                        <td>
                            <form action="index.php?act=removeFromCart" method="post">
                                <input type="hidden" name="ProductID" value="<?= htmlspecialchars($item['ProductID']) ?>">
                                <button type="submit">✖</button>
                            </form>
                        </td>
                        <td>
                            <div class="product-info">
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($img_path . $item['ImageURL']) ?>" alt="<?= htmlspecialchars($item['Name']) ?>" class="product-image">
                                </div>
                                <div class="product-info">
                                    <p><?= htmlspecialchars($item['Name']) ?></p>
                                    <?php if ($isOutOfStock) : ?>
                                        <p class="out-of-stock">Hết hàng</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                        <?php if (!empty($item['DiscountPercentage']) || !empty($item['DiscountAmount'])) : ?>
                            <span class="original_price"><?= number_format($item['Price'], 0, ',', '.') ?>đ</span>
                            <span class="current-price"><?= number_format($item['CurrentPrice'], 0, ',', '.') ?>đ</span>
                        <?php else : ?>
                            <span class="current-price"><?= number_format($item['Price'], 0, ',', '.') ?>đ</span>
                        <?php endif; ?>
                        </td>
                        <td>
                            <form action="index.php?act=updateCart" method="post" onsubmit="return validateQuantity(this);">
                                <input type="hidden" name="ProductID" value="<?= htmlspecialchars($item['ProductID']) ?>">
                                <input type="number" class="quantity-input" name="Quantity" value="<?= htmlspecialchars($quantity) ?>" min="1" max="<?= $item['StockQuantity'] ?>" data-max="<?= $item['StockQuantity'] ?>" <?= $isOutOfStock ? 'disabled' : '' ?> onchange="this.form.submit()">
                            </form>

                        </td>
                        <td class="total-price"><?= number_format($totalPrice, 0, ',', '.') ?>đ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Giỏ hàng của bạn đang trống.</p>
    <?php endif; ?>

    <div class="checkout-section">
        <button class="checkout-button" onclick="window.location.href='index.php?act=sanpham';">Tiếp tục mua hàng</button>

        <?php if ($cart_count > 0) : ?>
            <div class="total-summary">
                <span>Tổng cộng:</span>
                <span class="grand-total"><?= number_format($grandTotal, 0, ',', '.') ?>đ</span>
            </div>
            <button class="checkout-button" onclick="window.location.href='index.php?act=dathang';">Xác nhận</button>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các input số lượng
        const quantityInputs = document.querySelectorAll('.quantity-input');

        // Lặp qua từng input và thêm sự kiện khi thay đổi giá trị
        quantityInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                handleQuantityChange(this);
            });

            input.addEventListener('change', function() {
                handleQuantityChange(this);
            });
        });

        function handleQuantityChange(input) {
            const maxQuantity = parseInt(input.getAttribute('data-max'), 10);
            const currentQuantity = parseInt(input.value, 10);

            // Kiểm tra nếu số lượng vượt quá số lượng tồn kho
            if (currentQuantity > maxQuantity) {
                alert("Sản phẩm này chỉ còn " + maxQuantity + " sản phẩm trong kho.");
                input.value = maxQuantity; // Điều chỉnh số lượng về mức tối đa
            }

            // Kiểm tra nếu số lượng nhỏ hơn 1 (người dùng nhập số âm hoặc 0)
            if (currentQuantity < 1) {
                alert("Số lượng không thể nhỏ hơn 1.");
                input.value = 1; // Điều chỉnh số lượng về tối thiểu là 1
            }

            // Sau khi điều chỉnh, tự động gửi biểu mẫu để cập nhật giỏ hàng
            input.closest("form").submit();
        }
    });
</script>
