<?php
/*
* WysiwygPro Theme file
* Theme: Jet Black
* Author: Chris Bolt
*/
if (!defined('IN_WPRO')) exit;
class wproPlugin_jetblackTheme {
	function onBeforeDisplayDialog () {
		global $EDITOR, $DIALOG;
		if ($EDITOR->iframeDialogs) {
			$DIALOG->headContent->add(
'<!--[if IE]><style type="text/css">
.jetblack button.wproDisabled {
	background-color: #000000;
	filter:none;
}
.wproDialogEditorShared div.wproLoadMessage {
	filter:none;
}
</style><![endif]-->'
		
			);
		}		
	}
}

?>