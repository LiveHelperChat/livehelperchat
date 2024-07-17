<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/blocksender.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
        exit;
    }

    $Errors = array();

    if (!($currentUser->hasAccessTo('lhchat','allowblockusers') || $mail->user_id == $currentUser->getUserID())) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','User blocking failed, perhaps you do not have permission to block users?');
    }

    $definition = array(
        'expires' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 360)
        )
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $params = array();

    $params['btype'] = erLhcoreClassModelChatBlockedUser::BLOCK_EMAIL_CONV;
    $params['mail'] = $mail;

    if (!$form->hasValidData('expires')) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers', 'Please choose expire option!');
    } else {
        if ($form->expires > 0) {
            $params['expires'] = time() + ($form->expires * 24 * 3600);
        } else {
            $params['expires'] = 0;
        }
    }

    if (empty($Errors)) {
        erLhcoreClassModelChatBlockedUser::blockChat($params);
        $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
        $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers', 'Sender was blocked!'));
        header('Content-type: application/json');
        echo json_encode(array('error' => false, 'result' => $tpl->fetch()));
        exit;
    } else {
        $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
        $tpl->set('errors', $Errors);
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'result' => $tpl->fetch()));
        exit;
    }

}

$tpl->set('mail', $mail);
$tpl->set('input_data', $inputData);

print $tpl->fetch();
exit;

?>