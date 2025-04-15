<h1 ng-non-bindable><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','New policy');?> - <?php echo htmlspecialchars($role->name)?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form ng-non-bindable action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
	
	     <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose a module');?></h4>	
	    
	     <select class="form-control form-control-sm" id="ModuleSelectedID" name="Module">
	         <option value="*">---<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All modules');?>---</option>
		     <?php $moduleList = erLhcoreClassModules::getModuleList();asort($moduleList); foreach ($moduleList as $key => $Module) : ?>
		         <?php include(erLhcoreClassDesign::designtpl('lhpermission/newpolicy_row.tpl.php'));?>
		     <?php endforeach; ?>
	     </select>
		
	     <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose a module function');?></h4>	  
	        
	     <div id="ModuleFunctionsID">
	     	<label class="fs12"><input type="checkbox" name="ModuleFunction[]" value="*"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/modulefunctions','All functions');?></label>
	     </div>

        <h5 class="pt-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Options')?></h5>

        <div class="form-group">
            <label><input type="checkbox" value="1" name="type">&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Exclude permission. Operator will lose access to this function.')?></label>
        </div>

         <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Limitation');?></label>
            <textarea class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Enter any content which you will be able to get within users permissions')?>" name="Limitation"></textarea>
         </div>

	<div class="btn-group" role="group" aria-label="...">
	 <input type="submit" class="btn btn-sm btn-success" name="Store_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Save');?>"/>
	 <input type="submit" class="btn btn-sm btn-secondary" name="Cancel_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Cancel');?>"/>
	</div>

</form>



<script type="text/javascript">
$( "#ModuleSelectedID" ).change( function () {
	var module_val = $( "#ModuleSelectedID" ).val();
	if (module_val != '*'){

	    $.getJSON('<?php echo erLhcoreClassDesign::baseurl('permission/modulefunctions')?>/'+module_val ,{ }, function(data){
	        // If no error
	        if (data.error == 'false')
	        {
                $( "#ModuleFunctionsID" ).html(data.result);
	        }
    	});
	} else {
	    $( "#ModuleFunctionsID" ).html( '<label class="fs12"><input type="checkbox" name="ModuleFunction[]" value="*"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All functions');?></label>');
	}
});
</script>