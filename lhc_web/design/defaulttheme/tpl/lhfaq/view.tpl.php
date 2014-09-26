<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','View');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/messages','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('faq/view')?>/<?php echo $faq->id?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhfaq/form.tpl.php'));?>

	<br>
	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="Update" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
    </ul>

</form>