<?php

echo json_encode(array(
    "message" => [
        "enter_message" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter a message'),
    ],
    "operator" => [
        "pending_join" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Pending to join...'),
        "already_member" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Already a member'),
        "invite" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invite'),
        "cancel_invite" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Cancel invite'),
        "leave_group" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave the group'),
        "search_tip" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter operator name or surname or just click search to invite'),
        "leave_group_tip" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave the group, you still can join anytime you want.'),
    ]
));

?>