<?php
include __DIR__ . '/db-connect.php';


class Discounts
{
    private $dbconn;

    public function __construct($db)
    {
        $this->dbconn = $db;
    }
    public function AllProducts()
    {
        $sql = "SELECT ProductID, Name AS ProductName FROM products ORDER BY ProductID DESC";
        $result = $this->dbconn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
    public function create($data)
{
    $this->dbconn->beginTransaction();

    try {
        $stmt = $this->dbconn->prepare("INSERT INTO discounts (ProductID, DiscountPercentage, DiscountAmount, StartDate, EndDate) 
            VALUES (:ProductID, :DiscountPercentage, :DiscountAmount, :StartDate, :EndDate)");

        $stmt->execute([
            'ProductID' => $data['ProductID'],
            'DiscountPercentage' => $data['DiscountPercentage'],
            'DiscountAmount' => $data['DiscountAmount'],
            'StartDate' => $data['StartDate'],
            'EndDate' => $data['EndDate'],
        ]);

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
    $this->dbconn->beginTransaction();
    try {
        $stmt = $this->dbconn->prepare("UPDATE discounts SET ProductID = :ProductID, 
            DiscountPercentage = :DiscountPercentage, 
            DiscountAmount = :DiscountAmount,
            StartDate = :StartDate, EndDate = :EndDate
            WHERE DiscountID = :DiscountID");

        $stmt->execute([
            'ProductID' => $data['ProductID'],
            'DiscountPercentage' => $data['DiscountPercentage'],
            'DiscountAmount' => $data['DiscountAmount'],
            'StartDate' => $data['StartDate'],
            'EndDate' => $data['EndDate'],
            'DiscountID' => $data['DiscountID'],
        ]);

        $this->dbconn->commit();
    } catch (Exception $e) {
        $this->dbconn->rollBack();
        throw $e;
    } finally {
        $this->dbconn = null;
    }
}



    public function delete($id)
    {

        $sql = "DELETE FROM discounts WHERE DiscountID = ?";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute([$id]);
        return $result;
    }

    public function getAll()
    {
        $sql = "SELECT d.*, p.Name AS ProductName
        FROM discounts d 
        INNER JOIN products p ON d.ProductID = p.ProductID

        ORDER BY d.CreatedAt DESC; ";

        $result = $this->dbconn->query($sql)->fetchAll();

        $this->dbconn = null;

        return $result;
    }


    public function getById($id)
    {
        $sql = "
           SELECT * FROM discounts
            WHERE DiscountID = ?";

        $stmt = $this->dbconn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
