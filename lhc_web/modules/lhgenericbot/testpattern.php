<?php

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['id']);

$response = "";

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {
    $response = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('chat' => $chat, 'args' => ['chat' => $chat]));
} else {
    $response = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Chat does not exists or you do not have permission to access it!');
}

echo htmlspecialchars($response);
exit;