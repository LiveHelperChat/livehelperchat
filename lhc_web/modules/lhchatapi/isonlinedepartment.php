<?php

$json = json_encode(array('isonline' =>  erLhcoreClassChat::isOnline((int)$Params['user_parameters']['department_id'],true)));

if (isset($_GET['callback'])){
	echo $_GET['callback'] . '(' . $json . ')';
} else {
	echo $json;
}

exit;
?>