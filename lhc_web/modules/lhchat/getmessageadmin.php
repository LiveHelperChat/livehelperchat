<?php

header('Content-type: application/json');

try {
    $Chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params ['user_parameters'] ['chat_id']);

    if (erLhcoreClassChat::hasAccessToRead($Chat)) {

        $msg = erLhcoreClassModelmsg::fetch($Params ['user_parameters'] ['msgid']);

        if (is_object($msg)) {
            if ($msg->chat_id == $Chat->id) {

                $tpl = erLhcoreClassTemplate::getInstance('lhchat/syncadmin.tpl.php');
                $tpl->set('messages', array(
                    ( array )$msg
                ));
                $tpl->set('chat', $Chat);
                $tpl->set('see_sensitive_information', $currentUser->hasAccessTo('lhchat', 'see_sensitive_information'));

                echo json_encode(array(
                    'error' => 'f',
                    'msg' => trim($tpl->fetch())
                ));
                exit;
            }
        } else {
            echo json_encode(array(
                'error' => 'f',
                'msg' => trim('<div class="text-muted"><div class="msg-body">' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin', 'Message was removed!') . '</div></div>')
            ));
            exit;
        }
    }

} catch (Exception $e) {
    echo $e->getMessage();
}

echo json_encode(array(
    'error' => 't'
));

exit();