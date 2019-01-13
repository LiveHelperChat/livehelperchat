<h1><?php echo htmlspecialchars($item->id)?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">
    <ul class="nav nav-pills" role="tablist">
        <li class="nav-item" role="presentation"><a class="nav-link <?php if ($tab == '') : ?> active<?php endif;?>" href="#edit" aria-controls="edit" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Main');?></a></li>
        <li class="nav-item" role="presentation" ><a class="nav-link<?php if ($tab == 'tab_notification') : ?> active<?php endif;?>" href="#notification" aria-controls="notification" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Test notification');?></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="edit">
            <?php include(erLhcoreClassDesign::designtpl('lhnotifications/form.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_notification') : ?>active<?php endif;?>" id="notification">
            <?php include(erLhcoreClassDesign::designtpl('lhnotifications/form_test_notification.tpl.php'));?>
            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Send_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send test notification');?>"/>
            </div>
        </div>
    </div>
</form>
