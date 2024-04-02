<?php
    $loader       = new \Twig\Loader\FilesystemLoader($templateRoot);
    $twig         = new \Twig\Environment($loader, ['cache' => false]); 
    // Twig Custom Functions
    $reformatDate = new \Twig\TwigFunction('reformatDate', function($date, $del = '/') { 
        global $functions;
        return $functions->reformatDate($date, $del);
    });
    $twig->addFunction($reformatDate);
    
    $getLang = new \Twig\TwigFunction('getLang', function($str) { 
        global $functions;
        return $functions->getLang($str);
    });
    $twig->addFunction($getLang);

    $translate = new \Twig\TwigFunction('translate', function($q, $sl, $tl) { 
        $res= @file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=".$sl."&tl=".$tl."&hl=hl&q=".urlencode($q), $_SERVER['DOCUMENT_ROOT']."/transes.html");
        $res=json_decode($res);
        return $res[0][0][0];
    });
    $twig->addFunction($translate);
    $getNames = new \Twig\TwigFunction('getNames', function($ings) { 
        global $Database;
        $res = array();
        $arr = explode(",", $ings);
        foreach($arr as $a) {
            $Ing = $Database->query("SELECT I.*, IT.* FROM ingredients I JOIN ingredient_translation IT ON I.ingredient_id = IT.it_ingredient_id WHERE IT.it_lang_id = 1 AND I.ingredient_id = ?;")->bindData([$a])->fetchAll(PDO::FETCH_ASSOC)[0];
            $res[] = $Ing['it_ingredient_name']; 
        }
        return implode(', ', $res);
        
    });
    $twig->addFunction($getNames);
    

?>