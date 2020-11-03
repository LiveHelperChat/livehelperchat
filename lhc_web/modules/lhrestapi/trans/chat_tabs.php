<?php

echo json_encode(array(
    "chat_tabs" => [
        "open_chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat_tabs','Your open chats will appear here'),
        "chat_owner" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat_tabs','You are a chat owner')
    ]
));

?>