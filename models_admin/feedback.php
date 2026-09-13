<?php
include __DIR__ . '/db-connect.php';

class Feedback
{
    private $dbconn;

    public function __construct($db)
    {
        $this->dbconn = $db;
    }

    public function getAll()
    {
        $sql = "
        SELECT 
            f.FeedbackID, 
            u.hoten AS hoten, 
            u.Avatar AS user_image, 
            p.Name AS ProductName, 
            pi.ImageURL AS product_image, 
            f.ProductRating, 
            f.Comment, 
            f.CreatedAt
        FROM 
            feedback f
        INNER JOIN 
            users u ON f.UserID = u.id_user
        INNER JOIN 
            products p ON f.ProductID = p.ProductID
        INNER JOIN 
            productimages pi ON p.ProductID = pi.ProductID  
        ORDER BY 
            f.CreatedAt DESC
    ";
    
        
        $result = $this->dbconn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
        $this->dbconn = null;
    
        return $result;
    }
    public function fillterFeedbacks($rating = null) {
        $sql = "
            SELECT 
                f.FeedbackID, 
                u.hoten AS hoten, 
                u.Avatar AS user_image, 
                p.Name AS ProductName, 
                pi.ImageURL AS product_image, 
                f.ProductRating, 
                f.Comment, 
                f.CreatedAt
            FROM 
                feedback f
            INNER JOIN 
                users u ON f.UserID = u.id_user
            INNER JOIN 
                products p ON f.ProductID = p.ProductID
            INNER JOIN 
                productimages pi ON p.ProductID = pi.ProductID  
        ";

        if ($rating !== null) {
            $sql .= " WHERE f.ProductRating = :rating";
        }

        $sql .= " ORDER BY f.CreatedAt DESC";

        $stmt = $this->dbconn->prepare($sql);

        if ($rating !== null) {
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>