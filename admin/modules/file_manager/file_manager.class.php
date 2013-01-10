<?

class FileManager {

	var $auth;

	function FileManager()
	{
		$this->auth = 0;
	}
	
	function formatFilesize($dsize)
	{
		if (strlen($dsize) <= 9 && strlen($dsize) >= 7){
			$dsize = number_format($dsize / 1048576,1);
			return "$dsize MB";
		} elseif (strlen($dsize) >= 10){
			$dsize = number_format($dsize / 1073741824,1);
			return "$dsize GB";
		} else {
			$dsize = number_format($dsize / 1024,1);
			return "$dsize KB";
		}
	}
	
	function getformatImageDimensions($file)
	{
		list($w, $h) = getimagesize($file);
		return "$w x $h";
	}
	
	function ob_clean_all()
	{
		$ob_active = ob_get_length ()!== FALSE;
		while($ob_active)	{
			ob_end_clean();
			$ob_active = ob_get_length ()!== FALSE;
		}
		return FALSE;
	} 
	
	function downloadFile()
	{		
		global $_GET;
		if(isset($_GET['DOWNLOADFILE'])){
			//echo("made it");
			//exit;
			
			global $_SETTINGS;
			// grab the requested file's name
			$filename = $_GET['filename'];

			//echo("made it here too ".$filename."");
			
			// make sure it's a file before doing anything!
			if(is_file($filename))
			{
				//echo("made it here too ".$filename."");
				//die();
				//exit;
				
				// required for IE, otherwise Content-disposition is ignored
				if(ini_get('zlib.output_compression'))
				  ini_set('zlib.output_compression', 'Off');

				// addition by Jorg Weske
				$file_extension = strtolower(substr(strrchr($filename,"."),1));

				if( $filename == "" ) 
				{
				  echo "<html><title>".$_SETTINGS['admin_title']."</title><body>ERROR: download file NOT SPECIFIED. USE force-download.php?file=filepath</body></html>";
				  exit;
				} elseif ( ! file_exists( $filename ) ) 
				{
				  echo "<html><title>".$_SETTINGS['admin_title']."</title><body>ERROR: File not found. USE force-download.php?file=filepath</body></html>";
				  exit;
				};
				switch( $file_extension )
				{
				  case "pdf": $ctype="application/pdf"; break;
				  case "exe": $ctype="application/octet-stream"; break;
				  case "zip": $ctype="application/zip"; break;
				  case "doc": $ctype="application/msword"; break;
				  case "xls": $ctype="application/vnd.ms-excel"; break;
				  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
				  case "gif": $ctype="image/gif"; break;
				  case "png": $ctype="image/png"; break;
				  case "jpeg":
				  case "jpg": $ctype="image/jpg"; break;
				  default: $ctype="application/force-download";
				}
				
				$this->ob_clean_all();
				
				header("Pragma: public"); // required
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false); // required for certain browsers 
				header("Content-Type: $ctype");
				// change, added quotes to allow spaces in filenames, by Rajkumar Singh
				header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($filename));
				readfile("$filename");	
				exit;
			}
		}
	}
	
	function uploadsManagerFiles($panelId='')
	{
		
		global $_SESSION;
		global $_SETTINGS;
		$content = "";
		
		// GET CUSTOMER DIRECTORY
		$contents = scandir($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']);
		
		$goodfiles = array();
		$gooddirs = array();
		foreach($contents as $f)
		{
			if(strpos($f,'.')!==0)
			{	
				$display = 1;
				if($_SESSION['current_directory'] == '/')
				{
					if(!strstr($f,"uploads-"))
					{
						$display = 0;
					}
				}
				
				if($display == 1)
				{
					if(is_dir($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$f."/"))
					{
						array_push($gooddirs,$f);
					} else {
						array_push($goodfiles,$f);
					}	
				}
			}
		}
		sort($gooddirs);
		sort($goodfiles);
		
		$view = 'list';
		if($view=='list'){
			$i = 0;
			$j = 1;
			
			// BREAK DOWN THE DIR
			$theDir = rtrim($_SESSION['current_directory'],"/");
			
			// TESTING
			//echo "	DIR: ".$theDir."<br>";
			
			//echo $theDir;
			$theDirBackArray = explode("/",$theDir);
			array_pop($theDirBackArray);
			$theDirBack = implode("/",$theDirBackArray);
			
			// TESTING
			//echo "	BACK 1: ".$theDirBack." ".strlen($theDirBack)."<Br>";
			
			if(strlen($theDirBack) < strlen($_SESSION['default_directory'])){
				$theDirBack = $_SESSION['default_directory'];
			}
			
			// TESTING
			//echo "
			//	CURRENT: ".$theDir."<br>
			//	BACK 2: ".$theDirBack." <Br>
			//	DEFAULT: ".$_SESSION['default_customer_directory'.$xid]." ".strlen($_SESSION['default_customer_directory'.$xid])."";
				
			//var_dump($theDirBackArray);
			$inputId = random_number();
			$content .= "			<div class='wesley-cmsnav-toolbar'>
										<ul class='wesley-cmsnav-buttons wesley-cmsnav-leftbuttons'>
											<li><a class='root' title='Root'>Root</a></li>
											<li><a class='backdirectory' title='Back'>.. Back</a></li>
										</ul>
										<div>
											<input value='".$_SESSION['current_directory']."' class='filemanager-panel-path' />
										</div>
										<div class='filemanager-panel-details'>
											".count($goodfiles)." Files, ".count($gooddirs)." Folders
										</div>
									</div>
									<div class='overflowy drop-file-upload' style='border:1px solid #1px solid #BBB9BA; height:500px; overflow-y:scroll; '>
										<table class='panel-filemanager files table-in-table' cellpadding='0' cellspacing='0'>
											<tbody id=''>
												<tr id='tablehead'>
													<th style='width:21px; text-align:left;'> </th>
													<th style='width:21px; text-align:left;'> </th>
													<th style='text-align:left; width:300px;'>Filename</th>
													<th style='text-align:left;'>Download</th><th style='text-align:left;'>Type</th>
													<th style='text-align:left;'>Size</th><th style='text-align:left;'>Dimensions</th>
													<th style='text-align:left;'>Date</th>
													<th style='text-align:left;'> </th>
												</tr>";
			
			// MERGE ARRAYS
			$goodarray = array_merge($gooddirs,$goodfiles);
			
			$pages = array_chunk($goodarray, 25);
			//$i=1;
			$num=count($pages)+1;
			if(!$_REQUEST['showpage']){ $_REQUEST['showpage'] = 0; }
			$pgkey = (int)$_REQUEST['showpage']; // forces $_GET['showpage'] to be an integer
			
			
			//echo "<pre>";
			//var_dump($pages);
			//echo "</pre>";
			
			
			// LOOP FILES
			//
			$ti = 0;
			//foreach($pages[$pgkey] as $key => $file){
			foreach($goodarray as $key => $file)
			{				
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				

				
				$content .= "		<tr id='filerow-".$i."' class='filerow'>";
				
				// CHECKBOX
				if(is_dir($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file."/"))
				{
					$content .= "		<td> </td>";
				} else {
					$content .= "		<td>
											<input type='checkbox' class='filecheckbox' value='' >
										</td>";
				}
				
				// IMAGE
				$content .= "			<td>";
				if(is_dir($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file."/")){
					$content .= "			<img src='".$_SETTINGS['website']."admin/images/icons/folder_16.png' style=''>";
					$ext = 'Folder';
				}
				$image = 0;
				if($ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png'){
					$image = 1;
					$content .= "			<img rel='".$_SESSION['current_directory']."/".$file."' class='draggable-image' path='".str_replace("//","/",$_SESSION['current_directory']."/".$file)."' style='width:18px; height:18px;'>";
				}
				$content .= "			</td>";
				
				// FILENAME
				$content .= "			<td style='width:300px; overflow:hidden;'>";
				if(is_dir($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file."/")){
					$reverse = strrev($_SESSION['current_directory']);
					if($reverse[0] != "/"){ $_SESSION['current_directory'] .= "/"; }
					$content .= "		<span id='folderwrap-".$i."'><a class='dir' href='javascript:void(0);' rel='".$_SESSION['current_directory'].$file."' id='dir=".$i."'>".$file."</a></span>";	
				} else {
					$reverse = strrev($_SESSION['current_directory']);
					if($reverse[0] != "/"){ $_SESSION['current_directory'] .= "/"; }
					$content .= "		<span id='filewrap-".$i."'><a class='file' href='".$_SESSION['current_directory'].$file."' target='_blank' id='file-".$i."'>".$file."</a></span>";
				}
				$content .= "			</td>";
				
				// DOWNLOAD
				$content .= "			<td>";
				if(!is_dir($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file."/")){
					$content .= "			<a target='_blank' href='".$_SETTINGS['website'].$_SESSION['current_directory']."/".$file."'>Download</a>";
				}
				$content .= "			</td>";
				
				// EXTENSION
				$content .= "			<td>".$ext."</td>";
				
				// SIZE
				$size = formatBytes(filesize($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file.""));
				$content .= "			<td>".$size."</td>";
				
				// DIMENSIONS
				$dimensions = "";
				if($image == 1)
				{
					list($width, $height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file."");
					$dimensions = "".$width." x ".$height."";
					//$width = "100";
					//$height = "100";
				}
				$content .= "			<td>".$dimensions."</td>";
				//$content .= "			<td> </td>";
				
				//die('hello!');
				//exit;
				
				// DATE
				$content .= "			<td>".date("m/d/Y", filemtime($_SERVER['DOCUMENT_ROOT'].$_SESSION['current_directory']."/".$file.""))."</td>";
				
				// DELETE
				$content .= "			<td><a href='' id='delete-".$i."' class='file-delete-button'>Delete</a></td>";
				
				$content .= "		</tr>";	// END ROW
				
				// DELETE SCRIPT
				$content .= "		<script>
										$('#delete-".$i."').click(function(){ 
											var confirma = confirm('Are you sure you want to delete this file?');
											if(confirma){
												$.post('/admin/modules/customer_filemanager/customer_filemanager.php',{ DELETEFILE: '".$_SESSION['current_directory'].$file."' }, function(data) { 
													$('#filerow-".$i."').fadeOut(500); 
												});
											}
										});
										
										/*
										$('#deletefolder-".$i."').click(function(){
											var confirmb = confirm('Are you sure you want to delete this folder?');
											if(confirmb){
												$.post('/admin/modules/customer_filemanager/customer_filemanager.php',{ DELETEFOLDER: '.".$_SESSION['current_directory'].$file."' }, function() {
													$('#filerow-".$i."').fadeOut(500);
												});
											}
										});
										*/										
									</script>";
		
		if($ti == 1000){ break; }
		$j++;
		$i++;
		$ti++;
	}
	$content .= "	<tr><td colspan='9' style='border-top:1px solid #ccc; padding:10px 20px;'><a href='javascript:void(0);' id='delete-all'>Delete Checked</a></td></tr>";
	$content .= "	</tbody>
				</table>
			</div>";
			
	
	
	if($i == 0){
		$content .= "<p>0 Files</p>";
	}
	
	$content .= "	<script type='text/javascript'>
						// LAZYKARL
						//$('.lazyloader').lazyKarl();
						
						// OPEN DIRECTORY AJAX
						$('.dir').click(function(e){ 
							e.preventDefault();
							var folder = $(this).attr('rel');
							$.post('/admin/modules/file_manager/file_manager.php',{ DISPLAYCUSTOMERFILEMANAGERDIR: ''+folder+'',ajax:1 }, function(data) { 
								$('#pane-files').html(data); 
							}); 
						});
						
						// BACKUP DIRECTORY AJAX
						$('.backdirectory').click(function(e){
							e.preventDefault();
							$.post('/admin/modules/file_manager/file_manager.php',{ DISPLAYCUSTOMERFILEMANAGERDIR: '".$theDirBack."',ajax:1 }, function(data) { 
								$('#pane-files').html(data);
							});
						});
						
						// ROOT DIRECTORY AJAX
						$('.root').click(function(e){
							e.preventDefault();
							$.post('/admin/modules/file_manager/file_manager.php',{ DISPLAYCUSTOMERFILEMANAGERDIR: '".$_SESSION['default_directory']."',ajax:1 }, function(data) { 
								$('#pane-files').html(data);
							});
						});
						
						// DELETE CHECKED
						$('#delete-all').click(function(e){
							e.preventDefault();
							
							
							alert('delete checked!');
							//$.post('/admin/modules/file_manager/file_manager.php',{ DELETECHECKED: '".$_SESSION['default_directory']."',ajax:1 }, function(data) { 
							//$('#pane-files').html(data);
							//});
						});
						
					</script>";
		}
		
		//die('made it aaaaaaa');
		//exit();
		
		return $content;
	}
	
	function uploadsManager($dir,$panelId='')
	{
		$content1 = "";
		
		global $_SESSION;
		global $_SETTINGS;
				
		// SET DEFAULT DIRECTORY
		if($_SESSION['current_directory'] == '')
		{
			$_SESSION['current_directory'] = "/";
		}
		
		$_SESSION['default_directory'] = "/";
		
		if($dir != '')
		{
			$_SESSION['current_directory'] = $dir;
		}
		
		// CALL THE FILE MANAGER
		$content1 .= "<div class='' style='float:left; width:100%;'>";
		$content1 .= $this->uploadsManagerFiles($panelId);
		$content1 .= "</div>";
		
		// JS FOR UPLOADIFY 
		$content1 .= "	<script type='text/javascript'>
						
						</script>";
		
		return $content1;
	}	
	
	
}
?>