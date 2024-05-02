<?php
header('content-type: application/json; charset=utf-8');

$definition = array(
    'data' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$requestBody = json_decode(file_get_contents('php://input'),true);

$form = new erLhcoreClassInputForm(INPUT_GET, $definition, null, $requestBody);

$Chat = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

$errorTpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');

if ( is_object($Chat) && erLhcoreClassChat::hasAccessToRead($Chat) )
{
    if (isset($_SERVER['HTTP_X_CSRFTOKEN']) && $currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN']) && $form->hasInputField('data') && $form->hasValidData('data')) {
        $errors = array();
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_save_remarks', array('chat' => & $Chat, 'errors' => & $errors));

        if (empty($errors)) {

            if ($Params['user_parameters_unordered']['type'] == 'customer') {
                $remarks = erLhcoreClassModelMailconvRemarks::getInstance($Chat->customer_email, true);
                $remarks->remarks = $form->data;
                $remarks->saveThis(array('update' => array('remarks')));
            } else {
                $Chat->remarks = $form->data;
                $Chat->saveThis(array('update' => array('remarks')));
            }

            echo json_encode(array('error' => 'false'));
            exit;
        } else {
            $errorTpl->set('errors', $errors);
            echo json_encode(array('error' => 'true', 'result' => $errorTpl->fetch()),\JSON_INVALID_UTF8_IGNORE);
            exit;
        }
    } else {
        $errorTpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Form data not valid')));
        echo json_encode(array('error' => 'true', 'result' => $errorTpl->fetch()),\JSON_INVALID_UTF8_IGNORE);
        exit;
    }
} else {
    $errorTpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Has no access to this chat')));
    echo json_encode(array('error' => 'true', 'result' => $errorTpl->fetch()),\JSON_INVALID_UTF8_IGNORE);
    exit;
}
?>