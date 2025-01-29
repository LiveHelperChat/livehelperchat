<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

if (erLhcoreClassModelChat::findOne(['filter' => ['gbot_id' => $Params['user_parameters']['id']]])){
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bot was assigned to one of the chats. Please remove those chats first!')]);
    $tpl->set('hideErrorButton',true);
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'login';
} else {
    $gbot = erLhcoreClassModelGenericBotBot::fetch($Params['user_parameters']['id']);
    $gbot->removeThis();
    erLhcoreClassModule::redirect('genericbot/list');
    exit;
}



?>