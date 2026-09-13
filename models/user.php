<?php

function check_user($email, $pw) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $user = pdo_query_one($sql, $email);

    // Kiểm tra nếu người dùng tồn tại và mật khẩu khớp
    if ($user && password_verify($pw, $user['pass'])) {
        // Lưu toàn bộ thông tin người dùng vào một mảng
        $user_data = [
            'id_user' => $user['id_user'],
            'email' => $user['email'],
            'hoten' => $user['hoten'],
            'sdt' => $user['sdt'],
            'diachi' => $user['diachi'],
            'gioitinh' => $user['gioitinh'],
            'ngaysinh' => $user['ngaysinh'],
            'avatar' => $user['Avatar'],
            'role' => $user['Role']
        ];

        // Lưu mảng người dùng vào session
        $_SESSION['user'] = $user_data;

        // Đặt các giá trị cụ thể để sử dụng nhanh nếu cần
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['role'] = $user['Role'];

        return true;
    } else {
        // Nếu không đăng nhập thành công
        return false;
    }
}


function check_user_role($dbconn, $email, $pw)
{
    try {
        $stmt = $dbconn->prepare("SELECT pass, role FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($result && password_verify($pw, $result['pass'])) {
            $_SESSION['user'] = $result; // Store the result in the session
            return $result['role'];
        } else {
            return null;
        }
    } catch (PDOException $e) {
        error_log("Database error in check_user: " . $e->getMessage());
        return null;
    }
}

function add_user($hoten, $email, $pw) {
    // Kiểm tra xem email đã tồn tại chưa
    $sql_check = "SELECT COUNT(*) FROM users WHERE email = ?";
    $count = pdo_query_value($sql_check, $email);
    
    if ($count > 0) {
        return "Email đã được sử dụng.";
    } else {
        // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
        $hashed_pw = password_hash($pw, PASSWORD_BCRYPT);
        
        // Chuẩn bị truy vấn thêm người dùng
        $sql_insert = "INSERT INTO users (hoten, email, pass, Role) VALUES (?, ?, ?, 0)";
        $result = pdo_execute($sql_insert, $hoten, $email, $hashed_pw);
        
        if ($result) {
            return true; // Nếu thêm thành công
        } else {
            return "Không thể thêm người dùng."; // Nếu thêm không thành công
        }
    }
}

function get_user($id_user) {
        $sql = "SELECT * FROM users WHERE id_user = ?";
        $result = pdo_query_one($sql, $id_user);
        
        // Kiểm tra nếu không tìm thấy người dùng nào
        if ($result === false) {
            error_log("Không tìm thấy người dùng với ID: " . $id_user);
            return null;
        }
        
        return $result;
}


function get_user_email($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $result = pdo_query_one($sql, $email);
        return $result;
}

function update_user($id_user, $hoten, $email, $sdt, $diachi, $gioitinh, $ngaysinh, $profile_image) {
        // Nếu không có ảnh mới, giữ nguyên ảnh cũ
        if (empty($profile_image)) {
            $sql = "UPDATE users SET hoten = ?, email = ?, sdt = ?, diachi = ?, gioitinh = ?, ngaysinh = ? WHERE id_user = ?";
            pdo_execute($sql, $hoten, $email, $sdt, $diachi, $gioitinh, $ngaysinh, $id_user);
        } else {
            $sql = "UPDATE users SET hoten = ?, email = ?, sdt = ?, diachi = ?, gioitinh = ?, ngaysinh = ?, Avatar = ? WHERE id_user = ?";
            pdo_execute($sql, $hoten, $email, $sdt, $diachi, $gioitinh, $ngaysinh, $profile_image, $id_user);
        }
        
        return true;
}

