<?php 
$Page = @$_GET['page'];
session_start();

date_default_timezone_set("Africa/Algiers");
require_once( 'vendor/autoload.php' );
require_once( 'langs/fr.php' );
require_once( 'connect.php' );
require_once( 'functions.php' );
require_once( 'twig.php');
require_once( 'app/models.php');
$connection = createConn(DB_DATABASE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
$libs       = new \BMgr\Libs("assets/lib");
$libs       = $libs->getLibs();
$Database   = new Database($connection, $Models);
$functions  = new Utility();
$Settings   = new Settings();
require_once( 'app/app.php');


$Routes->create($Page);
$Main       = $Routes->getMain();
$Content    = $Routes->getContent();
$Passed     = $Routes->getPassed($Page); 
$settings   = $Settings->getSettings();
$links      = $Links->getLinks();
$load       = array( 
    "access"    => true,
    "assets"    => $libs,
    "Links"     => $links,
    "imgs"      => "assets/imgs/",
    "flags"     => "assets/flags/",
    "hash"      => $hash,  
    "tpl"       => $templateURL,
    "L"         => $Lang,
    "Main"      => $Main,
    "content"   => $Content,
    "Passed"    => $Passed,
    "Settings"  => $settings,
    "Page"      => $Page,
    "loggedIn"  => @$_SESSION['loggedIn']

);
if (isset($Passed))
    $load = array_merge($load, $Passed);

?>