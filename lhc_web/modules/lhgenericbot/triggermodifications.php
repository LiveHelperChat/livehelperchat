<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/triggermodifications.tpl.php');

$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

if ($bot instanceof erLhcoreClassModelGenericBotBot) {

    $triggers = erLhcoreClassModelGenericBotTrigger::getList(array(
        'filter' => array('bot_id' => $bot->id),
        'filtergt' => array('updated_at' => 0),
        'sort' => 'updated_at DESC',
        'limit' => 10,
        'ignore_fields' => array('actions')
    ));

    $userIds = array();
    foreach ($triggers as $trigger) {
        if ((int)$trigger->muser_id > 0) {
            $userIds[(int)$trigger->muser_id] = (int)$trigger->muser_id;
        }
    }

    $users = array();
    if (!empty($userIds)) {
        foreach (erLhcoreClassModelUser::getList(array('filterin' => array('id' => array_values($userIds)), 'limit' => false)) as $user) {
            $users[$user->id] = (string)$user;
        }
    }

    $tpl->set('bot', $bot);
    $tpl->set('triggers', $triggers);
    $tpl->set('users', $users);

} else {
    $tpl->set('bot', null);
    $tpl->set('triggers', array());
    $tpl->set('users', array());
}

echo $tpl->fetch();
exit;

?>
