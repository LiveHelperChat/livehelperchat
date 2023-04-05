<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/newdepartment.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);

if ($Params['user_parameters_unordered']['mode'] == 'group') {
    $userDep = new erLhcoreClassModelDepartamentGroupUser();
} else {
    $userDep = new erLhcoreClassModelUserDep();
}

$userDepAlias = new \LiveHelperChat\Models\Departments\UserDepAlias();
$userDepAlias->user_id = $user->id;

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

$depIds = [];
foreach (erLhcoreClassModelDepartament::getList(array('limit' => false)) as $departament) {
    $canEditDepartment = $departmentEditParams['individual']['edit_all'] || $departmentEditParams['individual']['edit_personal'] && in_array($departament->id, $departmentEditParams['individual']['id']);
    if ($canEditDepartment) {
        $depIds[] = $departament->id;
    }
}

$depGroupIds = [];
$departmentsGroups = erLhcoreClassModelDepartamentGroup::getList(array('sort' => 'name ASC', 'limit' => false));
foreach ($departmentsGroups as $departamentGroup) {
    $canEditDepartment = $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal'] && in_array($departamentGroup->id, $departmentEditParams['groups']['id']);
    if ($canEditDepartment) {
        $depGroupIds[] = $departamentGroup->id;
    }
}

if ($Params['user_parameters_unordered']['mode'] == 'group') {
    $present_dep_ids = erLhcoreClassModelDepartamentGroupUser::getCount(['filter' => ['user_id' => $user->id]],'count','dep_group_id','dep_group_id',false, true, true);
} else {
    $present_dep_ids = erLhcoreClassModelUserDep::getCount(['filter' => ['user_id' => $user->id]],'count','dep_id','dep_id',false, true, true);
}

$tpl->set('dep_group_ids',$depGroupIds);
$tpl->set('dep_ids',$depIds);
$tpl->set('present_dep_ids',$present_dep_ids);

// Verify permissions for edit
if ($Params['user_parameters_unordered']['editor'] == 'self') {
    $tpl->set('editor','self');
}

$userDep->user_id = $user->id;

if ($user instanceof erLhcoreClassModelUser) {
    if (ezcInputForm::hasPostData()) {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $response = array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject', 'Invalid CSRF token'));
        }

        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $Errors = erLhcoreClassUserValidator::validateDepartmentAssignment($userDep);

        $definition = array(
            'dep_ids' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 1], FILTER_REQUIRE_ARRAY
            )
        );

        $form = new ezcInputForm(INPUT_POST, $definition);

        $Errors = [];

        if ($form->hasValidData('dep_ids') && !empty($form->dep_ids)) {
            if ($Params['user_parameters_unordered']['mode'] == 'group') {
                // $userDep->dep_group_id = $form->dep_ids;
                $userDep->dep_group_ids = $form->dep_ids;
            } else {
                // $userDep->dep_id = $form->dep_ids;
                $userDep->dep_ids = $form->dep_ids;
            }
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment', 'Please choose a department!');
        }

        if ($Params['user_parameters_unordered']['mode'] == 'group') {
            if (empty($Errors) && erLhcoreClassModelDepartamentGroupUser::getCount(['filterin' => ['dep_group_id' => $userDep->dep_group_ids],'filter' => ['user_id' => $user->id]]) > 0) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment', 'This department department already have been added!');
            }
        } else {
            if (empty($Errors) && erLhcoreClassModelUserDep::getCount(['filterin' => ['dep_id' => $userDep->dep_ids],'filter' => ['user_id' => $user->id, 'type' => 0]]) > 0) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment', 'This department already have been added!');
            }
        }

        if (count($Errors) == 0) {
            if ($Params['user_parameters_unordered']['mode'] != 'group') {
                //$userDepAlias->dep_id = $userDep->dep_id;
                $userDepAlias->dep_ids = $userDep->dep_ids;
            } else {
                //$userDepAlias->dep_group_id = $userDep->dep_group_id;
                $userDepAlias->dep_group_ids = $userDep->dep_group_ids;
            }
        }

        if (count($Errors) == 0) {

            if ($Params['user_parameters_unordered']['mode'] == 'group') {
                foreach ($userDep->dep_group_ids as $dep_group_id) {
                    $userDep->id = null;
                    $userDep->dep_group_id = $dep_group_id;
                    $userDep->saveThis();
                }
            } else {
                foreach ($userDep->dep_ids as $dep_id) {
                    $userDep->id = null;
                    $userDep->max_chats = $user->max_active_chats;
                    $userDep->hide_online = $user->hide_online;
                    $userDep->exclude_autoasign = $user->exclude_autoasign;
                    $userDep->active_chats = erLhcoreClassChat::getCount(array('filter' => array('user_id' => $user->id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)));
                    $userDep->always_on = $user->always_on;
                    $userDep->dep_id = $dep_id;
                    $userDep->saveThis();
                }
            }

            $user->departments_ids = implode(',', erLhcoreClassModelUserDep::getCount(['filter' => ['user_id' => $user->id]],'count','dep_id','dep_id',false, true, true) );
            $user->updateThis(['update' => ['departments_ids']]);
            $firstAlias = null;

            if ($Params['user_parameters_unordered']['mode'] == 'group') {
                foreach ($userDepAlias->dep_group_ids as $dep_group_id) {
                    $userDepAlias->id = null;
                    $userDepAlias->dep_group_id = $dep_group_id;
                    erLhcoreClassUserValidator::validateAliasDepartment($userDepAlias);
                    if ($firstAlias === null) {
                        $firstAlias = clone $userDepAlias;
                    } else {
                        if ($firstAlias->has_photo) {
                            $newPath = explode('/',trim($firstAlias->filepath,'/'));
                            $newPath[5] = $userDepAlias->id;
                            $newPathString = implode('/', $newPath) . '/';
                            erLhcoreClassFileUpload::mkdirRecursive( $newPathString );
                            $userDepAlias->filepath = $newPathString;
                            copy($firstAlias->file_path_server,$userDepAlias->filepath . $userDepAlias->filename);
                            chmod($userDepAlias->filepath . $userDepAlias->filename, 0644);
                            $userDepAlias->updateThis();
                        }
                    }
                }
            } else {
                foreach ($userDepAlias->dep_ids as $dep_id) {
                    $userDepAlias->id = null;
                    $userDepAlias->dep_id = $dep_id;
                    erLhcoreClassUserValidator::validateAliasDepartment($userDepAlias);
                    if ($firstAlias === null) {
                        $firstAlias = clone $userDepAlias;
                    } else {
                        if ($firstAlias->has_photo) {
                            $newPath = explode('/',trim($firstAlias->filepath,'/'));
                            $newPath[5] = $userDepAlias->id;
                            $newPathString = implode('/', $newPath) . '/';
                            erLhcoreClassFileUpload::mkdirRecursive( $newPathString );
                            $userDepAlias->filepath = $newPathString;
                            copy($firstAlias->file_path_server,$userDepAlias->filepath . $userDepAlias->filename);
                            chmod($userDepAlias->filepath . $userDepAlias->filename, 0644);
                            $userDepAlias->updateThis();
                        }
                    }
                }
            }

            $tpl->set('updated', true);
            if ($Params['user_parameters_unordered']['mode'] != 'group') {
                $userDep->dep_id = 0;
            } else {
                $userDep->dep_group_id = 0;
            }

        } else {
            $tpl->set('errors', $Errors);
        }

        $db->commit();
    }

    $tpl->set('user', $user);
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