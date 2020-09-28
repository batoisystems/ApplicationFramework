<?php
session_start();
$config = dirname(realpath(dirname(__FILE__))) . "/osf/lib/hybridauth/config.php";
//if(file_exists($config)) print 'Hi';
require_once( dirname(realpath(dirname(__FILE__)))."/osf/lib/hybridauth/Hybrid/Auth.php" );
if( isset($_GET['IDP']) )
{
	if( $_GET['IDP'] == 'Google' )
	{
		try{
		  	$hybridauth = new Hybrid_Auth( $config );
			$adapter = $hybridauth->authenticate( "Google" );
			$aUserProfile = $adapter->getUserProfile();
			//print_r($aUserProfile);exit();
			$_SESSION['IDP'] = $adapter->id;
			$_SESSION['USERNAME'] = $aUserProfile->displayName;
			$_SESSION['FIRSTNAME'] = $aUserProfile->firstName;
			$_SESSION['LASTNAME'] = $aUserProfile->lastName;
			$_SESSION['IDVERIFIER'] = $aUserProfile->identifier;
		  }
		  catch( Exception $e ){
		  	switch( $e->getCode() ){
		  	  case 0 : echo "Unspecified error."; break;
		  	  case 1 : echo "Hybriauth configuration error."; break;
		  	  case 2 : echo "Provider not properly configured."; break;
		  	  case 3 : echo "Unknown or disabled provider."; break;
		  	  case 4 : echo "Missing provider application credentials."; break;
		  	  case 5 : echo "Authentification failed. "
		  	              . "The user has canceled the authentication or the provider refused the connection.";
		  	           break;
		  	  case 6 : echo "User profile request failed. Most likely the user is not connected "
		  	              . "to the provider and he should authenticate again.";
		  	           $adapter->logout();
		  	           break;
		  	  case 7 : echo "User not connected to the provider.";
		  	           $adapter->logout();
		  	           break;
		  	  case 8 : echo "Provider does not support this feature."; break;
		  	}
		  	echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
		}
	}
	elseif( $_GET['IDP'] == 'Yahoo' )
	{
		try{
		  	$hybridauth = new Hybrid_Auth( $config );
			$adapter = $hybridauth->authenticate( "Yahoo" );
			$aUserProfile = $adapter->getUserProfile();
			//print_r($aUserProfile);exit();
			$_SESSION['IDP'] = $adapter->id;
			$_SESSION['USERNAME'] = $aUserProfile->displayName;
			$_SESSION['FIRSTNAME'] = $aUserProfile->firstName;
			$_SESSION['LASTNAME'] = $aUserProfile->lastName;
			$_SESSION['IDVERIFIER'] = $aUserProfile->identifier;
		  }
		  catch( Exception $e ){
		  	switch( $e->getCode() ){
		  	  case 0 : echo "Unspecified error."; break;
		  	  case 1 : echo "Hybriauth configuration error."; break;
		  	  case 2 : echo "Provider not properly configured."; break;
		  	  case 3 : echo "Unknown or disabled provider."; break;
		  	  case 4 : echo "Missing provider application credentials."; break;
		  	  case 5 : echo "Authentification failed. "
		  	              . "The user has canceled the authentication or the provider refused the connection.";
		  	           break;
		  	  case 6 : echo "User profile request failed. Most likely the user is not connected "
		  	              . "to the provider and he should authenticate again.";
		  	           $adapter->logout();
		  	           break;
		  	  case 7 : echo "User not connected to the provider.";
		  	           $adapter->logout();
		  	           break;
		  	  case 8 : echo "Provider does not support this feature."; break;
		  	}
		  	echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
		}
	}
	elseif( $_GET['IDP'] == 'Live' )
	{
		try{
		  	$hybridauth = new Hybrid_Auth( $config );
			$adapter = $hybridauth->authenticate( "Live" );
			$aUserProfile = $adapter->getUserProfile();
			//print_r($aUserProfile);exit();
			$_SESSION['IDP'] = $adapter->id;
			$_SESSION['USERNAME'] = $aUserProfile->displayName;
			$_SESSION['FIRSTNAME'] = $aUserProfile->firstName;
			$_SESSION['LASTNAME'] = $aUserProfile->lastName;
			$_SESSION['IDVERIFIER'] = $aUserProfile->identifier;
		  }
		  catch( Exception $e ){
		  	switch( $e->getCode() ){
		  	  case 0 : echo "Unspecified error."; break;
		  	  case 1 : echo "Hybriauth configuration error."; break;
		  	  case 2 : echo "Provider not properly configured."; break;
		  	  case 3 : echo "Unknown or disabled provider."; break;
		  	  case 4 : echo "Missing provider application credentials."; break;
		  	  case 5 : echo "Authentification failed. "
		  	              . "The user has canceled the authentication or the provider refused the connection.";
		  	           break;
		  	  case 6 : echo "User profile request failed. Most likely the user is not connected "
		  	              . "to the provider and he should authenticate again.";
		  	           $adapter->logout();
		  	           break;
		  	  case 7 : echo "User not connected to the provider.";
		  	           $adapter->logout();
		  	           break;
		  	  case 8 : echo "Provider does not support this feature."; break;
		  	}
		  	echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
		}
	}
	elseif( $_GET['IDP'] == 'Twitter' )
	{
		try{
		  	$hybridauth = new Hybrid_Auth( $config );
			$adapter = $hybridauth->authenticate( "Twitter" );
			$aUserProfile = $adapter->getUserProfile();
			//var_dump($aUserProfile);
			$_SESSION['IDP'] = $adapter->id;
			$_SESSION['USERNAME'] = $aUserProfile->displayName;
			$_SESSION['FIRSTNAME'] = $aUserProfile->firstName;
			$_SESSION['LASTNAME'] = $aUserProfile->lastName;
			$_SESSION['IDVERIFIER'] = $aUserProfile->identifier;
		  }
		  catch( Exception $e ){
		  	switch( $e->getCode() ){
		  	  case 0 : echo "Unspecified error."; break;
		  	  case 1 : echo "Hybriauth configuration error."; break;
		  	  case 2 : echo "Provider not properly configured."; break;
		  	  case 3 : echo "Unknown or disabled provider."; break;
		  	  case 4 : echo "Missing provider application credentials."; break;
		  	  case 5 : echo "Authentification failed. "
		  	              . "The user has canceled the authentication or the provider refused the connection.";
		  	           break;
		  	  case 6 : echo "User profile request failed. Most likely the user is not connected "
		  	              . "to the provider and he should authenticate again.";
		  	           $adapter->logout();
		  	           break;
		  	  case 7 : echo "User not connected to the provider.";
		  	           $adapter->logout();
		  	           break;
		  	  case 8 : echo "Provider does not support this feature."; break;
		  	}
		  	echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
		}
	}
	elseif( $_GET['IDP'] == 'Facebook' )
	{
		try{
		  	$hybridauth = new Hybrid_Auth( $config );
			$adapter = $hybridauth->authenticate( "Facebook" );
			$aUserProfile = $adapter->getUserProfile();
			//print_r($aUserProfile);exit();
			$_SESSION['IDP'] = $adapter->id;
			$_SESSION['USERNAME'] = $aUserProfile->displayName;
			$_SESSION['FIRSTNAME'] = $aUserProfile->firstName;
			$_SESSION['LASTNAME'] = $aUserProfile->lastName;
			$_SESSION['IDVERIFIER'] = $aUserProfile->identifier;
		  }
		  catch( Exception $e ){
		  	switch( $e->getCode() ){
		  	  case 0 : echo "Unspecified error."; break;
		  	  case 1 : echo "Hybriauth configuration error."; break;
		  	  case 2 : echo "Provider not properly configured."; break;
		  	  case 3 : echo "Unknown or disabled provider."; break;
		  	  case 4 : echo "Missing provider application credentials."; break;
		  	  case 5 : echo "Authentification failed. "
		  	              . "The user has canceled the authentication or the provider refused the connection.";
		  	           break;
		  	  case 6 : echo "User profile request failed. Most likely the user is not connected "
		  	              . "to the provider and he should authenticate again.";
		  	           $adapter->logout();
		  	           break;
		  	  case 7 : echo "User not connected to the provider.";
		  	           $adapter->logout();
		  	           break;
		  	  case 8 : echo "Provider does not support this feature."; break;
		  	}
		  	echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
		}
	}
	else{print 'Error Selection!';exit();}
}
else {
	print 'Error!';
	exit();
}
header("location:index.php?ID=1&IDP={$_SESSION['IDP']}");
exit();
?>