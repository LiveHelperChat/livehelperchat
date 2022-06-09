<?php

echo json_encode(array(
    "chat_tabs" => [
        "open_chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat_tabs','Your open chats will appear here'),
        "chat_owner" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat_tabs','You are a chat owner'),
        "pending_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chat'),
        "active_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Active chat'),
        "bot_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Bot chat'),
        "closed_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Closed chat')
    ]
));

?>