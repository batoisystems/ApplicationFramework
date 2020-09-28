<?php
/*------------------------------------------------------------------------------+
 * Batoi Open Source Framework (OSF) - An Application Development Framework      |
 * (c)Copyright Ashwini Kumar Rath. All rights reserved.                        |
 * Website: https://framework.batoi.com                                         |
 * Licensed under the terms of the GNU General Public License Version 3 or later|
 * (the "GPL"): http://www.gnu.org/licenses/gpl.html                            |
 * NOTE: The copyright notice like this on any of the distributed files         |
 *       (downloaded or obtained as the part of Batoi Open Source Framework)    |
 *       must NOT be removed or modified.                                       |
 *------------------------------------------------------------------------------+
 */
$fTimeStart = microtime(true);
/* Include configuration file */
if(!file_exists(dirname(__FILE__).'/sys.inc.php')) //Check for System Configuration
{
	if(!file_exists())
	{
		// Load a page to say the framework files are misplaced
		print 'The framework files are misplaced';
		exit();
	}
	else
	{
		header("Location: {dirname(__FILE__)}/osf/install/");
		exit();
	}
}
require_once dirname(__FILE__).'/sys.inc.php';
$_DELIGHT = array();
try
{
	$DB = new PDO("{$_OSF['DSN']}", "{$_OSF['DBUSER']}", "{$_OSF['DBPASSWORD']}");	
}
catch (PDOException $oException) 
{
	 print "DB Connection error: " . $oException->getMessage();
}
spl_autoload_register(function ($sClassName) {
    if(file_exists(dirname(__FILE__).'/osf/lib/'.$sClassName . '.cls.php')) require_once (dirname(__FILE__).'/osf/lib/'.$sClassName . '.cls.php');
	elseif(file_exists(dirname(__FILE__).'/osf/model/class/'.$sClassName . '.cls.php')) require_once (dirname(__FILE__).'/osf/model/class/'.$sClassName . '.cls.php');
    else trigger_error("Unable to load class: $sClassName", E_USER_WARNING);
});
//if($_OSF['DEBUG'] > 0){print_r($_OSF);var_dump($DB);if($_OSF['DEBUG'] == 2){exit();}}

$APP  = new OSF_APP();
//if($_OSF['DEBUG'] > 0){print_r($_OSF);var_dump($APP);if($_OSF['DEBUG'] == 2){exit();}}
if($APP->bIsPublic != 1){$USER = new OSF_USER();$APP->initLog($fTimeStart,$USER->USERNAME);}
else{$APP->initLog($fTimeStart,0);}
//if($_OSF['DEBUG'] > 0){print_r($_OSF);var_dump($APP);if($_OSF['DEBUG'] == 2){exit();}}
/************************************************
 * OSF Application Class
************************************/
class OSF_APP
{
	public $ID;
	public $EVENTVERIFIER;
	public $FORMRULES;
	public $VIEWPARTS;
    public $PAGEVARS;
    public $BASEURL;
    public $URL;
    public $CONFIG;
    public $SYS;
    public $bIsPublic;
    public $aRoles;
    public $TABLEPREFIX;
    private $iRunID;
    private $fTimeStart;
    private $iLogStstus;
	public $iSSODefaultRole;
    private $iCtrId;
    private $sCtrName;
    public $ERROR = array();
    
	/* CONSTRUCTOR */
	public function __construct() 
	{
		global $DB, $_OSF;
		$this->BASEURL    = $_OSF['BASEURL'];
		$this->DB 	  = $DB;
		$this->TABLEPREFIX = $_OSF['TABLE_PREFIX'];           //Determine Table Prefix
		if( !isset($_OSF['CTRID']) ){print 'The Controller misses its Identity!';exit();}
		else $this->iCtrId =  $_OSF['CTRID'];
		$this->sCtrName = $this->getCtrName();
		array_walk($_REQUEST, array('OSF_APP','sanitize'));   //Sanitise  $_REQUEST
		$this->ID = $this->getAppId(); // Determine Event ID
		$this->getSysVars(); // Get System Variables
		$this->checkIfPublic(); //Determine Module Access
		$this->getConfigVars(); //Determine Config variables
		$this->ERROR['status'] = 0;
		$this->ERROR['text'] = '';
		$this->ERROR['wfstate'] = 1;
		return true;
	}

