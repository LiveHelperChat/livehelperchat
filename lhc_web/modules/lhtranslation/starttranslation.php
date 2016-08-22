<?php 

$visitorLanguage = (string)$Params['user_parameters']['visitor_language'];
$operatorLanguage = (string)$Params['user_parameters']['operator_language'];

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $errors = array();
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.before_messagetranslated', array('chat' => & $chat, 'errors' => & $errors));

    if (empty($errors)) {
        // User clicked button second time, and languages matches, that means he just stopped translation
        if ($chat->chat_locale == $visitorLanguage && $chat->chat_locale_to == $operatorLanguage) {
            $data['error'] = false;
            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
            $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Chat messages automatic translations has been stopped'));
            $data['result'] = $tpl->fetch();
            $chat->chat_locale = '';
            $chat->chat_locale_to = '';
            $chat->updateThis();
            $data['translation_status'] = false;
            echo json_encode($data);
        } else {
            try {
                $data = erLhcoreClassTranslate::setChatLanguages($chat, $visitorLanguage, $operatorLanguage);
                $data['error'] = false;
                $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
                $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Messages has been translated'));
                $data['result'] = $tpl->fetch();
                $data['translation_status'] = true;
                echo json_encode($data);
            } catch (Exception $e) {
                $data = array('error' => true);
                $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
                $tpl->set('errors', array($e->getMessage(), erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Please choose translation languages manually and click Auto translate')));
                $data['result'] = $tpl->fetch();
                $data['translation_status'] = false;
                $chat->chat_locale = '';
                $chat->chat_locale_to = '';
                $chat->updateThis();
                echo json_encode($data);
            }
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