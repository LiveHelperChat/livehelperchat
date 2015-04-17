<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/cannedmsgedit.tpl.php');

$Msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelCannedMsg', (int)$Params['user_parameters']['id'] );

/**
 * Append user departments filter
 * */
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true) {
	if (!in_array($Msg->department_id, $userDepartments)) {
		erLhcoreClassModule::redirect('chat/cannedmsg');
		exit;
	}
}

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('chat/cannedmsg');
    exit;
}

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
{
   $Errors = erLhcoreClassAdminChatValidatorHelper::validateCannedMessage($Msg, $userDepartments);

    if (count($Errors) == 0)
    {
        $Msg->saveThis();

        if (isset($_POST['Save_action'])) {
            erLhcoreClassModule::redirect('chat/cannedmsg');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('canned_message',$Msg);
$tpl->set('limitDepartments',$userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array());

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit canned message')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsgedit_path',array('result' => & $Result));

?>