<?php
/*------------------------------------------------------------------------------+
 * Batoi Open Source Framework (OSF) - An pplication Development Framework      |
 * (c)Copyright Ashwini Kumar Rath. All rights reserved.                        |
 * Website: https://framework.batoi.com                                         |
 * Licensed under the terms of the GNU General Public License Version 3 or later|
 * (the "GPL"): http://www.gnu.org/licenses/gpl.html                            |
 * NOTE: The copyright notice like this on any of the distributed files         |
 *       (downloaded or obtained as the part of Batoi Open Source Framework)    |
 *       must NOT be removed or modified.                                       |
 *------------------------------------------------------------------------------+
 */
class OSF_Grid
{
	/* CONSTRUCTOR */
	function __construct() 
	{
	    global $DB,$APP,$USER;
		$this->DB   = $DB;
		$this->APP  = $APP;	
		$this->USER = $USER;	
		return true;
	}
	
	private function funcStrip($value)
	{
		if(get_magic_quotes_gpc() != 0)
	  	{
	    	if(is_array($value))  
				if ($this->array_is_associative($value))
				{
					foreach( $value as $k=>$v)
						$tmp_val[$k] = stripslashes($v);
					$value = $tmp_val; 
				}				
				else  
					for($j = 0; $j < sizeof($value); $j++)
	        			$value[$j] = stripslashes($value[$j]);
			else
				$value = stripslashes($value);
		}
		return $value;
	}
	
	
	private function array_is_associative ($array)
	{
	    if ( is_array($array) && ! empty($array) )
	    {
	        for ( $iterator = count($array) - 1; $iterator; $iterator-- )
	        {
	            if ( ! array_key_exists($iterator, $array) ) { return true; }
	        }
	        return ! array_key_exists(0, $array);
	    }
	    return false;
	}

	
	private function funcSearchParam()
	{
		$sFld = $this->funcStrip($_POST['searchField']);
		if($sFld) 
		{
			$sFldata = $this->funcStrip($_POST['searchString']);
			$sFoper  = $this->funcStrip($_POST['searchOper']);
			
			$sWh .= " AND ".$sFld;
			switch ($sFoper) {
				case "bw":
					$sFldata .= "%";
					$sWh .= " LIKE '".$sFldata."'";
					break;
				case "eq":
					if(is_numeric($sFldata)) {
						$sWh .= " = ".$sFldata;
					} else {
						$sWh .= " = '".$sFldata."'";
					}
					break;
				case "ne":
					if(is_numeric($sFldata)) {
						$sWh .= " <> ".$sFldata;
					} else {
						$sWh .= " <> '".$sFldata."'";
					}
					break;
				case "lt":
					if(is_numeric($sFldata)) {
						$sWh .= " < ".$sFldata;
					} else {
						$sWh .= " < '".$sFldata."'";
					}
					break;
				case "le":
					if(is_numeric($sFldata)) {
						$sWh .= " <= ".$sFldata;
					} else {
						$sWh .= " <= '".$sFldata."'";
					}
					break;
				case "gt":
					if(is_numeric($sFldata)) {
						$sWh .= " > ".$sFldata;
					} else {
						$sWh .= " > '".$sFldata."'";
					}
					break;
				case "ge":
					if(is_numeric($sFldata)) {
						$sWh .= " >= ".$sFldata;
					} else {
						$sWh .= " >= '".$sFldata."'";
					}
					break;
				case "ew":
					$sWh .= " LIKE '%".$sFldata."'";
					break;
				case "ew":
					$sWh .= " LIKE '%".$sFldata."%'";
					break;
				default :
					$sWh = "";
			}
		}
		return $sWh;
	}
	
	function display() 
	{
		$iPage 	= $_POST['page']; // get the requested page
		$iLimit = $_POST['rows']; // get how many rows we want to have into the grid
		$iIdx 	= $_POST['sidx']; // get index row - i.e. user click to sort
		$iSord 	= $_POST['sord']; // get the direction
		if(!$iIdx) $iIdx =1;
		$sWh = "";
		
		$sSearchOn = $this->funcStrip($_POST['_search']);
		if($sSearchOn=='true') $sWh = $this->funcSearchParam();		
		
		$sQryNumRow = "SELECT userid FROM {$this->APP->TABLEPREFIX}addressbk WHERE userid = :userid".$sWh;  // cal the no. of rows for the query for paging the result 
		$oStmt      = $this->DB->prepare($sQryNumRow);
		$oStmt->bindParam(':userid', $this->USER->ID);	    
	    $oStmt->execute();
	    $iCount     = $oStmt->rowCount();  
				
		if( $iCount >0 ) $iTotalPages = ceil($iCount/$iLimit);
		else $iTotalPages = 0;
        if ($iPage > $iTotalPages) $iPage=$iTotalPages;
		$iStart = $iLimit*$iPage - $iLimit; // do not put $iLimit*($iPage - 1)
        if ($iStart<0) $iStart = 0; 
        
        $sSqlData = "SELECT addressbkid, userid, addbkfirstname, addbklastname, addbkemail, addbkcell, addbkgender, addbkphoto, addbknote, addbkcreateddate, addbkupdateddate FROM {$this->APP->TABLEPREFIX}addressbk WHERE userid = :userid ".$sWh." ORDER BY addbkupdateddate ". $iSord." LIMIT ".$iStart." , ".$iLimit; 
		$oStmt    = $this->DB->prepare($sSqlData);
		$oStmt->bindParam(':userid', $this->USER->ID);	    
	    $oStmt->execute();		
		$aRowData = $oStmt->fetchAll();
		
	    if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) 
	    {
  			header("Content-type: application/xhtml+xml;charset=utf-8"); 
	    } 
	    else 
	    {
  			header("Content-type: text/xml;charset=utf-8");
		}
		$et = ">";
  		$s = "<?xml version='1.0' encoding='utf-8'?$et\n";
		$s .= "<rows>";
		$s .= "<page>".$iPage."</page>";
		$s .= "<total>".$iTotalPages."</total>";
		$s .= "<records>".$iCount."</records>";
		
