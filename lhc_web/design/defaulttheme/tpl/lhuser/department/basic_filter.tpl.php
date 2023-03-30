<?php
$userDepartaments = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id);
$userDepartamentsRead = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id, true);
$userDepartamentsAutoExc = erLhcoreClassUserDep::getUserDepartamentsExcAutoassignIds($user->id);
$userDepartamentsParams = erLhcoreClassUserDep::getUserIndividualParams($user->id);

$userDepartamentsGroup = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id);
$userDepartamentsGroupRead = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id, true);
$userDepartamentsGroupAutoExc = erLhcoreClassModelDepartamentGroupUser::getUserGroupsExcAutoassignIds($user->id);
$userDepartamentsGroupParams = erLhcoreClassModelDepartamentGroupUser::getUserGroupsParams($user->id);

if ($selfedit === true) {

    $departmentEditParams = [
        'self_edit' => $selfedit,
        'all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','self_all_departments'),
        'individual' => [
            'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_assigned_departments'),
            'edit_all' => $editdepartaments,
            'all_dep'  => $userDepartamentsParams,
            'edit_personal' => false,
            'see_personal' => false,
        ],
        'groups' => [
            'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_assigned_departments_groups'),
            'edit_all' => $editdepartaments,
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

?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>#departments" method="post" enctype="multipart/form-data">
    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/departments_assignment.tpl.php'));?>
</form>
