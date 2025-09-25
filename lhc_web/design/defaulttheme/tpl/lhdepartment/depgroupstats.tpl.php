<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2 ';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Departments of group') . ': ' . htmlspecialchars($department_group->name);
$modalBodyClass = 'p-1';
$modalSize = 'xl';

// Get departments from current group
$currentGroupDepartments = erLhcoreClassModelDepartamentGroupMember::getList(array(
    'limit' => false,
    'filter' => array('dep_group_id' => $department_group->id)
));

// Get all departments data
$departmentIds = array();
foreach ($currentGroupDepartments as $groupMember) {
    $departmentIds[] = $groupMember->dep_id;
}

$departments = array();
$departmentsInOtherGroups = array();

if (!empty($departmentIds)) {
    // Get department details
    $departments = erLhcoreClassModelDepartament::getList(array(
        'limit' => false,
        'filter' => array('id' => $departmentIds),
        'sort' => 'name ASC'
    ));
    
    // Check which departments are also in other groups
    $otherGroupMemberships = erLhcoreClassModelDepartamentGroupMember::getList(array(
        'limit' => false,
        'filter' => array('dep_id' => $departmentIds),
        'filternot' => array('dep_group_id' => $department_group->id)
    ));
    
    // Group by department ID for easier lookup
    foreach ($otherGroupMemberships as $membership) {
        if (!isset($departmentsInOtherGroups[$membership->dep_id])) {
            $departmentsInOtherGroups[$membership->dep_id] = array();
        }
        $departmentsInOtherGroups[$membership->dep_id][] = $membership->dep_group_id;
    }
    
    // Get other group names for display
    if (!empty($departmentsInOtherGroups)) {
        $otherGroupIds = array();
        foreach ($departmentsInOtherGroups as $depId => $groupIds) {
            $otherGroupIds = array_merge($otherGroupIds, $groupIds);
        }
        $otherGroupIds = array_unique($otherGroupIds);
        
        $otherGroups = erLhcoreClassModelDepartamentGroup::getList(array(
            'limit' => false,
            'filter' => array('id' => $otherGroupIds),
            'sort' => 'name ASC'
        ));
        
        $otherGroupsById = array();
        foreach ($otherGroups as $group) {
            $otherGroupsById[$group->id] = $group;
        }
    }
}
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<div class="modal-body" ng-non-bindable>
    <div class="p-2">       
        <?php if (empty($departments)) : ?>
            <div class="alert alert-info">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','No departments assigned to this group')?>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Department')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Status')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Also assigned to other groups')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $department) : ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($department->name)?></strong>
                                <?php if ($department->disabled == 1) : ?>
                                    <span class="badge bg-secondary ms-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Disabled')?></span>
                                <?php endif; ?>
                                <?php if ($department->hidden == 1) : ?>
                                    <span class="badge bg-warning ms-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hidden')?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($department->disabled == 0 && $department->hidden == 0) : ?>
                                    <span class="badge bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Active')?></span>
                                <?php else : ?>
                                    <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inactive')?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($departmentsInOtherGroups[$department->id])) : ?>
                                    <span class="badge bg-warning me-1">
                                        <i class="material-icons fs12">warning</i>
                                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Shared')?> 
                                    </span>
                                    <div class="mt-1">
                                        <?php foreach ($departmentsInOtherGroups[$department->id] as $otherGroupId) : ?>
                                            <?php if (isset($otherGroupsById[$otherGroupId])) : ?>
                                                <small class="text-muted d-block">
                                                    • <?php echo htmlspecialchars($otherGroupsById[$otherGroupId]->name)?>
                                                </small>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <span class="badge bg-success">
                                        <i class="material-icons fs12">check</i>
                                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Exclusive')?> 
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php 
            $sharedCount = count($departmentsInOtherGroups);
            $exclusiveCount = count($departments) - $sharedCount;
            ?>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success"><?php echo $exclusiveCount?></h5>
                            <p class="card-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Exclusive departments')?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning"><?php echo $sharedCount?></h5>
                            <p class="card-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Shared departments')?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($sharedCount > 0) : ?>
                <div class="alert alert-warning mt-3">
                    <i class="material-icons">info</i>
                    <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Note')?></strong>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Some departments in this group are also assigned to other department groups. This may affect chat routing and operator assignments.')?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>

