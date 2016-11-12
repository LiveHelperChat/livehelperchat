<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','All departments')?></label><br>
             
<hr class="mt10 mb10">      
  
<div class="row">
    <div class="col-xs-6">
	    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    	
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Individual departments')?></h4>
        
        <div class="mx170">
        	<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
        	    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>" <?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?> /><?php echo htmlspecialchars($departament['name'])?></label><br>
        	<?php endforeach; ?>
    	</div>
    </div>
    
    <?php $departmentsGroups = erLhcoreClassModelDepartamentGroup::getList(array('limit' => false)); ?>
    
    <?php if (!empty($departmentsGroups)) : ?>
    <div class="col-xs-6">    	           
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Departments groups')?></h4>
       
        <?php foreach ($departmentsGroups as $departamentGroup) : ?>
            <label><input type="checkbox" name="UserDepartamentGroup[]" value="<?php echo $departamentGroup->id?>" <?php echo in_array($departamentGroup->id,$userDepartamentsGroup) ? ' checked="checked" ' : '';?> /><?php echo htmlspecialchars($departamentGroup->name)?></label><br>
        <?php endforeach; ?>
    </div>
    <?php endif;?>
    
</div>