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

class Settings {
	
	//var $title;
	
	function Settings()
	{
		//$this->title = "";
	}
	
	//
	// FUNCTION DISPLAY SETTING FIELD
	//
	function displaySettingField($rowa,$j=0,$ja=0){
		global $_SESSION;
		global $_SETTINGS;
	
		// GET VALUE IF USER SETTING
		if($rowa['user_setting'] == "1"){
			$sel2 = "SELECT * FROM settings_user WHERE ".
					"admin_user_id='".$_SESSION["session"]->admin->userid."' AND ".
					"setting_id='".$rowa['id']."' LIMIT 1";
			$res2 = doQuery($sel2);
			$row2 = mysql_fetch_array($res2);
			$rowa['value'] = $row2['value'];
		}							
		
		if(($rowa['type'] != "page") OR ($rowa['type'] == "page" AND checkActiveModule('0000000'))){
			?>			
			<TR>
				<Th width="300" height="40" style="padding-left:20px;">
				<?
				if($rowa['user_friendly_name'] != ""){
					echo $rowa['user_friendly_name'];
					$namer = $rowa['user_friendly_name'];
				} else {		
					echo $rowa['name'];
					$namer = $rowa['name'];
				}	
				
				echo "<br><small>".strtolower(str_replace(" ","_",$rowa['name']))."</small>"; 
				
				//
				// THE INFO TIP
				//
				info('<b>'.escape_smart($namer).'</b><br><small><i>$_SETTINGS["'.strtolower(str_replace(" ","_",$rowa['name'])).'"]</i></small><hr>'.escape_smart($rowa['description']).'');
				?>
				</Th>
				<TD>
				
				<?
				//
				// TEXTBOX
				//
				if($rowa['type'] == "Textbox"){
				?>
					<input type="text" size="50" name="<?=$rowa['name']?>" value="<?=$rowa['value']?>" />
					<?
				}
				?>
				
				
				<?
				if($rowa['type'] == "Image"){
					?>					
					<input type="text" name="<?=$rowa['name']?>" value="<?=basename($rowa['value'])?>" />
					<button type="button" onClick="SmallFileBrowser('../uploads/','<?=$rowa['name']?>')">Choose Image...</button>		
					<?
				}
				?>
				
				
				<?
				//
				// TEXTAREA
				//
				if($rowa['type'] == "Textarea"){
				?>
					<textarea name="<?=$rowa['name']?>" onFocus="textareaexpand(this);" onBlur="textareaclose(this);" style="height:50px;"><?=$rowa['value']?></textarea>
				<?
				}
				?>
				
				<?
				//
				// PAGE CLEAN URL
				// CMS MUST BE INSTALLED
				//
				if($rowa['type'] == "page"){
				?>
					<select name="<?=$rowa['name']?>">
						<option value=''> -- Select Page -- </option>
						<?
						$sel1 = "SELECT clean_url_name FROM pages WHERE active='1' ORDER BY clean_url_name ASC";
						$res1 = doQuery($sel1);
						$num1 = mysql_num_rows($res1);
						$i1 = 0;
						while($i1<$num1){
							$rowa1 = mysql_fetch_array($res1);
							?><option <? if($rowa['value'] == $rowa1['clean_url_name']){ ?> SELECTED <? } ?> value="<?=$rowa1['clean_url_name']?>"><?=$rowa1['clean_url_name']?></option><?
							$i1++;
						}
						?>
						
					</select>
					
				<?
				}
				?>
				
				<?
				//
				// BOOLEAN
				//					
				if($rowa['type'] == "Boolean"){					
				?>
					<span style="display:block; float:left; margin-right:5px;"><br>
					<input type="radio" style="display:inline-block;" name="<?=$rowa['name']?>" value="1" <? if($rowa['value'] == '1'){ ?> CHECKED <? } ?> /> <span style="display:inline-block;">Yes <small><i>True</i></small></span>
					<br>
					<input type="radio" style="display:inline-block;" name="<?=$rowa['name']?>" value="0" <? if($rowa['value'] == '0'){ ?> CHECKED <? } ?> />  <span style="display:inline-block;">No <small><i>False</i></small></span>
					<br><br></span>
				<?
				}					
				?>
				
				<?
				//
				// TABLE ROW ID
				//					
				if($rowa['type'] == "table_row_id"){
					$flag = 0;
					if(strstr($rowa['table'],":")){
						$tableArray = explode(":",$rowa['table']);
						$rowa['table'] = $tableArray[0];						
						$table = $tableArray[0];
						$flag = 1;
					} else {
						$table = $rowa['table'];
					}
					
					//echo "Table: ".$table."<br>";
					?>
					<select name="<?=$rowa['name']?>">
						<option value="0" <? if($rowa['value'] == '0'){ ?> SELECTED <? } ?>> -- Select Setting -- </option>
						<?
						$sel1 = "SELECT * FROM `".$table."` WHERE active='1'";
						$res1 = doQuery($sel1);
						$num1 = mysql_num_rows($res1);
						$i1 = 0;
						while($i1<$num1){
							$rowa1 = mysql_fetch_array($res1);
							$option = $rowa1['name'];
							if($flag == 1){ $option = $rowa1[$tableArray[1]]; }
							?><option <? if($rowa['value'] == $rowa1[0]){ ?> SELECTED <? } ?> value="<?=$rowa1[0]?>"><?=$option?></option><?
							$i1++;
						}
						?>						
					</select>
				<?
				}					
				?>				
				</TD>
			</TR>	
		<?
		}
	}
	
