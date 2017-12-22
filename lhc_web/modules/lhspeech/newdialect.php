<?php

$tpl = erLhcoreClassTemplate::getInstance('lhspeech/newdialect.tpl.php');

$item = new erLhcoreClassModelSpeechLanguageDialect();

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('speech/dialects');
    exit;
}

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
{
    $Errors = erLhcoreClassSpeech::validateDialect($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        erLhcoreClassModule::redirect('speech/dialects');
        exit;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$item);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('speech/dialects'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Dialects')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit dialect')));

?>