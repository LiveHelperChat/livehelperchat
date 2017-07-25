<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.newcannedmsg', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/newcannedmsg.tpl.php');
$CannedMessage = new erLhcoreClassModelCannedMsg();

/**
 * Append user departments filter
 * */
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('chat/cannedmsg');
    exit;
}

if (isset($_POST['Save_action']))
{
    $Errors = erLhcoreClassAdminChatValidatorHelper::validateCannedMessage($CannedMessage, $userDepartments);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_newcannedmsg', array('departments' => $userDepartments, 'scope' => 'global', 'errors' => & $Errors, 'msg' => & $CannedMessage));

    if (count($Errors) == 0)
    {
        $CannedMessage->saveThis();
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.newcannedmsg_saved', array('msg' => & $CannedMessage));

        erLhcoreClassModule::redirect('chat/cannedmsg');
        exit ;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('canned_message',$CannedMessage);
$tpl->set('limitDepartments',$userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array());

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message')),
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.newcannedmsg_path',array('result' => & $Result));
?>