function update_password($id_user, $current_password, $new_password) {
        // Lấy thông tin người dùng dựa vào id_user
        $sql = "SELECT pass FROM users WHERE id_user = ?";
        $user = pdo_query_one($sql, $id_user);

        // Kiểm tra mật khẩu hiện tại
        if ($user && password_verify($current_password, $user['pass'])) {
            // Kiểm tra nếu mật khẩu mới trùng với mật khẩu hiện tại
            if (password_verify($new_password, $user['pass'])) {
                return "Mật khẩu mới không được trùng với mật khẩu hiện tại.";
            }

            // Mã hóa mật khẩu mới trước khi lưu vào cơ sở dữ liệu
            $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Cập nhật mật khẩu mới vào cơ sở dữ liệu
            $sql_update = "UPDATE users SET pass = ? WHERE id_user = ?";
            pdo_execute($sql_update, $hashed_new_password, $id_user);

            return true;  // Đổi mật khẩu thành công
        } else {
            return "Mật khẩu hiện tại không đúng.";  // Mật khẩu hiện tại không khớp
        }
    }

    function dangxuat(){
        session_unset();
        session_destroy();
        header('Location: /greenplanet_copy/views/index.php');
        exit();
    }

    function dangnhap() {
        if (isset($_POST['dangnhap']) && $_POST['dangnhap']) {
            $email = $_POST['email'];
            $pw = $_POST['pw'];
    
            $login_success = check_user($email, $pw);
    
            if ($login_success) {
                // $_SESSION['user'] = $user_data;
                if ($_SESSION['role'] == 1) {
                    // Redirect to admin page if role is 1 (admin)
                    header('Location: /greenplanet_copy/admin/product/list-san-pham.php');  
                    exit();
                } else {
                    // Redirect to user home page for all other roles
                    header('Location: /greenplanet_copy/views/index.php');
                    exit();
                }
            } else {
                return "Email hoặc mật khẩu không đúng!";
            }
        }
        return '';
    }
    

    function dangky(){
        if (isset($_POST['dangky']) && $_POST['dangky']) {
            $hoten = $_POST['hoten'];
            $email = $_POST['email'];
            $pw = $_POST['pw'];
            $re_pw = $_POST['re_pw'];
    
            if (empty($hoten) || empty($email) || empty($pw) || empty($re_pw)) {
                return "Vui lòng điền đầy đủ thông tin.";
            } elseif ($pw != $re_pw) {
                return "Mật khẩu nhập lại không khớp.";
            } elseif (strlen($pw) < 8) {
                return "Mật khẩu phải ít nhất 8 ký tự.";
            } else {
                $result = add_user($hoten, $email, $pw);
                if ($result === true) {
                    $_SESSION['user'] = [
                        'id_user' => $result['id_user'],
                        'email' => $email,
                        'hoten' => $hoten,
                        'role' => 0
                    ];
                    header('Location: index.php?act=trangchu');
                    exit();
                } else {
                    return $result;
                }
            }
        }
        return '';
    }

    

    class Users
    {
        private $dbconn;
    
        public function __construct($db)
        {
            $this->dbconn = $db;
        }
    
        public function getProfileAdmin($id)
        {
            $sql = "SELECT * FROM users WHERE id_user = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
        public function changePassword($data) {
            try {
                $updateStmt = $this->dbconn->prepare("UPDATE users SET pass = :pass WHERE id_user = :id_user");
                $updateStmt->bindParam(':pass', $data['newPassword'], PDO::PARAM_STR);
                $updateStmt->bindParam(':id_user', $data['id'], PDO::PARAM_INT);
        
                return $updateStmt->execute(); 
            } catch (PDOException $e) {
                return false; 
            }
        }
        
        public function updateUser($id, $data) {
            $stmt = $this->dbconn->prepare("UPDATE users SET 
                hoten = :hoten, 
                email = :email, 
                sdt = :sdt, 
                diachi = :diachi, 
                gioitinh = :gioitinh, 
                ngaysinh = :ngaysinh, 
                Avatar = COALESCE(:avatar, Avatar) 
                WHERE id_user = :id_user");
        
            $stmt->bindParam(':id_user', $id, PDO::PARAM_INT);
            $stmt->bindParam(':hoten', $data['hoten']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':sdt', $data['sdt']);
            $stmt->bindParam(':diachi', $data['diachi']);
            $stmt->bindParam(':gioitinh', $data['gioitinh']);
            $stmt->bindParam(':ngaysinh', $data['ngaysinh']);
            $stmt->bindParam(':avatar', $data['avatar']);
            return $stmt->execute();
        }        
        
    }