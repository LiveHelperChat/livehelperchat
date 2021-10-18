<?php if (!(!isset($departmentEditParams['all_departments']) || $departmentEditParams['all_departments'] == false)) : ?>
<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','All departments')?></label><br>
<hr class="mt-1 mb-1">
<?php endif; ?>

<div class="row">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php $seeDepartments = false; if ($departmentEditParams['individual']['edit_all'] == true || $departmentEditParams['individual']['edit_personal'] == true || $departmentEditParams['individual']['read_all'] == true) : $seeDepartments = true;?>
    <div class="col-6">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Individual departments')?></h5>
            <div class="row" style="max-height: 600px;overflow: auto">
        	<?php foreach (erLhcoreClassModelDepartament::getList(array('limit' => false, 'filter' => array('archive' => 0))) as $departament) :
                $canEditDepartment = $departmentEditParams['individual']['edit_all'] || $departmentEditParams['individual']['edit_personal'] && in_array($departament->id,$departmentEditParams['individual']['id']);
                if ($canEditDepartment || $departmentEditParams['individual']['read_all'] == true) : ?>
                <div class="col-6">
                    <label class="font-weight-bold <?php if ($canEditDepartment != true) : ?>text-muted<?php endif; ?>" ng-non-bindable><?php echo htmlspecialchars($departament->name)?></label><br>
                    <label><span class="material-icons">mode_edit</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> onchange="$('#dep-ro-<?php echo $departament->id?>').prop('checked', false);" id="dep-full-<?php echo $departament->id?>" type="checkbox" name="UserDepartament[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartaments) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                    <label class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><span class="material-icons">edit_off</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> id="dep-ro-<?php echo $departament->id?>" type="checkbox" onchange="$('#dep-full-<?php echo $departament->id?>').prop('checked', false);" name="UserDepartamentRead[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsRead) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>
                </div>
        	    <?php endif; endforeach; ?>
            </div>

            <?php if ($departmentEditParams['individual']['edit_all'] == true) : ?>
                <?php $departments = erLhcoreClassModelDepartament::getList(array('limit' => false, 'filter' => array('archive' => 1))); ?>
                <?php if (!empty($departments)) : ?>
                <hr>
                <button type="button" onclick="$('#offline-departments').toggle();" class="btn btn-outline-secondary btn-sm mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show archived departments')?></button>
                <div class="row" id="offline-departments" style="max-height: 600px;overflow: auto; display: none">
                    <?php foreach ($departments as $departament) : ?>
                        <div class="col-6">
                            <label class="font-weight-bold" ng-non-bindable><?php echo htmlspecialchars($departament->name)?></label><br>
                            <label><input onchange="$('#dep-ro-<?php echo $departament->id?>').prop('checked', false);" id="dep-full-<?php echo $departament->id?>" type="checkbox" name="UserDepartament[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartaments) ? 'checked="checked"' : '';?> /><span class="material-icons">mode_edit</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                            <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><input id="dep-ro-<?php echo $departament->id?>" type="checkbox" onchange="$('#dep-full-<?php echo $departament->id?>').prop('checked', false);" name="UserDepartamentRead[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsRead) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($departmentEditParams['groups']['edit_all'] == true || $departmentEditParams['groups']['edit_personal'] == true || $departmentEditParams['groups']['read_all'] == true) : ?>
        <?php $departmentsGroups = erLhcoreClassModelDepartamentGroup::getList(array('sort' => 'name ASC', 'limit' => false)); ?>
        <?php if (!empty($departmentsGroups)) : ?>
        <div class="col-<?php if ($seeDepartments == true) : ?>6<?php else: ?>12<?php endif; ?>">
            <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Departments groups')?></h5>
            <div class="row" style="max-height: 600px;overflow: auto">
            <?php foreach ($departmentsGroups as $departamentGroup) :
                $canEditDepartment = $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal'] && in_array($departamentGroup->id,$departmentEditParams['groups']['id']);
                if ($canEditDepartment || $departmentEditParams['groups']['read_all'] == true) : ?>
                    <div class="col-<?php if ($seeDepartments == true) : ?>6<?php else: ?>3<?php endif; ?>">
                        <label class="font-weight-bold d-block" ng-non-bindable=""><?php echo htmlspecialchars($departamentGroup->name)?></label>
                        <label><span class="material-icons">mode_edit</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> onchange="$('#dep-group-ro-<?php echo $departamentGroup->id?>').prop('checked', false);" id="dep-group-full-<?php echo $departamentGroup->id?>" type="checkbox" name="UserDepartamentGroup[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroup) ? ' checked="checked" ' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                        <label><span class="material-icons text-muted">edit_off</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> onchange="$('#dep-group-full-<?php echo $departamentGroup->id?>').prop('checked', false);" id="dep-group-ro-<?php echo $departamentGroup->id?>" type="checkbox" name="UserDepartamentGroupRead[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroupRead) ? ' checked="checked" ' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label><br>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <?php endif;?>
    <?php endif;?>

</div>