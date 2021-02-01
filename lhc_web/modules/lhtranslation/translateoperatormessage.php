<?php
header ( 'content-type: application/json; charset=utf-8' );

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    if ($_POST['msg'] != '') {
        try {
            $translatedMessage = erLhcoreClassTranslate::translateTo( preg_replace('#\[translation\](.*?)\[/translation\]#is', '',$_POST['msg']), ($chat->chat_locale_to != '' ? $chat->chat_locale_to : substr(erLhcoreClassSystem::instance()->Language, 0, 2)), $chat->chat_locale);
            echo json_encode(array('error' => false,'msg' => $_POST['msg']."\n".'[translation]'. $translatedMessage .'[/translation]'));
        } catch (Exception $e) {
            echo json_encode(array('error' => true,'msg' => $_POST['msg']."\n".'[translation]'. $e->getMessage() .'[/translation]'));
        }
    } else {
        echo json_encode(array('error' => true,'msg' => ''));
    }
}

exit;
?>