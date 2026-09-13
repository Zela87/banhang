<?php 
function get_all_feedback_home(){
    $sql = "SELECT 
                f.comment, 
                f.ProductRating, 
                u.hoten AS user_name, 
                u.Avatar, 
                p.Name AS product_name, 
                pi.ImageURL AS product_image
            FROM feedback f
            JOIN users u ON u.id_user = f.UserID
            JOIN products p ON f.ProductID = p.ProductID
            JOIN productimages pi ON pi.ProductID = p.ProductID
            WHERE pi.isPrimary = 1 and f.ProductRating >= 4
            Order by f.ProductRating desc
            LIMIT 5";
    return pdo_query($sql);
}
function get_all_feedback_by_product_id($productID){
    $sql = "SELECT 
    f.*, 
    u.hoten, 
    u.Avatar
FROM feedback f
JOIN users u ON u.id_user = f.UserID
WHERE f.ProductID = ?";
    return pdo_query($sql, $productID);
}

function addProductReview($productID, $userID, $rating, $comment) {
    $sql = "INSERT INTO feedback (ProductID, UserID, ProductRating, Comment, CreatedAt) VALUES (?, ?, ?, ?, NOW())";
    return pdo_execute($sql, $productID, $userID, $rating, $comment);
}


?>