	//
	//
	//
	function updateSetting($row){
	
		global $_POST;
		global $_SESSION;
		global $_SETTINGS;
		
		//$_POST = escape_smart_array($_POST);
		$value = $_POST[str_replace(" ","_",$row['name'])];
		
		if($row['type'] == 'Image'){
			$value = basename($value);
		}
		
		
		
		if($row['user_setting'] == "1"){
			//
			// CHECK UPDATE USER SETTING
			//
			$sel1 = "SELECT * FROM settings_user WHERE ".
					"admin_user_id='".$_SESSION["session"]->admin->userid."' AND ".
					"setting_id='".$row['id']."' AND ".
					"active='1' ".
					"".$_SETTINGS['demosqland']." LIMIT 1";
			$res1 = doQuery($sel1);		
			$num1 = mysql_num_rows($res1);
			
			if($num1 < 1){				
				//
				// INSERT USER SETTING VALUE
				//
				$sel1 = "INSERT INTO settings_user SET ".
						"setting_id='".$row['id']."',".
						"admin_user_id='".$_SESSION["session"]->admin->userid."',".
						"value='".$value."',".
						"active='1'".
						"".$_SETTINGS['demosql']."";
				$res1 = doQuery($sel1);		
				$num1 = mysql_num_rows($res1);			
			} else {				
				//
				// UPDATE USER SETTING VALUE
				//
				$sel1 = "UPDATE settings_user SET ".
						"value='".$value."' ".
						"WHERE ".
						"setting_id='".$row['id']."' AND ".
						"admin_user_id='".$_SESSION["session"]->admin->userid."' AND ".						
						"active='1' ".
						"".$_SETTINGS['demosqland']."";
				$res1 = doQuery($sel1);		
				$num1 = mysql_num_rows($res1);				
			}									
		} else {			
			//
			// UPDATE UNIVERSAL SETTINGS
			//
			$sel1 = "UPDATE settings SET ". 
					"`value`='".escape_smart($value)."' ".
					"WHERE ".
					"`name`='".$row['name']."' AND `active`='1' ".
					"".$_SETTINGS['demosqland']."";
			$res1 = doQuery($sel1);
			if(mysql_error()){
				echo "<br>".$sel1;
				echo "<br>".mysql_error();
				die;
				exit; 
			}			
		}	
	}
}


/**
* Class to dynamically create a zip file (archive)
*
* @author Rochak Chauhan
*/

class createZip  {  

    public $compressedData = array();
    public $centralDirectory = array(); // central directory   
    public $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    public $oldOffset = 0;

    /**
     * Function to create the directory where the file(s) will be unzipped
     *
     * @param $directoryName string
     *
     */
    
    public function addDirectory($directoryName) {
        $directoryName = str_replace("\\", "/", $directoryName);  

        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x0a\x00";    
        $feedArrayRow .= "\x00\x00";    
        $feedArrayRow .= "\x00\x00";    
        $feedArrayRow .= "\x00\x00\x00\x00";

        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("v", strlen($directoryName) );
        $feedArrayRow .= pack("v", 0 );
        $feedArrayRow .= $directoryName;  

        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);

        $this -> compressedData[] = $feedArrayRow;
        
        $newOffset = strlen(implode("", $this->compressedData));

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x0a\x00";    
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("v", strlen($directoryName) );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $ext = "\x00\x00\x10\x00";
        $ext = "\xff\xff\xff\xff";  
        $addCentralRecord .= pack("V", 16 );

        $addCentralRecord .= pack("V", $this -> oldOffset );
        $this -> oldOffset = $newOffset;

        $addCentralRecord .= $directoryName;  

