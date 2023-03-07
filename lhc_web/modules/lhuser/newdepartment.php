<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/newdepartment.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);

if ($Params['user_parameters_unordered']['mode'] == 'group') {
    $userDep = new erLhcoreClassModelDepartamentGroupUser();
} else {
    $userDep = new erLhcoreClassModelUserDep();
}

$userDep->user_id = $user->id;

if ($user instanceof erLhcoreClassModelUser)
{
    if (ezcInputForm::hasPostData()) {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $response = array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Invalid CSRF token'));
        }

        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $Errors = erLhcoreClassUserValidator::validateDepartmentAssignment($userDep);

        $definition = array(
            'dep_ids' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',['min_range' => 1]
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );

        $Errors = [];

        if ( $form->hasValidData( 'dep_ids' ) ) {
            if ($Params['user_parameters_unordered']['mode'] == 'group') {
                $userDep->dep_group_id = $form->dep_ids;
            } else {
                $userDep->dep_id = $form->dep_ids;
            }
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','Please choose a department!');
        }

        if ($Params['user_parameters_unordered']['mode'] == 'group') {
            if (empty($Errors) && erLhcoreClassModelDepartamentGroupUser::getCount(['filter' => ['user_id' => $user->id, 'dep_group_id' => $userDep->dep_group_id]]) > 0) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','This department department already have been added!');
            }
        } else {
            if (empty($Errors) && erLhcoreClassModelUserDep::getCount(['filter' => ['user_id' => $user->id, 'dep_id' => $userDep->dep_id, 'type' => 0]]) > 0) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','This department already have been added!');
            }
        }

        if (count($Errors) == 0) {
            // @todo add dynamic attributes like active chats etc
            $userDep->saveThis();
            $tpl->set('updated',true);
        }  else {
            $tpl->set('errors',$Errors);
        }

        $db->commit();
    }

    $tpl->set('user', $user);
    $tpl->set('userDep', $userDep);
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    $tpl->set('auto_close_dialog',true);
    $tpl->set('chat_id',(int)$Params['user_parameters']['dep_id']);
    echo $tpl->fetch();
    exit;
}

exit;

?>