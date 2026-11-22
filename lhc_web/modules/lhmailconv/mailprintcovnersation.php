<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailprintconversation.tpl.php');

$conv = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if ($conv instanceof \erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv)) {
    if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
        if ($conv->from_address == $conv->from_name) {
            $conv->from_name = \LiveHelperChat\Helpers\Anonymizer::maskEmail($conv->from_name);
        }
        $conv->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($conv->from_address);
    }

    if (isset($conv->phone)) {
        $conv->phone_front = $conv->phone;

        if ($conv->phone != '' && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_see_unhidden')) {
            $conv->phone_front = \LiveHelperChat\Helpers\Anonymizer::maskPhone($conv->phone);
            if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','have_phone_link')) {
                $conv->phone = '';
            }
        }
    }

    $tpl->set('chat',$conv);
} else {
    http_response_code(403);
    $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>