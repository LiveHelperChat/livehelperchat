<?php

echo json_encode(array(
    "voice_call" => [
        "join_call" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Join call'),
        "cancel_join" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Cancel'),
        "stop_share_screen" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Stop sharing your screen'),
        "share_your_screen" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Share your screen'),
        "share_video" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Enable video'),
        "stop_sharing_video" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Disable video'),
        "unmute_mic" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Un-mute mic'),
        "mute_mic" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Mute mic'),
        "end_call_op" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Call for the visitor also will end.'),
        "end_call_button" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','End the call'),
        "leave_a_call" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Leave a call. Visitor will remain on the call'),
        "leave_room" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Leave the call'),
        "leave_call_op" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Leave the call'),
        "join_with_audio" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Audio call'),
        "join_with_audio_video" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Audio & video call'),
        "let_visitor_in" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Let visitor in'),
        "wait_let_in" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Please wait untill operator let's you in"),
        "me_audio" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Me"),
        "wait_join_long" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Please wait untill operator let's you join the call"),
        "visitor_waiting_in" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Visitor is waiting for someone to let him in!"),
        "pending_visitor_join" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Pending visitor to join the call!"),
        "visitor_joined" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Visitor has joined the call!"),
        "audio_call" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Operator"),
        "join_to_start" => erTranslationClassLhTranslation::getInstance()->getTranslation("chat/voice_video","Start conversation"),
    ]
));

?>