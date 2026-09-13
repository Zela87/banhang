<?php 
function get_all_promotions(){
    $sql = "SELECT * FROM promotions WHERE StartDate <= NOW() AND EndDate >= NOW() AND IsActive = 1";
    return pdo_query($sql);
}
function getPromotionByCode($promoCode) {
    $sql = "SELECT * FROM promotions 
            WHERE PromoCode = ? 
            AND StartDate <= NOW() 
            AND EndDate >= NOW() 
            AND IsActive = 1";
    return pdo_query_one($sql, $promoCode);  // Sử dụng pdo_query_one để lấy một bản ghi
}

function getPromotionUsageCount($promotionId) {
    $sql = "SELECT COUNT(*) FROM promotion_usage WHERE PromotionID = ?";
    return pdo_query_value($sql, $promotionId); // Sử dụng PromotionID thay vì promo_code
}

function applyPromotionUsageWithOrderId($promoCode, $userId, $orderId) {
    $promo = getPromotionByCode($promoCode);
    if ($promo) {
        $promotionId = $promo['PromotionID'];
        $sql = "INSERT INTO promotion_usage (UserID, PromotionID, OrderID, UsedAt) VALUES (?, ?, ?, NOW())";
        pdo_execute($sql, $userId, $promotionId, $orderId);
    }
}

function decrementPromotionUsage($promotionId) {
    $sql = "UPDATE promotions SET UsageLimit = UsageLimit - 1 WHERE PromotionID = ?";
    pdo_execute($sql, $promotionId);
}
function hasUserUsedPromo($promoCode, $userId) {
    $promo = getPromotionByCode($promoCode);
    if ($promo) {
        $promotionId = $promo['PromotionID'];
        $sql = "SELECT COUNT(*) FROM promotion_usage WHERE PromotionID = ? AND UserID = ?";
        $count = pdo_query_value($sql, $promotionId, $userId);
        return $count > 0;
    }
    return false;
}

?>