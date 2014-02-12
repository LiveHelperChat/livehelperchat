<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/cannedmsgedit.tpl.php');

$Msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelCannedMsg', (int)$Params['user_parameters']['id'] );

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('chat/cannedmsg');
    exit;
}

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
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
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Message' ) || $form->Message == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter canned message');
    }

    if ( $form->hasValidData( 'Position' )  )
    {
        $Msg->position = $form->Position;
    }

    if ( $form->hasValidData( 'DepartmentID' )  ) {
        $Msg->department_id = $form->DepartmentID;
    } else {
    	$Msg->department_id = 0;
    }

    if ( $form->hasValidData( 'Delay' )  )
    {
        $Msg->delay = $form->Delay;
    }

    if (count($Errors) == 0)
    {
        $Msg->msg = $form->Message;

        erLhcoreClassChat::getSession()->update($Msg);

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

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit canned message')));

?>