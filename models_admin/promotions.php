<?php
include_once __DIR__ . '/db-connect.php';


class Promotions
{
    private $dbconn;

    public function __construct($db)
    {
        $this->dbconn = $db;
    }

    public function create($data)
    {
        if ($this->dbconn === null) {
            try {
                $this->dbconn = connect_db();
            } catch (PDOException $e) {
                throw new Exception("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }
        $this->dbconn->beginTransaction();
        
        try {
            $stmt = $this->dbconn->prepare("INSERT INTO promotions (Title, Description, DiscountPercentage,PromotionTypeID,PromoCode,DiscountAmount,MinOrderValue,UsageLimit,ImageURL, IsActive,StartDate, EndDate) 
        VALUES (:Title, :Description, :DiscountPercentage,:PromotionTypeID,:PromoCode,:DiscountAmount,:MinOrderValue,:UsageLimit,:ImageURL,:IsActive, :StartDate, :EndDate)");

            $stmt->execute($data);

           
            $this->dbconn->commit();
        } catch (Exception $e) {
            $this->dbconn->rollBack();
            throw $e;
        } finally {
            $this->dbconn = null;
        }
    }

    public function update($data)
    {
        if ($this->dbconn === null) {
            try {
                $this->dbconn = connect_db();
            } catch (PDOException $e) {
                throw new Exception("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }
        $this->dbconn->beginTransaction();
        try {
  
            $stmt = $this->dbconn->prepare("UPDATE promotions SET Title = :Title, 
        Description = :Description,
        DiscountPercentage = :DiscountPercentage,
        PromotionTypeID = :PromotionTypeID,
        PromoCode = :PromoCode,
        DiscountAmount = :DiscountAmount,
        MinOrderValue = :MinOrderValue,
        UsageLimit = :UsageLimit,
        ImageURL = :ImageURL,
        IsActive = :IsActive,
        StartDate = :StartDate, EndDate = :EndDate
        WHERE PromotionID  = :PromotionID ");
            $stmt->execute($data);
            
            $this->dbconn->commit();
        } catch (Exception $e) {
            $this->dbconn->rollBack();
            throw $e;
        } finally {
            // Đóng kết nối
            $this->dbconn = null;
        }
    }


    public function delete($id)
    {
        if ($this->dbconn === null) {
            try {
                $this->dbconn = connect_db();
            } catch (PDOException $e) {
                throw new Exception("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }
        $sql = "DELETE FROM promotions WHERE PromotionID = ?";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute([$id]);
        return $result;
    }

    public function getAll()
    {
        if ($this->dbconn === null) {
            try {
                $this->dbconn = connect_db();
            } catch (PDOException $e) {
                throw new Exception("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }
        $sql = "SELECT * FROM promotions ORDER BY CreatedAt DESC";
        $result = $this->dbconn->query($sql)->fetchAll();

        // Đóng kết nối
        $this->dbconn = null;

        return $result;
    }
    public function getAllType()
    {
        if ($this->dbconn === null) {
            try {
                $this->dbconn = connect_db();
            } catch (PDOException $e) {
                throw new Exception("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }
        $sql = "SELECT * FROM promotion_types ORDER BY PromotionTypeID DESC";
        $result = $this->dbconn->query($sql)->fetchAll();

        // Đóng kết nối
        $this->dbconn = null;

        return $result;
    }

    public function getById($id)
    {
        if ($this->dbconn === null) {
            try {
                $this->dbconn = connect_db();
            } catch (PDOException $e) {
                throw new Exception("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }
        $sql = "
           SELECT * FROM promotions
            WHERE PromotionID = ?";

        $stmt = $this->dbconn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

}
