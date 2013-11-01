<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','New policy');?> - <?php echo htmlspecialchars($role->name)?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Assigned functions');?></h5>
	<table cellpadding="0" cellspacing="0">
	<tr>
	     <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose a module');?></td>
	     <td>
	     <select id="ModuleSelectedID" name="Module">
	         <option value="*">---<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All modules');?>---</option>
		     <?php foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
		         <option value="<?php echo $key?>"><?php echo htmlspecialchars($Module['name']);?></option>
		     <?php endforeach; ?>
	     </select>
	     </td>
	 </tr>
	 <tr>
	     <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose a module function');?></td>
	     <td id="ModuleFunctionsID">
	        <select name="ModuleFunction" >
	         <option value="*"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All functions');?></option>
	        </select>
	     </td>
	 </tr>
	</table>

	<ul class="button-group radius">
	 <li><input type="submit" class="small button" name="Store_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Save');?>"/></li>
	 <li><input type="submit" class="small button" name="Cancel_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Cancel');?>"/></li>
	</ul>

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
	    $( "#ModuleFunctionsID" ).html( '<select name="ModuleFunction" ><option value="*"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All functions');?></option></select>');
	}
});
</script>