	/* Application Data Sanitization*/
	static function sanitize(&$sVal, $sKey)
	{
		$sVal = trim($sVal);
		//$sVal = escapeshellcmd($sVal);
		//$sVal = escapeshellarg($sVal);
		return $sVal;
	}
	
	/* Get Controller Name */
	private function getCtrName()
	{
		$sQry     = "SELECT ctrname FROM {$this->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
	    $oStmt    = $this->DB->prepare($sQry); 
	    $oStmt->bindParam(':ctrid',$this->iCtrId); 
		$oStmt->execute();
		$iNumRows  = $oStmt->rowCount();
		if($iNumRows == 1)
		{
			$aNumRows    	   = $oStmt->fetchAll();
			return $aNumRows[0][0];
		}
	}
	
	/* Determine Event ID */
	private function getAppId()
	{
		if( (!isset($_REQUEST['ID'])) || ($_REQUEST['ID'] == '') ) $iAPPID = $this->getDefaultEventId();
		else $iAPPID = $_REQUEST['ID'];
		if($iAPPID != 0)
		{
			$sQry = "SELECT eventverifier FROM {$this->TABLEPREFIX}od_event WHERE eventid = :eventid";
			$oStmt = $this->DB->prepare($sQry);
			$oStmt->bindParam(':eventid', $iAPPID);
			$oStmt->execute();
			//$aRows = $oStmt->fetchAll();
			$iRecordNumber  = $oStmt->rowCount();
			if ($iRecordNumber == 1) return $iAPPID;
			else
			{
				header("HTTP/1.0 404 Not Found");
				exit();
			}	 
		}
		else{header("HTTP/1.0 404 Not Found");exit();}
	}
	
	/* Get System Variables */
	private function getSysVars()
	{		
		$sQry = "SELECT sysid,appname,author,description,baseurl,ssodefaultroleid,logstatus,sysstatus  FROM {$this->TABLEPREFIX}od_sys";
		$oStmt = $this->DB->prepare($sQry);
		$oStmt->execute();
		$aRow = $oStmt->fetchAll();
		if(sizeof($aRow) == 1)
		{
			//$this->BASEURL    = stripslashes($aRow[0]['baseurl']);
			if($_SERVER['QUERY_STRING']) $this->URL = $this->BASEURL.'/'.$this->sCtrName.'?'.$_SERVER['QUERY_STRING'];
			else
			{
				$iDefault      = $this->getDefaultEventId();
			    $this->URL     = $this->BASEURL.'/'.$this->sCtrName.'?ID='.$iDefault;
			}
			$this->iLogStatus = stripslashes($aRow[0]['logstatus']);
			$this->iSSODefaultRole = stripslashes($aRow[0]['ssodefaultroleid']);
			if (stripslashes($aRow[0]['sysstatus']) == 0)
			{
				print stripslashes($aRow[0]['appname']).' has been deactivated.';
				exit();
			}
		}
		else
		{
			print 'System Error!';
			exit();
		}
		return true;
	}
	
	private function getConfigVars()
	{
		$sQry = "SELECT configid,configname,configvalue FROM {$this->TABLEPREFIX}od_config WHERE configid <> ''";
		$oStmt = $this->DB->prepare($sQry);
		$oStmt->execute();
		$aRows = $oStmt->fetchAll();		
		foreach($aRows as $aRow)
		{
		    $sConfigName                = stripslashes($aRow['configname']);
		    $this->CONFIG[$sConfigName] = stripslashes($aRow['configvalue']);
		}	
	}
	
