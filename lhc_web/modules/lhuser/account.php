<?php

$tpl = new erLhcoreClassTemplate( 'lhuser/account.tpl.php' );

if (isset($_POST['Update_account']))
{    
   $definition = array(
        'Password' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'Password1' => new ezcInputFormDefinitionElement(
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
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Wrong email address');
    } 
    
    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Please enter name');
    }
    
    if ( !$form->hasValidData( 'Surname' ) || $form->Surname == '')
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Please enter surname');
    }
    
    if ( $form->hasInputField( 'Password' ) && (!$form->hasInputField( 'Password1' ) || $form->Password != $form->Password1  ) ) // check for optional field
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Passwords mismatch');
    }
    
    if (count($Errors) == 0)
    {        
        $currentUser = erLhcoreClassUser::instance();
        $UserData = $currentUser->getUserData();

        // Update password if neccesary
        if ($form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ))
        {
            $UserData->setPassword($form->Password);
        }
        
        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->surname = $form->Surname;
        
        erLhcoreClassUser::getSession()->update($UserData);
        $tpl->set('account_updated','done');
        
    }  else {
        $tpl->set('errArr',$Errors);
    }
}

$currentUser = erLhcoreClassUser::instance();

$allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');

if ($allowEditDepartaments && isset($_POST['UpdateDepartaments_account']))
{    
   if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
   {
       erLhcoreClassUserDep::addUserDepartaments($_POST['UserDepartament']);
   }    

   $tpl->set('account_updated_departaments','done');
}

// If already set during account update
if (!isset($UserData))
{    
    $UserData = $currentUser->getUserData();
}

$tpl->set('editdepartaments',$allowEditDepartaments);
$tpl->set('alldepartaments',$currentUser->hasAccessTo('lhdepartament','alldepartaments'));


$tpl->set('user',$UserData);

$Result['content'] = $tpl->fetch();


?>