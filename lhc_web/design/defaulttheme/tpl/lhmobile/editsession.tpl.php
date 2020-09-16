<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Edit session');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mobile/editsession')?>/<?php echo $item->id?>" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhmobile/parts/form.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
        <input type="submit" class="btn btn-secondary" name="Send_notifications" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send test notification');?>"/>
    </div>

</form>