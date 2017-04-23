<?php
header('content-type: application/json; charset=utf-8');
/**
 * These operations are executed directly in an iframe. Most of the time it's postMessage
 * */
$definition = array(
    'operation' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
    )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->operation) != '')
{
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    $errors = array();

    switch ($form->operation) {
        case 'lhc_screenshot':
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.before_screenshot_addoperacion',array('chat' => & $Chat, 'errors' => & $errors));
            break;
        case (preg_match('/^lhc_cobrowse/', $form->operation) ? true : false):
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('cobrowse.before_started',array('chat' => & $Chat, 'errors' => & $errors));
            break;
    }

    if(empty($errors)) {
        // Has access to read, chat && billing success
        if (erLhcoreClassChat::hasAccessToRead($Chat)) {
            $currentUser = erLhcoreClassUser::instance();

            if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
                echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token'));
                exit;
            }

            $Chat->operation .= $form->operation . "\n";
            $Chat->updateThis();

            echo json_encode(array('error' => 'false'));
        }
    } else {
        echo json_encode(array('error' => 'true', 'errors' => $errors ));
    }

    $db->commit();
} else {
    echo json_encode(array('error' => 'true'));
}

exit;

?>