<?
/*************************************************************************************************************************************
*
*	88      a8P   ad88888ba   88888888ba,    
*	88    ,88'   d8"     "8b  88      `"8b   
*	88  ,88"     Y8,          88        `8b  
*	88,d88'      `Y8aaaaa,    88         88  
*	8888"88,       `"""""8b,  88         88  
*	88P   Y8b            `8b  88         8P  
*	88     "88,  Y8a     a8P  88      .a8P   
*	88       Y8b  "Y88888P"   88888888Y"'    
*
* 	This file is part of KSD's Wes software.
*   Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	either the Wes Forever License (the "Forever License"),
*	or the Wes Annual Licencse (the "Annual License");
*	you may not use this file exept in compliance
*	with at least one Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/
include'../../../includes/config.php';


//Begin writing headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");

//Use the switch-generated Content-Type
header("Content-Type: application/vnd.ms-excel");

//Force the download
$header="Content-Disposition: attachment; filename=Events_Subscribers.xls;";
header($header);
header("Content-Transfer-Encoding: binary");


$select = "SELECT * FROM event_alert WHERE active='1' ORDER BY created DESC";
$result = doQuery($select);


$data = "\"Name\"	\"Email\"	\"Email\"	\"Created\"\n";
while ($row = mysql_Fetch_array($result)) {
	$data .= "\"{$row["name"]}\"	\"{$row["email"]}\"	\"{$row['opt_in']}\"	\"{$row["created"]}\"\n";
}

echo $data;
?>