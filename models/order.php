<?php
function addOrder($userId, $shippingAddress, $shipping_fee, $grandTotal, $discount, $finalPrice, $paymentMethod, $status)
{
    $sql = "INSERT INTO orders (UserID, OrderDate, ShippingAddress, ShippingFee, TotalAmount, Discount, FinalPrice, pttt, Status, CreatedAt, UpdatedAt) 
                VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    return pdo_execute_return_last_id($sql, $userId, $shippingAddress, $shipping_fee, $grandTotal, $discount, $finalPrice, $paymentMethod, $status);
}


function addOrderItem($orderId, $productId, $quantity, $totalPrice)
{
    $sql = "INSERT INTO orderitems (OrderID, ProductID, Quantity, TotalPrice) VALUES (?, ?, ?, ?)";
    pdo_execute($sql, $orderId, $productId, $quantity, $totalPrice);
}

function getOrderHistoryByUser($userId)
{
    $sql = "
        SELECT oi.OrderID, o.OrderDate, oi.ProductID, oi.Quantity, oi.TotalPrice, o.FinalPrice, pi.ImageURL, 
            p.Name, p.Price
        FROM orderitems oi
        JOIN orders o ON oi.OrderID = o.OrderID
        JOIN products p ON oi.ProductID = p.ProductID
        LEFT JOIN productimages pi ON p.ProductID = pi.ProductID
        WHERE o.UserID = ?
        ORDER BY o.OrderDate DESC, oi.OrderID ASC, oi.TotalPrice, o.FinalPrice DESC
    ";

    return pdo_query($sql, $userId);
}
function updateOrderStatus($orderId, $status) {
    $sql = "UPDATE orders SET Status = ? WHERE OrderID = ?";

    // Gọi hàm pdo_execute để thực thi câu lệnh SQL với tham số
    return pdo_execute($sql, $status, $orderId);
}

?>