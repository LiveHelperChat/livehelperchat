<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="Name"  value="<?php echo htmlspecialchars($departament_group->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Maximum pending chats');?></label>
    <input type="text" class="form-control" name="PendingMax"  value="<?php echo htmlspecialchars($departament_group->pending_max);?>" />
</div>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Departments');?></h4>

<div class="row" style="max-height:500px;overflow-y:auto;">
        <?php $depIds = $departament_group->departments_ids; foreach (erLhcoreClassModelDepartament::getList() as $department) : ?>
        <div class="col-xs-6">
            <div class="form-group mb0">
                <label><input type="checkbox" name="departaments[]" value="<?php echo $department->id?>" <?php if (in_array($department->id, $depIds)) : ?>checked="checked"<?php endif;?>> <?php echo htmlspecialchars($department->name)?> </label>
            </div>
        </div>
        <?php endforeach;?>
</div>

