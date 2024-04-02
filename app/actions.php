<?php 
    
    $Actions
        ->add('login', function() {
            global $Database, $Lang;
            extract($_POST);
            if ($Database->allowLogin($username, $password)) {
                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $username;
                return ['success', ''];
            } else {
                return ['error', $Lang['login']['errors']['wrong']];
            }
            return ['error', 'err'];
        }, []) 
        ->add('logout', function() {
            global $Database, $Lang;
            extract($_POST);
            session_destroy();
            return ['success', ''];
        }, [])
        ->add('get-recipe', function() {
            global $Database, $Lang;
            extract($_POST);
            $Recipe = $Database->query("SELECT * FROM recipe_translation WHERE rt_recipe_id = ? AND rt_lang_id = 1;")->bindData([$id])->fetchAll(PDO::FETCH_ASSOC)[0];
            return ['success', $Recipe];

        })
?>