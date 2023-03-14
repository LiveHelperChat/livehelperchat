<?php if (!(!isset($departmentEditParams['all_departments']) || $departmentEditParams['all_departments'] == false)) : ?>

<div class="pb-2">
    <label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','All departments')?></label>
</div>

<input type="submit" class="btn btn-sm btn-secondary" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />
<hr class="mt-3 mb-3">
<?php endif; ?>

<div class="row">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php $seeDepartments = false; if ($departmentEditParams['individual']['edit_all'] == true || $departmentEditParams['individual']['edit_personal'] == true || $departmentEditParams['individual']['read_all'] == true) : $seeDepartments = true;?>
    <div class="col-6">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Individual departments')?>

            <a class="btn btn-success btn-xs action-image text-white" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/newdepartment/<?php echo $user->id?><?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})"><span class="material-icons fs11 me-0">add</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','New')?></a>

        </h5>
            <div class="row" style="max-height: 600px;overflow: auto">
        	<?php foreach (erLhcoreClassModelDepartament::getList(array('limit' => false)) as $departament) :
                $canEditDepartment = $departmentEditParams['individual']['edit_all'] || $departmentEditParams['individual']['edit_personal'] && in_array($departament->id,$departmentEditParams['individual']['id']);
                if ($canEditDepartment || $departmentEditParams['individual']['read_all'] == true) : ?>

                <?php if (in_array($departament->id,$userDepartamentsRead) || in_array($departament->id,$userDepartaments)) : ?>
                <div class="col-6" id="dep-indv-id-<?php echo $departament->id?>">
                        <label class="fw-bold <?php if ($canEditDepartment != true) : ?>text-muted<?php endif; ?>" ng-non-bindable>

                            <span <?php if ($canEditDepartment == true) : ?>onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/user/editdepartment/<?php echo $user->id?>/<?php echo $departament->id?><?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Edit')?>" <?php endif;?> class="material-icons <?php if ($canEditDepartment != true) : ?>text-muted<?php else : ?>action-image<?php endif; ?>"><?php if ($canEditDepartment != true) : ?>edit_off<?php else : ?>edit<?php endif; ?></span>
                            <?php echo htmlspecialchars($departament->name)?>

                            <?php if (in_array($departament->id,$userDepartamentsRead)) : ?><span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>">visibility</span><?php endif; ?>

                            <?php if (in_array($departament->id,$userDepartamentsAutoExc)) : ?><span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assignment workflow')?>">group_off</span><?php endif; ?>

                            <span class="badge bg-secondary<?php if (!isset($departmentEditParams['individual']['all_dep'][$departament->id]['assign_priority']) || $departmentEditParams['individual']['all_dep'][$departament->id]['assign_priority'] == 0) : ?> bg-light text-muted<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assignment priority, optional, default - 0')?>">
                                <span class="material-icons">transfer_within_a_station</span><?php if (isset($departmentEditParams['individual']['all_dep'][$departament->id])) : ?><?php echo $departmentEditParams['individual']['all_dep'][$departament->id]['assign_priority']?><?php else : ?>0<?php endif;?>
                            </span>

                            <span class="badge bg-secondary<?php if (!isset($departmentEditParams['individual']['all_dep'][$departament->id]['chat_min_priority']) || $departmentEditParams['individual']['all_dep'][$departament->id]['chat_min_priority'] == 0) : ?> bg-light text-muted<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Min chat priority for chat being assigned by my assign priority')?>">
                                <span class="material-icons">remove</span><?php if (isset($departmentEditParams['individual']['all_dep'][$departament->id])) : ?><?php echo $departmentEditParams['individual']['all_dep'][$departament->id]['chat_min_priority']?><?php else : ?>0<?php endif;?>
                            </span>

                            <span class="badge bg-secondary<?php if (!isset($departmentEditParams['individual']['all_dep'][$departament->id]['chat_max_priority']) || $departmentEditParams['individual']['all_dep'][$departament->id]['chat_max_priority'] == 0) : ?> bg-light text-muted<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Max chat priority for chat being assigned by my assign priority')?>">
                                <span class="material-icons">add</span><?php if (isset($departmentEditParams['individual']['all_dep'][$departament->id])) : ?><?php echo $departmentEditParams['individual']['all_dep'][$departament->id]['chat_max_priority']?><?php else : ?>0<?php endif;?>
                            </span>



                            <?php if ($canEditDepartment == true) : ?><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Remove')?>" onclick="$.postJSON('/user/editdepartment/<?php echo $user->id?>/<?php echo $departament->id?>/(action)/remove<?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>');$('#dep-indv-id-<?php echo $departament->id?>').fadeOut()" class="material-icons action-image text-danger">delete</span><?php endif; ?>

                        </label>
                </div>
                <?php endif; ?>

                <?php /*
                <div class="col-6 d-none">

                    <label class="fw-bold <?php if ($canEditDepartment != true) : ?>text-muted<?php endif; ?>" ng-non-bindable><?php echo htmlspecialchars($departament->name)?></label><br>
                    <label><span class="material-icons">mode_edit</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> onchange="$('#dep-exclude-<?php echo $departament->id?>').toggle();$('#dep-ro-<?php echo $departament->id?>').prop('checked', false);" id="dep-full-<?php echo $departament->id?>" type="checkbox" name="UserDepartament[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartaments) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                    <div class="d-block">
                        <div id="dep-exclude-<?php echo $departament->id?>" style="display: <?php echo in_array($departament->id,$userDepartaments) ? 'block' : 'none';?>" class="ms-4">
                            <label><span class="material-icons">assignment_ind</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> type="checkbox" name="UserDepartamentAutoExc[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsAutoExc) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assignment workflow')?></label>
                            <div>
                                <label class="d-block fs13 text-muted pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assignment priority, optional, default - 0')?></label>
                                <input type="text" class="form-control form-control-sm" name="UserDepartamentAssignPriority[<?php echo $departament->id?>]" value="<?php if (isset($departmentEditParams['individual']['all_dep'][$departament->id])) : ?><?php echo $departmentEditParams['individual']['all_dep'][$departament->id]['assign_priority']?><?php else : ?>0<?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Agents with higher assignment priority will be assigned first to chat')?>" />
                                <div class="row">
                                    <div class="col-12 fs13 text-muted">
                                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Min and Max chat priority for chat being assigned by my assign priority')?></label>
                                    </div>
                                    <div class="col-6">
                                        <input name="UserDepartamentAssignMinPriority[<?php echo $departament->id?>]" value="<?php if (isset($departmentEditParams['individual']['all_dep'][$departament->id])) : ?><?php echo $departmentEditParams['individual']['all_dep'][$departament->id]['chat_min_priority']?><?php else : ?>0<?php endif;?>" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-6">
                                        <input name="UserDepartamentAssignMaxPriority[<?php echo $departament->id?>]" value="<?php if (isset($departmentEditParams['individual']['all_dep'][$departament->id])) : ?><?php echo $departmentEditParams['individual']['all_dep'][$departament->id]['chat_max_priority']?><?php else : ?>0<?php endif;?>" type="text" class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <label class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><span class="material-icons">edit_off</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> id="dep-ro-<?php echo $departament->id?>" type="checkbox" onchange="$('#dep-full-<?php echo $departament->id?>').prop('checked', false);" name="UserDepartamentRead[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsRead) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>


                </div>*/ ?>



        	    <?php endif; endforeach; ?>
            </div>

            <?php /*if ($departmentEditParams['individual']['edit_all'] == true) : ?>
                <?php $departments = erLhcoreClassModelDepartament::getList(array('limit' => false, 'filter' => array('archive' => 1))); ?>
                <?php if (!empty($departments)) : ?>
                <hr>
                <button type="button" onclick="$('#offline-departments').toggle();" class="btn btn-outline-secondary btn-sm mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show archived departments')?></button>
                <div class="row" id="offline-departments" style="max-height: 600px;overflow: auto; display: none">
                    <?php foreach ($departments as $departament) : ?>
                        <div class="col-6">
                            <label class="fw-bold" ng-non-bindable><?php echo htmlspecialchars($departament->name)?></label><br>
                            <label><input onchange="$('#dep-ro-<?php echo $departament->id?>').prop('checked', false);" id="dep-full-<?php echo $departament->id?>" type="checkbox" name="UserDepartament[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartaments) ? 'checked="checked"' : '';?> /><span class="material-icons">mode_edit</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                            <label class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><input id="dep-ro-<?php echo $departament->id?>" type="checkbox" onchange="$('#dep-full-<?php echo $departament->id?>').prop('checked', false);" name="UserDepartamentRead[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsRead) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endif;*/ ?>
    </div>
    <?php endif; ?>

    <?php if ($departmentEditParams['groups']['edit_all'] == true || $departmentEditParams['groups']['edit_personal'] == true || $departmentEditParams['groups']['read_all'] == true) : ?>
        <?php $departmentsGroups = erLhcoreClassModelDepartamentGroup::getList(array('sort' => 'name ASC', 'limit' => false)); ?>
        <?php if (!empty($departmentsGroups)) : ?>
        <div class="col-<?php if ($seeDepartments == true) : ?>6<?php else: ?>12<?php endif; ?>">

            <?php if ($departmentEditParams['groups']['read_all'] == false) : ?>
                <div class="alert alert-warning" role="alert">
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','You have permission to edit departments group, but do not have permission to read them. Missing one of these permissions')?>. <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Changes will not be saved!')?></b></p>
                    <ul>
                        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','For account page')?> - <span class="badge bg-info">see_assigned_departments_groups</span></li>
                        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','For user edit page')?> - <span class="badge bg-info">see_user_assigned_departments_groups</span></li>
                    </ul>
                </div>
            <?php endif; ?>

            <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Departments groups')?> <a class="btn btn-success btn-xs action-image text-white" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/newdepartment/<?php echo $user->id?>/(mode)/group<?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})"><span class="material-icons fs11 me-0">add</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','New')?></a></h5>


            <div class="row">
            <?php foreach ($departmentsGroups as $departamentGroup) :
                $canEditDepartment = $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal'] && in_array($departamentGroup->id,$departmentEditParams['groups']['id']);
                if ($canEditDepartment || $departmentEditParams['groups']['read_all'] == true) : ?>
                <?php if (in_array($departamentGroup->id,$userDepartamentsGroup) ||  in_array($departamentGroup->id,$userDepartamentsGroupRead)) : ?>

            <div class="col-6" id="depgroup-indv-id-<?php echo $departamentGroup->id?>">
                <label class="fw-bold <?php if ($canEditDepartment != true) : ?>text-muted<?php endif; ?>" ng-non-bindable>

                    <span <?php if ($canEditDepartment == true) : ?>onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/user/editdepartment/<?php echo $user->id?>/<?php echo $departamentGroup->id?>/(mode)/group<?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Edit')?>" <?php endif;?> class="material-icons <?php if ($canEditDepartment != true) : ?>text-muted<?php else : ?>action-image<?php endif; ?>"><?php if ($canEditDepartment != true) : ?>edit_off<?php else : ?>edit<?php endif; ?></span>

                    <?php echo htmlspecialchars($departamentGroup->name)?>

                    <?php if (in_array($departamentGroup->id,$userDepartamentsGroupRead)) : ?><span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>">visibility</span><?php endif; ?>

                    <?php if (in_array($departamentGroup->id,$userDepartamentsGroupAutoExc)) : ?><span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assignment workflow')?>">group_off</span><?php endif; ?>

                    <span class="badge bg-secondary<?php if (!isset($departmentEditParams['groups']['all_group'][$departamentGroup->id]['assign_priority']) || $departmentEditParams['groups']['all_group'][$departamentGroup->id]['assign_priority'] == 0) : ?> bg-light text-muted<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assignment priority, optional, default - 0')?>">
                        <span class="material-icons">transfer_within_a_station</span><?php if (isset($departmentEditParams['groups']['all_group'][$departamentGroup->id]['assign_priority'])) : ?><?php echo $departmentEditParams['groups']['all_group'][$departamentGroup->id]['assign_priority']?><?php else : ?>0<?php endif;?>
                    </span>

                    <span class="badge bg-secondary<?php if (!isset($departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_min_priority']) || $departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_min_priority'] == 0) : ?> bg-light text-muted<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Min chat priority for chat being assigned by my assign priority')?>">
                        <span class="material-icons">remove</span><?php if (isset($departmentEditParams['groups']['all_group'][$departamentGroup->id])) : ?><?php echo $departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_min_priority']?><?php else : ?>0<?php endif;?>
                    </span>

                    <span class="badge bg-secondary<?php if (!isset($departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_max_priority']) || $departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_max_priority'] == 0) : ?> bg-light text-muted<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Max chat priority for chat being assigned by my assign priority')?>">
                        <span class="material-icons">add</span><?php if (isset($departmentEditParams['groups']['all_group'][$departamentGroup->id])) : ?><?php echo $departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_max_priority']?><?php else : ?>0<?php endif;?>
                    </span>

                    <?php if ($canEditDepartment == true) : ?><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Remove')?>" onclick="$.postJSON('/user/editdepartment/<?php echo $user->id?>/<?php echo $departamentGroup->id?>/(action)/remove/(mode)/group<?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>');$('#depgroup-indv-id-<?php echo $departamentGroup->id?>').fadeOut()" class="material-icons action-image text-danger">delete</span><?php endif; ?>

                </label>
            </div>

                <?php endif; endif; endforeach;?>
            </div>

