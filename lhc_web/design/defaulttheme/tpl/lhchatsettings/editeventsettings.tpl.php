<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php $eventtrackoptions['show_department'] = true; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/ga/event_form.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="SaveConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Save');?>" /> <input type="submit" class="btn btn-secondary" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Update');?>" /> <input type="submit" class="btn btn-secondary" name="CancelConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Cancel');?>" />
    </div>

</form>