		foreach($aRowData as $aRow)
		{
			$s .= "<row id='". stripslashes($aRow[addressbkid])."'>";			
			$s .= "<cell>". stripslashes($aRow[addressbkid])."</cell>";
			$s .= "<cell><![CDATA[". stripslashes($aRow[addbkfirstname])."]]></cell>";
			$s .= "<cell><![CDATA[". stripslashes($aRow[addbklastname])."]]></cell>";
			$s .= "<cell><![CDATA[". stripslashes($aRow[addbkemail])."]]></cell>";
			$s .= "<cell>". $aRow[addbkcell]."</cell>";	
			if( $aRow[addbkgender] == '1') $s .= "<cell>Female</cell>";
			else if( $aRow[addbkgender] == '2') $s .= "<cell>Male</cell>";			
			if( $aRow[addbkphoto] != '') $s .= "<cell>". stripslashes($aRow[addbkphoto])."</cell>";	
			else $s .= "<cell>N/A</cell>";		
			$s .= "<cell><![CDATA[". stripslashes($aRow[addbknote])."]]></cell>";	
			$s .= "</row>";
		}			
		$s .= "</rows>";	
		print $s;
		return true;		
	}
	
	function edit() 
	{
		$sOperation	  =	$_REQUEST['oper'];
		$iID    	  = $_POST['id'];
		$sFirstName   = $_POST['addbkfirstname'];
    	$sLastName    = $_POST['addbklastname'];    	
    	$sEmail       = $_POST['addbkemail'];
    	$sCellNo      = $_POST['addbkcell'];
    	$iGender      = $_POST['addbkgender'];
    	$sNote        = $_POST['addbknote']; 
    	$iUserId      = $this->USER->ID;   	
		if($sOperation == 'add')
		{
			$sQry  =  "INSERT INTO {$this->APP->TABLEPREFIX}addressbk (userid, addbkfirstname, addbklastname, addbkemail, addbkcell, addbkgender, addbknote)VALUES('$iUserId', '$sFirstName','$sLastName','$sEmail','$sCellNo','$iGender', '$sNote')";
			$oStmt = $this->DB->prepare($sQry);
			$oStmt->bindParam(':userid', $this->USER->ID);
			$oStmt->bindParam(':addbkfirstname', $sFirstName);
			$oStmt->bindParam(':addbklastname', $sLastName);
			$oStmt->bindParam(':addbkemail', $sEmail);
			$oStmt->bindParam(':addbkcell', $sCellNo);
			$oStmt->bindParam(':addbkgender', $iGender);
			$oStmt->bindParam(':addbknote', $sNote);
				
			
		}
		elseif($sOperation == 'edit')
		{
			 $sQry = "UPDATE {$this->APP->TABLEPREFIX}addressbk SET addbkfirstname = '$sFirstName', addbklastname = '$sLastName', addbkemail = '$sEmail',addbkcell = '$sCellNo',addbkgender = '$iGender', addbknote = '$sNote' WHERE addressbkid = '$iID'";
			 $oStmt = $this->DB->prepare($sQry);
			 $oStmt->bindParam(':userid', $this->USER->ID);
			 $oStmt->bindParam(':addbkfirstname', $sFirstName);
			 $oStmt->bindParam(':addbklastname', $sLastName);
			 $oStmt->bindParam(':addbkemail', $sEmail);
			 $oStmt->bindParam(':addbkcell', $sCellNo);
			 $oStmt->bindParam(':addbkgender', $iGender);
			 $oStmt->bindParam(':addbknote', $sNote);
		}
		elseif($sOperation == 'del') 
		{
			$sQry = "DELETE from {$this->APP->TABLEPREFIX}addressbk WHERE addressbkid = '$iID'";
			$oStmt = $this->DB->prepare($sQry);
		}
		$oStmt->execute();	
    	return true;		
	}	
	
} //END OF CLASS
?>