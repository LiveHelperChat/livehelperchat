<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

if (erLhcoreClassModelChat::findOne(['filter' => ['gbot_id' => $Params['user_parameters']['id']]])){
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bot was assigned to one of the chats. Please remove those chats first!')]);
    $tpl->set('hideErrorButton',true);
    echo json_encode(['error' => true, 'result' => $tpl->fetch()]);
    exit;
} else {
    $gbot = erLhcoreClassModelGenericBotBot::fetch($Params['user_parameters']['id']);
    $gbot->removeThis();
    echo json_encode(['error' => false]);
    exit;
}



?>