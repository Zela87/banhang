<?php

$error_message = '';
$success_message = '';
$shipping_fee = 0;
$discount = 0;
$finalPrice = 0;
$freeShipping = false;
$extraProduct = false;
$grandTotal = 0;
$giftItem = null;
$cart_items = [];

// Kiểm tra nếu là "Mua ngay"
if (isset($_POST['buy-now'])) {
    $productID = (int)$_POST['ProductID'];
    $totalQuantity = (int)$_POST['Quantity'];

    $product = getProductDetails($productID);

    if ($product) {
        $item = [
            'ProductID' => $productID,
            'Name' => $product['Name'],
            'Quantity' => $totalQuantity,
            'CurrentPrice' => $product['CurrentPrice'],
            'ImageURL' => $product['PrimaryImageURL'] ?? ''
        ];

        // Lưu sản phẩm vào session để xử lý cho "Mua ngay"
        $_SESSION['buy_now'] = $item;
    }
}

// Lấy giỏ hàng từ session nếu là "Mua ngay", nếu không lấy từ giỏ hàng thông thường
if (isset($_SESSION['buy_now'])) {
    $cart_items[] = $_SESSION['buy_now'];
} else {
    $cart_items = getCartItems($_SESSION['user']['id_user']);
}

// Kiểm tra giỏ hàng và tính tổng giá trị giỏ hàng
foreach ($cart_items as $item) {
    $quantity = $item['Quantity'];
    $price = $item['CurrentPrice'];
    $totalPrice = $price * $quantity;
    $grandTotal += $totalPrice;
}

// Mặc định phí vận chuyển
$shipping_fee = (strpos(strtolower($_SESSION['user']['diachi']), 'hà nội') !== false) ? 50000 : 100000;

// Xử lý áp dụng mã khuyến mại
if (isset($_POST['applyPromo'])) {
    $promoCode = $_POST['promoCode'];
    $userId = $_SESSION['user']['id_user'];
    $promo = getPromotionByCode($promoCode);

    if ($promo) {
        if (hasUserUsedPromo($promoCode, $userId)) {
            $error_message = "Bạn đã sử dụng mã khuyến mại này rồi. Vui lòng chọn mã khác.";
        } else {
            $promotionId = $promo['PromotionID'];
            $currentUsage = getPromotionUsageCount($promotionId);

            if ($currentUsage < $promo['UsageLimit']) {
                if ($grandTotal >= $promo['MinOrderValue']) {
                    if (!empty($promo['DiscountPercentage']) && $promo['DiscountPercentage'] != 0) {
                        $discount = $grandTotal * ($promo['DiscountPercentage'] / 100);
                    } elseif (!empty($promo['DiscountAmount']) && $promo['DiscountAmount'] != 0) {
                        $discount = $promo['DiscountAmount'];
                    }

                    // Xử lý các loại khuyến mãi đặc biệt
                    if ($promo['PromotionTypeID'] == 3) { // Freeship
                        $freeShipping = true;
                        $shipping_fee = 0;
                    }

                    $isPromoApplied = false;

                    // Xử lý quà tặng nếu mã "Mua 4 tặng 1"
                    if ($promo['PromotionTypeID'] == 4 && empty($discount)) {
                        $totalQuantity = 0;
                        foreach ($cart_items as $item) {
                            $totalQuantity += $item['Quantity'];
                        }

                        if ($totalQuantity >= 4) {
                            // Kiểm tra sản phẩm tặng còn trong kho hay không
                            $extraProduct = $cart_items[0];
                            $productStock = checkProductStock($extraProduct['ProductID']); // Hàm kiểm tra tồn kho

                            if ($productStock > 0) {
                                $giftItem = $extraProduct;
                                $_SESSION['cart'][] = [
                                    'ProductID' => $extraProduct['ProductID'],
                                    'Name' => 'Sản phẩm tặng: ' . $extraProduct['Name'],
                                    'Quantity' => 1,
                                    'CurrentPrice' => 0
                                ];
                                $_SESSION['giftItem'] = $giftItem;

                                // Xác nhận áp dụng khuyến mại thành công
                                $isPromoApplied = true;
                            } else {
                                // Nếu không còn sản phẩm trong kho để tặng
                                $error_message = "Rất tiếc, sản phẩm khuyến mại đã hết hàng. Vui lòng chọn mã khuyến mại khác.";
                                $isPromoApplied = false;
                            }
                        } else {
                            $error_message = "Bạn cần mua ít nhất 4 sản phẩm để được tặng thêm.";
                            $isPromoApplied = false;
                        }
                    } else {
                        // Xác nhận các loại khuyến mại khác được áp dụng
                        if ($discount > 0 || $freeShipping) {
                            $isPromoApplied = true;
                        }
                    }

                    // Lưu các biến vào session nếu khuyến mại được áp dụng
                    if ($isPromoApplied) {
                        $_SESSION['discount'] = $discount;
                        $_SESSION['freeShipping'] = $freeShipping;
                        $_SESSION['shipping_fee'] = $shipping_fee;
                        $_SESSION['promoCode'] = $promoCode;
                        $_SESSION['promotionId'] = $promotionId;

                        $finalPrice = $grandTotal + $shipping_fee - $discount;
                        $_SESSION['finalPrice'] = $finalPrice;

                        $success_message = "Mã khuyến mại $promoCode đã được áp dụng thành công!";
                    }
                } else {
                    $error_message = "Giá trị đơn hàng chưa đủ để áp dụng mã khuyến mại.";
                }
            } else {
                $error_message = "Mã khuyến mại đã hết lượt dùng.";
            }
        }
    } else {
        $error_message = "Mã khuyến mại không hợp lệ hoặc hết hạn.";
    }
}

