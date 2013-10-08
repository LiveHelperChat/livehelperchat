<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/newcannedmsg.tpl.php');
$Departament = new erLhcoreClassModelCannedMsg();

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
         )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Message' ) || $form->Message == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a canned message');
    }

    if ( $form->hasValidData( 'Position' )  )
    {
    	$Departament->position = $form->Position;
    }

    if ( $form->hasValidData( 'Delay' )  )
    {
    	$Departament->delay = $form->Delay;
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

$tpl->set('msg',$Departament);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message')),
)

?>