	/* Determine Default Event ID */
	private function getDefaultEventId()
	{
		$sQry     = "SELECT defaulteventid FROM {$this->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
		$oStmt    = $this->DB->prepare($sQry); 
	    $oStmt->bindParam(':ctrid', $this->iCtrId); 
	    $oStmt->execute();
	    $aRows    = $oStmt->fetchAll();
	    $iDefault = 0;
	    foreach($aRows as $aRow)
	  	{
		 	$iDefault = stripslashes($aRow['defaulteventid']);
	  	}
	    return $iDefault;	
	}
	
	/*Determine Module Access*/
	private function checkIfPublic()
	{
       $sQry     = "SELECT ispublic FROM {$this->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
       $oStmt    = $this->DB->prepare($sQry); 
	   $oStmt->bindParam(':ctrid',$this->iCtrId); 
	   $oStmt->execute();
	   $aRows    = $oStmt->fetchAll();
	   foreach($aRows as $aRow)
	      {
			 $this->bIsPublic = stripslashes($aRow['ispublic']);
		  }
	  return $this->bIsPublic;   
	}
	
	/* Initialise Log */
	function initLog($fTimeStart,$sUsername)
	{
		if ($this->iLogStstus == 1)
		{
			$this->fTimeStart = $fTimeStart;
			$sAccessTime = date("d F Y, g:i a");
			$sAccessIP   = $_SERVER['REMOTE_ADDR'];
			$sLogFileName = dirname(__FILE__).'/log/'.date("Y-n-j").'.txt';
			if(!$handle = fopen($sLogFileName, 'a+'))
			{
			    print 'System Error!';
			    exit();
			}
			if ($sUsername == '0') $sLogContent = $sAccessTime.' - Application accessed publicly through URL '.$this->URL.' from IP '.$sAccessIP.".\n";
			else $sLogContent = $sAccessTime.' - Application accessed by user '.$sUsername.' through URL '.$this->URL.' from IP '.$sAccessIP.".\n";
			if (is_writable($sLogFileName)) 
			{	    
			   if (fwrite($handle, $sLogContent) === FALSE) 
			   {
				   print 'System Error!';
			       exit();
			   }
			} 
			else
			{
				print 'System Error!';
			    exit();
			}
			fclose($handle);
		}
		return true;
	}
	
	/* Update Log */
	function updateLog()
	{
		if ($this->iLogStstus == 1)
		{
			$fTimeEnd = microtime(true);
	        $fElapsedTime = $fTimeEnd - $this->fTimeStart;
	        $sDeliveryTime = date("d F Y, g:i a");
			$sLogFileName = dirname(__FILE__).'/osf/data/log/'.date("Y-n-j").'.txt';
			if(!$handle = fopen($sLogFileName, 'a+'))
			{
			    print 'System Error!';
			    exit();
			}
			$sLogContent = $sDeliveryTime.' - Application executed in '.$fElapsedTime." sec.\n";
			if (is_writable($sLogFileName)) 
			{	    
			   if (fwrite($handle, $sLogContent) === FALSE) 
			   {
				   print 'System Error!';
			       exit();
			   }
			} 
			else
			{
				print 'System Error!';
			    exit();
			}
			fclose($handle);
		}
		return true;
	}
}//End of class OSF_APP

/***********************************
 * OSF User Class                  *
************************************/
class OSF_USER
{
	public $ID;	
	public $USERNAME;
	public $EMAIL;
	public $FULLNAME;
	public $LASTLOGIN;
	public $IDVERIFIER;
	public $iRole;
	public $sRequestUrl;
	
	/* CONSTRUCTOR */
	public function __construct() 
	{		
		global $DB, $APP, $_OSF;
		$this->DB  = $DB;
		$this->APP = $APP;
		$this->iCtrId       = $_OSF['CTRID'];
		$this->ID           = 0;
		$this->IDVERIFIER   = '';		
		$this->sRequestUrl  = $APP->URL;
		if($this->validate()) return true;
	}
	
