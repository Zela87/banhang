<?php
require_once __DIR__ . '/../models/user.php';

class UserController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new Users($db);
    }

    public function index($id)
    {
        $user = $this->userModel->getProfileAdmin($id);
        return $user;
    }
    public function changePassword($id){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $oldPassword = $_POST['oldPassword'];
            $newPassword = $_POST['newPassword'];
            $rePassword = $_POST['rePassword'];
            $user = $this->userModel->getProfileAdmin($id);
    
            if (!$user) {
                return ['status' => false, 'message' => 'Người dùng không tồn tại'];
            }
    
            // Sử dụng password_verify để kiểm tra mật khẩu cũ
            if (!password_verify($oldPassword, $user['pass'])) {
                return ['status' => false, 'message' => 'Mật khẩu cũ không chính xác'];
            }
    
            if ($newPassword !== $rePassword) {
                return ['status' => false, 'message' => 'Mật khẩu mới và nhập lại mật khẩu không khớp'];
            }
    
            // Mã hóa mật khẩu mới trước khi lưu vào cơ sở dữ liệu
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
            $data = [
                'newPassword' => $hashedPassword,
                'id' => $id,
            ];
    
            $this->userModel->changePassword($data);
    
            return ['status' => true, 'message' => 'Đổi mật khẩu thành công'];
        }
        return ['status' => false, 'message' => 'Đã xảy ra lỗi'];
    }

public function update($id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hoten = $_POST['hoten'];
        $email = $_POST['email'];
        $sdt = $_POST['sdt'];
        $diachi = $_POST['diachi'];
        $gioitinh = $_POST['gioitinh'];
        $ngaysinh = $_POST['ngaysinh'];

        // Use an absolute path for uploads
        $uploadDir = '/greenplanet_copy/assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destinationPath = null; // Ensure it's null by default
        if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == UPLOAD_ERR_OK) {
            $tempFilePath = $_FILES['uploaded_file']['tmp_name'];

            $fileExtension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
            $newFileName = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_FILENAME) .  '.' . $fileExtension;

            $destinationPath = $uploadDir . $newFileName;

            if (!move_uploaded_file($tempFilePath, $destinationPath)) {
                echo "Lỗi ảnh";
                return;
            }

            // Make the destination path relative for storing in the database
            $destinationPath = '/greenplanet_copy/assets/images/' . $newFileName;
        }

        $data = [
            'hoten' => $hoten,
            'email' => $email,
            'sdt' => $sdt,
            'diachi' => $diachi,
            'gioitinh' => $gioitinh,
            'ngaysinh' => $ngaysinh,
            'avatar' => $destinationPath
        ];

        $result = $this->userModel->updateUser($id, $data);
        header('Location: /greenplanet_copy/admin/profile/ho-so.php');
    }
}

}

