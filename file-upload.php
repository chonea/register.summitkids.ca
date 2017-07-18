<?php
// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array("jpeg","jpg","gif","png");
// max file size in bytes
$sizeLimit = 3 * 1024 * 1024;

require('libraries/Valums-file-uploader/server/php.php');
$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
$result = $uploader->handleUpload('files/uploads/images/');

/*
			if ($returnJSON['success'] != false) {

				list($width,$height) = getimagesize($uploadedfile);
			
				$newwidth_lg = 200;
				$newheight_lg = ($height/$width)*$newwidth_lg;
				$tmp_lg = imagecreatetruecolor($newwidth_lg,$newheight_lg);
				
				$newwidth_md = 100;
				$newheight_md = ($height/$width)*$newwidth_md;
				$tmp_md = imagecreatetruecolor($newwidth_md,$newheight_md);
				
				$newwidth_sm = 50;
				$newheight_sm = ($height/$width)*$newwidth_sm;
				$tmp_sm = imagecreatetruecolor($newwidth_sm,$newheight_sm);
				
				imagecopyresampled($tmp_lg,$src,0,0,0,0,$newwidth_lg,$newheight_lg,$width,$height);
				imagecopyresampled($tmp_md,$src,0,0,0,0,$newwidth_md,$newheight_md,$width,$height);
				imagecopyresampled($tmp_sm,$src,0,0,0,0,$newwidth_sm,$newheight_sm,$width,$height);
	
				$filehash = bin2hex(openssl_random_pseudo_bytes(16)); // random 32 character hash string
				$filedir = "files/uploads/images/";
				$filename_lg = $filedir . $filehash . "_lg." . $extension;
				$filename_md = $filedir . $filehash . "_md." . $extension;
				$filename_sm = $filedir . $filehash . "_sm." . $extension;
				
				imagejpeg($tmp_lg,$filename_lg,100);
				imagejpeg($tmp_md,$filename_md,100);
				imagejpeg($tmp_sm,$filename_sm,100);
				
				imagedestroy($src);
				imagedestroy($tmp_lg);
				imagedestroy($tmp_md);
				imagedestroy($tmp_sm);
			}
*/

// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
?>