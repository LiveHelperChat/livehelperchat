<?php

header ( 'content-type: application/json; charset=utf-8' );

$visitorLanguage = (string)$Params['user_parameters']['visitor_language'];
$operatorLanguage = (string)$Params['user_parameters']['operator_language'];

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $errors = array();
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.before_messagetranslated', array('chat' => & $chat, 'errors' => & $errors));

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        $errors[] = 'Invalid CSRF token!';
    }

    if (empty($errors)) {
        // User clicked button second time, and languages matches, that means they just stopped translation

        if ((!isset($_POST['live_translations']) || $_POST['live_translations'] == 'false') && isset($chat->chat_variables_array['lhc_live_trans']) && $chat->chat_variables_array['lhc_live_trans'] === true) {
            $chatVariablesArray = $chat->chat_variables_array;
            unset($chatVariablesArray['lhc_live_trans']);
            $chat->chat_variables_array = $chatVariablesArray;
            $chat->chat_variables = json_encode($chatVariablesArray);
            $chat->updateThis(array('update' => array('chat_variables')));
        }

        try {
            $data = erLhcoreClassTranslate::setChatLanguages($chat, $visitorLanguage, $operatorLanguage, array('translate_old' => (isset($_POST['translate_old']) && $_POST['translate_old'] == 'true')));
            $data['error'] = false;
            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
            $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Settings has been saved'));
            $data['result'] = $tpl->fetch();
            $data['translation_status'] = (isset($_POST['translate_old']) && $_POST['translate_old'] == 'true');

            if (isset($_POST['live_translations']) && $_POST['live_translations'] == 'true') {
                $chatVariablesArray = $chat->chat_variables_array;
                $chatVariablesArray['lhc_live_trans'] = true;
                $chat->chat_variables_array = $chatVariablesArray;
                $chat->chat_variables = json_encode($chatVariablesArray);
                $chat->updateThis(array('update' => array('chat_variables')));
            }

            echo json_encode($data);

        } catch (Exception $e) {
            $data = array('error' => true);
            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
            $tpl->set('errors', array($e->getMessage(), erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Please choose translation languages manually and click Auto translate')));
            $data['result'] = $tpl->fetch();
            $data['translation_status'] = false;
            echo json_encode($data);
        }

    } else {
        $data = array('error' => true);
        $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
        $tpl->set('errors', $errors);
        $data['result'] = $tpl->fetch();
        $data['translation_status'] = false;
        echo json_encode($data);
    }
}

exit;
?>