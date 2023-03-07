<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/editdepartment.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);

if ($Params['user_parameters_unordered']['mode'] == 'group') {
    $dep = erLhcoreClassModelDepartamentGroup::fetch($Params['user_parameters']['dep_id']);
    $userDep = erLhcoreClassModelDepartamentGroupUser::findOne(['filter' => ['user_id' => $user->id, 'dep_group_id' => $dep->id]]);
} else {
    $dep = erLhcoreClassModelDepartament::fetch($Params['user_parameters']['dep_id']);
    $userDep = erLhcoreClassModelUserDep::findOne(['filter' => ['user_id' => $user->id, 'dep_id' => $dep->id, 'type' => 0]]);
}

if ($user instanceof erLhcoreClassModelUser && ($dep instanceof erLhcoreClassModelDepartament || $dep instanceof erLhcoreClassModelDepartamentGroup))
{
    if ($Params['user_parameters_unordered']['action'] == 'remove') {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $response = array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Invalid CSRF token'));
        }

        $userDep->removeThis();
        exit;
    }

    if (ezcInputForm::hasPostData()) {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $response = array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Invalid CSRF token'));
        }

        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $Errors = erLhcoreClassUserValidator::validateDepartmentAssignment($userDep);

        if (count($Errors) == 0) {
            $userDep->updateThis(['update' => ['exc_indv_autoasign','ro','read_only','chat_max_priority','chat_min_priority','assign_priority']]);

            if ($dep instanceof erLhcoreClassModelDepartamentGroup) {
                $userDep->afterSave();
            }

            $tpl->set('updated',true);
        }  else {
            $tpl->set('errors',$Errors);
        }

        $db->commit();
    }

    $tpl->set('user', $user);
    $tpl->set('dep', $dep);
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