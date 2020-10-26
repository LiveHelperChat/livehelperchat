<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/view.tpl.php');

$item =  erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

$tpl->setArray(array(
    'item' => $item,
));

$Result['content'] = $tpl->fetch();

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