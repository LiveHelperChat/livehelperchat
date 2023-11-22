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
    "homepage.always_online_activity" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Based on activity')
));

?>

