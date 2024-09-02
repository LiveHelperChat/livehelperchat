<?php

session_write_close();

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/sendemail.tpl.php');

$item = new erLhcoreClassModelMailconvMessage();

$chat = null;

if (is_numeric($Params['user_parameters_unordered']['chat_id'])) {

    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters_unordered']['chat_id']);

    if (!erLhcoreClassChat::hasAccessToRead($chat)) {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
        echo $tpl->fetch();
        exit;
    }

    $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['active' => 1, 'mail' => $chat->department->email]]);

    if (is_object($mailbox)) {
        $item->mailbox_id = $mailbox->id;
        $item->mailbox_front = $mailbox->mail;
    }

    $item->from_address = $chat->email;
    $item->from_name = $chat->nick;

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.new_mail_from_chat', array(
        'uparams' => $Params['user_parameters_unordered'],
        'msg' => & $item,
        'chat' => & $chat,
        'tpl' => & $tpl
    ));

    $tpl->set('chat',$chat);
} else {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.new_mail_from_vars', array(
        'uparams' => $Params['user_parameters_unordered'],
        'msg' => & $item,
        'chat' => & $chat,
        'tpl' => & $tpl
    ));
}

$tpl->set('uparams',$Params['user_parameters_unordered']);

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassMailconvValidator::validateNewEmail($item, $chat);

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF token!';
    }

    if (empty($Errors)) {

        $response = array();
        erLhcoreClassMailconvValidator::sendEmail($item, $response, $currentUser->getUserID(), ['background' => true]);

        if ($response['send'] == true) {
            $tpl->set('updated',true);
            $tpl->set('outcome',$response);
            $tpl->set('item',$item);
        } else {
            $tpl->set('errors',$response['errors']);
        }

    } else {
        $tpl->set('errors',$Errors);
    }

}

$tpl->setArray(array(
    'item' => $item,
));

if (isset($Params['user_parameters_unordered']['layout']) && $Params['user_parameters_unordered']['layout'] == 'popup') {
    $Result['pagelayout'] = 'popup';
}

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::design('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/conversations'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>