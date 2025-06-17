<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Configuration')?></h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    
    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Assume first n letter from keyword is valid')?></label>
        <input type="number" min="1" max="5" title="min 1, max 5" placeholder="min 1, max 5" class="form-control" name="first_n_letters" value="<?php isset($sc_options['first_n_letters']) ? print $sc_options['first_n_letters'] : print 1; ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Minimum percentage to match for related tag to be considered as valid')?></label>
        <input type="number" min="0" max="90" class="form-control" title="min 0, max 90" placeholder="min 0, max 90" name="min_percentage" value="<?php isset($sc_options['min_percentage']) ? print $sc_options['min_percentage'] : print 0; ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Top n matches tags should be considered as valid')?></label>
        <input type="number" min="1" max="10" title="min 1, max 10" placeholder="min 1, max 10" class="form-control" name="top_n_match" value="<?php isset($sc_options['top_n_match']) ? print $sc_options['top_n_match'] : print 1; ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Maximum number of canned messages to show by default')?></label>
        <input type="number" min="50" max="5000" title="min 50, max 5000" placeholder="min 50, max 5000" class="form-control" name="max_result" value="<?php isset($sc_options['max_result']) ? print $sc_options['max_result'] : print 50; ?>" />
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
