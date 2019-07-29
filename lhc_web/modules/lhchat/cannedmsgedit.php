<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/cannedmsgedit.tpl.php');

$Msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelCannedMsg', (int)$Params['user_parameters']['id'] );

/**
 * Append user departments filter
 * */
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true) {
	if (!in_array($Msg->department_id, $userDepartments) && $Msg->department_id != 0) {
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
    $previousState = $Msg->getState();

   $Errors = erLhcoreClassAdminChatValidatorHelper::validateCannedMessage($Msg, $userDepartments);
   
   erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.canned_msg_before_save',array('departments' => $userDepartments, 'errors' => & $Errors, 'msg' => & $Msg, 'scope' => 'global'));
   
    if (count($Errors) == 0)
    {
        $Msg->saveThis();
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.canned_msg_after_save',array('msg' => & $Msg));

        $currentState = $Msg->getState();

        erLhcoreClassLog::logObjectChange(array(
            'object' => $Msg,
            'check_log' => true,
            'msg' => array(
                'prev' => $previousState,
                'curr' => $currentState,
                'user_id' => $currentUser->getUserID()
            )
        ));
        
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
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.cannedmsg.js').'"></script>';

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit canned message')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsgedit_path',array('result' => & $Result));

?>