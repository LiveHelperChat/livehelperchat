<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/messages','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chatbox/edit')?>/<?php echo $chatbox->id?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhchatbox/form.tpl.php'));?>

	<div class="btn-group" role="group" aria-label="...">
      <input type="submit" class="btn btn-default" name="Update" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
      <input type="submit" class="btn btn-default" name="Cancel" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>