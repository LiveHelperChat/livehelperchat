<?php

$tpl = new erLhcoreClassTemplate('lhuser/edit.tpl.php');

if (isset($_POST['Update_account']))
{    
   $definition = array(
        'Password' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'Password1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),    
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
        ),
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        ),
        'Surname' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        ),
    );
  
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Wrong email address');
    } 
    
    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Please enter name');
    }
    
    if ( !$form->hasValidData( 'Surname' ) || $form->Surname == '')
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Please enter surname');
    }
    
    if ( $form->hasInputField( 'Password' ) && (!$form->hasInputField( 'Password1' ) || $form->Password != $form->Password1  ) ) // check for optional field
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Passwords mismatch');
    }
    
    if (count($Errors) == 0)
    {        
        
        $UserData = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', (int)$Params['user_parameters']['user_id'] );

        // Update password if neccesary
        if ($form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ))
        {
            $UserData->setPassword($form->Password);
        }
        
        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->surname = $form->Surname;
        
        erLhcoreClassUser::getSession()->update($UserData);
        
        erLhcoreClassModule::redirect('user/userlist');
        return ;
        
    }  else {
        $tpl->set('errArr',$Errors);
    }
}

if (isset($_POST['UpdateDepartaments_account']))
{    
   if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
   {
       erLhcoreClassUserDep::addUserDepartaments($_POST['UserDepartament'],$Params['user_parameters']['user_id']);
   }    

   $tpl->set('account_updated_departaments','done');
}

// If already set during account update
if (!isset($UserData))
{    
    $UserData = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', (int)$Params['user_parameters']['user_id'] );
}


$tpl->set('user',$UserData);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','System configuration')),

array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Users')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit').' - '.$UserData->name.' '.$UserData->surname)


)

?>