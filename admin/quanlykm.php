<?php
require_once __DIR__ . '/../models_admin/promotions.php';

class PromotionsController
{
    private $dbconn;
    private $promotionsModel;

    public function __construct($db)
    {
        $this->dbconn = $db; // Thêm dòng này để khởi tạo `$dbconn`
        $this->promotionsModel = new Promotions($db);
    }

    public function getTypeAll() {
        $sql = "SELECT * FROM promotion_types";
        $stmt = $this->dbconn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function index()
    {
        $promotions = $this->promotionsModel->getAll();
        return $promotions;
    }
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'Title' => $_POST['Title'] ?? '',
                'Description' => $_POST['Description'] ?? '',
                'DiscountPercentage' => $_POST['DiscountPercentage'] ?? 0,
                'PromotionTypeID' => $_POST['PromotionTypeID'] ?? 1,
                'PromoCode' => $_POST['PromoCode'] ?? '',
                'DiscountAmount' => $_POST['DiscountAmount'] ?? 0,
                'MinOrderValue' => $_POST['MinOrderValue'] ?? 0,
                'UsageLimit' => $_POST['UsageLimit'] ?? 0,
                'ImageURL' => $_POST['ImageURL'] ?? '',
                'IsActive' => $_POST['IsActive'] ?? 1,
                'StartDate' => $_POST['StartDate'],
                'EndDate' => $_POST['EndDate']
            ];

            $this->promotionsModel->create($data);
            header('Location: /greenplanet_copy/admin/promotions/list-san-pham.php');
        }
    }


    public function edit($id)
{
    $promotions = $this->promotionsModel->getById($id);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $PromotionID = $id;
        $data = [
            'PromotionID' => $PromotionID,
            'Title' => $_POST['Title'] ?? '',
            'Description' => $_POST['Description'] ?? '',
            'DiscountPercentage' => $_POST['DiscountPercentage'] ?? 0,
            'PromotionTypeID' => $_POST['PromotionTypeID'] ?? 1,
            'PromoCode' => $_POST['PromoCode'] ?? '',
            'DiscountAmount' => $_POST['DiscountAmount'] ?? 0,
            'MinOrderValue' => $_POST['MinOrderValue'] ?? 0,
            'UsageLimit' => $_POST['UsageLimit'] ?? 0,
            'ImageURL' => $_POST['ImageURL'] ?? '',
            'IsActive' => $_POST['IsActive'] ?? 1,
            'StartDate' => $_POST['StartDate'],
            'EndDate' => $_POST['EndDate']
        ];
        if (empty($_POST['Title']) || empty($_POST['StartDate']) || empty($_POST['EndDate'])) {
            echo "Vui lòng điền đầy đủ các trường bắt buộc.";
            return;
        }        
        // Cập nhật khuyến mãi
        $this->promotionsModel->update($data);

        // Chuyển hướng sau khi cập nhật thành công
        header('Location: /greenplanet_copy/admin/promotions/list-san-pham.php');
    }
    return $promotions;
}


    public function delete($id)
    {
        if ($this->promotionsModel->delete($id)) {
            header('Location: /greenplanet_copy/admin/promotions/list-san-pham.php');
        } else {
            header('Location: /greenplanet_copy/admin/promotions/list-san-pham.php');
        }
    }
}
