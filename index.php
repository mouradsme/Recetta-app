<?php 

require_once( 'includes/init.php' );

$template   = $twig->load('index.html')->render($load);
 // POST method is reserved for AJAX actions
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    echo $template;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
    $Actions->create($_POST['action']);
?> 