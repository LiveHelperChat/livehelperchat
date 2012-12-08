<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','New policy');?> - <?=$role->name?></legend>

<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/permission/editrole/'.$role->id)?>" method="post">
						
			<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Assigned functions');?></legend> 			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose module');?></td>
			     <td>			    
			     <select id="ModuleSelectedID" name="Module">			     
			         <option value="*">---<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All modules');?>---</option>
    			     <? foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
    			         <option value="<?=$key?>"><?=$Module['name'];?></option>
    			     <? endforeach; ?>
			     </select>			     
			     </td>			  
			 </tr>
			 <tr>
			     <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Choose module function');?></td>
			     <td id="ModuleFunctionsID">
			        <select name="ModuleFunction" >
			         <option value="*"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All functions');?></option>
			        </select>
			     </td>
			 </tr>
			</table>			
<br />

			<input type="submit" class="default-button" name="Store_policy" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Save');?>"/>
			<input type="submit" class="default-button" name="Cancel_policy" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','Cancel');?>"/>
			</fieldset>
					
		</form>
	</div>
</div>

</fieldset>

<script type="text/javascript">
$( "#ModuleSelectedID" ).change( function () { 
	var module_val = $( "#ModuleSelectedID" ).val();
	if (module_val != '*'){
	    
	    $.getJSON('<?=erLhcoreClassDesign::baseurl('/permission/modulefunctions/')?>'+module_val ,{ }, function(data){ 
	        // If no error
	        if (data.error == 'false')
	        {	 
                $( "#ModuleFunctionsID" ).html(data.result);
	        }		
    	});
	} else {
	    $( "#ModuleFunctionsID" ).html( '<select name="ModuleFunction" ><option value="*"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newpolicy','All functions');?></option></select>');
	}
});
</script>