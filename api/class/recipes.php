<?php
    class Recipe{
        private $conn;
        public function __construct($db){
            $this->conn = $db;
        }
        public function getRecipesByIngredients($lang, $ingredients = []){
            sort($ingredients);
            $Ingredients = implode(",", $ingredients);
            $Count = sizeof($ingredients);
            $sqlQuery = " SELECT T1.*, T2.*, T3.*, T4.*
            FROM recipes T1 
            LEFT JOIN recipe_translation T2 ON T1.recipe_id = T2.rt_recipe_id 
            LEFT JOIN images T3 ON T3.image_for = 'recipe' AND T3.image_for_id = T1.recipe_id 
            LEFT JOIN (SELECT *, GROUP_CONCAT(DISTINCT ri_ingredient_id ORDER BY ri_ingredient_id ASC) as Ing FROM recipe_ingredients GROUP BY ri_recipe_id) T4 ON T4.ri_recipe_id = T1.recipe_id
            WHERE T2.rt_lang_id = ? AND Ing = '$Ingredients'
            GROUP BY recipe_id 

            ORDER BY T1.recipe_created DESC;";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([$lang]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function getAllCombinations($ingredients) {
            $result = uniqueCombination($ingredients, 1, sizeof($ingredients));
                return $result;
        }
        public function getLatestRecipes($lang, $limit, $start = 0){

            $sqlQuery = "SELECT T1.*, T2.*, T3.*
            FROM recipes T1 
            LEFT JOIN recipe_translation T2 ON T1.recipe_id = T2.rt_recipe_id 
            LEFT JOIN images T3 ON T3.image_for = 'recipe' AND T3.image_for_id = T1.recipe_id 
            WHERE rt_lang_id = ? 
            GROUP BY rt_recipe_id 
            ORDER BY T1.recipe_created DESC LIMIT $start, $limit;";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([$lang]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getSingleRecipe($id, $lang){
            $sqlQuery = "SELECT T1.*, T2.*, T3.*
            FROM recipes T1 
            LEFT JOIN recipe_translation T2 ON T1.recipe_id = T2.rt_recipe_id 
            LEFT JOIN images T3 ON T3.image_for = 'recipe' AND T3.image_for_id = T1.recipe_id 
            WHERE recipe_id = ? AND rt_lang_id = ? 
            GROUP BY rt_recipe_id ;";


            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute([$id, $lang]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        


    }
?>

