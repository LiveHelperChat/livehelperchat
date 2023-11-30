<?php

echo json_encode(array(
    "homepage.invisible" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Invisible'),
    "homepage.visible"  => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Visible'),
    "homepage.change_visibility" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible'),
    "homepage.change_online_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings', 'Change my status to online/offline'),
    "homepage.status_offline" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account', 'Offline'),
    "homepage.status_online" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account', 'Online'),
    "homepage.always_online" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my persistent status to online'),
    "homepage.always_online_mode" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Always online'),
    "homepage.always_online_activity" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Based on activity'),

    // Open chat
    'front_default.chat_id_to_open' => erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Chat ID to open'),
    'front_default.open_a_chat' => erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open a chat'),

    // Widgets
    'widget_title.pending_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats'),
    'widget_title.bot_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Bot chats'),
    'widget_title.active_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats'),
    'widget_title.unread_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Unread messages'),
    'widget_title.group_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Group chats'),
    'widget_title.subject_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Ongoing trigger alerts!'),
    'widget_title.transfer_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred chats'),
    'widget_title.my_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','My active and pending chats'),
    'widget_title.depgroups_stats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Departments stats'),
    'widget_title.online_op' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online operators'),
));

?>