	private function validate()
	{
	   if (!isset($_COOKIE['C_LOGINKEY']))
	   {
		   $bAllowAccess  = false;
	   }
	   else
	   {
		   $_SESSION['REQUESTURL'] = '';
		   //$aCookieKey = unserialize(base64_decode($_COOKIE['C_LOGINKEY']));
		   //print $_COOKIE['C_LOGINKEY'];print '<br>';
		   $sSessionKey = $_COOKIE['C_LOGINKEY'];
		   $sSessionStatus = '1';
		   $sQry		   = "SELECT userid, idverifier, username, firstname, lastname, emailid, roleid FROM {$this->APP->TABLEPREFIX}od_usersession WHERE sessionkey = :sessionkey AND sessionstatus = :sessionstatus";
		   $oStmt 		   = $this->DB->prepare($sQry); 
		   $oStmt->bindParam(':sessionkey',$sSessionKey);
		   $oStmt->bindParam(':sessionstatus',$sSessionStatus);
		   $oStmt->execute();
		   $iRecordNumber  = $oStmt->rowCount();
		   //print '$iRecordNumber = '.$iRecordNumber;exit();
		   if($iRecordNumber == 1)
		   {
				$aRows    	   = $oStmt->fetchAll();
				//print_r($aRows);exit();
				foreach($aRows as $aRow)
				{
				    $this->ID = $aRow['userid'];
				    $this->IDVERIFIER   = $aRow['idverifier'];
				    $this->iRole      = stripslashes($aRow['roleid']);			    
				    $sQry  = "SELECT roles FROM {$this->APP->TABLEPREFIX}od_event WHERE eventid = :eventid";
			   	    $oStmt = $this->DB->prepare($sQry); 
					$oStmt->bindParam(':eventid', $this->APP->ID);
					$oStmt->execute();
					$iNumRows  = $oStmt->rowCount();
					if($iNumRows == 1)
				    {
				        $aNumRows    	   = $oStmt->fetchAll();
				        foreach($aNumRows as $aNumRow)
						{
						    $sRoles   = $aNumRow['roles'];
						}
						$aRoles = explode(',', $sRoles);
					    if (!in_array($this->iRole, $aRoles)) $bAllowAccess  = false;
		                else
		                {
		                    $this->USERNAME   = stripslashes($aRow['username']);
		                    $this->EMAIL      = stripslashes($aRow['emailid']);
						    $this->FULLNAME   = stripslashes($aRow['firstname']).' '.stripslashes($aRow['lastname']);
						    //$this->LASTLOGIN  = stripslashes($aRow['lastlogin']);
		                    $bAllowAccess  = true;
		                }	
				    }
				    else $bAllowAccess  = false; 							
				}				   
			}
			else $bAllowAccess  = false;
		}
		if($bAllowAccess) return true;
		else
		{
			$_SESSION['REQUESTURL'] = $this->sRequestUrl;
			$sQry  = "SELECT signinctrid FROM {$this->APP->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
		   	$oStmt = $this->DB->prepare($sQry); 
			$oStmt->bindParam(':ctrid', $this->iCtrId);
			$oStmt->execute();
			$aRows = $oStmt->fetchAll();
			foreach($aRows as $aRow)
			{
			    $iSignInCtrlID   = $aRow['signinctrid'];
			}			
			$sQry  = "SELECT ctrname FROM {$this->APP->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
		   	$oStmt = $this->DB->prepare($sQry); 
			$oStmt->bindParam(':ctrid', $iSignInCtrlID);
			$oStmt->execute();
			$aRows = $oStmt->fetchAll();
		    foreach($aRows as $aRow)
			{
			    $sSignInCtrlName   = stripslashes($aRow['ctrname']);
			}
			$sRedirectPath = $this->APP->BASEURL.'/'.$sSignInCtrlName;
			header("Location: $sRedirectPath");
			exit();
		}
	}
}//End of OSF_USER class
?>