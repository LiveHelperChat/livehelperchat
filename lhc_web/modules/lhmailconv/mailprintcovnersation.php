<?php

$conv = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
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

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailprintconversation.tpl.php');
$tpl->set('chat',$conv);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>