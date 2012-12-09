<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','New policy');?> - <?php echo $role->name?></legend>

<div class="articlebody">

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/permission/editrole/'.$role->id)?>" method="post">
						
			<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Assigned functions');?></legend> 			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose module');?></td>
			     <td>			    
			     <select id="ModuleSelectedID" name="Module">			     
			         <option value="*">---<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All modules');?>---</option>
    			     <?php foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
    			         <option value="<?php echo $key?>"><?php echo $Module['name'];?></option>
    			     <?php endforeach; ?>
			     </select>			     
			     </td>			  
			 </tr>
			 <tr>
			     <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose module function');?></td>
			     <td id="ModuleFunctionsID">
			        <select name="ModuleFunction" >
			         <option value="*"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All functions');?></option>
			        </select>
			     </td>
			 </tr>
			</table>			
<br />

			<input type="submit" class="default-button" name="Store_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Save');?>"/>
			<input type="submit" class="default-button" name="Cancel_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Cancel');?>"/>
			</fieldset>
					
		</form>
	</div>
</div>

</fieldset>

<script type="text/javascript">
$( "#ModuleSelectedID" ).change( function () { 
	var module_val = $( "#ModuleSelectedID" ).val();
	if (module_val != '*'){
	    
	    $.getJSON('<?php echo erLhcoreClassDesign::baseurl('/permission/modulefunctions/')?>'+module_val ,{ }, function(data){ 
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