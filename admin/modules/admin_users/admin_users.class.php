<?
/*******************************************************************
*
* Karl Steltenpohl Development 
* Web Business Framework
* Version 1.0
* Copyright 2009 Karl Steltenpohl Development All Rights Reserved
*
*******************************************************************/
class AdminUsers {
	
	//var $title;
	
	/*** CLASS CONSTRUCTOR ***/
	function AdminUsers(){
		//$this->title = "";
	}
	
	/*** ADD PERMISSIONS ***/
	function ConcatSpecialPrivs ($xid, $priv, $update=0){
		global $_SESSION;
		
		// GET THE CURRENT PERMISSION STRING
		$select = 	"SELECT special_privs ".
				"FROM admin WHERE ".
				"admin_id='".$xid."' ".
				"".$_SESSION['demosqland']."";
					
		$res = doquery($select);
		$row = mysql_fetch_Array($res);

		// IF THE PERMISSION IS IN THE DATABASE ALREADY
		// THEN VALUE = JUST THIS PRIVILEDGE
		if (strstr($row["special_privs"],$priv)) {
			$val = $row["special_privs"];
		}
		
		if ($update == 1) {	
			$val = $row["special_privs"].",".$priv;	
			$select = 	"UPDATE admin SET ".
						"special_privs='$val' ".
						"where ".
						"admin_id=$xid ".
						"".$_SESSION['demosqland']."";			
			doQuery($select);
		}

			
		$val = ",".$priv;
		
		return $val;
	}

	/*** REMOVE PERMISSIONS ***/
	function RemoveSpecialPriv ($xid, $priv,$update=0){

		global $_SESSION;
		
		$select = 	"SELECT special_privs FROM admin WHERE ".
					"admin_id=$xid ".
					"".$_SESSION['demosqland']."";
		
		$res = doquery($select);
		$row = mysql_fetch_Array($res);

		$val = str_replace(",,",",",str_replace($priv,"",$row["special_privs"]));
		
		if ($update == 1) {		
			$select = 	"UPDATE admin SET special_privs='$val' where ".
						"admin_id=$xid ".
						"".$_SESSION['demosqland']."";			
			doQuery($select);
		}

		$val = "";
		
		return $val;
	}
	
}
?>