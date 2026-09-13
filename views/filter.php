<?php
include_once '../models_admin/db-connect.php';

$conn = connect_db(); // Kết nối cơ sở dữ liệu

// Khởi tạo biến toàn cục
$priceFilter = [];
$filterCondition = '';
$categoryID = '';

// Hàm để tạo điều kiện lọc giá
function createPriceFilterCondition($priceFilter)
{
    if (empty($priceFilter) || in_array('all', $priceFilter)) {
        return '';
    }

    $conditions = [];
    foreach ($priceFilter as $priceRange) {
        switch ($priceRange) {
            case "under100":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) < 100000";
                break;
            case "100to200":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) BETWEEN 100000 AND 200000";
                break;
            case "200to500":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) BETWEEN 200000 AND 500000";
                break;
            case "500to1000":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) BETWEEN 500000 AND 1000000";
                break;
            case "1000to3000":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) BETWEEN 1000000 AND 3000000";
                break;
            case "3000to5000":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) BETWEEN 3000000 AND 5000000";
                break;
            case "above5000":
                $conditions[] = "(CASE 
                                    WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                                    WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                                    ELSE products.Price 
                                END) >= 5000000";
                break;
        }
    }
    return ' AND (' . implode(' OR ', $conditions) . ')';
}

// Hàm để tạo điều kiện sắp xếp
function createSortCondition($sort)
{
    switch ($sort) {
        case 'name_asc':
            return 'ORDER BY products.Name ASC';
        case 'name_desc':
            return 'ORDER BY products.Name DESC';
        case 'price_asc':
            return 'ORDER BY CurrentPrice ASC';
        case 'price_desc':
            return 'ORDER BY CurrentPrice DESC';
        default:
            return 'ORDER BY products.Name ASC';
    }
}

// Hàm để hiển thị sản phẩm
function displayProducts($products)
{
    global $img_path;
    if (count($products) > 0) {
        foreach ($products as $product) {
?>
            <form action="index.php?act=addToCart" method="post">
                <div class="product-card">
                    <?php if (!empty($product['DiscountPercentage'])) : ?>
                        <div class="sale">
                            <p>-<?= htmlspecialchars($product['DiscountPercentage']) ?>%</p>
                        </div>
                    <?php endif; ?>

                    <a href="index.php?act=chitietsp&ProductID=<?= htmlspecialchars($product['ProductID']) ?>">
                        <div class="img">
                            <img src="<?= htmlspecialchars($img_path . $product['ImageURL']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>">
                        </div>
                    </a>

                    <a href="index.php?act=chitietsp&ProductID=<?= htmlspecialchars($product['ProductID']) ?>">
                        <p class="name_product"><?= htmlspecialchars($product['Name']) ?></p>
                    </a>
                    <?php if ($product['StockQuantity'] > 0) : ?>
                        <div class="price">
                            <?php if (!empty($product['DiscountPercentage']) || !empty($product['DiscountAmount'])) : ?>
                                <p class="original_price"><?= number_format($product['Price'], 0, ',', '.') ?>đ</p>
                                <p class="current_price"><?= number_format($product['CurrentPrice'], 0, ',', '.') ?>đ</p>
                            <?php else : ?>
                                <p class="current_price"><?= number_format($product['Price'], 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="ProductID" value="<?= htmlspecialchars($product['ProductID']) ?>">
                        <input type="hidden" name="Name" value="<?= htmlspecialchars($product['Name']) ?>">
                        <input type="hidden" name="Quantity" value="1">
                        <input type="submit" value="Thêm vào giỏ" name="addToCart">
                    <?php else : ?>
                        <p class="out-of-stock">Hết hàng</p>
                    <?php endif; ?>
                </div>
            </form>
<?php
        }
    } else {
        echo '<p>Không có sản phẩm nào để hiển thị</p>';
    }
}

// Xử lý yêu cầu lọc sản phẩm và sắp xếp
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy danh mục và bộ lọc giá
    if (!empty($_POST['categoryID'])) {
        $categoryID = $_POST['categoryID'];
        $filterCondition .= ' AND products.CategoryID = :categoryID';
    }

    if (!empty($_POST['filter'])) {
        $priceFilter = $_POST['filter'];
        $filterCondition .= createPriceFilterCondition($priceFilter);
    }

    // Xử lý sắp xếp
    $sortCondition = createSortCondition($_POST['sort'] ?? '');

    // Truy vấn SQL để lấy sản phẩm
    $sql = "SELECT products.*, 
            CASE 
                WHEN d.DiscountPercentage IS NOT NULL AND d.DiscountPercentage > 0 THEN products.Price * (1 - d.DiscountPercentage / 100)
                WHEN d.DiscountAmount IS NOT NULL AND d.DiscountAmount > 0 THEN products.Price - d.DiscountAmount
                ELSE products.Price 
            END AS CurrentPrice,
            productimages.ImageURL, d.DiscountPercentage, d.DiscountAmount
            FROM products 
            JOIN productimages ON products.ProductID = productimages.ProductID 
            LEFT JOIN discounts d ON products.ProductID = d.ProductID
            WHERE productimages.IsPrimary = 1" . $filterCondition . " " . $sortCondition;

    // Chuẩn bị và thực thi truy vấn
    $stmt = $conn->prepare($sql);

    // Gán tham số cho truy vấn
    if (!empty($categoryID)) {
        $stmt->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hiển thị sản phẩm
    displayProducts($result);
}

// Lấy danh mục sản phẩm từ cơ sở dữ liệu
$categorySql = "SELECT CategoryID, Name FROM categories ORDER BY CategoryID";
$categoryStmt = $conn->prepare($categorySql);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>