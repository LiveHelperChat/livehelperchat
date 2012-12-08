<?php
$tpl = new erLhcoreClassTemplate( 'lhchat/startchat.tpl.php');
$tpl->set('referer','');

if (isset($_POST['StartChat']))
{
   $definition = array(
        'Username' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        ),
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
        ),
        'DepartamentID' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'int'
        ),
    );
  
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Username' ) || $form->Username == '' )
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
    }
    
    if ($form->hasValidData( 'Username' ) && $form->Username != '' && strlen($form->Username) > 10)
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 10 characters');
    }
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Wrong email');
    }
    
    if (count($Errors) == 0)
    {
       $chat = new erLhcoreClassModelChat();
       $chat->nick = $form->Username;
       $chat->email = $form->Email;
       $chat->time = time();
       $chat->status = 0;
       $chat->dep_id = $form->DepartamentID;
       $chat->setIP();
       $chat->hash = erLhcoreClassChat::generateHash();
       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
       
       // Store chat
       erLhcoreClassChat::getSession()->save($chat);
       
       // Redirect user
       erLhcoreClassModule::redirect('chat/chat/' . $chat->id . '/' . $chat->hash);
       return ;
    } else {        
        $tpl->set('errArr',$Errors);
    }  
}

if (isset($_GET['URLReferer']))
{
    $tpl->set('referer',$_GET['URLReferer']);
}

if (isset($_POST['URLRefer']))
{
    $tpl->set('referer',$_POST['URLRefer']);
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat';

$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill form to start chat')))

?>