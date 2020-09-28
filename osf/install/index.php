<?php
/*------------------------------------------------------------------------------+
 * Batoi Open Source Framework (OSF) - An application Development Framework      |
 * (c)Copyright Ashwini Kumar Rath. All rights reserved.                        |
 * Website: https://framework.batoi.com                                         |
 * Licensed under the terms of the GNU General Public License Version 3 or later|
 * (the "GPL"): http://www.gnu.org/licenses/gpl.html                            |
 * NOTE: The copyright notice like this on any of the distributed files         |
 *       (downloaded or obtained as the part of Batoi Open Source Framework)    |
 *       must NOT be removed or modified.                                       |
 *------------------------------------------------------------------------------+
 */
//ini_set('error_reporting', E_ALL);
date_default_timezone_set('Greenwich');
session_start();
if(isset($_GET['ID'])) $iID = $_GET['ID'];
else $iID = 1;
/* Autoload function for class */
spl_autoload_register(function ($sClassName) {
	require_once (dirname(dirname(__FILE__)).'/ide/class/'.$sClassName . '.cls.php');
	if (!class_exists($sClassName, false)) trigger_error("Unable to load class: $sClassName", E_USER_WARNING);
});
switch($iID)
{
    case 1:
    		$VIEWPARTS = array();
        $VIEWPARTS[]            = 'header.install.tpl.php';
			  $VIEWPARTS[]            = 'setup.application.tpl.php';
			  $VIEWPARTS[]            = 'footer.install.tpl.php';
    		$PAGEVARS['title']      = 'Setup Application';
    		$PAGEVARS['headertext'] = 'Setup Application';      		
    break;
    case 2:
      if(isset($_POST['hidInstallStatus']))
      {
         $oInstall = new Install();
         $sMessage = $oInstall->createApplication();
         if($sMessage == '')
         {
            $sPath = $_SERVER['PHP_SELF'].'?ID=3';	
	          header("Location: $sPath");
	          exit();
         }
         else
         {
            $sPath = $_SERVER['PHP_SELF'].'?ID=4';	
	          header("Location: $sPath");
	          exit();
         }
      }
		  $VIEWPARTS[]            = 'header.install.tpl.php';
			$VIEWPARTS[]            = 'setup.app-standalone.form.tpl.php';
			$VIEWPARTS[]            = 'footer.install.tpl.php';
    	$PAGEVARS['title']      = 'Define Application';
    	$PAGEVARS['headertext'] = 'Define Application';      		
    break;
    case 3:
            include '../../sys.inc.php';
            include '../ide/script/dal.inc.php';
            $APP             = new APP();
            $IDE             = new IDE();
		    $aAppDetails     = $IDE->getAppDetails();
    		$VIEWPARTS         = array('header.install.tpl.php', 'setup.app-standalone.success.tpl.php', 'footer.install.tpl.php');
    		$PAGEVARS['title'] = 'Success!';
    		$PAGEVARS['headertext'] = 'Success!';      		
    break;
    case 4:
            include '../../sys.inc.php';
            include '../ide/script/dal.inc.php';
            $APP             = new APP();
            $IDE             = new IDE();
		    $aAppDetails     = $IDE->getAppDetails();
    		$VIEWPARTS         = array('header.install.tpl.php', 'error.tpl.php', 'footer.install.tpl.php');
    		$PAGEVARS['title'] = 'Error!';
    		$PAGEVARS['headertext'] = 'Error!';      		
    break;
}
if(!empty($VIEWPARTS)) 
{
	foreach($VIEWPARTS as $sViewPart) include(dirname(dirname(__FILE__)).'/ide/html/'.$sViewPart);
}
?>
