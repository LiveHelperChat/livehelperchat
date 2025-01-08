<?php

if ($Params['user_parameters_unordered']['action'] == 'chatsmoment') {
    $dateStr = $_POST['ts'];
    $dateObj = new DateTime($dateStr, new DateTimeZone(date_default_timezone_get()));
    $linuxTimestamp = $dateObj->getTimestamp();
    $chats = erLhcoreClassModelChat::getList(['sort' => 'id ASC', 'limit' => 10, 'filterlte' => ['time' => $linuxTimestamp], 'filtergte' => ['cls_time' => $linuxTimestamp], 'filter' => ['user_id' => $Params['user_parameters']['id']]]);
    $tpl = erLhcoreClassTemplate::getInstance('lhstatistic/momentary_chats.tpl.php');
    $tpl->set('previousChats', $chats);
    echo $tpl->fetch();
    exit;
}

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