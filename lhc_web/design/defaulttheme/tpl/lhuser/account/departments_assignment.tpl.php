<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','All departments')?></label><br>
             
<hr class="mt-1 mb-1">      
  
<div class="row">
    <div class="col-6">
	    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    	
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Individual departments')?></h4>
        

            <div class="row" style="max-height: 600px;overflow: auto">
        	<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
                <div class="col-6">
                    <label class="font-weight-bold"><?php echo htmlspecialchars($departament['name'])?></label><br>

                    <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><input type="checkbox" name="UserDepartamentRead[]" value="<?php echo $departament['id']?>" <?php echo in_array($departament['id'],$userDepartamentsRead) ? 'checked="checked"' : '';?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read Only')?></label>

                    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>" <?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?> />Assign</label><br>
                </div>
        	<?php endforeach; ?>
            </div>

    </div>
    
    <?php $departmentsGroups = erLhcoreClassModelDepartamentGroup::getList(array('limit' => false)); ?>
    
    <?php if (!empty($departmentsGroups)) : ?>
    <div class="col-6">    	           
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Departments groups')?></h4>
       
        <?php foreach ($departmentsGroups as $departamentGroup) : ?>
            <label><input type="checkbox" name="UserDepartamentGroup[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroup) ? ' checked="checked" ' : '';?> /><?php echo htmlspecialchars($departamentGroup->name)?></label><br>
        <?php endforeach; ?>
    </div>
    <?php endif;?>

</div>