<?php
if (!defined('IN_WPRO')) exit;
		
		global $EDITOR, $DIALOG;
		
		if (!$EDITOR->thumbnails) {
			$i = 0;
			foreach ($files as $file) {
				$filename = $file['name'];
				$extension = strrchr(strtolower($filename),'.');
				$icon = str_replace('.', '', $extension);
				$thumb_src = $EDITOR->themeFolderURL.$EDITOR->theme."/wysiwygpro/icons/{$icon}32.gif";
				$files[$i]['thumbURL'] = $thumb_src;
				$i++;
			}
		} else {
			
			//$timeLimit = 2;
			$thumbLimit = 20;
			$failLimit = 5;
			
			// can we create thumbnails?
			// if we cannot return false...	
			$canGif = false;
			if (function_exists('imagecreate')) {
				//if (!(imagetypes() & IMG_PNG)) return false;
				//$canJpeg = function_exists("imagejpeg") ? true : false; 				
				//$canPng = function_exists("imagepng") ? true : false;  
				if (!function_exists('imagegif') && (!function_exists('imagecreatefromgif') || !function_exists('imagepng') ) ) {
					$canGif = false;
				} else {
					$canGif = true;
				}
			} else {
				return false;
			}
			
			$GDExtensions = array('.jpg','.jpeg','.gif','.png'); // filetypes that can be resized with GD
			
			$fs = new wproFilesystem;
			require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
			$imageEditor = new wproImageEditor();
			//$imageEditor->adjustMemoryLimit = false; // we want to prevent time outs so we won't create thumbnails on huge images.
			$imageEditor->fileCHMOD = $EDITOR->fileCHMOD; // mode for new thumbnails
			
			// initiate variables
			$dirWritable = is_writable($directory);
			$thumb_src = '';
			$thumbDirExists = false;
			
			// create thumb cache folder
			if (file_exists($directory.$EDITOR->thumbnailFolderName) && $fs->fileNameOk($EDITOR->thumbnailFolderName)) {
				//$thumbDirCreated = true;
				$thumbDirExists = true;
			} else  {
				// create thumb cache dir			
				if (count($files) && $dirWritable && !strstr($url, $EDITOR->thumbnailFolderName) && $fs->fileNameOk($EDITOR->thumbnailFolderName)) {
					if (!$fs->makeDir($directory.$EDITOR->thumbnailFolderName, $EDITOR->folderCHMOD)) {
						$dirWritable = false;
						$thumbDirExists = false;	
					} else {
						$thumbDirExists = true;	
						//$thumbDirCreated = true;
					}			
				} else {
					//$thumbDirCreated = false;
					$thumbDirExists = false;
				}
			}
			$i=0;
			$limit = 0;
			$failed = 0;
			
			
			foreach ($files as $file) {
				
				$thumbCreated = false;
			 
				$filename = $file['name'];
				
				/*** KSD CODE ****
				$filearray = explode(".",$file);
					
				$filenamearray[0]['filename'] = $filename;
				$filenamearray[0]['width'] = 94;					
				$filenamearray[1]['filename'] = $filearray[0]."_w150".$filearray[1];
				$filenamearray[1]['width'] = 150;					
				$filenamearray[2]['filename'] = $filearray[0]."_w300".$filearray[1];
				$filenamearray[2]['width'] = 300;					
				$filenamearray[3]['filename'] = $filearray[0]."_w600".$filearray[1];
				$filenamearray[3]['width'] = 600;					
				$filenamearray[4]['filename'] = $filearray[0]."_w1024".$filearray[1];
				$filenamearray[4]['width'] = 1024;
				
				
				foreach ($filenamearray as $filename) {
				
					$extension = strrchr(strtolower($filename['filename']),'.');
					
					if (!in_array($extension, $GDExtensions)) {
						$i++; 
						continue; 
					}
					
					list ($width, $height) = @getimagesize($directory.$filename['filename']);
			
					// if image is already thumbnail size, ignore.
					if ($width <= $filename['width'] && $height <= $filename['width']) {
						$thumb_src = $url.$filename['filename'];
						$thumbCreated = true;
					} else {
					// if dir is writable create a thumbnail cache folder and store thumbnails there.
						if ($dirWritable && $thumbDirExists) {
						// create thumbnail folder if there are images to display
						//if ($thumbDirExists) {
							$thumbDir = $directory.$EDITOR->thumbnailFolderName.'/'.$filename['filename'];
							$thumbURL =  $url.$EDITOR->thumbnailFolderName.'/'.$filename['filename'];
							// create and store thumbnail
							if (is_file($thumbDir)) {
								$thumb_src = $thumbURL;
								$thumbCreated = true;
							} elseif (is_file($thumbDir.'.png')) {
								$thumb_src = $thumbURL.'.png';
								$thumbCreated = true;
							} elseif ($limit < $thumbLimit && $failed < $failLimit && ($extension == '.gif' && $canGif || $extension != '.gif')) {
								if ($thumbDimensions = @$imageEditor->proportionalResize($directory.$filename['filename'], $thumbDir, $filename['width'], $filename['width'])) {
									list ($thumb_width, $thumb_height, $thumb_src) = $thumbDimensions;
									$thumb_src = $url.$EDITOR->thumbnailFolderName.strrchr($thumb_src, '/');
									//exit($thumb_src);
									$thumbCreated = true;
									$limit ++;
								}
							}
						}						
					}
				}
				*** END KSD ADDED FOREACH ***/
				
				
				$extension = strrchr(strtolower($filename),'.');
				
				if (!in_array($extension, $GDExtensions)) {
					$i++; 
					continue; 
				}
				
				list ($width, $height) = @getimagesize($directory.$filename);
		
				// if image is already thumbnail size, ignore.
				if ($width <= 94 && $height <= 94) {
					$thumb_src = $url.$filename;
					$thumbCreated = true;
				} else {
				// if dir is writable create a thumbnail cache folder and store thumbnails there.
					if ($dirWritable && $thumbDirExists) {
					// create thumbnail folder if there are images to display
					//if ($thumbDirExists) {
						$thumbDir = $directory.$EDITOR->thumbnailFolderName.'/'.$filename;
						$thumbURL =  $url.$EDITOR->thumbnailFolderName.'/'.$filename;
						// create and store thumbnail
						if (is_file($thumbDir)) {
							$thumb_src = $thumbURL;
							$thumbCreated = true;
						} elseif (is_file($thumbDir.'.png')) {
							$thumb_src = $thumbURL.'.png';
							$thumbCreated = true;
						} elseif ($limit < $thumbLimit && $failed < $failLimit && ($extension == '.gif' && $canGif || $extension != '.gif')) {
							
							
							/******/
							$filearray = explode(".",$filename);
							
							$newthumbdir0 = $directory.$EDITOR->thumbnailFolderName.'/'.$filearray[0]."_w94.".$filearray[1];
							$newthumbdir1 = $directory.$EDITOR->thumbnailFolderName.'/'.$filearray[0]."_w150.".$filearray[1];
							$newthumbdir2 = $directory.$EDITOR->thumbnailFolderName.'/'.$filearray[0]."_w300.".$filearray[1];
							$newthumbdir3 = $directory.$EDITOR->thumbnailFolderName.'/'.$filearray[0]."_w600.".$filearray[1];
							$newthumbdir4 = $directory.$EDITOR->thumbnailFolderName.'/'.$filearray[0]."_w1024.".$filearray[1];
							
							
							if ($thumbDimensions = @$imageEditor->proportionalResize($directory.$filename, $newthumbdir0, 94, 94)) {
								
								
								
								if ($width >= 150 && $height >= 150) {
									$thumbDimensions1 = @$imageEditor->proportionalResize($directory.$filename, $newthumbdir1, 150, 150);
								}
								
								if ($width >= 300 && $height >= 300) {
									$thumbDimensions2 = @$imageEditor->proportionalResize($directory.$filename, $newthumbdir2, 300, 300);
								}
								
								if ($width >= 600 && $height >= 600) {
									$thumbDimensions3 = @$imageEditor->proportionalResize($directory.$filename, $newthumbdir3, 600, 600);
								}
								
								if ($width >= 1024 && $height >= 1024) {
									$thumbDimensions4 = @$imageEditor->proportionalResize($directory.$filename, $newthumbdir4, 1024, 1024);
								}
								
								
								/******/
								
								
								list ($thumb_width, $thumb_height, $thumb_src) = $thumbDimensions;
								$thumb_src = $url.$EDITOR->thumbnailFolderName.strrchr($thumb_src, '/');
								//exit($thumb_src);
								$thumbCreated = true;
								$limit ++;
							}
						}
					}						
				}
				
				
				
				
				// thumbnail could not be created...
				if (!$thumbCreated) {
					
					// the dir is not writable so we cannot cache the thumbnails, but we will create a dynamic thumbnail...
					// create dynamic thumbnail
					// work out if we can create a thumbnail
					if ($extension == '.gif' && $canGif || $extension != '.gif') {
						
						$thumb_src = $EDITOR->editorLink($urlBase.'&action=thumbnail&gzip=false&file='.urlencode($filename));
						//echo $thumb_src."\n<br />";
						$failed ++;
					} else {
						$icon = str_replace('.', '', $extension);
						$thumb_src = $EDITOR->themeFolderURL.$EDITOR->theme."/wysiwygpro/icons/{$icon}32.gif";
					}
					
				}
				$files[$i]['thumbURL'] = $thumb_src;
				$i++;
			}	
		}	
		return true;


?>