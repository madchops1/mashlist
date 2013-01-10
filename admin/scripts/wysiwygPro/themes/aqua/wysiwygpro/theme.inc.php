<?php
/*
* WysiwygPro Theme file
* Theme: aqua
* Author: Chris Bolt
*/
if (!defined('IN_WPRO')) exit;
class wproPlugin_aquaTheme {
	function onLoadDialog () {
		global $DIALOG;
		$DIALOG->template->registerTemplate(WPRO_DIR.'core/tpl/UITabbed.tpl.php', dirname(__FILE__).'/UITabbed.tpl.php');
	}
}

?>