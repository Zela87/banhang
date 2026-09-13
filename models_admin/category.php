<?php 
    include '../models/db-connect.php';

    function get_category($CategoryID){
        global $dbconn;
        try{
            $stmt = $dbconn->prepare("select * from categories where ProductID = {$CategoryID}");
            // Thực thi câu truy vấn
            $stmt->execute();
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC); 
            // Lấy danh sách kết quả
            $result = $stmt->fetchAll();
            return $result;
            // var_dump($result);
        } catch (PDOException $e) {
            error_log("Lỗi cơ sở dữ liệu trong quá trình thêm sản phẩm: " . $e->getMessage());
            return "Đã xảy ra lỗi, vui lòng thử lại sau.";
        }
        
    }

    function get_all_products($dbconn){
        try{
            // Sử dụng Prepare 
            $stmt = $dbconn->prepare("select * from products"); 
            //khai báo exception
            $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Thực thi câu truy vấn
            $stmt->execute();
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC); 
            // Lấy danh sách kết quả
            $result = $stmt->fetchAll();
            return $result;
        } catch(PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
?>