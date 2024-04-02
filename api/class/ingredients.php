<?php
    class Ingredient{
        private $conn;
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function searchIngredients($lang, $value){

            $sqlQuery = "SELECT T1.*, T2.*
            FROM ingredients T1 
            LEFT JOIN ingredient_translation T2 ON T1.ingredient_id = T2.it_ingredient_id 
            WHERE it_lang_id = ? AND T2.it_ingredient_name LIKE '$value%';";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([$lang]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function getSingleIngredient($lang, $id){

            $sqlQuery = "SELECT T1.*, T2.*
            FROM ingredients T1 
            LEFT JOIN ingredient_translation T2 ON T1.ingredient_id = T2.it_ingredient_id 
            WHERE it_lang_id = ? AND ingredient_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([$lang, $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getAllIngredients($lang) {
            $sqlQuery = "SELECT T1.*, T2.*
            FROM ingredients T1 
            LEFT JOIN ingredient_translation T2 ON T1.ingredient_id = T2.it_ingredient_id 
            WHERE it_lang_id = ? GROUP BY it_ingredient_id;";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([$lang]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }



    }
?>

