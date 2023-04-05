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

            <?php if ($departmentEditParams['individual']['edit_all'] || $departmentEditParams['individual']['edit_personal']) : ?>
            <a class="btn btn-success btn-xs action-image text-white" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/newdepartment/<?php echo $user->id?><?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})"><span class="material-icons fs11 me-0">add</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','New')?></a>
            <?php endif; ?>

        </h5>
            <div class="row" style="max-height: 600px;overflow: auto">
        	<?php foreach (erLhcoreClassModelDepartament::getList(array('limit' => false)) as $departament) :
                $canEditDepartment = $departmentEditParams['individual']['edit_all'] || $departmentEditParams['individual']['edit_personal'] && in_array($departament->id,$departmentEditParams['individual']['id']);
                if ($canEditDepartment || $departmentEditParams['individual']['read_all'] == true) : ?>

                <?php if (in_array($departament->id,$userDepartamentsRead) || in_array($departament->id,$userDepartaments)) : ?>
                <div class="col-12" id="dep-indv-id-<?php echo $departament->id?>">

                        <hr class="pb-1 mb-0 mt-1 border-top">

                        <label class="fw-bold <?php if ($canEditDepartment != true) : ?>text-muted<?php endif; ?>" ng-non-bindable>

                            <span <?php if ($canEditDepartment == true) : ?>onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/user/editdepartment/<?php echo $user->id?>/<?php echo $departament->id?><?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Edit')?>" <?php endif;?> class="material-icons <?php if ($canEditDepartment != true) : ?>text-muted<?php else : ?>action-image<?php endif; ?>"><?php if ($canEditDepartment != true) : ?>edit_off<?php else : ?>edit<?php endif; ?></span>
                            <?php echo htmlspecialchars($departament->name)?>

                            <?php $userDepAlias = \LiveHelperChat\Models\Departments\UserDepAlias::findOne(['filter' => ['dep_id' => $departament->id, 'user_id' => $user->id]]); ?>
                            <?php if (is_object($userDepAlias)) : ?>
                                <?php if ($userDepAlias->nick != '') : ?>
                                    <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Alias nick')?>"><span class="material-icons">supervisor_account</span><?php echo htmlspecialchars($userDepAlias->nick);?></span>
                                <?php endif; ?>
                                <?php if ($userDepAlias->avatar != '') : ?>
                                    <span class="bg-light border p-1 d-inline-block rounded">
                                        <img title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Avatar')?>" width="25" height="25" src="<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($userDepAlias->avatar)?>" alt="" title="Click to set avatar">
                                    </span>
                                <?php endif; ?>
                                <?php if ($userDepAlias->has_photo) : ?>
                                    <span class="bg-light border p-1 d-inline-block rounded">
                                        <img itle="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Profile picture')?>" src="<?php echo $userDepAlias->photo_path?>" alt="" width="25" height="25" />
                                    </span>
                                <?php endif;?>
                            <?php endif; ?>

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

        	    <?php endif; endforeach; ?>
            </div>
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

            <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Departments groups')?>

                <?php if ( $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal']) : ?>
                <a class="btn btn-success btn-xs action-image text-white" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/newdepartment/<?php echo $user->id?>/(mode)/group<?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})"><span class="material-icons fs11 me-0">add</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','New')?></a></h5>
                <?php endif; ?>

            <div class="row">
            <?php foreach ($departmentsGroups as $departamentGroup) :
                $canEditDepartment = $departmentEditParams['groups']['edit_all'] || $departmentEditParams['groups']['edit_personal'] && in_array($departamentGroup->id,$departmentEditParams['groups']['id']);
                if ($canEditDepartment || $departmentEditParams['groups']['read_all'] == true) : ?>
                <?php if (in_array($departamentGroup->id,$userDepartamentsGroup) ||  in_array($departamentGroup->id,$userDepartamentsGroupRead)) : ?>

            <div class="col-12" id="depgroup-indv-id-<?php echo $departamentGroup->id?>">

                <hr class="pb-1 mb-0 mt-1 border-top">

                <label class="fw-bold <?php if ($canEditDepartment != true) : ?>text-muted<?php endif; ?>" ng-non-bindable>

                    <span <?php if ($canEditDepartment == true) : ?>onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/user/editdepartment/<?php echo $user->id?>/<?php echo $departamentGroup->id?>/(mode)/group<?php if (isset($departmentEditParams['self_edit']) && $departmentEditParams['self_edit'] === true) : ?>/(editor)/self<?php endif; ?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Edit')?>" <?php endif;?> class="material-icons <?php if ($canEditDepartment != true) : ?>text-muted<?php else : ?>action-image<?php endif; ?>"><?php if ($canEditDepartment != true) : ?>edit_off<?php else : ?>edit<?php endif; ?></span>

                    <?php echo htmlspecialchars($departamentGroup->name)?>

                    <?php $userDepAlias = \LiveHelperChat\Models\Departments\UserDepAlias::findOne(['filter' => ['dep_group_id' => $departamentGroup->id, 'user_id' => $user->id]]); ?>
                    <?php if (is_object($userDepAlias)) : ?>
                        <?php if ($userDepAlias->nick != '') : ?>
                            <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Alias nick')?>"><span class="material-icons">supervisor_account</span><?php echo htmlspecialchars($userDepAlias->nick);?></span>
                        <?php endif; ?>
                        <?php if ($userDepAlias->avatar != '') : ?>
                            <span class="bg-light border p-1 d-inline-block rounded">
                                       <img title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Avatar')?>" width="25" height="25" src="<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($userDepAlias->avatar)?>" alt="" title="Click to set avatar">
                                    </span>
                        <?php endif; ?>
                        <?php if ($userDepAlias->has_photo) : ?>
                            <span class="bg-light border p-1 d-inline-block rounded">
                                        <img itle="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Profile picture')?>" src="<?php echo $userDepAlias->photo_path?>" alt="" width="25" height="25" />
                                    </span>
                        <?php endif;?>
                    <?php endif; ?>

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

        </div>
        <?php endif;?>
    <?php endif;?>

</div>