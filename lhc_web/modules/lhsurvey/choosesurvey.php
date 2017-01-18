<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if (is_numeric($Params['user_parameters']['survey_id'])) {
    erLhcoreClassChatHelper::redirectToSurvey(array('survey_id' => $Params['user_parameters']['survey_id'], 'chat' => $chat, 'user' => $currentUser->getUserData()));
    echo json_encode(array('error' => 'false','chat_id' => $chat->id,'result' => 'updated'));
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhsurvey/choosesurvey.tpl.php');
$tpl->set('chat', $chat);

print $tpl->fetch();
exit;

?>