<?php

erLhcoreClassRestAPIHandler::setHeaders();

erTranslationClassLhTranslation::$htmlEscape = false;

header('Cache-Control: max-age=84600');
header("Expires:".gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
header("Last-Modified: ".gmdate("D, d M Y H:i:s", time())." GMT");
header("Pragma: cache");
header("User-Cache-Control: max-age=84600");

$translations = array(
    "button" => [
        "minimize" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Minimize'),
        "end_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','End chat'),
        "start_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat'),
        "bb_code" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','BB code'),
        "print" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Print'),
        "send" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Send'),
        "popup" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Popup')
    ],
    "chat" => [
        "option_sound" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from the operator'),
        "chat_closed" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat',"This chat is closed now. You can close window."),
        "drop_files" => erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Drop your files here.'),
        "type_here" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Type your message here...')
    ],
    "start_chat" => [
        "thank_you_for_feedback" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Thank you for your feedback...'),
        "leave_a_message" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message')
    ],
    "online_chat" => [
        "go_to_survey" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Go to Survey.'),
        "leave_a_message" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message')
    ],
    "department" => [
        "offline" => "--=" . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Offline') . "=--"
    ],
    "file" => [
        "uploading" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Uploading'),
        "incorrect_type" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Incorrect file type!'),
        "to_big_file" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','File to big!')
    ],
    "notifications" => [
        "subscribing" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/notifications','Subscribing...')
    ],
    "bot" => [
        "please_choose" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose!')
    ],
    "bbcode" => [
        "img_link" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Please enter link to an image!'),
        "link" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Please enter a link!'),
        "link_here" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Here is a link')
    ]
);

echo json_encode($translations);

exit;
?>