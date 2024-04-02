<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $Models = [
        0 => 'categories',
        1 => 'languages',
        2 => 'latest-recipes',
        3 => 'single-recipe',
        4 => 'ingredients',
        5 => 'search-recipes'
    ];
    include_once '../class/Combinations.php';
    include_once '../config/database.php';
    include_once '../class/recipes.php';
    include_once '../class/categories.php';
    include_once '../class/ingredients.php';
    include_once '../class/languages.php';
    $database = new Database();
    $db = $database->getConnection();
    $lang = @$_GET['l'] ? $_GET['l'] : 1;
    $Model = $Models[@$_GET['m']?$_GET['m']:0];
    switch ($Model):
        case 'categories':
            $model = new Category($db);
            $Results = $model->getCategories($lang);
        break;
        case 'languages':
            $model = new Language($db);
            $Results = $model->getLanguages();
        break;
        case 'latest-recipes':
            $model = new Recipe($db);
            $Results = $model->getLatestRecipes($lang, 5);
        break;
        case 'single-recipe':
            $id = $_GET['id'];
            $model = new Recipe($db);
            $Results = $model->getSingleRecipe($id, $lang);
        break;
        case 'ingredients':
            $model = new Ingredient($db);
            $Results = $model->getAllIngredients($lang);
        break;
        case 'search-recipes':
            $Ingredient = new Ingredient($db);
            $model = new Recipe($db);
            $ingredients = $_GET['ingredients'];
            $ingredients = json_decode( $ingredients ); 
            $Combinations = $model->getAllCombinations($ingredients);
            $Results = [];
            foreach ($Combinations as $k => $Comb) {
                $Combs = [];
                foreach ($Comb as $c) 
                    $Combs[] = $Ingredient->getSingleIngredient($lang, $c)['it_ingredient_name'];
                
                $Results[] = [$Combs, $model->getRecipesByIngredients($lang, $Comb)];
            }
        break;
        default:
        $Results = [];
    endswitch;

    $itemCount = sizeof($Results);
    if($itemCount > 0){
        $response = ['status' => 'success', 'body' => $Results];
    }
    else{ 
        $response = ['status' => 'error', 'body' => 'Not found'];
    }
    echo json_encode($response);
?>