<?php
require_once __DIR__ . '/../models_admin/discounts.php';

class DiscountsController
{
    private $discountsModel;

    public function __construct($db)
    {
        $this->discountsModel = new Discounts($db);
    }

    public function index()
    {
        $discounts = $this->discountsModel->getAll();
        return $discounts;
    }
    public function getAllProducts()
    {
        $products = $this->discountsModel->AllProducts();
        return $products;
    }
    public function add()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ProductID = $_POST['ProductID'];
        $DiscountPercentage = !empty($_POST['DiscountPercentage']) ? $_POST['DiscountPercentage'] : null;
        $DiscountAmount = !empty($_POST['DiscountAmount']) ? $_POST['DiscountAmount'] : null;
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];

        $data = [
            'ProductID' => $ProductID,
            'DiscountPercentage' => $DiscountPercentage,
            'DiscountAmount' => $DiscountAmount,
            'StartDate' => $StartDate,
            'EndDate' => $EndDate,
        ];

        $this->discountsModel->create($data);
        header('Location: /greenplanet_copy/admin/discounts/list-san-pham.php');
    }
}

public function edit($id)
{
    $discounts = $this->discountsModel->getById($id);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $DiscountID = $id;
        $ProductID = $_POST['ProductID'];
        $DiscountPercentage = !empty($_POST['DiscountPercentage']) ? $_POST['DiscountPercentage'] : null;
        $DiscountAmount = !empty($_POST['DiscountAmount']) ? $_POST['DiscountAmount'] : null;
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];

        $data = [
            'DiscountID' => $DiscountID,
            'ProductID' => $ProductID,
            'DiscountPercentage' => $DiscountPercentage,
            'DiscountAmount' => $DiscountAmount,
            'StartDate' => $StartDate,
            'EndDate' => $EndDate,
        ];

        $this->discountsModel->update($data);

        header('Location: /greenplanet_copy/admin/discounts/list-san-pham.php');
    }
    return $discounts;
}


    public function delete($id)
    {
        if ($this->discountsModel->delete($id)) {
            header('Location: /greenplanet_copy/admin/discounts/list-san-pham.php');
        } else {
            header('Location: /greenplanet_copy/admin/discounts/list-san-pham.php');
        }
    }

}