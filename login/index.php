<?php
session_start(); 
$_OSFCTRID  = 1;
include "../load.osf.php";
/* Move the control to the respective pages according to the Event ID */
switch($APP->ID)
{ 
    case '1': //Case for eventname "SignIn"
		if( isset($_GET['IDP']) )
		{
			$oSign = new OSF_Sign();
			$oSign->signinIDP();
		}
		else
        {
            $oSign = new OSF_Sign();
            $sServerHost = $_SERVER['HTTP_HOST'];
            // Destroy any user session - basically make cookie key blank
            if (isset($_COOKIE['C_LOGINKEY']))
            {
                setcookie("C_LOGINKEY","",time()-3600, '/', '.'.$sServerHost);
            }
            //print_r($_SERVER);
			$APP->VIEWPARTS = array('header-sign.tpl.php', 'signin.tpl.php', 'footer-sign.tpl.php');
			$APP->PAGEVARS['TITLE'] = "Sign In";
		}
    break;
	
    case '2': //Case for eventname "Validate"
        $oSign = new OSF_Sign();
        if ( isset($_REQUEST["hidStatus"]) && ($_REQUEST["hidStatus"] == '1') ) $oSign->createUserSession();
        $APP->VIEWPARTS = array('header-sign.tpl.php', 'signin.tpl.php', 'footer-sign.tpl.php');
        $APP->PAGEVARS['TITLE'] = "User Sign In";
        break;

    case '21': //Case for eventname "OTP Validate"
        $oSign = new OSF_Sign();
        $oSign->checkMFA();
        if( isset($_REQUEST["hidStatus"]) && ($_REQUEST["hidStatus"] == '2') ) $oSign->validateOTP();
        $APP->VIEWPARTS = array('header-sign.tpl.php', 'otp.tpl.php', 'footer-sign.tpl.php');
        $APP->PAGEVARS['TITLE'] = "Multi-factor Authentication - OTP Verification";
        break;

    case '3': //Case for eventname "Password"
        $APP->VIEWPARTS = array('header-sign.tpl.php', 'forgot-pwd.tpl.php', 'footer-sign.tpl.php');
        $APP->PAGEVARS['TITLE'] = "Forgot your password?";
        break;

    case '4': //Case for eventname "retrievePassword"
        if ($_REQUEST["hidPwd"]) 
        {
            $oSignIn = new OSF_Sign();
            $oSignIn->getPassword($_REQUEST["txtEmailId"]);
        }
        $APP->VIEWPARTS = array('header-sign.tpl.php', 'forgot-pwd.tpl.php', 'footer-sign.tpl.php');
        $APP->PAGEVARS['TITLE'] = "Forgot your password?";
        break;


    case '5': //Case for eventname "SignOut"
        $oSign = new OSF_Sign();
        $oSign->signOut();
        break;

    default:
    break;
} //End of switch statement
include "../load.view.php";
?>
