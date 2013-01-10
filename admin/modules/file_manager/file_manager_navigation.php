<?

?>
<li><a class="Add <? if($_REQUEST['SUB'] == ''){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>">Upload Files</a></li>
<li><a class="Search <? if($_REQUEST['SUB'] == 'FileManager'){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=FileManager">New File Manager</a></li>


<li><a class="Documents <? if($_REQUEST['DOCUMENTS'] == '1'){ ?> active <? }?>" href="?VIEW=<?=$_GET['VIEW']?>&DOCUMENTS=1">Documents</a></li>
<li><a class="Photos <? if($_REQUEST['PHOTOS'] == '1'){ ?> active <? }?>" href="?VIEW=<?=$_GET['VIEW']?>&PHOTOS=1&THUMBNAILS=<?=$_REQUEST['THUMBNAILS']?>">Photos</a></li>
<li><a class="Videos <? if($_REQUEST['VIDEOS'] == '1'){ ?> active <? }?>" href="?VIEW=<?=$_GET['VIEW']?>&VIDEOS=1">Videos</a></li>
<li><a class="Audio <? if($_REQUEST['AUDIO'] == '1'){ ?> active <? }?>" href="?VIEW=<?=$_GET['VIEW']?>&AUDIO=1">Audio</a></li>
<li><a class="Info" href="?VIEW=<?=$_GET['HELP']?>&SUB=FILEMANAGER">Help</a></li>


