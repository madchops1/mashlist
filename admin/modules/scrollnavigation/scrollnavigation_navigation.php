<?

?>
<li><a class="Search <? if($_REQUEST['SUB'] == "ITEMS"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=ITEMS">Items</a></li>

<? /*
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWITEM"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=NEWITEM">New Item</a></li>
*/ ?>

<li><a class="Category <? if($_REQUEST['SUB'] == "SORT_ITEMS"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=SORT_ITEMS">Sort Items</a></li>
