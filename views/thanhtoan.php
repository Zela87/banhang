<?php
$orderInfo = $_SESSION['order_info'] ?? null;

if (!$orderInfo || !isset($orderInfo['OrderID'])) {
    echo "Không có thông tin đơn hàng. Vui lòng quay lại giỏ hàng và thử lại.";
    exit;
}

$orderId = $orderInfo['OrderID'];
?>

<section class="bank-transfer-payment">
    <h2>Thanh toán chuyển khoản ngân hàng</h2>
    <p>Vui lòng chuyển khoản với các thông tin sau:</p>
    <ul>
        <li><strong>Số tài khoản:</strong> 123456789 (Ngân hàng ABC)</li>
        <li><strong>Tên người nhận:</strong> Công ty TNHH ABC</li>
        <li><strong>Nội dung chuyển khoản:</strong> Thanh toán đơn hàng #<?= htmlspecialchars($orderId); ?></li>
        <li><strong>Số tiền:</strong> <?= number_format($orderInfo['finalPrice'], 0, ',', '.'); ?>đ</li>
    </ul>
    <form method="post" action="index.php?act=confirm_bank_payment">
        <button type="submit" name="confirmPayment">Xác nhận đã thanh toán</button>
    </form>
</section>
