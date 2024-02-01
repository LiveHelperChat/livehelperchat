<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/editdepartment.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);
$canContinue = true;

if ($Params['user_parameters_unordered']['editor'] == 'self' && $Params['user_parameters']['user_id'] != $currentUser->getUserID()) {
    $canContinue = false;
}

if ($Params['user_parameters_unordered']['mode'] == 'group') {
    $dep = erLhcoreClassModelDepartamentGroup::fetch($Params['user_parameters']['dep_id']);
    $userDep = erLhcoreClassModelDepartamentGroupUser::findOne(['filter' => ['user_id' => $user->id, 'dep_group_id' => $dep->id]]);
    $userDepAlias = \LiveHelperChat\Models\Departments\UserDepAlias::findOne(['filter' => ['user_id' => $user->id, 'dep_group_id' => $dep->id]]);
} else {
    $dep = erLhcoreClassModelDepartament::fetch($Params['user_parameters']['dep_id']);
    $userDep = erLhcoreClassModelUserDep::findOne(['filter' => ['user_id' => $user->id, 'dep_id' => $dep->id, 'type' => 0]]);
    $userDepAlias = \LiveHelperChat\Models\Departments\UserDepAlias::findOne(['filter' => ['dep_id' => $dep->id, 'user_id' => $user->id]]);
}

if (!($userDepAlias instanceof \LiveHelperChat\Models\Departments\UserDepAlias)) {
    $userDepAlias = new \LiveHelperChat\Models\Departments\UserDepAlias();
    $userDepAlias->user_id = $user->id;
    if ($Params['user_parameters_unordered']['mode'] == 'group') {
        $userDepAlias->dep_group_id = $dep->id;
    } else {
        $userDepAlias->dep_id = $dep->id;
    }
}

$userDepartaments = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id);
$userDepartamentsRead = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id, true);
$userDepartamentsAutoExc = erLhcoreClassUserDep::getUserDepartamentsExcAutoassignIds($user->id);
$userDepartamentsParams = erLhcoreClassUserDep::getUserIndividualParams($user->id);

$userDepartamentsGroup = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id);
$userDepartamentsGroupRead = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id, true);
$userDepartamentsGroupAutoExc = erLhcoreClassModelDepartamentGroupUser::getUserGroupsExcAutoassignIds($user->id);
$userDepartamentsGroupParams = erLhcoreClassModelDepartamentGroupUser::getUserGroupsParams($user->id);

if ($Params['user_parameters_unordered']['editor'] == 'self') {

    $departmentEditParams = [
        'self_edit' => true,
        'all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','self_all_departments'),
        'individual' => [
            'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_assigned_departments'),
            'edit_all' => $currentUser->hasAccessTo('lhuser','editdepartaments'),
            'all_dep'  => $userDepartamentsParams,
            'edit_personal' => false,
            'see_personal' => false,
        ],
        'groups' => [
            'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_assigned_departments_groups'),
            'edit_all' => $currentUser->hasAccessTo('lhuser','editdepartaments'),
            'all_group' => $userDepartamentsGroupParams,
            'edit_personal' => false,
            'see_personal' => false,
        ]
    ];

    if ($departmentEditParams['individual']['edit_all'] == false) {
        $departmentEditParams['individual']['id'] = array_merge(
            erLhcoreClassUserDep::getUserDepartamentsIndividual(
                $user->id
            ),
            erLhcoreClassUserDep::getUserDepartamentsIndividual(
                $user->id,
                true
            )
        );
    }

    if ($departmentEditParams['groups']['edit_all'] == false) {
        $departmentEditParams['groups']['id'] = array_merge(
            erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                $user->id
            ),
            erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                $user->id,
                true
            )
        );
    }

} else {
    $departmentEditParams = [
        'self_edit' => false,
        'all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','edit_all_departments'),
        'individual' => [
            'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments') || erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual'),
            'edit_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual'),
            'edit_personal' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_individual'),
            'all_dep'  => $userDepartamentsParams,
        ],
        'groups' => [
            'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments_groups') || erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group'),
            'edit_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group'),
            'edit_personal' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_group'),
            'all_group' => $userDepartamentsGroupParams
        ]
    ];

    if ($departmentEditParams['individual']['edit_all'] == false) {
        $departmentEditParams['individual']['id'] = array_merge(
            erLhcoreClassUserDep::getUserDepartamentsIndividual(
                erLhcoreClassUser::instance()->getUserID()
            ),
            erLhcoreClassUserDep::getUserDepartamentsIndividual(
                erLhcoreClassUser::instance()->getUserID(),
                true
            )
        );
    }

    if ($departmentEditParams['groups']['edit_all'] == false) {
        $departmentEditParams['groups']['id'] = array_merge(
            erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                erLhcoreClassUser::instance()->getUserID()
            ),
            erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                erLhcoreClassUser::instance()->getUserID(),
                true
            )
        );
    }
}

// Verify permissions for edit
if ($Params['user_parameters_unordered']['editor'] == 'self') {
    $tpl->set('editor','self');
}

if ($Params['user_parameters_unordered']['mode'] == 'group') {
    $canContinue = $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal'] && in_array($dep->id,$departmentEditParams['groups']['id']);
} else {
    $canContinue = $departmentEditParams['individual']['edit_all'] || $departmentEditParams['individual']['edit_personal'] && in_array($dep->id, $departmentEditParams['individual']['id']);
}

if ($canContinue === true && $user instanceof erLhcoreClassModelUser && ($dep instanceof erLhcoreClassModelDepartament || $dep instanceof erLhcoreClassModelDepartamentGroup))
{
    if ($Params['user_parameters_unordered']['action'] == 'remove') {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $response = array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Invalid CSRF token'));
        }

        $userDep->removeThis();
        $userDepAlias->id > 0 && $userDepAlias->removeThis();

        $prevDepIds = $user->departments_ids;
        $user->departments_ids = implode(',', erLhcoreClassModelUserDep::getCount(['filter' => ['user_id' => $user->id]],'count','dep_id','dep_id',false, true, true) );
        $user->updateThis(['update' => ['departments_ids']]);

        erLhcoreClassLog::logObjectChange(array(
            'object' => $user,
            'msg' => array(
                'action' => 'account_data_dep_rem',
                'class' => get_class($userDep),
                'object_id' => (get_class($userDep) == 'erLhcoreClassModelUserDep' ? $userDep->dep_id : $userDep->dep_group_id),
                'prev' => $prevDepIds,
                'new' => $user->departments_ids,
                'user_id' => $currentUser->getUserID()
            )
        ));

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
            $Errors = erLhcoreClassUserValidator::validateAliasDepartment($userDepAlias);
        }

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
    $tpl->set('userDepAlias', $userDepAlias);

    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/modal_error.tpl.php');
    $tpl->set( 'errors', ['No permission to edit!']);
    echo $tpl->fetch();
    exit;
}

exit;

?>