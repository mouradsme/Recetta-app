<?php 
define('DB_HOSTNAME', 'localhost'); 
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');  
define('DB_DATABASE', 'recetta');  
$production     = false;
$templateRoot   = "templates/default";
$templateURL  = "./" . $templateRoot; 
$version      = (($production)? "1.0": time());
$hash         = "?v=". $version;
?>