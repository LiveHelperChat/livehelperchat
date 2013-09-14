<?php

$hash = sha1($_SERVER['REMOTE_ADDR'].$Params['user_parameters']['timets'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
echo json_encode(array('result' => $hash));

exit;