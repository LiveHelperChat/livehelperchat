<?php

header ('content-type: application/json; charset=utf-8');

$visitorLanguage = (string) $Params['user_parameters']['visitor_language'];
$operatorLanguage = (string) $Params['user_parameters']['operator_language'];

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $errors = [];
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.before_messagetranslated', ['chat' => & $chat, 'errors' => & $errors]);

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
            
            $msgStop = new erLhcoreClassModelmsg();
            $msgStop->chat_id = $chat->id;
            $msgStop->user_id = -1;
            $msgStop->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Automatic translation stopped');
            $msgStop->time = time();
            $msgStop->saveThis();

            $chat->last_msg_id = $msgStop->id;
            $chat->updateThis(['update' => ['chat_variables','last_msg_id']]);
        }

        try {
            $data = erLhcoreClassTranslate::setChatLanguages($chat, $visitorLanguage, $operatorLanguage, ['translate_old' => (isset($_POST['translate_old']) && $_POST['translate_old'] == 'true')]);
            $data['error'] = false;
            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
            $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Settings has been saved'));
            $data['result'] = $tpl->fetch();
            
            $data['translation_status'] = isset($_POST['live_translations']) && $_POST['live_translations'] == 'true';

            if (isset($_POST['live_translations']) && $_POST['live_translations'] == 'true') {
                $chatVariablesArray = $chat->chat_variables_array;
                $chatVariablesArray['lhc_live_trans'] = true;
                $chat->chat_variables_array = $chatVariablesArray;
                $chat->chat_variables = json_encode($chatVariablesArray);
                
                $msgStart = new erLhcoreClassModelmsg();
                $msgStart->chat_id = $chat->id;
                $msgStart->user_id = -1;
                $msgStart->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Automatic translation started') . ' [' . $chat->chat_locale . '] => [' . $chat->chat_locale_to . '].' . (isset($_POST['translate_old']) && $_POST['translate_old'] == 'true' ? ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Old messages will be translated.') : '' );
                $msgStart->time = time();
                $msgStart->saveThis();

                $chat->last_msg_id = $msgStart->id;
                $chat->updateThis(['update' => ['chat_variables','last_msg_id']]);

            }

            echo json_encode($data);

        } catch (Exception $e) {
            $data = ['error' => true];
            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
            $tpl->set('errors', [$e->getMessage(), erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Please choose translation languages manually and click Auto translate')]);
            $data['result'] = $tpl->fetch();
            $data['translation_status'] = false;
            echo json_encode($data);
        }

    } else {
        $data = ['error' => true];
        $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
        $tpl->set('errors', $errors);
        $data['result'] = $tpl->fetch();
        $data['translation_status'] = false;
        echo json_encode($data);
    }
}

exit;
