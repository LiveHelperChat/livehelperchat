<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','New group');?></legend> 

<div class="articlebody">
<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/user/newgroup/')?>" method="post">
			<table>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Save_group" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save');?>"/> <input type="submit" class="default-button" name="Save_group_and_assign_user" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save and assign user');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>

</fieldset>