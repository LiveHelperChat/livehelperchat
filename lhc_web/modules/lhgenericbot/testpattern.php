<?php

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['id']);

$response = "";

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {
    $response = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('as_json' => true, 'chat' => $chat, 'args' => ['chat' => $chat]));
    if (strpos($response,'{') === 0) {
        $response = json_encode(json_decode($response,true), JSON_PRETTY_PRINT);
    }
} else {
    $response = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Chat does not exists or you do not have permission to access it!');
}

echo nl2br(htmlspecialchars($response));
exit;