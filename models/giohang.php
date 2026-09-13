<?php

function update_cart_item($id_user, $ProductID, $new_quantity) {
    $sql = "UPDATE cartitems SET Quantity = ? WHERE id_user = ? AND ProductID = ?";
    pdo_execute($sql, $new_quantity, $id_user, $ProductID);  // Cập nhật trực tiếp số lượng mới
}


function remove_from_cart($id_user, $ProductID) {
    $sql = "DELETE FROM cartitems WHERE id_user = ? AND ProductID = ?";
    return pdo_execute($sql, $id_user, $ProductID);
}

function add_to_cart($id_user, $ProductID, $Quantity) {
    // Lấy thông tin sản phẩm từ cơ sở dữ liệu để kiểm tra tồn kho
    $sql_product = "SELECT StockQuantity FROM products WHERE ProductID = ?";
    $product = pdo_query_one($sql_product, $ProductID);

    if (!$product) {
        // Nếu không tìm thấy sản phẩm, thoát ra
        return;
    }

    $stockQuantity = (int)$product['StockQuantity'];

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng hay chưa
    $sql_check = "SELECT Quantity FROM cartitems WHERE id_user = ? AND ProductID = ?";
    $existing_item = pdo_query_one($sql_check, $id_user, $ProductID);

    if ($existing_item) {
        // Nếu sản phẩm đã có, tính tổng số lượng sau khi thêm
        $new_quantity = $existing_item['Quantity'] + $Quantity;

        // Kiểm tra nếu tổng số lượng vượt quá tồn kho
        if ($new_quantity > $stockQuantity) {
            // Hiển thị thông báo lỗi (có thể dùng session hoặc JavaScript)
            $_SESSION['error_message'] = "Không thể thêm vào giỏ. Sản phẩm chỉ còn " . $stockQuantity . " cái trong kho.";
            return;
        }

        // Nếu hợp lệ, cập nhật số lượng trong giỏ
        $sql_update = "UPDATE cartitems SET Quantity = ? WHERE id_user = ? AND ProductID = ?";
        pdo_execute($sql_update, $new_quantity, $id_user, $ProductID);
    } else {
        // Nếu chưa có, kiểm tra nếu số lượng yêu cầu không vượt quá tồn kho
        if ($Quantity > $stockQuantity) {
            $_SESSION['error_message'] = "Không thể thêm vào giỏ. Sản phẩm chỉ còn " . $stockQuantity . " cái trong kho.";
            return;
        }

        // Nếu hợp lệ, thêm sản phẩm vào giỏ
        $sql_insert = "INSERT INTO cartitems (id_user, ProductID, Quantity) VALUES (?, ?, ?)";
        pdo_execute($sql_insert, $id_user, $ProductID, $Quantity);
    }
}

function getCartItems($id_user) {
    $sql = "SELECT 
                ci.ProductID, 
                ci.Quantity, 
                p.*,
                d.DiscountPercentage,
                d.DiscountAmount,
                pi.ImageURL,
                CASE 
                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN p.Price * (1 - d.DiscountPercentage / 100)
                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN p.Price - d.DiscountAmount
                    ELSE p.Price 
                END AS CurrentPrice
            FROM 
                cartitems ci
            JOIN 
                products p ON ci.ProductID = p.ProductID
            LEFT JOIN 
                discounts d ON p.ProductID = d.ProductID 
                AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
            JOIN 
                productimages pi ON pi.ProductID = p.ProductID 
            WHERE 
                ci.id_user = ? 
            AND 
                pi.IsPrimary = 1";
    
    return pdo_query($sql, $id_user);
}


function get_cart_count($id_user) {
    $sql = "SELECT SUM(Quantity) as total FROM cartitems WHERE id_user = ?";
    $result = pdo_query_one($sql, $id_user);
    return $result['total'] ? $result['total'] : 0;
}

function get_order_history($user_id) {
    $sql = "SELECT o.OrderID, p.ProductID, p.Name, p.Price, oi.Quantity, pi.ImageURL, 
            d.DiscountPercentage, d.DiscountAmount,
            CASE 
                WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100) 
                WHEN d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                ELSE p.Price 
            END AS CurrentPrice
            FROM orders o
            JOIN orderitems oi ON o.OrderID = oi.OrderID
            JOIN products p ON oi.ProductID = p.ProductID
            JOIN productimages pi ON pi.ProductID = p.ProductID AND pi.IsPrimary = 1
            LEFT JOIN discounts d ON p.ProductID = d.ProductID 
                AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
            WHERE o.UserID = ?
            ORDER BY o.OrderDate DESC";
    return pdo_query($sql, $user_id);
}


function handleUpdateCart($id_user) {
    if (isset($_POST['ProductID']) && isset($_POST['Quantity'])) {
        $ProductID = (int)$_POST['ProductID'];
        $Quantity = (int)$_POST['Quantity'];
        
        // Lấy thông tin sản phẩm từ cơ sở dữ liệu để kiểm tra tồn kho
        $product = get_product($ProductID);
        
        if ($product) {
            $stockQuantity = (int)$product['StockQuantity'];

            // Kiểm tra nếu số lượng yêu cầu vượt quá số lượng tồn kho
            if ($Quantity > $stockQuantity) {
                // Điều chỉnh số lượng về tối đa tồn kho nếu vượt quá
                $Quantity = $stockQuantity;
                $_SESSION['error_message'] = "Sản phẩm '" . htmlspecialchars($product['Name']) . "' chỉ còn " . $stockQuantity . " sản phẩm trong kho.";
            }

            // Nếu số lượng lớn hơn 0, cập nhật giỏ hàng
            if ($Quantity > 0) {
                update_cart_item($id_user, $ProductID, $Quantity);
            } else {
                // Nếu số lượng là 0 hoặc nhỏ hơn, xóa sản phẩm khỏi giỏ hàng
                remove_from_cart($id_user, $ProductID);
            }
        }
    }

    header('Location: index.php?act=giohang');
    exit();
}

function clearCart($userId) {
    $sql = "DELETE FROM cartitems WHERE id_user = ?";
    pdo_execute($sql, $userId);
}

?>