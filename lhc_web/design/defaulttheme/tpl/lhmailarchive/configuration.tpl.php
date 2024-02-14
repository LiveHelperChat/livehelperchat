<h1>Configuration</h1>

<form ng-non-bindable action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated!'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><input type="checkbox" name="automatic_archiving" value="on" <?php isset($ar_options['automatic_archiving']) && $ar_options['automatic_archiving'] == true ? print 'checked="checked"' : '' ?> > Automatic archiving</label>
    </div>
    <div class="form-group">
        <label>Archive older mails's than defined days</label>
        <input type="text" class="form-control" name="older_than" value="<?php isset($ar_options['older_than']) && $ar_options['older_than'] == true ? print $ar_options['older_than'] : '' ?>" />
    </div>
    <h4>Archive size options</h4>
    <div class="form-group">
        <label><input type="radio" name="archive_strategy" value="1" <?php isset($ar_options['archive_strategy']) && $ar_options['archive_strategy'] == 1 ? print 'checked="checked"' : '' ?> >Create new archive every month. New archive will be created every month</label>
    </div>
    <div class="form-group">
        <label><input type="radio" name="archive_strategy" value="2" <?php isset($ar_options['archive_strategy']) && $ar_options['archive_strategy'] == 2 ? print 'checked="checked"' : '' ?> >Create new archive If mails's number in last archive reaches defined number</label>
        <input type="text" class="form-control" name="max_mails" value="<?php isset($ar_options['max_mails']) ? print htmlspecialchars($ar_options['max_mails']) : ''?>" >
    </div>
    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="Save">
</form>