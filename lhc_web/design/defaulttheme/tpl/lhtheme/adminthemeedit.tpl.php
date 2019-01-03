<div class="row">
    <div class="col-10"><h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('admintheme/form','Edit theme');?> - <?php echo htmlspecialchars($form->name)?></h1></div>
    <?php /*?><div class="col-2">
        <a class="btn btn-secondary" href="?export=1"><i class="material-icons mr-0">file_download</i></a>
    </div>*/ ?>
</div>

<form action="<?php echo erLhcoreClassDesign::baseurl('theme/adminthemeedit')?>/<?php echo $form->id?>" method="post" autocomplete="off" enctype="multipart/form-data">

    <?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('admintheme/form','Updated'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
	<?php endif; ?>

    <?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/form.tpl.php'));?>

	<div class="btn-group" role="group" aria-label="...">
      <input type="submit" class="btn btn-secondary" name="SaveAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
      <input type="submit" class="btn btn-secondary" name="UpdateAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
      <input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>