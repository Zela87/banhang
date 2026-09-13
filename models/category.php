<?php 
    include_once '../models/db-connect.php';

    function get_all_categories() {
        $sql = "SELECT * FROM categories order by CategoryID desc";
        return pdo_query($sql);
    }

