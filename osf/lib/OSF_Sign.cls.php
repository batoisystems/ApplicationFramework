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
class OSF_Sign
{
	private $sLoginToken;

	/* CONSTRUCTOR */
	public function __construct() 
	{		
		global $DB,$APP,$USER;
		$this->DB  = $DB;
		$this->APP = $APP;
		$sToken                  = md5(rand());
		$_SESSION['SigninToken'] = $sToken;
		$this->sLoginToken       = $sToken;         
		return true;
	}
	
	/* Create New User Session */
	public function createUserSession()
	{
		if(!($_SESSION['SigninToken'] == $this->sLoginToken))
		{
			header("Location: $_SERVER[PHP_SELF]");
			exit();
		}
		$aSuppliedUserDetails = array();
		$aSuppliedUserDetails['username'] = $_POST['txtUsername'];
		$aSuppliedUserDetails['status']   = '1';
		$sQryUser      = "SELECT userid, idverifier, username, password, firstname, lastname, email, roleid, enablemfa FROM {$this->APP->TABLEPREFIX}od_user WHERE username = :username AND userstatus = :userstatus";
		$oStmt    	   = $this->DB->prepare($sQryUser); 
		$oStmt->bindParam(':username',$aSuppliedUserDetails['username']); 
		$oStmt->bindParam(':userstatus',$aSuppliedUserDetails['status']);
		$oStmt->execute();
		$iRecordNumber  = $oStmt->rowCount();
		if($iRecordNumber == 1)
		{
		    $aRows = $oStmt->fetchAll();
		    if(password_verify($_POST['txtPassword'], $aRows[0]['password']))
		    {
		    	/* Create New Login Key */
			    $sRefURL = ( isset($_SESSION['REQUESTURL']) ) ? $_SESSION['REQUESTURL'] : '';
				$sSessionKeyTemp = md5(rand());
				$aFullURL = explode('/osf/install/', $_SERVER['HTTP_REFERER']);
				$sFullURL      = $aFullURL[0];
				$sOffShoot = '/osf/install/';
				if (substr($sFullURL,-strlen($sOffShoot))===$sOffShoot) $sFullURL = substr($sFullURL, 0, strlen($sFullURL)-strlen($sOffShoot));
				setcookie('C_LOGINKEY', $sSessionKeyTemp, time() + (86400 * 30), "/");
				setcookie('C_REF_URL', $sRefURL, time() + (86400 * 30), "/");
				
				/* Create New User Session */
				$iUserID = $aRows[0]['userid'];
				$sIDVerifier = $aRows[0]['idverifier'];
				$sUserName = $aRows[0]['username'];
				$sFirstName = $aRows[0]['firstname'];
				$sLastName = $aRows[0]['lastname'];
				$sEmailID = $aRows[0]['email'];
				$iRoleID = $aRows[0]['roleid'];
				$sEnableMFA = $aRows[0]['enablemfa'];
				//$sSessionKey = $aCookieKey['KEY'];
				$sSessionKey = $sSessionKeyTemp;
				$sDevice = $_SERVER['HTTP_USER_AGENT'];
				$sUserIP = $_SERVER['REMOTE_ADDR'];
				$sOTP    = rand(111111, 999999);
				$sSessionStstus = ($sEnableMFA == 'y') ? '0' : '1';
				$oDateTime = new DateTime("now", new DateTimeZone('GMT'));
				$sDateTimeNow = $oDateTime->format("Y-m-d H:i:s");
				$sQry = "INSERT INTO {$this->APP->TABLEPREFIX}od_usersession SET userid=:userid, idverifier=:idverifier, username=:username, firstname=:firstname, lastname=:lastname, emailid=:emailid, roleid=:roleid, sessionkey=:sessionkey, cookiekey=:cookiekey, device=:device, userip=:userip, otp=:otp, sessionstatus=:sessionstatus, updatedstamp=:updatedstamp";
				$oStmt  = $this->DB->prepare($sQry);
				$oStmt->bindParam(':userid', $iUserID);
				$oStmt->bindParam(':idverifier', $sIDVerifier);
				$oStmt->bindParam(':username', $sUserName);
				$oStmt->bindParam(':firstname', $sFirstName);	
				$oStmt->bindParam(':lastname', $sLastName);	
				$oStmt->bindParam(':emailid', $sEmailID);
				$oStmt->bindParam(':roleid', $iRoleID);
				$oStmt->bindParam(':sessionkey', $sSessionKey);	
				$oStmt->bindParam(':cookiekey', $sSessionKeyTemp);
				$oStmt->bindParam(':device', $sDevice);
				$oStmt->bindParam(':userip', $sUserIP);
				$oStmt->bindParam(':otp', $sOTP);
				$oStmt->bindParam(':sessionstatus', $sSessionStstus);
				$oStmt->bindParam(':updatedstamp', $sDateTimeNow);
				$oStmt->execute();
				$iInsertedId  = $this->DB->lastInsertId();
				//print $aCookieKey['KEY'];
				//print '$iInsertedId = '.$iInsertedId;exit();
				if($iInsertedId > 0)
				{
					if($sEnableMFA == 'y')
					{
						/* Send OTP */
						$sHeaders  = 'MIME-Version: 1.0' . "\r\n";
$sHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$sHeaders .= 'From: noreply@noreply.com<noreply@noreply.com>' . "\r\n";
$sHeaders .= 'Reply-To: noreply@noreply.com' . "\r\n";
$sSubject = 'Your OTP for Application Login';
$sMessage  = <<<FORGOTYOURPWD
<html>
<body>
<p><strong>Dear {$sFirstName}</strong>,</p>
<p>Please use the following One-Time Passcode (OTP) to Sign In at the web address <a href="{$_SERVER['PHP_SELF']}" title="{$_SERVER['PHP_SELF']}">{$_SERVER['PHP_SELF']}</a><br />
<strong>{$sOTP}</strong>
<p><strong>Best Regards</strong></p>
<p><strong>Admin</strong></p>
</body>
</html>
FORGOTYOURPWD;
						if(mail($sEmailID, $sSubject, $sMessage, $sHeaders))
						{
							$this->APP->ERROR['status'] = 0;
							$this->APP->ERROR['text'] = 'An email/SMS (as appropriately setup by your Application Admin) has been sent to you with One-Time Passcode (OTP).';
						}
					}
					else
					{
						/* Success in sign in without MFA */
						$aUserData['ID'] = $iUserID;
						$aUserData['SessionID'] = $iInsertedId;
						$this->goNext($aUserData);

					}
				}
				else
				{
					$this->APP->ERROR['status'] = 1;
					$this->APP->ERROR['text'] = 'The user session could not be created.';
				}
		    }
		    else
		    {
		    	$this->APP->ERROR['status'] = 1;
				$this->APP->ERROR['text'] = 'The Password you entered is not correct.';
		    }
		}
		else
		{
			$this->APP->ERROR['status'] = 1;
			$this->APP->ERROR['text'] = 'The Username you entered is not correct!';
		}
		return true;
	}

