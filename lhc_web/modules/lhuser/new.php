<?php

$tpl = new erLhcoreClassTemplate( 'lhuser/new.tpl.php');

$UserData = new erLhcoreClassModelUser();
$UserDepartaments = isset($_POST['UserDepartament']) ? $_POST['UserDepartament'] : array();

if (isset($_POST['Update_account']))
{    
   $definition = array(
        'Password' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        ),
        'Password1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
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
        'Username' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        ),
    );
  
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Wrong email address');
    } 
    
    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please enter name');
    }
    
    if ( !$form->hasValidData( 'Surname' ) || $form->Surname == '')
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please enter surname');
    } 
    
    if ( !$form->hasValidData( 'Username' ) || $form->Username == '')
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please enter username');
    }
    
    if ( $form->hasValidData( 'Username' ) && $form->Username != '' && erLhcoreClassModelUser::userExists($form->Username) === true )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User exists');
    }
    
    if ( !$form->hasValidData( 'Password' ) || !$form->hasValidData( 'Password1' ) || $form->Password == '' || $form->Password1 == '' || $form->Password != $form->Password1    ) // check for optional field
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Passwords mismatch');
    }
    
    if (count($Errors) == 0)
    {  
        $UserData->setPassword($form->Password);
        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->surname = $form->Surname;
        $UserData->username = $form->Username;
        
        erLhcoreClassUser::getSession()->save($UserData);
        
        if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
        {
           erLhcoreClassUserDep::addUserDepartaments($_POST['UserDepartament'],$UserData->id);
        } 
        
        erLhcoreClassModule::redirect('user/userlist');
        return ;
        
    }  else {
        
        if ( $form->hasValidData( 'Email' ) )
        {
            $UserData->email = $form->Email;
        }
        
        $UserData->name = $form->Name;
        $UserData->surname = $form->Surname;
        $UserData->username = $form->Username;
        
        $tpl->set('errArr',$Errors);
    }
}


$tpl->set('user',$UserData);
$tpl->set('userdepartaments',$UserDepartaments);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','System configuration')),

array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Users')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user'))

)

?>