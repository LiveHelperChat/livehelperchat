<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.newcannedmsg', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/newcannedmsg.tpl.php');
$Departament = new erLhcoreClassModelCannedMsg();

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
   $definition = array(
        'Message' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Position' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0)
        ),
        'Delay' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0)
         ),
        'DepartmentID' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
        ),
        'AutoSend' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Message' ) || $form->Message == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a canned message');
    }
    
    if ( $form->hasValidData( 'AutoSend' ) && $form->AutoSend == true )
    {
    	$Departament->auto_send = 1;
    } else {
    	$Departament->auto_send = 0;
    }
    
    if ( $form->hasValidData( 'Position' )  )
    {
    	$Departament->position = $form->Position;
    }

    if ( $form->hasValidData( 'Delay' )  )
    {
    	$Departament->delay = $form->Delay;
    }
    
	if ( $form->hasValidData( 'DepartmentID' )  ) {
        $Departament->department_id = $form->DepartmentID;        
        if ($userDepartments !== true) {
        	if (!in_array($Departament->department_id, $userDepartments)) {
        		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please choose a department');
        	}
        }
    } else {
    	// User has to choose a department
    	if ($userDepartments !== true) {    	
    		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please choose a department');    		
    	} else {
    		$Departament->department_id = 0;
    	}
    }
    
    if (count($Errors) == 0)
    {
        $Departament->msg = $form->Message;
        erLhcoreClassChat::getSession()->save($Departament);
        erLhcoreClassModule::redirect('chat/cannedmsg');
        exit ;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('canned_message',$Departament);
$tpl->set('limitDepartments',$userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array());

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message')),
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.newcannedmsg_path',array('result' => & $Result));
?>