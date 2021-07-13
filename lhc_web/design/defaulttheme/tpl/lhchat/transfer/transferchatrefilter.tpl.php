<div class="mt-2">

    <?php include(erLhcoreClassDesign::designtpl('lhchat/transfer/transfer_custom_multiinclude.tpl.php'));?>

    <?php foreach (erLhcoreClassModelDepartamentGroup::getList() as $depGroupItem) : $idsUnique = [];?>
        <h6>
            <input type="button" class="btn btn-xs btn-secondary" value="Show/Hide" onclick="$('#dep-group-filter-id-<?php echo $depGroupItem->id?>').toggle()">&nbsp;<?php echo htmlspecialchars($depGroupItem->name)?>
        </h6>
        <div id="dep-group-filter-id-<?php echo $depGroupItem->id?>" style="display: none">
            <?php $membersDeps = erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_group_id' => $depGroupItem->id))); ?>
            <?php foreach ($membersDeps as $memberDep) {$idsUnique[] = $memberDep->dep_id; } ?>
            <?php if (!empty($idsUnique)) : ?>
                <?php foreach (erLhcoreClassModelDepartament::getList(array('sort' => 'sort_priority ASC, name ASC','filterin' => array('id' => $idsUnique))) as $depGroup) : ?>
                    <div class="checkbox"><label><input type="radio" name="DepartamentID<?php echo $departments_filter['chat_id']?>" value="<?php echo $depGroup->id?>"/> <?php echo htmlspecialchars($depGroup->name)?><?php if ($depGroup->id == $departments_filter['dep_id']) : ?> <b>[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','current');?>]</b><?php endif;?></label></div>
                <?php endforeach; ?>
            <?php endif;?>
        </div>
    <?php endforeach; ?>

    <?php $memberOf = erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_id' => $departments_filter['dep_id'])));$hasMembers = !empty($memberOf); ?>
    <?php if (!empty($memberOf)) : ?>
        <h6 class="border-top pt-2">
            <input type="button" class="btn btn-xs btn-secondary" value="Show/Hide" onclick="$('.member-of-dep-filter').toggle()">&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Member of these departments groups')?>
            </h6>
        </h6>
    <?php endif; ?>

    <?php foreach ($memberOf as $member) : $idsUnique = [];?>
        <div class="member-of-dep-filter" style="display: none">
        <?php $membersDeps = erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_group_id' => $member->dep_group_id))); ?>
        <?php foreach ($membersDeps as $memberDep) {$idsUnique[] = $memberDep->dep_id; } ?>
        <?php if (!empty($idsUnique)) : ?>
        <label><b><?php echo htmlspecialchars(erLhcoreClassModelDepartamentGroup::fetch($member->dep_group_id))?></b></label>
        <?php foreach (erLhcoreClassModelDepartament::getList(array('sort' => 'sort_priority ASC, name ASC','filterin' => array('id' => $idsUnique))) as $depGroup) : ?>
            <div class="checkbox"><label><input type="radio" name="DepartamentID<?php echo $departments_filter['chat_id']?>" value="<?php echo $depGroup->id?>"/> <?php echo htmlspecialchars($depGroup->name)?><?php if ($depGroup->id == $departments_filter['dep_id']) : ?> <b>[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','current');?>]</b><?php endif;?></label></div>
        <?php endforeach; ?>
        <?php endif;?>
        </div>
    <?php endforeach; ?>

    <h6 class="border-top pt-2">
        <input type="button" class="btn btn-xs btn-secondary" value="Show/Hide" onclick="$('#online-transfer-dep').toggle()">&nbsp;<span class="label label-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Online');?></span>
        </h6>
    <div id="online-transfer-dep" style="display: <?php $hasMembers == true ? print 'none' : 'block'?>">
    <?php
        $departments = erLhcoreClassModelDepartament::getList(array_merge($departments_filter['filter'],array('sort' => 'sort_priority ASC, name ASC')));
        $onlineDepartments = erLhcoreClassChat::getLoggedDepartmentsIds(array_keys($departments), $departments_filter['explicit']);
        foreach ($departments as $departament) : if (in_array($departament->id, $onlineDepartments)) : ?>
            <div class="checkbox"><label><input type="radio" name="DepartamentID<?php echo $departments_filter['chat_id']?>" value="<?php echo $departament->id?>"/> <?php echo htmlspecialchars($departament->name)?><?php if ($departament->id == $departments_filter['dep_id']) : ?> <b>[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','current');?>]</b><?php endif;?></label></div>
    <?php endif; endforeach; ?>
    </div>

    <h6 class="border-top pt-2">
        <input type="button" class="btn btn-xs btn-secondary" value="Show/Hide" onclick="$('#offline-transfer-dep').toggle()">&nbsp;<span class="label label-default"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Offline');?></span>
    </h6>
    <div id="offline-transfer-dep" style="display: none">
    <?php foreach ($departments as $departament) : if (!in_array($departament->id, $onlineDepartments)) : ?>
       <div class="checkbox"><label><input type="radio" name="DepartamentID<?php echo $departments_filter['chat_id']?>" value="<?php echo $departament->id?>"/> <?php echo htmlspecialchars($departament->name)?></label></div>
    <?php endif; endforeach; ?>
    </div>


</div>
