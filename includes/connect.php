<?php
require "configs.php";
function createConn($db_database, $db_hostname, $db_username, $db_password) {
        try { 
            $pdo = new PDO("mysql:host=$db_hostname;dbname=$db_database;", $db_username, $db_password); 
            $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $pdo->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(PDOException $e) {
           echo "Connection failed: " . $e->getMessage();
        }
}
?>