<?php /*
            <div class="row d-none" style="max-height: 600px;overflow: auto">
            <?php foreach ($departmentsGroups as $departamentGroup) :
                $canEditDepartment = $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal'] && in_array($departamentGroup->id,$departmentEditParams['groups']['id']);
                if ($canEditDepartment || $departmentEditParams['groups']['read_all'] == true) : ?>
                    <div class="col-<?php if ($seeDepartments == true) : ?>6<?php else: ?>3<?php endif; ?>">
                        <label class="fw-bold d-block" ng-non-bindable=""><?php echo htmlspecialchars($departamentGroup->name)?></label>
                        <label><span class="material-icons">mode_edit</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> onchange="$('#dep-group-exclude-<?php echo $departamentGroup->id?>').toggle();$('#dep-group-ro-<?php echo $departamentGroup->id?>').prop('checked', false);" id="dep-group-full-<?php echo $departamentGroup->id?>" type="checkbox" name="UserDepartamentGroup[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroup) ? ' checked="checked" ' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                        <div class="d-block">
                            <div class="ms-4" id="dep-group-exclude-<?php echo $departamentGroup->id?>" style="display: <?php echo in_array($departamentGroup->id,$userDepartamentsGroup) ? 'block' : 'none';?>">
                                <label><span class="material-icons">assignment_ind</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> type="checkbox" name="UserDepartamentGroupAutoExc[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroupAutoExc) ? ' checked="checked" ' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assignment workflow')?></label>
                                <div>
                                    <label class="d-block fs13 text-muted pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assignment priority, optional, default - 0')?></label>
                                    <input type="text" class="form-control form-control-sm" name="UserDepartamentGroupAssignPriority[<?php echo $departamentGroup->id?>]" value="<?php if (isset($departmentEditParams['groups']['all_group'][$departamentGroup->id])) : ?><?php echo $departmentEditParams['groups']['all_group'][$departamentGroup->id]['assign_priority']?><?php else : ?>0<?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Agents with higher assignment priority will be assigned first to chat')?>" />
                                    <div class="row">
                                        <div class="col-12 fs13 text-muted">
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Min and Max chat priority for chat being assigned by my assign priority')?></label>
                                        </div>
                                        <div class="col-6">
                                            <input name="UserDepGroupAssignMinPriority[<?php echo $departamentGroup->id?>]" value="<?php if (isset($departmentEditParams['groups']['all_group'][$departamentGroup->id])) : ?><?php echo $departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_min_priority']?><?php else : ?>0<?php endif;?>" type="text" class="form-control form-control-sm" />
                                        </div>
                                        <div class="col-6">
                                            <input name="UserDepGroupAssignMaxPriority[<?php echo $departamentGroup->id?>]" value="<?php if (isset($departmentEditParams['groups']['all_group'][$departamentGroup->id])) : ?><?php echo $departmentEditParams['groups']['all_group'][$departamentGroup->id]['chat_max_priority']?><?php else : ?>0<?php endif;?>" type="text" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <label class="text-muted"><span class="material-icons text-muted">edit_off</span><input <?php if ($canEditDepartment != true) : ?>disabled<?php endif; ?> onchange="$('#dep-group-full-<?php echo $departamentGroup->id?>').prop('checked', false);" id="dep-group-ro-<?php echo $departamentGroup->id?>" type="checkbox" name="UserDepartamentGroupRead[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroupRead) ? ' checked="checked" ' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label><br>
                    </div>
                <?php endif; endforeach; ?>
            </div>*/ ?>



        </div>
        <?php endif;?>
    <?php endif;?>

</div>