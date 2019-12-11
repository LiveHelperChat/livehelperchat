<?php
erLhcoreClassRestAPIHandler::setHeaders();

$validSound = array(
    'new_message.mp3',
    'new_message.ogg',
    'new_message.wav',
);


if (in_array($Params['user_parameters']['sound'], $validSound)) {
    echo file_get_contents(erLhcoreClassDesign::design('sound/' . $Params['user_parameters']['sound'], true));
}

exit;
?>