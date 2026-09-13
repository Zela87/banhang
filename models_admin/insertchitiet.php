<?php
include_once 'db-connect.php';

// Ensure the database connection
$dbconn = connect_db();

function getProduct_loai($dbconn, $productID) {
    $query = "SELECT p.*, c.Name AS CategoryName 
              FROM products p 
              JOIN categories c ON p.CategoryID = c.CategoryID 
              WHERE p.ProductID = :productID";
    $stmt = $dbconn->prepare($query);
    $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductInfo($conn, $productID) {
    $query_info = "SELECT * FROM product_info pi 
                   WHERE ProductID = :productID 
                   ORDER BY DetailID";
    $stmt_info = $conn->prepare($query_info);
    $stmt_info->bindParam(':productID', $productID, PDO::PARAM_INT);
    $stmt_info->execute();
    return $stmt_info->fetchAll(PDO::FETCH_ASSOC);
}

function getProductFeedback($conn, $productID) {
    $query_feedback = "SELECT f.*, u.hoten, u.Avatar
                       FROM feedback f 
                       JOIN users u ON f.UserID = u.id_user 
                       WHERE f.ProductID = ?";
    $stmt_feedback = $conn->prepare($query_feedback);
    $stmt_feedback->execute([$productID]);
    return $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);
}

function getProductImages($conn, $productID) {
    $imageQuery = "SELECT * FROM productimages WHERE ProductID = ?";
    $imageStmt = $conn->prepare($imageQuery);
    $imageStmt->execute([$productID]);
    return $imageStmt->fetchAll(PDO::FETCH_ASSOC);
}
function handleFeedbackSubmission($conn, $productID) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productRating']) && isset($_POST['comment'])) {
        $productRating = $_POST['productRating'];
        $comment = $_POST['comment'];
        $userID = 1; // Giả sử bạn đã có ID người dùng.

        $insertQuery = "INSERT INTO feedback (UserID, ProductID, ProductRating, Comment, CreatedAt) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->execute([$userID, $productID, $productRating, $comment]);

        return $stmt ? "Đánh giá của bạn đã được gửi thành công." : "Đã xảy ra lỗi khi gửi đánh giá.";
    }
    return '';
}
function getRelatedProducts($conn, $currentProductID, $categoryID) {
    $relatedProductsSql = "SELECT p.*, pi.ImageURL, d.DiscountPercentage,
                            d.DiscountAmount,
                            CASE 
                                WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100) 
                                WHEN d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                                ELSE p.Price 
                            END AS CurrentPrice
                        FROM products p 
                        LEFT JOIN discounts d ON p.ProductID = d.ProductID 
                        JOIN productimages pi ON p.ProductID = pi.ProductID 
                        WHERE pi.IsPrimary = 1 
                        AND p.CategoryID = ? 
                        AND p.ProductID != ? 
                        LIMIT 4";

    $stmt = $conn->prepare($relatedProductsSql);
    $stmt->execute([$categoryID, $currentProductID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
