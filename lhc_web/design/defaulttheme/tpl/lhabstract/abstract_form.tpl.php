<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php foreach ($object->getFields() as $fieldName => $attr) : ?>
	<?php if (!isset($attr['hide_edit'])) : ?>
		<?php if ($attr['type'] == 'checkbox') : ?>
		<label><?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?> <?php echo htmlspecialchars($attr['trans']);?><?php echo $attr['required'] == true ? ' *' : ''?><br/><br/></label>
		<?php else : ?>
		<label><?php echo htmlspecialchars($attr['trans']);?><?php echo $attr['required'] == true ? ' *' : ''?></label>
		<?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?>
		<?php endif;?>
	<?php endif;?>
<?php endforeach;?>

<br />

<ul class="button-group radius">
	<li><input type="submit" class="small button" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
	<li><input type="submit" class="small button" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/></li>
	<li><input type="submit" class="small button" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
</ul>



