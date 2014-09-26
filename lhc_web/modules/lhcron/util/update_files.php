<?php 

// php cron.php -s site_admin -c cron/util/update_files

echo "Downloading most recent version\n";

$content = erLhcoreClassModelChatOnlineUser::executeRequest('https://github.com/LiveHelperChat/livehelperchat/archive/master.zip');

echo "Download completed\n";
echo "Storing a file\n";
file_put_contents('var/tmpfiles/master.zip', $content);
echo "File was stored\n";

$zip = new ZipArchive;

$date = date('Ymd');

if ($zip->open('var/tmpfiles/master.zip') === true) {
	echo "Zip opened";
	if (!file_exists('var/tmpfiles/update')){
		mkdir('var/tmpfiles/update'); 
	}
	
	ezcBaseFile::removeRecursive('var/tmpfiles/backupfiles');
	
	if (!file_exists('var/tmpfiles/backupfiles')){			
		mkdir('var/tmpfiles/backupfiles');
		mkdir('var/tmpfiles/backupfiles/design');
	} 
	
	$zip->extractTo('var/tmpfiles/update');
	$zip->close(); 
		
	$foldersSwitch = array('doc','ezcomponents','lib','modules','pos','translations','design/backendtheme','design/defaulttheme');
	
	echo "Switching folders\n";
	
	foreach ($foldersSwitch as $folder) {
		rename($folder,'var/tmpfiles/backupfiles/' . $folder.'_' . $date);
		rename('var/tmpfiles/update/livehelperchat-master/lhc_web/'.$folder, $folder);
	}
	unlink('var/tmpfiles/master.zip');
	echo "Your old folders can be found - var/tmpfiles/backupfiles\n";
	
	ezcBaseFile::removeRecursive('var/tmpfiles/update');
	
	$jsonObject = json_decode(erLhcoreClassModelChatOnlineUser::executeRequest('https://raw.githubusercontent.com/LiveHelperChat/livehelperchat/master/lhc_web/doc/update_db/structure.json'),true);
	
	echo "----------------\nUpdating database\n----------------\n";
	if (is_array($jsonObject)){
		$errorMessages = erLhcoreClassUpdate::doTablesUpdate($jsonObject);
		if (empty($errorMessages)) {
	
			$CacheManager = erConfigClassLhCacheConfig::getInstance();
			$CacheManager->expireCache();
	
			echo "UPDATE DONE\n";
		} else {
			echo "ERROR:\n".implode("\n", $errorMessages);
		}
	}
	
} else {
	echo "Could not download archive!!!";
}
 


?>