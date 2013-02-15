<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/account.tpl.php' );

$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData();
        
if (isset($_POST['Update']))
{    
   $definition = array(
        'Password' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Password1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),       
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
        ),
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'Surname' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'Username' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )        
    );
  
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Username' ) ) {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Please enter username!');
    }  elseif ( $form->hasValidData( 'Username' ) && $form->Username != $UserData->username && !erLhcoreClassModelUser::userExists($form->Username) ) {
    	$UserData->username = $form->Username;
    } elseif ( $form->hasValidData( 'Username' ) && $form->Username != $UserData->username) {
    	$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User exists!');
    }
    
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
        // Update password if neccesary
        if ( $form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ) && $form->Password != '' )
        {
            $UserData->setPassword($form->Password);
        }
        
        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->surname = $form->Surname;
        
        erLhcoreClassUser::getSession()->update($UserData);
        $tpl->set('account_updated','done');
        
    }  else {
        $tpl->set('errors',$Errors);
    }
}

$currentUser = erLhcoreClassUser::instance();

$allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');

if ($allowEditDepartaments && isset($_POST['UpdateDepartaments_account']))
{    
  
   $globalDepartament = array();
   if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
       $UserData->all_departments = 1;
       $globalDepartament[] = 0;
   } else {
       $UserData->all_departments = 0;
   }    

   erLhcoreClassUser::getSession()->update($UserData);

   if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
   {
       $globalDepartament = array_merge($_POST['UserDepartament'],$globalDepartament);       
   }

   if (count($globalDepartament) > 0) {
       erLhcoreClassUserDep::addUserDepartaments($globalDepartament,false,$UserData);
   } else {
       erLhcoreClassUserDep::addUserDepartaments(array(),false,$UserData);
   }
   
   $tpl->set('account_updated_departaments','done');
}

// If already set during account update
if (!isset($UserData))
{    
    $UserData = $currentUser->getUserData();
}

$tpl->set('editdepartaments',$allowEditDepartaments);

$tpl->set('user',$UserData);

$Result['content'] = $tpl->fetch();


?>