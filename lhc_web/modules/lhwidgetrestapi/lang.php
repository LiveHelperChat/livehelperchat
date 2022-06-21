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
        "start_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start a chat'),
        "bb_code" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','BB code'),
        "print" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Print'),
        "dwntxt" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Download as txt'),
        "mail" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Send chat to your email!'),
        "email" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Email'),
        "send" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Send to email'),
        "popup" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Popup'),
        "record_voice" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Record voice message'),
        "start_chat_With_us" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start a chat with us!'),
        "cancel" => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel'),
        "voice" => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Voice call'),
        "scroll_bottom" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','↓ Scroll to the bottom'),
        "new_msg" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New message!'),
        "new_msgm" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New messages!'),
        "new" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New'),
        "close" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Close'),
        "back_to_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Back'),
        "email_explain" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','We will send you a trascript of your chat with agent, so you can browse it later.')
    ],
    "chat" => [
        "option_sound" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from the operator'),
        "chat_closed" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','This chat is closed now. You can close window.'),
        "drop_files" => erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Drop your files here.'),
        "type_here" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your message...'),
        "enter_email" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendchat', 'Enter your e-mail')
    ],
    "start_chat" => [
        "thank_you_for_feedback" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Thank you for your feedback...'),
        "leave_a_message" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message'),
        "cant_start_a_chat" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat can not be started now. Please try again later.'),
        "visitor" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor')
    ],
    "online_chat" => [
        "go_to_survey" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Go to Survey.'),
        "leave_a_message" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message'),
        "send_failed" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There was an issue sending the message. Try again.'),
        "send_connection" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','No internet connection.')
    ],
    "department" => [
        "offline" => "--=" . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Offline') . "=--",
        "choose_a_product" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Choose a product'),
        "product" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Product')
    ],
    "file" => [
        "uploading" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Uploading'),
        "incorrect_type" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Incorrect file type!'),
        "to_big_file" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','File too big!'),
        "processing" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Processing...'),
        "upload_failed" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Upload failed!'),
        "completed" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/file','Completed.'),
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
        "link_here" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Here is a link'),
        "img_download" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Download')
    ],
    "voice" => [
        "cancel_voice_message" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Cancel voice message'),
        "record_voice_message" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Record voice message'),
        "stop_recording" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Stop recording'),
        "play_recorded" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Play recorded message'),
        "stop_playing_recorded" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Stop playing recorded audio'),
        "send" =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Send voice message')
    ]
);

echo json_encode($translations);

exit;
?>