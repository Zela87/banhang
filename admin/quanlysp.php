<?php
require_once __DIR__ . '/../models_admin/product.php';


class ProductController
{
    private $productModel;

    public function __construct($db)
    {
        $this->productModel = new Product($db);
    }

    public function index()
    {
        $products = $this->productModel->getAll();
        return $products;
    }
    public function getCategories()
    {
        $categories = $this->productModel->getAllCategories();
        return $categories;
    }
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category_id = $_POST['category_id'];
            $Content = $_POST['Content'];
            $createdAt = (new DateTime())->format('Y-m-d H:i:s');

            $destinationPath = '';
            $uploadDir = '/greenplanet_copy/assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Ensure the directory exists
            }

            if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == UPLOAD_ERR_OK) {
                $tempFilePath = $_FILES['uploaded_file']['tmp_name'];

                $fileExtension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);

                $newFileName = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_FILENAME) .  '.' . $fileExtension;

                $destinationPath =  $uploadDir . $newFileName;

                if (!move_uploaded_file($tempFilePath, $destinationPath)) {
                    echo "Lỗi ảnh";
                    return;
                }
            }

            $data = [
                'Name' => $_POST['name'],
                'Description' => $_POST['Description'],
                'CategoryID' => $category_id,
                'Price' =>  $_POST['Price'],
                'StockQuantity' => $_POST['StockQuantity'],
                'CreatedAt' => $createdAt,
                'UpdatedAt' => $createdAt,

            ];

            $this->productModel->createProduct($data, $destinationPath, $Content);
            header('Location: /greenplanet_copy/admin/product/list-san-pham.php');
        }
    }

    public function edit($id)
{
    $product = $this->productModel->getById($id);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $updateAt = (new DateTime())->format('Y-m-d H:i:s');
        $category_id = $_POST['category_id'];
        $Content = $_POST['Content'];

        // Sử dụng ảnh hiện tại nếu không có ảnh mới
        $destinationPath = $product['image_url'];
        $uploadDir = '/greenplanet_copy/assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Xử lý nếu có ảnh mới được tải lên
        if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == UPLOAD_ERR_OK) {
            $tempFilePath = $_FILES['uploaded_file']['tmp_name'];
            $fileExtension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
            $newFileName = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_FILENAME) . '.' . $fileExtension;
            $destinationPath = $uploadDir . $newFileName;

            if (!move_uploaded_file($tempFilePath, $destinationPath)) {
                echo "Lỗi ảnh";
                return;
            }
        }

        // Dữ liệu cập nhật sản phẩm
        $data = [
            'ProductID' => $id,
            'Name' => $_POST['name'],
            'Description' => $_POST['Description'],
            'CategoryID' => $category_id,
            'Price' => $_POST['Price'],
            'StockQuantity' => $_POST['StockQuantity'],
            'UpdatedAt' => $updateAt,
        ];
        
        // Cập nhật sản phẩm
        $this->productModel->updateProduct($data, $destinationPath, $Content);

        // Kiểm tra nếu chưa có ảnh trước đó, thì thêm ảnh mới
        if (empty($product['image_url']) && !empty($destinationPath)) {
            $this->productModel->addProductImage($id, $destinationPath);
        }

        header('Location: /greenplanet_copy/admin/product/list-san-pham.php');
    }
    return $product;
}

    


    public function delete($id)
    {
        if ($this->productModel->delete($id)) {
            header('Location: /greenplanet_copy/admin/product/list-san-pham.php');
        } else {
            header('Location: /greenplanet_copy/admin/product/list-san-pham.php');
        }
    }

    public function searchProductsByName($search)
    {
        return $this->productModel->searchProductsByName($search);
    }
}
