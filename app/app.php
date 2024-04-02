<?php 
    $Routes     = new Routes([!@$_SESSION['loggedIn'], 'login']);
    $Actions    = new Actions();
    $Links      = new Links();
    
    include 'links.php';
    $Settings->select('links')
        ->set('icons', false)
        ->set('text', true);
    include 'routes.php';
    include 'actions.php';
?>