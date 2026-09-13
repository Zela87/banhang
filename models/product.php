<?php

function get_product($ProductID){
    $sql = "SELECT * FROM products WHERE ProductID = ?";
    return pdo_query_one($sql, $ProductID);
}

function get_all_products_home(){
    $sql = "SELECT * FROM products LIMIT 4";
    return pdo_query($sql);
}

function get_products_with_discounts(){
    $sql = "SELECT 
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
    products p
LEFT JOIN 
    discounts d ON p.ProductID = d.ProductID 
    AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
JOIN 
    productimages pi ON pi.ProductID = p.ProductID
WHERE 
    pi.IsPrimary = 1
                LIMIT 8";

    return pdo_query($sql);
}
function get_products_sale() {
    $sql = "SELECT 
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
                products p
            LEFT JOIN 
                discounts d ON p.ProductID = d.ProductID 
                AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
            JOIN 
                productimages pi ON pi.ProductID = p.ProductID
            WHERE 
                pi.IsPrimary = 1
                AND (d.DiscountPercentage IS NOT NULL OR d.DiscountAmount IS NOT NULL)"; 

    return pdo_query($sql);
}

function get_newest_products_with_discounts(){
    $sql = "SELECT 
            p.*,
            d.DiscountPercentage,
            d.DiscountAmount,
            pi.ImageURL,
            CASE 
                WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN p.Price - d.DiscountAmount
                WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN p.Price - d.DiscountAmount
            ELSE p.Price 
                END AS CurrentPrice
            FROM 
                products p
            LEFT JOIN 
                discounts d ON p.ProductID = d.ProductID 
                AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
            JOIN 
                productimages pi ON pi.ProductID = p.ProductID
            WHERE 
                pi.IsPrimary = 1
            ORDER BY p.CreatedAt DESC
            LIMIT 8";

    return pdo_query($sql);
}

function searchProducts($keyword) {
    $sql = "SELECT p.*,
            d.DiscountPercentage,
            d.DiscountAmount,
            pi.ImageURL,
            CASE 
                WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                ELSE p.Price 
            END AS CurrentPrice 
            FROM products p
            LEFT JOIN 
                    discounts d ON p.ProductID = d.ProductID 
                    AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
                JOIN 
                    productimages pi ON pi.ProductID = p.ProductID
            JOIN categories c ON p.CategoryID = c.CategoryID
            WHERE p.Name LIKE ? OR c.Name LIKE ? and pi.IsPrimary = 1";
    $keyword = "%$keyword%";
    
    return pdo_query($sql, $keyword, $keyword);
}



function get_recommended_products()
{
    $sql = "SELECT p.*, pi.ImageURL, d.DiscountPercentage,
                d.DiscountAmount,
                CASE 
                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN p.Price * (1 - d.DiscountPercentage / 100)
        WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN p.Price - d.DiscountAmount
                    ELSE p.Price 
                END AS CurrentPrice
                FROM products p
                LEFT JOIN discounts d ON p.ProductID = d.ProductID
                JOIN productimages pi ON pi.ProductID = p.ProductID
                WHERE pi.IsPrimary = 1"; // Giới hạn 8 sản phẩm yêu thích
    return pdo_query($sql);
}

function getFilteredProducts($priceFilter = [], $sortOrder = 'name_asc')
{

    $filterCondition = '';
    $sortCondition = 'ORDER BY p.Name ASC'; // Mặc định sắp xếp theo tên

    // Xử lý bộ lọc giá
    if (!empty($priceFilter) && !in_array('all', $priceFilter)) {
        $filterCondition .= ' AND (';
        foreach ($priceFilter as $priceRange) {
            switch ($priceRange) {
                case 'under100':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) < 100000 OR';
                    break;
                case '100to200':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) >= 100000 AND (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) < 200000 OR';
                    break;
                case '200to500':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) >= 200000 AND (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) < 500000 OR';
                    break;
                case '500to1000':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) >= 500000 AND (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) < 1000000 OR';
                    break;
                case '1000to3000':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) >= 1000000 AND (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) < 3000000 OR';
                    break;
                case '3000to5000':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) >= 3000000 AND (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) < 5000000 OR';
                    break;
                case 'above5000':
                    $filterCondition .= ' (CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price END) >= 5000000 OR';
                    break;
            }
        }
        $filterCondition = rtrim($filterCondition, 'OR') . ')';
    }

    // Xử lý sắp xếp sản phẩm
    switch ($sortOrder) {
        case 'name_desc':
            $sortCondition = 'ORDER BY p.Name DESC';
            break;
        case 'price_asc':
            $sortCondition = 'ORDER BY (CASE 
                WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                ELSE p.Price END) ASC';
            break;
        case 'price_desc':
            $sortCondition = 'ORDER BY (CASE 
                WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                ELSE p.Price END) DESC';
            break;
    }

    // Truy vấn sản phẩm từ database
    $sql = "SELECT 
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
                products p
            LEFT JOIN 
                discounts d ON p.ProductID = d.ProductID 
                AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
            JOIN 
                productimages pi ON pi.ProductID = p.ProductID
            WHERE 
                pi.IsPrimary = 1" . $filterCondition . " " . $sortCondition;

    // Chuẩn bị và thực thi truy vấn
    return pdo_query($sql);
}


function get_all_products()
{
    $sql = "SELECT 
                    p.*,
                    d.DiscountPercentage,
                    d.DiscountAmount,
                    pi.ImageURL,
                    CASE 
                        WHEN d.DiscountPercentage IS NOT NULL THEN p.Price * (1 - d.DiscountPercentage / 100)
                        when d.DiscountAmount IS NOT NULL THEN p.Price - d.DiscountAmount
                        ELSE p.Price 
                    END AS CurrentPrice
                FROM 
                    products p
                LEFT JOIN 
                    discounts d ON p.ProductID = d.ProductID 
                    AND CURRENT_DATE BETWEEN d.StartDate AND d.EndDate
                JOIN 
                    productimages pi ON pi.ProductID = p.ProductID
                WHERE 
                    pi.IsPrimary = 1";

    return pdo_query($sql);
}

function getProductDetails($ProductID)
{
    $query = "SELECT p.*, c.Name AS CategoryName, d.DiscountPercentage,
    d.DiscountAmount,
                CASE 
                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN p.Price * (1 - d.DiscountPercentage / 100)
        WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN p.Price - d.DiscountAmount
                    ELSE p.Price 
                END AS CurrentPrice,
                pi.ImageURL AS PrimaryImageURL
                FROM products p
                LEFT JOIN discounts d ON p.ProductID = d.ProductID
                JOIN productimages pi ON pi.ProductID = p.ProductID
                JOIN categories c ON p.CategoryID = c.CategoryID 
                WHERE p.ProductID = ?";

    return pdo_query_one($query, $ProductID) ?: null;
}


function addProductFeedback($userID, $ProductID, $productRating, $comment)
{
    $query = "INSERT INTO feedback (UserID, ProductID, ProductRating, Comment, CreatedAt) 
              VALUES (?, ?, ?, ?, NOW())";
    return pdo_execute($query, $userID, $ProductID, $productRating, $comment);
}

function updateProductStock($productId, $quantity) {
    $sql = "UPDATE products SET StockQuantity = StockQuantity - ? WHERE ProductID = ?";
    pdo_execute($sql, $quantity, $productId);
}

function checkProductStock($productId) {
    $sql = "SELECT StockQuantity FROM products WHERE ProductID = ?";
    pdo_execute($sql, $productId);
}