        $this -> centralDirectory[] = $addCentralRecord;  
    }    
    
    /**
     * Function to add file(s) to the specified directory in the archive
     *
     * @param $directoryName string
     *
     */
    
    public function addFile($data, $directoryName)   {

        $directoryName = str_replace("\\", "/", $directoryName);  
    
        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x14\x00";    
        $feedArrayRow .= "\x00\x00";    
        $feedArrayRow .= "\x08\x00";    
        $feedArrayRow .= "\x00\x00\x00\x00";

        $uncompressedLength = strlen($data);  
        $compression = crc32($data);  
        $gzCompressedData = gzcompress($data);  
        $gzCompressedData = substr( substr($gzCompressedData, 0, strlen($gzCompressedData) - 4), 2);
        $compressedLength = strlen($gzCompressedData);  
        $feedArrayRow .= pack("V",$compression);
        $feedArrayRow .= pack("V",$compressedLength);
        $feedArrayRow .= pack("V",$uncompressedLength);
        $feedArrayRow .= pack("v", strlen($directoryName) );
        $feedArrayRow .= pack("v", 0 );
        $feedArrayRow .= $directoryName;  

        $feedArrayRow .= $gzCompressedData;  

        $feedArrayRow .= pack("V",$compression);
        $feedArrayRow .= pack("V",$compressedLength);
        $feedArrayRow .= pack("V",$uncompressedLength);

        $this -> compressedData[] = $feedArrayRow;

        $newOffset = strlen(implode("", $this->compressedData));

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x14\x00";    
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x08\x00";    
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V",$compression);
        $addCentralRecord .= pack("V",$compressedLength);
        $addCentralRecord .= pack("V",$uncompressedLength);
        $addCentralRecord .= pack("v", strlen($directoryName) );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("V", 32 );

        $addCentralRecord .= pack("V", $this -> oldOffset );
        $this -> oldOffset = $newOffset;

        $addCentralRecord .= $directoryName;  

        $this -> centralDirectory[] = $addCentralRecord;  
    }

    /**
     * Fucntion to return the zip file
     *
     * @return zipfile (archive)
     */

    public function getZippedfile() {

        $data = implode("", $this -> compressedData);  
        $controlDirectory = implode("", $this -> centralDirectory);  

        return   
            $data.  
            $controlDirectory.  
            $this -> endOfCentralDirectory.  
            pack("v", sizeof($this -> centralDirectory)).     
            pack("v", sizeof($this -> centralDirectory)).     
            pack("V", strlen($controlDirectory)).             
            pack("V", strlen($data)).                
            "\x00\x00";                             
    }


}


/*
  MySQL database backup class, version 1.0.0
  Written by Vagharshak Tozalakyan <vagh@armdex.com>
  Released under GNU Public license
*/


define('MSB_VERSION', '1.0.0');

define('MSB_NL', "\r\n");

define('MSB_STRING', 0);
define('MSB_DOWNLOAD', 1);
define('MSB_SAVE', 2);

class MySQL_Backup
{

  var $server = 'localhost';
  var $port = 3306;
  var $username = 'root';
  var $password = '';
  var $database = '';
  var $link_id = -1;
  var $connected = false;
  var $tables = array();
  var $drop_tables = true;
  var $struct_only = false;
  var $comments = true;
  var $backup_dir = '';
  var $fname_format = 'd_m_y__H_i_s';
  var $error = '';


  function Execute($task = MSB_STRING, $fname = '', $compress = false)
  {
    if (!($sql = $this->_Retrieve()))
    {
      return false;
    }
    if ($task == MSB_SAVE)
    {
      if (empty($fname))
      {
        $fname = $this->backup_dir;
        $fname .= date($this->fname_format);
        $fname .= ($compress ? '.sql.gz' : '.sql');
      }
      return $this->_SaveToFile($fname, $sql, $compress);
    }
    elseif ($task == MSB_DOWNLOAD)
    {
      if (empty($fname))
      {
        $fname = date($this->fname_format);
        $fname .= ($compress ? '.sql.gz' : '.sql');
      }
      return $this->_DownloadFile($fname, $sql, $compress);
    }
    else
    {
      return $sql;
    }
  }


  function _Connect()
  {
    $value = false;
    if (!$this->connected)
    {
      $host = $this->server . ':' . $this->port;
      $this->link_id = mysql_connect($host, $this->username, $this->password);
    }
    if ($this->link_id)
    {
      if (empty($this->database))
      {
        $value = true;
      }
      elseif ($this->link_id !== -1)
      {
        $value = mysql_select_db($this->database, $this->link_id);
      }
      else
      {
        $value = mysql_select_db($this->database);
      }
    }
    if (!$value)
    {
      $this->error = mysql_error();
    }
    return $value;
  }


  function _Query($sql)
  {
    if ($this->link_id !== -1)
    {
      $result = mysql_query($sql, $this->link_id);
    }
    else
    {
      $result = mysql_query($sql);
    }
    if (!$result)
    {
      $this->error = mysql_error();
    }
    return $result;
  }