	/* Check MFA */
	private function checkMFA($iUserIDTemp)
	{
		$sQryUser = "SELECT enablemfa FROM {$this->APP->TABLEPREFIX}od_user WHERE userid = :userid";
		$oStmt = $this->DB->prepare($sQryUser); 
		$oStmt->bindParam(':userid',$iUserIDTemp);
		$oStmt->execute();
		$iRecordNumber  = $oStmt->rowCount();
		if($iRecordNumber == 1)
		{
			$aRowsUser = $oStmt->fetchAll();
			$sEnableMFA = $aRowsUser[0]['enablemfa'];
		}
		else $sEnableMFA = 'n';
		return $sEnableMFA;
	}

	/* Validate OTP and formalise login */
	public function validateOTP()
	{
		if( !isset($_COOKIE['C_LOGINKEY']) || !isset($_COOKIE['C_REF_URL']) )
		{
			header("Location: $_SERVER[PHP_SELF]");
			exit();
		}
		$sSessionKey = $_COOKIE['C_LOGINKEY'];
		$sRefURL = $_COOKIE['C_REF_URL'];
		$sSessionStatus = '1';
		$sOTP = $_POST['otpText'];
		//print $sSessionKey.'<br>'.$sSessionStatus.'<br>'.$sOTP.'<br>';
		$sQryUser      = "SELECT usersessionid, userid FROM {$this->APP->TABLEPREFIX}od_usersession WHERE sessionkey = :sessionkey AND sessionstatus = :sessionstatus AND otp = :otp";
		$oStmt    	   = $this->DB->prepare($sQryUser); 
		$oStmt->bindParam(':sessionkey',$sSessionKey);
		$oStmt->bindParam(':sessionstatus',$sSessionStatus);
		$oStmt->bindParam(':otp',$sOTP);
		$oStmt->execute();
		$iRecordNumber  = $oStmt->rowCount();
		
		//print $iRecordNumber;exit();
		if ($iRecordNumber == 1)
		{
			$aRowsUserSessions  	= $oStmt->fetchAll();
			$iUserIDTemp = $aRowsUserSessions[0]['userid'];
			$iUserSessionID = $aRowsUserSessions[0]['usersessionid'];
			$aUserData['ID'] = $iUserIDTemp;
			$aUserData['SessionID'] = $iUserSessionID;
			$sEnableMFA = $this->checkMFA($iUserIDTemp);
			if( $sEnableMFA == 'n' )
			{
				header("Location: $_SERVER[PHP_SELF]");
				exit();
			}
			$this->goNext($aUserData);
		}
		else
		{
			$this->APP->ERROR['status'] = 1;
			$this->APP->ERROR['text'] = 'OTP is not correct.';
		}
		return true;
	}

