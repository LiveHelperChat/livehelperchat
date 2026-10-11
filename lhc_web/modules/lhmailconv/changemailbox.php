<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/changemailbox.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if (!($mail instanceof erLhcoreClassModelMailconvConversation) || !erLhcoreClassChat::hasAccessToRead($mail) || !erLhcoreClassChat::hasAccessToWrite($mail)) {
    echo 'No permission to access this conversation!';
    exit;
}

$inputData = [
    'new_mailbox_id' => $mail->mailbox_id,
    'source_mail' => [],
];

if (ezcInputForm::hasPostData($mail)) {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        echo json_encode(['error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Invalid CSRF token')]);
        exit;
    }

    $definition = array(
        'new_mailbox_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ($form->hasValidData('new_mailbox_id')) {
        $inputData['new_mailbox_id'] = $form->new_mailbox_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose a mailbox');
    }

    if (empty($Errors)) {
        try {
            $mail->mailbox_id = $inputData['new_mailbox_id'];
            $mail->updateThis(['update' => ['mailbox_id']]);
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', [$e->getMessage()]);
        }
    } else {
        $tpl->set('errors', $Errors);
    }

}

$tpl->set('mail', $mail);
$tpl->set('input_data', $inputData);

print $tpl->fetch();
exit;

?>