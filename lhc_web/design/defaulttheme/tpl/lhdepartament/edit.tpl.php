<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit department');?> - <?=$departament->name?></legend> 
<div class="articlebody">
<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>
	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/departament/edit/'.$departament->id)?>" method="post">
			<table>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></td><td><input class="inputfield" type="text" name="Name"  value="<?=htmlspecialchars($departament->name);?>" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_departament" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Update');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>
</fieldset>
