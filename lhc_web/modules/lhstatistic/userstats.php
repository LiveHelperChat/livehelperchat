<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/userstats.tpl.php');

try {
    $user = erLhcoreClassModelUser::fetch($Params['user_parameters']['id']);
    $tpl->set('user', $user);
} catch(Exception $e) {
    $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

echo $tpl->fetch();
exit;

?>