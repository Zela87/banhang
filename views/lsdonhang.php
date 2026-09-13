<?php
// Lấy lịch sử đơn hàng từ cơ sở dữ liệu
$order_history = getOrderHistoryByUser($id_user);
?>
<div class="container">
    <div class="breadcrumb">
        <a href="index.php?act=index">Trang chủ</a> /
        <a href="index.php?act=hoso">Hồ sơ</a> /
        <span>Lịch sử đơn hàng</span>
    </div>
</div>

<section class="products-section">
    <h2>Lịch sử đơn hàng</h2>

    <?php
    $lastOrderId = null;
    $firstOrder = true;

    if (!empty($order_history)) : ?>
        <?php foreach ($order_history as $order) : ?>
            <?php
            if ($lastOrderId !== $order['OrderID']) :
                if (!$firstOrder) : ?>
                    </div>
                    <hr>
                <?php endif; ?>

                <div class="order-group">
                    <h3>Ngày đặt hàng: <?= date('d/m/Y', strtotime($order['OrderDate'])); ?></h3>
                    <p>Mã đơn hàng: <?= $order['OrderID']; ?></p>
                    <div class="final">
                        <span>Tổng đơn hàng: <?= number_format($order['FinalPrice'], 0, ',', '.'); ?>đ</s>
                    </div>
                <?php
                $lastOrderId = $order['OrderID'];
                $firstOrder = false;
            endif;
                ?>

                <div class="order-item_ls" style="<?= $order['TotalPrice'] == 0 ? 'font-size: 0.9em; opacity: 0.8;' : ''; ?>">
                    <div class="order-image">
                        <img src="<?= htmlspecialchars($img_path . $order['ImageURL']); ?>" alt="<?= htmlspecialchars($order['Name']); ?>">
                    </div>
                    <div class="order-details">
                        <h4><?= htmlspecialchars($order['Name']); ?> <?= $order['TotalPrice'] == 0 ? '(Quà tặng)' : ''; ?></h4>
                        <p>Số lượng: <?= $order['Quantity']; ?></p>
                        <p>Thành tiền:
                            <span class="price">
                                <?php if ($order['TotalPrice'] == 0) : ?>
                                    <span class="gift_price">0đ</span>
                                <?php else : ?>
                                    <span class="current_price"><?= number_format($order['TotalPrice'], 0, ',', '.'); ?>đ</span>
                                <?php endif; ?>
                            </span>
                        </p>
                    </div>
                    <div class="order-actions">
                        <a href="index.php?act=chitietsp&ProductID=<?= $order['ProductID']; ?>" class="btn-buy-again">Mua lại</a>
                        <a href="index.php?act=danhgia&productID=<?= $order['ProductID']; ?>&orderID=<?= $order['OrderID']; ?>" class="btn-review">Đánh giá</a>
                    </div>
                </div>
            <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p>Bạn chưa có đơn hàng nào.</p>
            <?php endif; ?>
</section>