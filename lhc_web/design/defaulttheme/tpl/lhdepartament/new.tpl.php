<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department');?></legend> 
<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/departament/new/')?>" method="post">
			<table>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Name');?></td><td><input class="inputfield" type="text" name="Name"  value="" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Save_departament" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Save');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>

</fieldset>