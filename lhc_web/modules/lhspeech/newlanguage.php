<?php

$tpl = erLhcoreClassTemplate::getInstance('lhspeech/newlanguage.tpl.php');

$item = new erLhcoreClassModelSpeechLanguage();

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('speech/languages');
    exit;
}

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
{
    $Errors = erLhcoreClassSpeech::validateLanguage($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        erLhcoreClassModule::redirect('speech/languages');
        exit;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$item);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('speech/languages'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Languages')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit dialect')));

?>