<?php

header ( 'content-type: application/json; charset=utf-8' );

try {
    $publicChat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);

    if (!($publicChat instanceof erLhcoreClassModelChat)) {
        throw new Exception('Chat not found!');
    }

    // Try to find present chat group chat id
    $groupChat = erLhcoreClassModelGroupChat::findOne(array('filter' => array('chat_id' => $publicChat->id)));

    $newPublicChat = false;

    if (!($groupChat instanceof erLhcoreClassModelGroupChat)) {

        if ($publicChat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            throw new Exception('Private conversation could not be started because chat is closed!');
        }

        // Create a group chat
        $groupChat = new erLhcoreClassModelGroupChat();
        $groupChat->name = $publicChat->id . ' ['.$publicChat->nick.']';
        $groupChat->type = erLhcoreClassModelGroupChat::SUPPORT_CHAT;
        $groupChat->user_id = $currentUser->getUserID();
        $groupChat->time = time();
        $groupChat->tm = 1;
        $groupChat->chat_id = $publicChat->id;
        $groupChat->saveThis();

        $newPublicChat = true;
    }

    $gcOptions = erLhcoreClassModelChatConfig::fetch('groupchat_options')->data;

    $supervisors = [];

    if (isset($gcOptions['supervisor']) && is_numeric($gcOptions['supervisor'])) {
        $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];

        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT `lh_users`.* FROM `lh_users`
        INNER JOIN `lh_groupuser` ON `lh_groupuser`.`user_id` = `lh_users`.`id` 
        INNER JOIN `lh_userdep` ON `lh_userdep`.`user_id` = `lh_users`.`id` 
        WHERE `lh_users`.`disabled` = 0 AND `lh_groupuser`.`group_id` = :group_id AND `lh_userdep`.`ro` = 0 AND `lh_userdep`.`hide_online` = 0 AND `lh_userdep`.`dep_id` = :dep_id AND (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1)");

        $stmt->bindValue(':dep_id',$publicChat->dep_id,PDO::PARAM_INT);
        $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
        $stmt->bindValue(':group_id',$gcOptions['supervisor'],PDO::PARAM_INT);
        $stmt->execute();

        $supervisorsData = $stmt->fetchAll();

        foreach ($supervisorsData as $supervisorData) {
            $user = new erLhcoreClassModelUser();
            $user->setState($supervisorData);
            if ($user->id != $currentUser->getUserID() && ($newPublicChat == true || erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $groupChat->id, 'user_id' => $user->id))) == 0)){
                $supervisors[] = ['nick' => $user->name_official, 'id' => $user->id];
            }
        }
    }

    echo json_encode(array(
        'chat' => $groupChat,
        'supervisors' => $supervisors
    ));

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>