<?php   
$Routes 
        ->add('home', 'home', 'Home', function() { 
            global $Database, $Routes, $Languages, $Lang;
            $Recipes = $Database->query("SELECT RE.recipe_id, RE.recipe_rating, RT.rt_recipe_name, RT.rt_recipe_image, RT.rt_recipe_link FROM recipes RE JOIN recipe_translation RT ON RT.rt_recipe_id = RE.recipe_id ORDER BY RAND() LIMIT 10;")->fetchAll(PDO::FETCH_ASSOC);
            $Latest = $Database->query("SELECT RE.recipe_id, RE.recipe_rating, RT.rt_recipe_name, RT.rt_recipe_image, RT.rt_recipe_link FROM recipes RE JOIN recipe_translation RT ON RT.rt_recipe_id = RE.recipe_id ORDER BY RE.recipe_created DESC LIMIT 10;")->fetchAll(PDO::FETCH_ASSOC);
            $Routes->Pass('Recipes', $Recipes);
            $Routes->Pass('Latest', $Latest);
			$Routes->Pass('pageTitle', $Lang['titles']['home']);


        })
        ->add('logout', '#', '#', function() {
            session_unset();
            session_destroy();
            header('Location: index.php?p=login');
        }) 
        ->add('login', 'login', 'Login', function() {
            global $Database, $Routes, $Languages, $Lang;
            $Routes->Pass('pageTitle', $Lang['titles']['login']);
        }) 
        ->add('recipes', 'recipes', 'Recipes', function() {
            global $Database, $Routes, $Languages, $Lang;
            $p = (int) ( isset($_GET['p']) ? $_GET['p'] : 0);
            $Routes->Pass('Current',$p);
            $p = 10 * $p;
            $Recipes = $Database->query("SELECT RE.recipe_id, RE.recipe_rating, RT.rt_recipe_name, RT.rt_recipe_image, RT.rt_recipe_link FROM recipes RE JOIN recipe_translation RT ON RT.rt_recipe_id = RE.recipe_id  
            LIMIT $p, 10 ;")->fetchAll(PDO::FETCH_ASSOC);
            $Routes->Pass('Recipes', $Recipes);
            $Routes->Pass('Page', 'recipes');
            $Routes->Pass('id', '');
            $Routes->Pass('pageTitle', $Lang['titles']['recipes']);
        }) 
        ->add('category', 'recipes', 'Recipes', function() {
            global $Database, $Routes, $Languages, $Lang;
            $id = $_GET['id'];
            $Category = $Database->query("SELECT C.*, CT.* FROM categories C JOIN category_translation CT ON C.category_id = CT.ct_category_id WHERE CT.ct_lang_id = 1 AND C.category_id = ?;")->bindData([$id])->fetchAll(PDO::FETCH_ASSOC)[0];  
            $Routes->Pass('Category', $Category);
            $Recipes = $Database->query("SELECT RE.recipe_id, RE.recipe_rating, RT.rt_recipe_name, RT.rt_recipe_image, RT.rt_recipe_link FROM recipes RE JOIN recipe_translation RT ON RT.rt_recipe_id = RE.recipe_id JOIN recipe_categories RC ON RC.rc_recipe_id = RE.recipe_id WHERE RC.rc_category_id = ?;")->bindData([$id])->fetchAll(PDO::FETCH_ASSOC);
            $Routes->Pass('Recipes', $Recipes);
            $Routes->Pass('Page', 'category');
            $Routes->Pass('id',  $id);
            
            $Routes->Pass('pageTitle', $Lang['titles']['recipes']);
        }) 
        ->add('categories', 'categories', 'Categories', function() {
            global $Database, $Routes, $Languages, $Lang;      
            $Categories = $Database->query("SELECT CT.ct_category_name as name, CT.ct_category_id as id, C.category_count as size FROM categories C JOIN category_translation CT ON CT.ct_category_id = C.category_id WHERE CT.ct_lang_id = 1 ORDER BY C.category_count DESC;")->fetchAll(PDO::FETCH_ASSOC);
            $Routes->Pass('Categories', $Categories);
            $Routes->Pass('pageTitle', $Lang['titles']['categories']);
        }) 
        ->add('search', 'search', 'Search', function() {
            global $Database, $Routes, $Languages, $Lang;           
            $Ingredients = $Database->query("SELECT IT.* , I.* FROM ingredients I JOIN ingredient_translation IT ON IT.it_ingredient_id = I.ingredient_id WHERE IT.it_lang_id = 1 ORDER BY I.ingredient_used DESC ;")->fetchAll(PDO::FETCH_ASSOC);
            $Routes->Pass('Ingredients', $Ingredients);
            $Routes->Pass('pageTitle', $Lang['titles']['search']);
        }) 
        ->add('results', 'recipes', 'Results', function() {
            global $Database, $Routes, $Languages, $Lang, $functions;
            $Ingredients = $_GET['ingredients'];
            $mode = $_GET['mode'];

            $p = (int) ( isset($_GET['p']) ? $_GET['p'] : 0);
            $Routes->Pass('Current',$p);
            $p = 10 * $p;
            if ($mode === 'strict') {
                $Results = array();
                $IngredientsArray = explode(",", $Ingredients);
                $Combs = $functions->comb($IngredientsArray);
                $a = 0;
                foreach ($Combs as $Comb) {
                    sort($Comb);
                    $Ingredients = implode(',', $Comb);
                    $title = $Ingredients;
                    $Found = $Database->query("SELECT RE.recipe_id, RE.recipe_rating, RT.rt_recipe_name, RT.rt_recipe_image, RT.rt_recipe_link FROM recipes RE JOIN recipe_translation RT ON RT.rt_recipe_id = RE.recipe_id  
                    WHERE RE.recipe_ingredients = ? LIMIT $p, 10 ;")->bindData([$Ingredients])->fetchAll(PDO::FETCH_ASSOC);
                    if (sizeof($Found) > 0) {
                        $Results[] = $Found; 
                        $Titles[] = ['key' => (++$a-1) , 'title' => $title];   
                    }
                }
                $Recipes = $functions->sort_by_count($Results);  
                $COUNT = sizeof($Found);
            }
            if ($mode === 'all') {
                $Recipes = $Database->query("SELECT RE.recipe_id, RE.recipe_rating, RT.rt_recipe_name, RT.rt_recipe_image, RT.rt_recipe_link FROM recipes RE JOIN recipes_all RA ON RE.recipe_id = RA.recipe_id JOIN recipe_translation RT ON RT.rt_recipe_id = RA.recipe_id  
                WHERE RA.recipe_ingredient IN ($Ingredients) GROUP BY RE.recipe_id LIMIT $p, 10 ;
")->fetchAll(PDO::FETCH_ASSOC);
                $COUNT = $Database->query("SELECT count(DISTINCT RE.recipe_id) as c FROM recipes RE JOIN recipes_all RA ON RE.recipe_id = RA.recipe_id JOIN recipe_translation RT ON RT.rt_recipe_id = RA.recipe_id WHERE RA.recipe_ingredient IN ($Ingredients);
")->fetchAll(PDO::FETCH_ASSOC)[0]['c'];
            }
            $Routes->Pass('Recipes', $Recipes);
            $Routes->Pass('COUNT', $COUNT );
            $Routes->Pass('Ingredients', $Ingredients);
            $Routes->Pass('Page', 'results');
            $Routes->Pass('id', ''); 
            $Routes->Pass('mode', $mode);
            $Routes->Pass('pageTitle', $Lang['recipes']['titles']['results']);
            if ($mode == "strict") 
            $Routes->Pass('Titles', $Titles);
        }) 
		
?>