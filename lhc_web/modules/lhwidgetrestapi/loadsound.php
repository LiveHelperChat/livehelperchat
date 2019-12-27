<?php
erLhcoreClassRestAPIHandler::setHeaders();

$validSound = array(
    'new_message_mp3' => 'new_message.mp3',
    'new_message_ogg' => 'new_message.ogg',
    'new_message_wav' => 'new_message.wav',
);

if (key_exists($Params['user_parameters']['sound'], $validSound)) {
    echo file_get_contents(erLhcoreClassDesign::design('sound/' . $validSound[$Params['user_parameters']['sound']], true));
}

exit;
?>