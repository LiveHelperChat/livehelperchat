<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgroupchat/edit.tpl.php');

$item = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('department/departments');
        exit;
    }

    $Errors = erLhcoreClassGroupChat::validateGroupChat($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        if (isset($_POST['Save_action'])) {
            erLhcoreClassModule::redirect('groupchat/list');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$item);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('groupchat/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Group chats')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit a group chat').' - '.$item->name));

?>