  function _GetTables()
  {
    $value = array();
    if (!($result = $this->_Query('SHOW TABLES')))
    {
      return false;
    }
    while ($row = mysql_fetch_row($result))
    {
      if (empty($this->tables) || in_array($row[0], $this->tables))
      {
        $value[] = $row[0];
      }
    }
    if (!sizeof($value))
    {
      $this->error = 'No tables found in database.';
      return false;
    }
    return $value;
  }


  function _DumpTable($table)
  {
    $value = '';
    $this->_Query('LOCK TABLES ' . $table . ' WRITE');
    if ($this->comments)
    {
      $value .= '#' . MSB_NL;
      $value .= '# Table structure for table `' . $table . '`' . MSB_NL;
      $value .= '#' . MSB_NL . MSB_NL;
    }
    if ($this->drop_tables)
    {
      $value .= 'DROP TABLE IF EXISTS `' . $table . '`;' . MSB_NL;
    }
    if (!($result = $this->_Query('SHOW CREATE TABLE ' . $table)))
    {
      return false;
    }
    $row = mysql_fetch_assoc($result);
    $value .= str_replace("\n", MSB_NL, $row['Create Table']) . ';';
    $value .= MSB_NL . MSB_NL;
    if (!$this->struct_only)
    {
      if ($this->comments)
      {
        $value .= '#' . MSB_NL;
        $value .= '# Dumping data for table `' . $table . '`' . MSB_NL;
        $value .= '#' . MSB_NL . MSB_NL;
      }
            
      $value .= $this->_GetInserts($table);
    }
    $value .= MSB_NL . MSB_NL;
    $this->_Query('UNLOCK TABLES');
    return $value;
  }


  function _GetInserts($table)
  {
    $value = '';
    if (!($result = $this->_Query('SELECT * FROM ' . $table)))
    {
      return false;
    }
    while ($row = mysql_fetch_row($result))
    {
      $values = '';
      foreach ($row as $data)
      {
        $values .= '\'' . addslashes($data) . '\', ';
      }
      $values = substr($values, 0, -2);
      $value .= 'INSERT INTO ' . $table . ' VALUES (' . $values . ');' . MSB_NL;
    }
    return $value;
  }


  function _Retrieve()
  {
    $value = '';
    if (!$this->_Connect())
    {
      return false;
    }
    if ($this->comments)
    {
      $value .= '#' . MSB_NL;
      $value .= '# MySQL database dump' . MSB_NL;
      $value .= '# Created by MySQL_Backup class, ver. ' . MSB_VERSION . MSB_NL;
      $value .= '#' . MSB_NL;
      $value .= '# Host: ' . $this->server . MSB_NL;
      $value .= '# Generated: ' . date('M j, Y') . ' at ' . date('H:i') . MSB_NL;
      $value .= '# MySQL version: ' . mysql_get_server_info() . MSB_NL;
      $value .= '# PHP version: ' . phpversion() . MSB_NL;
      if (!empty($this->database))
      {
        $value .= '#' . MSB_NL;
        $value .= '# Database: `' . $this->database . '`' . MSB_NL;
      }
      $value .= '#' . MSB_NL . MSB_NL . MSB_NL;
    }
    if (!($tables = $this->_GetTables()))
    {
      return false;
    }
    foreach ($tables as $table)
    {
      if (!($table_dump = $this->_DumpTable($table)))
      {
        $this->error = mysql_error();
        return false;
      }
      $value .= $table_dump;
    }
    return $value;
  }


  function _SaveToFile($fname, $sql, $compress)
  {
    if ($compress)
    {
      if (!($zf = gzopen($fname, 'w9')))
      {
        $this->error = 'Can\'t create the output file.';
        return false;
      }
      gzwrite($zf, $sql);
      gzclose($zf);
    }
    else
    {
      if (!($f = fopen($fname, 'w')))
      {
        $this->error = 'Can\'t create the output file.';
        return false;
      }
      fwrite($f, $sql);
      fclose($f);
    }
    return true;
  }

} 

function mailAttachment($file, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {

    $filename = basename($file);
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use diff. tyoes here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
} 




function directoryToArray($directory, $recursive) {
    $array_items = array();
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($directory. "/" . $file)) {
                    if($recursive) {
                        $array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
                    }
                    $file = $directory . "/" . $file ."/";
                    $array_items[] = preg_replace("/\/\//si", "/", $file);
                } else {
                    $file = $directory . "/" . $file;
                    $array_items[] = preg_replace("/\/\//si", "/", $file);
                }
            }
        }
        closedir($handle);
    }
    return $array_items;
}

function pr($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

?>