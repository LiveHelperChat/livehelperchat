<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/view.tpl.php');

$item =  erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if (!($item instanceof \erLhcoreClassModelMailconvConversation)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
    if (isset($mailData['mail'])) {
        $item = $mailData['mail'];
    }
}

$tpl->setArray(array(
    'item' => $item,
));

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'chattabs';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/conversations'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Conversations')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'View')
    )
);

?>