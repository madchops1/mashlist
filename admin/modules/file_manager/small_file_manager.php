<?
include '../../../includes/config.php';
global $_SETTINGS;

$dir = $_REQUEST['f'];
$thumbdir = $dir.'wpThumbnails/';

if($dir == ""){
	$dir = "../uploads/";
	$thumbdir = "../uploads/wpThumbnails/";
}

?>
<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/scripts/jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/scripts/jquery/lazykarl/lazykarl.js"></script>
<script>
    $().ready(function() {
        $("body").lazyKarl();
    });
</script>
<html>
<head>



<title>Select an Image...</title>

<style>
	body{
		background-color:#DDDDDD;
		font-family:arial;
		font-size:10px;
	}
	
	.pagination{
		
		margin:5px;
		/*padding:5px;*/
	}
	
	.item{
		clear:both;
		border:1px solid #fff;
		background-color:#f5f5f5;
		margin:5px;
		padding:5px;
	}
	
	
	
	.item div.imagewrapper img{
		width:20px;
	}
	
	span.filename{
		float:left;
		display:block;
		height:20px;
		margin:5px;
	}
	
	.button{
		float:right;
		margin:5px;
	}
	
	form{
		margin:0px;
		padding:0px;
	}
	label{
		width:150px;
		text-align:left;
		font-size:8px;
		
		
	}
	
	.page{
		display:inline-block;
		width:20px;
		height:20px;
		
	}
	
	img{
		width:30px;
		height:30px;
		position:absolute;
	}
	
	.details{
		
		margin-left:40px;
	}
	
	.active{
		
		font-weight:bold;
	}
	
</style>


</head>
<body>



<?
// GET ALL FILES
$files = scandir('../../'.$dir);

// FILTER GOOD FILES
$goodfiles = array();
foreach($files as $f){
	if(strpos($f,'.')!==0){
		if(
			strpos(strtolower($f), '.gif',1)||
			strpos(strtolower($f), '.jpg',1)||
			strpos(strtolower($f), '.jpeg',1)||
			strpos(strtolower($f), '.png',1)
		){ 
			array_push($goodfiles,$f);
		}
	}
}
sort($goodfiles);
// PAGINATION

/*
$pages = array_chunk($goodfiles, 25);
$i=1;
$num=count($pages)+1;

$pgkey = (int)$_REQUEST['showpage']; // forces $_GET['showpage'] to be an integer
echo '<div class="pagination">';
while($i<$num){
	$k = $i-1;
	if($_REQUEST['showpage'] == $i){ $active = 'active'; } else { $active = ''; }
	echo '<a href="?f='.$_REQUEST['f'].'&fieldname='.$_REQUEST['fieldname'].'&showpage='.$k.'" class="page '.$active.'">'.$i.'</a>';
	$i++;
}
echo '</div>';
*/

$i = 0;

//foreach($pages[$pgkey] as $key => $file){
foreach($goodfiles as $key => $file){
		
				$t = "";
				$displayfile = $file;
				$filearray = explode(".",$file);
				
				// GET FULL SIZE ORIGINAL
				//if(is_file('../../'.$dir.$file.'')){
					
				list($width, $height, $type, $attr) = getimagesize('../../'.$dir.$file.'');
				$files = "<span class='filename'><label>Filename:</label> ".$file." (".$width."px x ".$height."px)</span>";
				
				//}
				
				// CHECK FOR A 94px THUMBNAIL
				if(is_file('../../'.$thumbdir.$filearray[0].'_w94.'.$filearray[1].'')){
					$t = 'wpThumbnails/';
					$displayfile = $filearray[0].'_w94.'.$filearray[1];
					
				}
				
				// CHECK FOR A 150px THUMBNAIL
				if(is_file('../../'.$thumbdir.$filearray[0].'_w150.'.$filearray[1].'') AND $t == ""){
					$t = 'wpThumbnails/';
					$displayfile = $filearray[0].'_w150.'.$filearray[1];
				}
				
				// CHECK FOR A 300px THUMBNAIL
				if(is_file('../../'.$thumbdir.$filearray[0].'_w300.'.$filearray[1].'') AND $t == ""){
					$t = 'wpThumbnails/';
					$displayfile = $filearray[0].'_w300.'.$filearray[1];
				}
			
				echo "<div class='item'>";
				echo "	<form name='filechooser".$i."' method='post' action=''>";
				//echo "		<div class='imagewrapper'>";
				
				echo "			<script>";
				echo "				function hoverImage".$i."(){";
				echo "					var img".$i." = document.getElementById('image-".$i."');";
				echo "					if(img".$i." && img".$i.".style) {";
				echo "						img".$i.".style.width = '".($width/3)."px';";
				echo "						img".$i.".style.height = '".($height/3)."px';";
				echo "						img".$i.".style.zIndex = 500;";
				echo "					}";
				echo "				}";
				echo "				function hoverOut".$i."(){";
				echo "					var img".$i." = document.getElementById('image-".$i."');";
				echo "					if(img".$i." && img".$i.".style) {";
				echo "						img".$i.".style.width = '30px';";
				echo "						img".$i.".style.height = '30px';";
				echo "						img".$i.".style.zIndex = 1;";
				echo "					}";
				echo "				}";
				echo "			</script>";
				echo "					<img id='image-".$i."' rel='../../".$dir.$t.$displayfile."' style='' onmouseout='javascript:hoverOut".$i."();' onmouseover='javascript:hoverImage".$i."();'>";
				
				
				echo "		<div class='details'>";
				echo $files;
				echo "		</div>";
				
				echo "<input type='hidden' name='filename".$i."' value='".$file."'>";
				echo "<input class='button' type='button' value='Select Image...' onclick='post_value".$i."();'>";
				echo "<br clear='all'></form></div>";	
				
				echo "<script langauge='javascript'>";
				echo "function post_value".$i."(){";
				//echo "  var fieldName = '".$_REQUEST['fieldname']."';";
				echo "opener.document.wesform.".$_REQUEST['fieldname'].".value = document.filechooser".$i.".filename".$i.".value;";
				echo "self.close();";
				echo "}";
				echo "</script>";
				
				$i++;
	
}
	
/*
echo '<div class="pagination">';
$i=1;
while($i<$num){
	$k = $i - 1;
	if($_REQUEST['showpage'] == $i){ $active = 'active'; } else { $active = ''; }
	echo '<a href="?f='.$_REQUEST['f'].'&fieldname='.$_REQUEST['fieldname'].'&showpage='.$k.'" class="page '.$active.'">'.$i.'</a>';
	$i++;
}
echo '</div>';
*/

?>
</body>
</html>