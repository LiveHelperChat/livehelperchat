<?php
$userDepartaments = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id);
$userDepartamentsRead = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id, true);
$userDepartamentsAutoExc = erLhcoreClassUserDep::getUserDepartamentsExcAutoassignIds($user->id);
$userDepartamentsParams = erLhcoreClassUserDep::getUserIndividualParams($user->id);

$userDepartamentsGroup = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id);
$userDepartamentsGroupRead = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id, true);
$userDepartamentsGroupAutoExc = erLhcoreClassModelDepartamentGroupUser::getUserGroupsExcAutoassignIds($user->id);
$userDepartamentsGroupParams = erLhcoreClassModelDepartamentGroupUser::getUserGroupsParams($user->id);

$departmentEditParams = [
    'self_edit' => true,
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

?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>#departments" method="post" enctype="multipart/form-data">
    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/departments_assignment.tpl.php'));?>
</form>
