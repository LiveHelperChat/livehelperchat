<div class="row">
    <div class="col-10"><h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('admintheme/form','Personal theme');?></h1></div>
</div>

<form action="<?php echo erLhcoreClassDesign::baseurl('theme/personaltheme')?>" method="post" autocomplete="off" enctype="multipart/form-data">

    <div class="form-group">
        <label><input type="checkbox" name="EnabledPersonal" value="on" <?php if ($enabledPersonal == true) : ?>checked="checked"<?php endif;?> /> Enable personal theme for me</label>
    </div>

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