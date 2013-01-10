<?php
if (!defined('IN_WPRO')) exit;

				
				// check vars
				if (!isset($params['folderID']) || !isset($params['folderPath']) || empty($params['file'])) {
					exit;
				}
				ini_set('display_errors', false);
				$params['folderPath'] = base64_decode($params['folderPath']);
				// display a dynamic thumbnail...
				$x = null;
				if ($arr = $this->getFolder($params['folderID'], $params['folderPath'], $x)) {
					$directory = $arr['directory'];
					$file = $params['file'];
					
					/*** KSD WEB BUSINESS FRAMEWORK CODE ***
					$filenamearray = explode(".",$file);
					
					$filearray[0] = $file;
					$filearray[0]['width'] = 94;					
					$filearray[1] = $filenamearray[0]."_w150".$filenamearray[1];
					$filearray[1]['width'] = 150;					
					$filearray[2] = $filenamearray[0]."_w300".$filenamearray[1];
					$filearray[2]['width'] = 300;					
					$filearray[3] = $filenamearray[0]."_w600".$filenamearray[1];
					$filearray[3]['width'] = 600;					
					$filearray[4] = $filenamearray[0]."_w1024".$filenamearray[1];
					$filearray[4]['width'] = 1024;
					
					//$file_150 = $filenamearray[0]."_w150".$filenamearray[1];
					//$file_300 = $filenamearray[0]."_w300".$filenamearray[1];
					//$file_600 = $filenamearray[0]."_w600".$filenamearray[1];
					//$file_1024 = $filenamearray[0]."_w1024".$filenamearray[1];
									
					$file = "";
					
					foreach($filearray as $file){
						$fs = new wproFilesystem();
						if ($fs->fileNameOK($file)) {
							if (is_file($directory.$file)) {
								// if the thumbnail folder exists & is  writable lets cache the thumbnail
								if (file_exists($directory.$EDITOR->thumbnailFolderName) && $fs->fileNameOk($EDITOR->thumbnailFolderName) && is_writable($directory.$EDITOR->thumbnailFolderName)) {
									$savePath = $directory.$EDITOR->thumbnailFolderName.'/'.$file;
									// do not create if it already exists
									if (is_file($savePath)) {
										$savePath = '';
									}
								} else {
									$savePath = '';
								}
								require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
								
								
								$imageEditor = new wproImageEditor();
								//if (!$imageEditor->proportionalResize($directory.$file, '', 94, 94)) {
								if (!$imageEditor->proportionalResize($directory.$file, '', $filearray[$ii]['width'], $filearray[$ii]['width'])) {
									$extension = strrchr(strtolower($file),'.');
									$GDExtensions = array('.jpg','.jpeg','.gif','.png'); // filetypes that can be resized with GD
									if (in_array($extension, $GDExtensions)) {
										$icon = str_replace('.', '', $extension);
										$thumb_src = $EDITOR->themeFolderURL.$EDITOR->theme."/wysiwygpro/icons/{$icon}32.gif";
										header('Location: '.$thumb_src);
									}
								} else if (!empty($savePath)) {
									// cache the thumbnail
									//$imageEditor->proportionalResize($directory.$file, $savePath, 94, 94);
									$imageEditor->proportionalResize($directory.$file, $savePath, $file['width'], $file['width']);
								}
							}
						}
					}
					** END KSD WEB BUSINESS FRAMEWORK CODE ***/
					
					
					
					$fs = new wproFilesystem();
					if ($fs->fileNameOK($file)) {
						if (is_file($directory.$file)) {
							// if the thumbnail folder exists & is  writable lets cache the thumbnail
							if (file_exists($directory.$EDITOR->thumbnailFolderName) && $fs->fileNameOk($EDITOR->thumbnailFolderName) && is_writable($directory.$EDITOR->thumbnailFolderName)) {
								$savePath = $directory.$EDITOR->thumbnailFolderName.'/'.$file;
								// do not create if it already exists
								if (is_file($savePath)) {
									$savePath = '';
								}
							} else {
								$savePath = '';
							}
							require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
							
							
							$imageEditor = new wproImageEditor();
							if (!$imageEditor->proportionalResize($directory.$file, '', 150, 150)) {
								$extension = strrchr(strtolower($file),'.');
								$GDExtensions = array('.jpg','.jpeg','.gif','.png'); // filetypes that can be resized with GD
								if (in_array($extension, $GDExtensions)) {
									$icon = str_replace('.', '', $extension);
									$thumb_src = $EDITOR->themeFolderURL.$EDITOR->theme."/wysiwygpro/icons/{$icon}32.gif";
									header('Location: '.$thumb_src);
								}
							} else if (!empty($savePath)) {
								// cache the thumbnail
								$imageEditor->proportionalResize($directory.$file, $savePath, 150, 150);
							}
							
							
						}
					}
					
				}
				exit;
				
?>