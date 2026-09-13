<?php
if (!function_exists('connect_db')) {
    function connect_db() {
        try {
            $dbconn = new PDO("mysql:host=localhost:3307;dbname=greenplanet", "root", "");
            $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbconn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbconn->exec("SET NAMES 'utf8'");
            return $dbconn;
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
}

if (!function_exists('disconnect_db')) {
    function disconnect_db(&$dbconn) {
        $dbconn = null;
    }
}

    function getProducts($conn, $filterCondition = '', $categoryID = '') {
        // Truy vấn SQL để lấy sản phẩm
        $sql = "SELECT products.*, productimages.ImageURL 
                FROM products 
                JOIN productimages ON products.ProductID = productimages.ProductID 
                WHERE productimages.IsPrimary = 1" . $filterCondition;
    
        $stmt = $conn->prepare($sql);
    
        // Gán tham số cho truy vấn nếu có categoryID
        if (!empty($categoryID)) {
            $stmt->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>