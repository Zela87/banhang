<?php
include __DIR__ . '/db-connect.php';


class Product
{
    private $dbconn;

    public function __construct($db)
    {
        $this->dbconn = $db;
    }

    public function createProduct($data, $image,$content)
    {
        $this->dbconn->beginTransaction();

        try {
            $stmt = $this->dbconn->prepare("INSERT INTO products (Name, Description, CategoryID, Price, StockQuantity, CreatedAt, UpdatedAt) 
        VALUES (:Name, :Description, :CategoryID, :Price, :StockQuantity, :CreatedAt, :UpdatedAt)");

            $stmt->execute($data);

            $productId = $this->dbconn->lastInsertId();

            if (!empty($image)) {
                $this->addProductImage($productId, $image);
            }
            if (!empty($content)) {
                $this->addProductInfo($productId, $content);
            }
            $this->dbconn->commit();
        } catch (Exception $e) {
            $this->dbconn->rollBack();
            throw $e;
        } finally {
            $this->dbconn = null;
        }
    }
    private function addProductInfo($productId, $content)
    {
        $stmt = $this->dbconn->prepare("INSERT INTO product_info (ProductID, Content) VALUES (:ProductID, :Content)");
    
        $stmt->execute([
            ':ProductID' => $productId,
            ':Content' => $content,
        ]);
    }

    public function updateProduct($data, $image, $content) {
        $productId = $data['ProductID'];
        $this->dbconn->beginTransaction();
        try {
            // Cập nhật sản phẩm chính
            $stmt = $this->dbconn->prepare("UPDATE products SET Name = :Name, 
              Description = :Description, CategoryID = :CategoryID, 
              Price = :Price, StockQuantity = :StockQuantity, UpdatedAt = :UpdatedAt 
              WHERE ProductID = :ProductID");
            $stmt->execute($data);
    
            // Cập nhật ảnh sản phẩm nếu có
            if (!empty($image)) {
                $this->UpdateProductImage($productId, $image);
            }
    
            // Cập nhật thông tin bổ sung
            if (!empty($content)) {
                $this->updateProductInfo($productId, $content);
            }
    
            $this->dbconn->commit();
        } catch (Exception $e) {
            $this->dbconn->rollBack();
            throw $e;
        }
    }
    
    
    private function updateProductInfo($productId, $content)
    {
        $stmt = $this->dbconn->prepare("SELECT COUNT(*) FROM product_info WHERE ProductID = :ProductID");
        $stmt->execute([':ProductID' => $productId]);
        $exists = $stmt->fetchColumn();
        if ($exists > 0) {
            $stmt = $this->dbconn->prepare("UPDATE product_info SET Content = :Content WHERE ProductID = :ProductID");
            
            $stmt->execute([
                ':Content' => $content,
                ':ProductID' => $productId,
            ]);
        } else {
            $this->addProductInfo($productId, $content);
        }
    }
    public function delete($id)
    {
        $this->dbconn->beginTransaction();
        try {
            $sqlDiscounts = "DELETE FROM discounts WHERE ProductID = ?";
            $discounts = $this->dbconn->prepare($sqlDiscounts);
            $discounts->execute([$id]);

            $sqlproduct_info = "DELETE FROM product_info WHERE ProductID = ?";
            $product_info = $this->dbconn->prepare($sqlproduct_info);
            $product_info->execute([$id]);

            $sqlFeedback = "DELETE FROM feedback WHERE ProductID = ?";
            $feedback = $this->dbconn->prepare($sqlFeedback);
            $feedback->execute([$id]);

            $sqlProductimages = "DELETE FROM productimages WHERE ProductID = ?";
            $productimages = $this->dbconn->prepare($sqlProductimages);
            $productimages->execute([$id]);

            $sqlproduct_info = "DELETE FROM product_info WHERE ProductID = ?";
            $product_info = $this->dbconn->prepare($sqlproduct_info);
            $product_info->execute([$id]);

            $sql = "DELETE FROM products WHERE ProductID = ?";
            $stmt = $this->dbconn->prepare($sql);
            $result = $stmt->execute([$id]);
            $this->dbconn->commit();

            return $result;
        } catch (Exception $e) {
            $this->dbconn->rollBack();
            throw $e;
        }finally {
            $this->dbconn = null;
        }
    }

    public function getAll()
    {

        $sql = "
        SELECT p.*, c.name AS category_name, pi.ImageURL AS image_url 
        FROM products p
        LEFT JOIN categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN productimages pi ON p.ProductID = pi.ProductID AND pi.IsPrimary = 1
        ORDER BY p.CreatedAt DESC
    ";
        $result = $this->dbconn->query($sql)->fetchAll();

        // Đóng kết nối
        $this->dbconn = null;

        return $result;
    }


    public function getById($id)
    {
        $sql = "
            SELECT p.*, pi.ImageUrl AS image_url,info.Content AS Content
            FROM products p 
            LEFT JOIN productimages pi ON p.ProductID = pi.ProductID AND pi.IsPrimary = 1
            LEFT JOIN product_info info ON p.ProductID = info.ProductID  
            WHERE p.ProductID = ?";

        $stmt = $this->dbconn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }


    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories";
        return $this->dbconn->query($sql)->fetchAll();
    }
    public function addProductImage($productId, $image)
    {
        $stmt = $this->dbconn->prepare("INSERT INTO productimages (ProductID, ImageURL,IsPrimary) VALUES (:ProductID, :ImageURL,:IsPrimary )");

        return $stmt->execute(['ProductID' => $productId, 'ImageURL' => $image, 'IsPrimary' => 1]);
    }
    public function UpdateProductImage($productId, $image) {
        $stmt = $this->dbconn->prepare("UPDATE productimages SET ImageURL = :ImageURL, IsPrimary = :IsPrimary WHERE ProductID = :ProductID");
        return $stmt->execute([ 'ImageURL' => $image, 'IsPrimary' => 1, 'ProductID' => $productId]);
    }    
    
    public function searchProductsByName($search)
    {
        $stmt = $this->dbconn->prepare("SELECT p.*, pi.ImageUrl AS image_url, c.name AS category_name
                                    FROM products p 
                                    LEFT JOIN categories c ON p.CategoryID = c.CategoryID
                                    LEFT JOIN productimages pi ON p.ProductID = pi.ProductID AND pi.IsPrimary = 1
                                    LEFT JOIN product_info info ON p.ProductID = info.ProductID  
                                    WHERE p.Name LIKE ?");
        $stmt->execute(['%' . $search . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
