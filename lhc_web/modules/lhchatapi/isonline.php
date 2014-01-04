<?php

$json = json_encode(array('isonline' =>  erLhcoreClassChat::isOnline(false,false,array('online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']))));

if (isset($_GET['callback'])){
	echo $_GET['callback'] . '(' . $json . ')';
} else {
	echo $json;
}

exit;
?>