// Xử lý cập nhật thông tin khách hàng
if (isset($_POST['updateInfo'])) {
    $hoten = $_POST['hoten'];
    $diachi = $_POST['diachi'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $userId = $_SESSION['user']['id_user'];

    // update_info_user($userId, $hoten, $diachi, $email, $sdt);
    $_SESSION['user']['hoten'] = $hoten;
    $_SESSION['user']['diachi'] = $diachi;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['sdt'] = $sdt;

    $success_message = "Thông tin cá nhân đã được cập nhật thành công!";
}

// Xử lý đặt hàng
if (isset($_POST['placeOrder'])) {
    $paymentMethod = $_POST['payment'];
    $userId = $_SESSION['user']['id_user'];
    $shippingAddress = $_SESSION['user']['diachi'];

    $discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
    $freeShipping = isset($_SESSION['freeShipping']) && $_SESSION['freeShipping'];
    $shipping_fee = $freeShipping ? 0 : ((strpos(strtolower($_SESSION['user']['diachi']), 'hà nội') !== false) ? 50000 : 100000);
    $giftItem = isset($_SESSION['giftItem']) ? $_SESSION['giftItem'] : null;
    $promoCode = isset($_SESSION['promoCode']) ? $_SESSION['promoCode'] : null;
    $promotionId = isset($_SESSION['promotionId']) ? $_SESSION['promotionId'] : null;

    $finalPrice = $grandTotal + $shipping_fee - $discount;

    if ($paymentMethod === 'bank_transfer') {
        $orderId = addOrder($userId, $shippingAddress, $shipping_fee, $grandTotal, $discount, $finalPrice, $paymentMethod, 'Chờ xác nhận');

        $_SESSION['order_info'] = [
            'OrderID' => $orderId,
            'UserID' => $userId,
            'shippingAddress' => $shippingAddress,
            'shipping_fee' => $shipping_fee,
            'grandTotal' => $grandTotal,
            'discount' => $discount,
            'finalPrice' => $finalPrice,
            'paymentMethod' => $paymentMethod,
            'cart_items' => $cart_items,
            'promoCode' => $promoCode,
            'promotionId' => $promotionId,
            'giftItem' => $giftItem
        ];
        header('Location: index.php?act=thanhtoan');
        exit;
    }

    $orderId = addOrder($userId, $shippingAddress, $shipping_fee, $grandTotal, $discount, $finalPrice, $paymentMethod, 'Chờ xác nhận');

    if ($orderId) {
        foreach ($cart_items as $item) {
            $productId = $item['ProductID'];
            $quantity = $item['Quantity'];
            $totalPrice = $item['CurrentPrice'] * $quantity;
            addOrderItem($orderId, $productId, $quantity, $totalPrice);
            updateProductStock($productId, $quantity);
        }
        if ($giftItem) {
            $giftProductId = $giftItem['ProductID'];
            addOrderItem($orderId, $giftProductId, 1, 0);
            updateProductStock($giftProductId, 1);
        }
        if (!empty($promoCode)) {
            applyPromotionUsageWithOrderId($promoCode, $userId, $orderId);
            decrementPromotionUsage($promotionId);
        }
        clearCart($userId);
        unset($_SESSION['freeShipping']);
        unset($_SESSION['discount']);
        unset($_SESSION['giftItem']);
        unset($_SESSION['promoCode']);
        unset($_SESSION['promotionId']);
        header('Location: index.php?act=thanhcong');
    } else {
        $error_message = "Đặt hàng không thành công. Vui lòng thử lại!";
    }
}

// Tính tổng giá cuối cùng
$finalPrice = $grandTotal + $shipping_fee - $discount;
?>


<section class="container_pay">
    <div class="breadcrumb">
        <a href="index.php?act=giohang">Giỏ hàng</a> /
        <span>Đặt hàng</span>
    </div>
    <h2>Đặt hàng</h2>

    <!-- Form Section: Information & Promotions -->
    <div class="form-section">
        <h3>Thông tin nhận hàng</h3>
        <form method="post" action="index.php?act=dathang">
            <!-- Customer Information -->
            <div class="form-group">
                <label for="hoten">Họ tên:</label>
                <input type="text" name="hoten" id="hoten" value="<?= htmlspecialchars($_SESSION['user']['hoten']) ?>" placeholder="Họ tên" required>
            </div>
            <div class="form-group">
                <label for="diachi">Địa chỉ:</label>
                <input type="text" name="diachi" id="diachi" value="<?= htmlspecialchars($_SESSION['user']['diachi']) ?>" placeholder="Nhập địa chỉ nhận hàng" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="sdt">Số điện thoại:</label>
                <input type="tel" name="sdt" id="sdt" placeholder="Số điện thoại" value="<?= htmlspecialchars($_SESSION['user']['sdt']) ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="updateInfo">Cập nhật thông tin</button>
            </div>

            <!-- Shipping & Payment Options -->
            <div class="form-group">
                <label for="shipping">Vận chuyển:</label>
                <select name="shipping" id="shipping" required>
                    <option value="delivery">Giao hàng tận nơi</option>
                </select>
            </div>

            <div class="form-group">
                <label for="payment">Phương thức thanh toán:</label>
                <select name="payment" id="payment" required>
                    <option value="cod">Thanh toán khi nhận hàng</option>
                    <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                </select>
            </div>

            <!-- Promotion Code -->
            <div class="form-group">
                <label for="promoCode">Nhập mã khuyến mại (nếu có):</label>
                <input type="text" name="promoCode" id="promoCode" placeholder="Mã khuyến mại">
                <button type="submit" name="applyPromo">Áp dụng</button>
            </div>

            <!-- Display Success or Error Message -->
            <?php if ($success_message) : ?>
                <p id="success" class="message success"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>
            <?php if ($error_message) : ?>
                <p id="error" class="message error"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
    </div>
    <div class="order-summary">
        <h3>Tóm tắt đơn hàng</h3>
        <?php foreach ($cart_items as $item) : ?>
            <div class="order-item">
                <div>
                    <img src="<?= htmlspecialchars($img_path . $item['ImageURL']) ?>" alt="<?= htmlspecialchars($item['Name']) ?>">
                    <p><?= htmlspecialchars($item['Name']) ?> (SL: <?= $item['Quantity'] ?>)</p><br>
                    <span><?= number_format($item['CurrentPrice'] * $item['Quantity'], 0, ',', '.') ?>đ</span>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="order-item">
            <span>Tạm tính:</span>
            <span class="total-price"><?= number_format($grandTotal, 0, ',', '.') ?>đ</span>
        </div>

        <?php if ($giftItem) : ?>
            <div class="order-item">
                <span>Phần quà tặng:</span>
                <span class="gift-item"><?= htmlspecialchars($giftItem['Name']) ?> (SL: 1)</span>
            </div>
        <?php endif; ?>

        <div class="order-item">
            <span>Phí vận chuyển:</span>
            <span class="shipping-fee"><?= number_format($shipping_fee, 0, ',', '.') ?>đ</span>
        </div>

        <div class="order-item">
            <span>Giảm giá:</span>
            <span id="discount" class="discount"><?= number_format($discount, 0, ',', '.') ?>đ</span>
        </div>

        <div class="order-item">
            <span>Tổng cộng:</span>
            <span id="finalPrice" class="grand-total"><?= number_format($finalPrice, 0, ',', '.') ?>đ</span>
        </div>
        <!-- Submit Order Button -->
        <div class="form-group">
            <button type="submit" class="submit-btn" name="placeOrder">Đặt hàng</button>
        </div>
    </div>
    </form>
    </div>
</section>