<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php foreach ($object->getFields() as $fieldName => $attr) : ?>
	<?php if (!isset($attr['hide_edit'])) : ?>	
		<?php if ($attr['type'] == 'title') : ?>
			<?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?>
		<?php elseif ($attr['type'] == 'checkbox') : ?>
		    <div class="form-group">
			<label><?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?> <?php echo $attr['trans'];?><?php echo $attr['required'] == true ? ' *' : ''?></label>
			</div>
		<?php else : ?>
		    <div class="form-group">
			<label><?php echo $attr['trans'];?><?php echo $attr['required'] == true ? ' *' : ''?></label>
			<?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?>
			</div>
		<?php endif;?>
	<?php endif;?>
<?php endforeach;?>

<br />
<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-default" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
	<input type="submit" class="btn btn-default" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
	<input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>