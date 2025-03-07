<?php

if ($Params['user_parameters_unordered']['action'] == 'chatsmoment') {

    $linuxTimestampEnd = $linuxTimestamp = time();


    if (isset($_POST['ts']) && $_POST['ts'] != '') {
        $dateStr = $_POST['ts'];
        $dateObj = new DateTime($dateStr, new DateTimeZone(date_default_timezone_get()));
        $linuxTimestamp = $dateObj->getTimestamp();
    } else {
        $linuxTimestamp = time();
    }

    if (isset($_POST['ts_end']) && $_POST['ts_end'] != '') {
        $dateStr = $_POST['ts_end'];
        $dateObj = new DateTime($dateStr, new DateTimeZone(date_default_timezone_get()));
        $linuxTimestampEnd = $dateObj->getTimestamp();
    } else {
        $linuxTimestampEnd = $linuxTimestamp;
    }

    if ($linuxTimestampEnd == $linuxTimestamp) {
        $customFilter = ['((`status` = 2 AND `time` <= ' . $linuxTimestamp . ' AND `cls_time` >= ' . $linuxTimestamp .') OR (status IN (0,1) AND `time` <= ' . $linuxTimestamp. '))'];
        $chats = erLhcoreClassModelChat::getList(['sort' => 'id ASC', 'limit' => 100, 'customfilter' => $customFilter,  'filter' => ['user_id' => $Params['user_parameters']['id']]]);
    } else {
        $chats = erLhcoreClassModelChat::getList(['sort' => 'id ASC', 'limit' => 100, 'filtergte' => ['time' => $linuxTimestamp], 'filterlte' => ['cls_time' => $linuxTimestampEnd], 'filter' => ['user_id' => $Params['user_parameters']['id']]]);
    }

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