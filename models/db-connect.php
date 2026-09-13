<?php
// Kết nối cơ sở dữ liệu
function connect_db(){
    try {
        $dbconn = new PDO("mysql:host=localhost:3307;dbname=greenplanet", "root", "");
        $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbconn->exec("SET NAMES 'utf8'");
        return $dbconn;
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        return null;
    }
}


function disconnect_db(&$dbconn) {
    $dbconn = null;
}
function pdo_execute($sql, ...$params){
    try {
        global $dbconn;
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($params);
        return true;
    } catch (PDOException $e) {
        error_log("Lỗi thực thi SQL: " . $e->getMessage());
        return false;
    }
}

/**
 * Thực thi câu lệnh SQL truy vấn dữ liệu (SELECT)
 */
function pdo_query($sql, ...$params){
    try {
        global $dbconn; // Sử dụng kết nối PDO từ biến toàn cục
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e; // Ném ngoại lệ để xử lý lỗi
    }
}


/**
 * Thực thi câu lệnh SQL truy vấn một bản ghi (SELECT một dòng)
 */
function pdo_query_one($sql, ...$params){
    try {
        global $dbconn;
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * Thực thi câu lệnh SQL truy vấn một giá trị (SELECT một giá trị)
 */
function pdo_query_value($sql, ...$params){
    try {
        global $dbconn;
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        throw $e;
    }
}
function pdo_execute_return_last_id($sql, ...$params){
    try {
        global $dbconn; // Sử dụng kết nối PDO từ biến toàn cục
        $stmt = $dbconn->prepare($sql);
        $stmt->execute($params);
        return $dbconn->lastInsertId(); // Trả về ID của bản ghi vừa được thêm vào
    } catch (PDOException $e) {
        error_log("Lỗi thực thi SQL: " . $e->getMessage());
        return false;
    }
}
