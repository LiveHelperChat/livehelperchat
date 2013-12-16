<?php

$json = json_encode(array('isonline' =>  erLhcoreClassChat::isOnline()));

if (isset($_GET['callback'])){
	echo $_GET['callback'] . '(' . $json . ')';
} else {
	echo $json;
}

exit;
?>