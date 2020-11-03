<?php

echo json_encode(array(
    "chat_canned" => [
        "canned" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages'),
        "navigate" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','🔍 Navigate with ⮃ and ↵ Enter. Esc to quit.'),
        "send_instantly" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Send instantly')
    ]
));

?>