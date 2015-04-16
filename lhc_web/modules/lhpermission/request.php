<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/request.tpl.php');

$permissionsRequested = (string)$Params['user_parameters']['permissions'];
$tpl->set('permission',$permissionsRequested);

if (ezcInputForm::hasPostData()) {
    $definition = array(
        'Permissions' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'UserID' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        )
    );
    
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect();
        exit;
    }
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    $userRecipient = false;
    
    if ( !$form->hasValidData( 'UserID' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/request','Please choose a user!');
    } else {
        $userRecipient = erLhcoreClassModelUser::fetch($form->UserID);
    }
    
    if ($userRecipient !== false && $userRecipient->rec_per_req == 0) {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/request','This user can not receive permision request!');
    }
    
    if ( $form->hasValidData( 'Permissions' ) && $form->Permissions != '' )
    {
        $permissionsCombinations = explode(',', $form->Permissions);
        $permissionsRequestedData = array();
        foreach ($permissionsCombinations as $combination) {
            list($module,$function) = explode('_f_', $combination);                        
            $moduleName = erLhcoreClassModules::getModuleName($module);            
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhpermission.getmodulename',array('module' => $module, 'name' => & $moduleName));                        
            $functionName = erLhcoreClassModules::getFunctionName($module,$function);
            $permissionsRequestedData[] = $moduleName.' - '.$functionName;
        }    
        
        $tpl->set('permission',$form->Permissions);        
    } else {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/request','Permissions were not provided');
    }
    
    if (empty($Errors)) {
        erLhcoreClassChatMail::sendMailRequestPermission($userRecipient, $currentUser->getUserData(),implode("\n", $permissionsRequestedData));
        $tpl->set('requested',true);
    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('users',erLhcoreClassModelUser::getUserList(array('limit' => 100,'filter' => array('rec_per_req' => 1))));
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>