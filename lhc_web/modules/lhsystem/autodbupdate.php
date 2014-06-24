<?php 

$ipUpdate = erLhcoreClassModelChatConfig::fetch('update_ip')->current_value;

if ($ipUpdate != '' && erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ipUpdate)) ) {
	
	$jsonObject = json_decode(erLhcoreClassModelChatOnlineUser::executeRequest('https://raw.githubusercontent.com/LiveHelperChat/livehelperchat/master/lhc_web/doc/update_db/structure.json'),true);
	
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
	echo "Your IP is not allowed to perform updates\n";
}
exit;


?>