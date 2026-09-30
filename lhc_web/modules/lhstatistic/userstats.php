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

if ($Params['user_parameters_unordered']['action'] == 'autoassign') {

    $user_id = (int)$Params['user_parameters']['id'];
    $chat_id = isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0;

    if ($chat_id <= 0) {
        echo '<div class="alert alert-danger p-2 m-0">' . erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats', 'Please enter a valid chat ID') . '</div>';
        exit;
    }

    $autoAssign = new \LiveHelperChat\Mcp\Tools\AutoAssignTools();
    $explain = $autoAssign->explainOperatorForChat($chat_id, $user_id);

    $tpl = erLhcoreClassTemplate::getInstance('lhstatistic/autoassign_explain.tpl.php');
    $tpl->set('explain', $explain);
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