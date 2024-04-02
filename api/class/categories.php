<?php
    class Category{
        private $conn;
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function getCategories($lang){

            $sqlQuery = "SELECT T1.*, T2.*, T3.*
            FROM categories T1 
            LEFT JOIN category_translation T2 ON T1.category_id = T2.ct_category_id 
            LEFT JOIN images T3 ON T3.image_for = 'category' AND T3.image_for_id = T1.category_id 
            WHERE ct_lang_id = ?
            GROUP BY ct_category_id ;";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([$lang]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getSingleCategory($id, $lang){
            $sqlQuery = "SELECT T1.*, T2.*, T3.*
            FROM categories T1 
            LEFT JOIN category_translation T2 ON T1.category_id = T2.ct_category_id 
            LEFT JOIN images T3 ON T3.image_for = 'category' AND T3.image_for_id = T1.category_id 
            WHERE category_id = ? AND ct_lang_id = ?
            GROUP BY ct_category_id ;";


            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute([$id, $lang]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        


    }
?>

