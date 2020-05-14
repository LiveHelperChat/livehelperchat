<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgroupchat/new.tpl.php');
$item = new erLhcoreClassModelGroupChat();

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('groupchat/list');
    exit;
}

if (isset($_POST['Save_action']) || isset($_POST['Update_action']))
{
    $Errors = erLhcoreClassGroupChat::validateGroupChat($item);

    if (count($Errors) == 0)
    {
        $item->user_id = erLhcoreClassUser::instance()->getUserID();
        $item->time = time();
        $item->saveThis();

        if (isset($_POST['Update_action'])) {
            erLhcoreClassModule::redirect('groupchat/edit','/' . $item->id);
        } else {
            erLhcoreClassModule::redirect('groupchat/list');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$item);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('groupchat/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('groupchat/group','Group chats')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('groupchat/group','New group chat')),
)

?>