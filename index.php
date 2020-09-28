<?php
ini_set('error_reporting', E_ALL);
session_start();
$_OSFCTRID  = 2;
// var_dump(gd_info());
// exit();
// $im = imagegrabscreen();
// imagepng($im, "myscreenshot.png");
// imagedestroy($im);

include "./load.osf.php";
/* Move the control to the respective pages according to the Event ID */
switch($APP->ID)
{ 
    case 6: //Case for eventname "Default"
    $APP->VIEWPARTS = array('header-main.tpl.php', 'hello-world.tpl.php', 'footer-main.tpl.php');
    break;

    default:
    break;
} //End of switch statement
include "./load.view.php";
?>