	private function goNext($aUserData)
	{
		
		//print '$iUserSessionID = '.$iUserSessionID.'<br>$iUserIDTemp = '.$iUserIDTemp.'<br>';
		/* Update usersession table */
		$oDateTime = new DateTime("now", new DateTimeZone('GMT'));
		$sDateTimeNow = $oDateTime->format("Y-m-d H:i:s");
		$sSessionStatus = '1';
		$sQry   =  "UPDATE {$this->APP->TABLEPREFIX}od_usersession SET sessionstatus = :sessionstatus, updatedstamp = :updatedstamp WHERE usersessionid = :usersessionid";
		$oStmt  = $this->DB->prepare($sQry);
		$oStmt->bindParam(':sessionstatus', $sSessionStatus);
		$oStmt->bindParam(':updatedstamp', $sDateTimeNow);
		$oStmt->bindParam(':usersessionid', $aUserData['SessionID']);
		$oStmt->execute();
		
		// Get the user details from user table finally
		$sUserStatus = '1';
		$sQryUser      = "SELECT userid, idverifier, username, firstname, lastname, email, roleid FROM {$this->APP->TABLEPREFIX}od_user WHERE userid = :userid AND userstatus = :userstatus";
		$oStmt    	   = $this->DB->prepare($sQryUser);
		$oStmt->bindParam(':userid',$aUserData['ID']);
		$oStmt->bindParam(':userstatus',$sUserStatus);
		$oStmt->execute();
		$iRecordUser  = $oStmt->rowCount();
		//print $iRecordUser;exit();
		if($iRecordUser == 1)
		{
			$aRows  	= $oStmt->fetchAll();
		    foreach($aRows as $aRow)
			{
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
					    $sRoles   = stripslashes($aNumRow['roles']);
					}
					$aRoles = explode(',', $sRoles); 
					if (!in_array($this->iRole, $aRoles)) $bAllowAccess  = false;
			        else
	                {	                    
			            $_SESSION['USERID'] = stripslashes($aRow['userid']);
			            $_SESSION['IDVERIFIER'] = stripslashes($aRow['idverifier']);
					    $bAllowAccess  = true;
	                }
			    }
			    else $bAllowAccess  = false; 
			    
			}
		}
		else $bAllowAccess  = false;
		//print '$bAllowAccess = '.$bAllowAccess;exit();
		if($bAllowAccess)
		{
		    if( $sRefURL != '')
			{
			   header ("Location:{$sRefURL}");
			   exit();
			}
		    else
			{
				$sQry  = "SELECT defaultctrid, defaulteventid FROM {$this->APP->TABLEPREFIX}od_role WHERE roleid = :roleid";
		   	    $oStmt = $this->DB->prepare($sQry); 
				$oStmt->bindParam(':roleid', $this->iRole);
				$oStmt->execute();
                $aRows = $oStmt->fetchAll();
                foreach($aRows as $aRow)
				{
					$iDefaultCtrID   = stripslashes($aRow['defaultctrid']);
					$iDefaultEventID = stripslashes($aRow['defaulteventid']);
				}				
				$sQry  = "SELECT ctrname FROM {$this->APP->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
			   	$oStmt = $this->DB->prepare($sQry); 
				$oStmt->bindParam(':ctrid', $iDefaultCtrID);
				$oStmt->execute();
				$aRows = $oStmt->fetchAll();
			    foreach($aRows as $aRow)
				{
				    $sDefaultCtrlName   = stripslashes($aRow['ctrname']);
				}
				$sRedirectPath = $this->APP->BASEURL.'/'.$sDefaultCtrlName.'?ID='.$iDefaultEventID;
				//print $sRedirectPath;exit();
			    header("Location: $sRedirectPath");
			    exit();				
			}
		}
		else
		{
		    $this->APP->ERROR['status'] = 1;
			$this->APP->ERROR['text'] = 'The Username/Password you entered is not correct.';
		}
		return true;
	}
	
	/* Signin by 3rd party IDP */
	public function signinIDP()
	{
		$sUsername     = $_SESSION['USERNAME'];
		$sPassword     = md5($_SESSION['IDP']);
		$sQryUser      = "SELECT userid, idverifier, roleid FROM {$this->APP->TABLEPREFIX}od_user WHERE username = :username AND idprovider = :idprovider AND idverifier = :idverifier AND userstatus = '1'";
		$oStmt    	   = $this->DB->prepare($sQryUser); 
		$oStmt->bindParam(':username',$sUsername);
		$oStmt->bindParam(':idprovider',$_SESSION['IDP']);
		$oStmt->bindParam(':idverifier',$_SESSION['IDVERIFIER']);
		$oStmt->execute();
		$iRecordNumber  = $oStmt->rowCount();
		//print '$iRecordNumber = '.$iRecordNumber;exit();
		if($iRecordNumber == 1)
		{
		    $aRows  	= $oStmt->fetchAll();
		    foreach($aRows as $aRow)
			{
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
					    $sRoles   = stripslashes($aNumRow['roles']);
					}
					$aRoles = explode(',', $sRoles); 
					if (!in_array($this->iRole, $aRoles)) $bAllowAccess  = false;
			        else
	                {	                    
					    $sErrMsg = '';
			            $_SESSION['USERID'] = stripslashes($aRow['userid']);
			            $_SESSION['IDVERIFIER'] = stripslashes($aRow['idverifier']);
					    $bAllowAccess  = true;
	                }
			    }
			    else $bAllowAccess  = false; 
			    
			}		    
		}
		else $bAllowAccess  = false;
		//print_r($_SESSION);exit();
		if($bAllowAccess)
		{
		    if($_SESSION['REQUESTURL'])
			{
			   //print_r($_SESSION);exit();
			   //$sRedirectPath = $this->APP->BASEURL.$_SESSION['REQUESTURL'];
			   $sRedirectPath = $_SESSION['REQUESTURL'];
			   //print $sRedirectPath;exit();
			   header ("Location:{$sRedirectPath}");
			   exit();
			}
		    else
			{
				$sQry  = "SELECT defaultctrid, defaulteventid FROM {$this->APP->TABLEPREFIX}od_role WHERE roleid = :roleid";
		   	    $oStmt = $this->DB->prepare($sQry); 
				$oStmt->bindParam(':roleid', $this->iRole);
				$oStmt->execute();
                $aRows = $oStmt->fetchAll();
                foreach($aRows as $aRow)
				{
					$iDefaultCtrID   = stripslashes($aRow['defaultctrid']);
					$iDefaultEventID = stripslashes($aRow['defaulteventid']);
				}				
				$sQry  = "SELECT ctrname FROM {$this->APP->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
			   	$oStmt = $this->DB->prepare($sQry); 
				$oStmt->bindParam(':ctrid', $iDefaultCtrID);
				$oStmt->execute();
				$aRows = $oStmt->fetchAll();
			    foreach($aRows as $aRow)
				{
				    $sDefaultCtrlName   = stripslashes($aRow['ctrname']);
				}
				$sRedirectPath = $this->APP->BASEURL.'/'.$sDefaultCtrlName.'?ID='.$iDefaultEventID;
				//print $sRedirectPath;exit();
			    header("Location: $sRedirectPath");
			    exit();
			}
		}
		else
		{
		    $sLastLogin  = gmdate("Y-m-d H:i:s");
			$sIDProvider = $_SESSION['IDP'];
			$sUserToken  = $_SESSION['IDVERIFIER'];
			$sPasswordTemp = md5($_SESSION['IDP']);
			if(trim($_SESSION['LASTNAME']) == '') $_SESSION['LASTNAME'] = ' ';
			$iUserStatus = '1';
			$sQry  =  "INSERT INTO {$this->APP->TABLEPREFIX}od_user (idprovider, idverifier, username, password, email, firstname, lastname, userstatus, roleid, lastlogin) VALUES (:idprovider, :idverifier, :username, :password, :email, :firstname, :lastname, :userstatus, :roleid, :lastlogin)";
			$oStmt = $this->DB->prepare($sQry);
			$oStmt->bindParam(':idprovider', $sIDProvider);
			$oStmt->bindParam(':idverifier', $sUserToken);
			$oStmt->bindParam(':username', $_SESSION['USERNAME']);
			$oStmt->bindParam(':password', $sPasswordTemp);
			$oStmt->bindParam(':email', $_SESSION['USERNAME']);
			$oStmt->bindParam(':firstname', $_SESSION['FIRSTNAME']);
			$oStmt->bindParam(':lastname', $_SESSION['LASTNAME']);
			$oStmt->bindParam(':userstatus', $iUserStatus);
			$oStmt->bindParam(':roleid', $this->APP->iSSODefaultRole);
			$oStmt->bindParam(':lastlogin', $sLastLogin);
			$oStmt->execute();
			//echo $oStmt->debugDumpParams();
			//echo var_export($oStmt->errorInfo());exit();
			$sQry  = "SELECT defaultctrid, defaulteventid FROM {$this->APP->TABLEPREFIX}od_role WHERE roleid = :roleid";
		   	$oStmt = $this->DB->prepare($sQry); 
			$oStmt->bindParam(':roleid', $this->APP->iSSODefaultRole);
			$oStmt->execute();
            $aRows = $oStmt->fetchAll();
            foreach($aRows as $aRow)
			{
				$iDefaultCtrID   = stripslashes($aRow['defaultctrid']);
				$iDefaultEventID = stripslashes($aRow['defaulteventid']);
			}				
			$sQry  = "SELECT ctrname FROM {$this->APP->TABLEPREFIX}od_controller WHERE ctrid = :ctrid";
		   	$oStmt = $this->DB->prepare($sQry); 
			$oStmt->bindParam(':ctrid', $iDefaultCtrID);
			$oStmt->execute();
			$aRows = $oStmt->fetchAll();
		    foreach($aRows as $aRow)
			{
			    $sDefaultCtrlName   = stripslashes($aRow['ctrname']);
			}
			$sRedirectPath = $this->APP->BASEURL.'/'.$sDefaultCtrlName.'?ID='.$iDefaultEventID;
			//print $sRedirectPath;exit();
		    header("Location: $sRedirectPath");
		    exit();
		}
		return true;
	}

	/* Forgot your password */
    public function getPassword($sForgotPwdSec)
    {
	    $sWebsiteURL     = $this->APP->BASEURL;
	    $sMsg            = '';
	    $sSqlPwdDetails  = "SELECT userid, firstname, username  FROM {$this->APP->TABLEPREFIX}od_user WHERE email = :email";
		$oStmt    	   	 = $this->DB->prepare($sSqlPwdDetails); 
		$oStmt->bindParam(':email',$sForgotPwdSec); 
		$oStmt->execute();
		$aForgotPwdSec   = $oStmt->fetchAll();
	    $iRecordNumber   = $oStmt->rowCount();	
        if($iRecordNumber == 0) 
        {
            $sMsg = 'The email id does not exist';
            return $sMsg;
        }	    	
		else 
		{
		    foreach($aForgotPwdSec as $aRowPwd)
			{
		    	$sFirstName       = stripslashes(trim($aRowPwd['firstname']));
		    	$iId              = stripslashes(trim($aRowPwd['userid']));
		    	$sUsername        = stripslashes(trim($aRowPwd['username']));
		    	$sPwd             = $this->createPassword();
		    	$sUpdatePassword  = $this->updatePassword($sPwd,$iId);
			}			    
			// Include file to send the message to the User
			include $this->APP->BASEURL.'/osf/view/forgot-pwd-msg.tpl.php';
			if(mail($sForgotPwdSec, $sSubject, $sMessage, $sHeaders))
			{
				$sMsg  = 'An email has been sent with your sign in details. Please <a href='.$_SERVER[PHP_SELF].' title="Click here">click here</a> to Sign In with the sent details.';
				return $sMsg;
			}			
	    }		    	
	}
	
	/* Function to create a new password */
	private function createPassword($len = 6)
    {        
    	$chars = uniqid();
	    $s = "";
	    for ($i = 0; $i < $len; $i++) {
	        $int         = rand(0, strlen($chars)-1);
	        $rand_letter = $chars[$int];
	        $s           = $s . $rand_letter;
	    }
	    return $s;
	}	

	/* Function to update password for the user*/
	private function updatePassword($sPassword,$iId)
    {        
        $sNewPassword = md5($sPassword);
        $sQry  		  = "UPDATE {$this->APP->TABLEPREFIX}od_user SET password = :password WHERE userid = :userid";
	    $oStmt    	  = $this->DB->prepare($sQry); 
		$oStmt->bindParam(':password',$sNewPassword);
		$oStmt->bindParam(':userid',$iId); 
		$oStmt->execute();
    	return true;
	}

	/* Function for signing out the user from the system */
	public function signOut()
    {        
        if( (isset($_COOKIE['C_LOGINKEY'])) && (isset($_COOKIE['C_REF_URL'])) )
		{
			//$aCookieKey = unserialize(base64_decode($_COOKIE['C_LOGINKEY']));
			$sSessionKey = $_COOKIE['C_LOGINKEY'];
			if($sSessionKey != '')
			{
				$sQry = "DELETE from {$this->APP->TABLEPREFIX}od_usersession WHERE sessionkey = :sessionkey";
				$oStmt = $this->DB->prepare($sQry);
				$oStmt->bindParam(':sessionkey', $sSessionKey);
				$oStmt->execute();
			}
		}
        //session_start();
        session_unset();
        session_destroy();
        unset($_COOKIE['C_LOGINKEY']);
        setcookie('C_LOGINKEY', '', time() - 3600);
        unset($_COOKIE['C_REF_URL']);
        setcookie('C_REF_URL', '', time() - 3600);
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    	return true;
	}
	
}//End of class

?>