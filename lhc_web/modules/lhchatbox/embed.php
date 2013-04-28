<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header("Content-type: text/javascript");

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/embed.tpl.php');
$tpl->set('heightchatcontent',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);


echo $tpl->fetch();
exit;