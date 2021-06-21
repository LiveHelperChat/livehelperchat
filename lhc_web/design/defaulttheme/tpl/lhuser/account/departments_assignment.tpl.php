<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','All departments')?></label><br>
             
<hr class="mt-1 mb-1">      
  
<div class="row">
    <div class="col-6">
	    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    	
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Individual departments')?></h4>

            <div class="row" style="max-height: 600px;overflow: auto">
        	<?php foreach (erLhcoreClassModelDepartament::getList(array('limit' => false, 'filter' => array('archive' => 0))) as $departament) : ?>
                <div class="col-6">
                    <label class="font-weight-bold" ng-non-bindable><?php echo htmlspecialchars($departament->name)?></label><br>
                    <label><input onchange="$('#dep-ro-<?php echo $departament->id?>').prop('checked', false);" id="dep-full-<?php echo $departament->id?>" type="checkbox" name="UserDepartament[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartaments) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                    <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><input id="dep-ro-<?php echo $departament->id?>" type="checkbox" onchange="$('#dep-full-<?php echo $departament->id?>').prop('checked', false);" name="UserDepartamentRead[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsRead) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>
                </div>
        	<?php endforeach; ?>
            </div>

            <?php $departments = erLhcoreClassModelDepartament::getList(array('limit' => false, 'filter' => array('archive' => 1))); ?>
            <?php if (!empty($departments)) : ?>
            <hr>
            <button type="button" onclick="$('#offline-departments').toggle();" class="btn btn-outline-secondary btn-sm mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show archived departments')?></button>
            <div class="row" id="offline-departments" style="max-height: 600px;overflow: auto; display: none">
                <?php foreach ($departments as $departament) : ?>
                    <div class="col-6">
                        <label class="font-weight-bold" ng-non-bindable><?php echo htmlspecialchars($departament->name)?></label><br>
                        <label><input onchange="$('#dep-ro-<?php echo $departament->id?>').prop('checked', false);" id="dep-full-<?php echo $departament->id?>" type="checkbox" name="UserDepartament[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartaments) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label><br>
                        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><input id="dep-ro-<?php echo $departament->id?>" type="checkbox" onchange="$('#dep-full-<?php echo $departament->id?>').prop('checked', false);" name="UserDepartamentRead[]" value="<?php echo $departament->id?>" <?php echo in_array($departament->id,$userDepartamentsRead) ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>


    </div>
    
    <?php $departmentsGroups = erLhcoreClassModelDepartamentGroup::getList(array('limit' => false)); ?>
    
    <?php if (!empty($departmentsGroups)) : ?>
    <div class="col-6">    	           
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Departments groups')?></h4>
       
        <?php foreach ($departmentsGroups as $departamentGroup) : ?>
            <label><input type="checkbox" name="UserDepartamentGroup[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroup) ? ' checked="checked" ' : '';?> />&nbsp;<?php echo htmlspecialchars($departamentGroup->name)?></label><br>
        <?php endforeach; ?>
    </div>
    <?php endif;?>

</div>