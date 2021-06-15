<h1 class="attr-header">Mobile Options</h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="notifications" <?php isset($mb_options['notifications']) && ($mb_options['notifications'] == true) ? print 'checked="checked"' : ''?> /> Enable notifications</label><br/>
    </div>

    <div class="form-group">
        <label>FCM Key</label>
        <input type="text" class="form-control" name="fcm_key" value="<?php isset($mb_options['fcm_key']) ? print $mb_options['fcm_key'] : ''?>" />
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
