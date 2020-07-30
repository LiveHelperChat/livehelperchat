<?php

try {

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        $signature = $conv->mailbox->signature;

        $signature = str_replace([
            '{operator}',
            '{department}'
        ],[
            $currentUser->getUserData()->name_official,
            $conv->department_name
            ],$signature);
        
        echo json_encode([
            'intro' => '<p>' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','On') . ' ' . date('Y-m-d H:i',$message->udate).', '. ($message->from_name != '' ? $message->from_name : $message->from_address) . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','wrote') . ':</p>',
            'signature' => '<div class="gmail_signature">' . $signature . '</div>']);
        exit;

    } else {
        throw new Exception("No permission to read conversation.");
    }

} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/errors/adminchatnopermission.tpl.php');
    echo $tpl->fetch();
    exit;
}

?>