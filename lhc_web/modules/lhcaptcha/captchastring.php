<?php

$hash = sha1(erLhcoreClassIPDetect::getIP().$Params['user_parameters']['timets'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
echo json_encode(array('result' => $hash));

exit;