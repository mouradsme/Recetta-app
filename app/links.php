<?php 
$Links->select('menu')
    ->create(array('home', 'menu.home' , 'fa-home', true))
    ->create(array('search', 'menu.search' , 'fa-search', true))
    ->create(array('recipes', 'menu.recipes' , 'fa-home', true))
    ->create(array('categories', 'menu.categories' , 'fa-home', true))
    ->create(array('users', 'menu.users' , 'fa-home', true))
    ->create(array('news', 'menu.news' , 'fa-home', true))
;
// $Links->select('menu')
// ->create(array('home', 'menu.home' , 'fa-home', true))
?>