<?php

$json = json_encode(array('isonline' => erLhcoreClassChat::isOnlineUser((int)$Params['user_parameters']['user_id'])));

if (isset($_GET['callback'])){
	echo $_GET['callback'] . '(' . $json . ')';
} else {
	echo $json;
}

exit;
?>