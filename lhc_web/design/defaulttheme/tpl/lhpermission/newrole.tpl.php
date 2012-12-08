<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New role');?></legend>

<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/permission/newrole/')?>" method="post">
			<table>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="" /></td>
				</tr>
			</table>
			<br />
			<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Policy list');?></legend> 			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <th>&nbsp;</th>
			     <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Module');?></th>
			     <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Function');?></th>	
			</tr>			     
			</table>			<br />
			<input type="submit" class="default-button" name="New_policy" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New policy');?>"/>
			
			</fieldset>
			<br />
			<input type="submit" class="default-button" name="Save_role" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Save');?>"/>	
			<input type="submit" class="default-button" name="Cancel_role" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Cancel');?>"/>	
		</form>
	</div>
</div>

</fieldset>