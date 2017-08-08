<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/transferconfiguration','Transfer configuration')?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/transferconfiguration','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/transferconfiguration','Options for chat transfer to department')?></h4>
    <div class="form-group">
        <label><input type="checkbox" value="on" name="change_department" <?php echo isset($transfer_data['change_department']) && $transfer_data['change_department'] == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Change chat department to transferred department on chat transfer')?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="make_pending" <?php echo isset($transfer_data['make_pending']) && $transfer_data['make_pending'] == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Make chat status pending on transfer to department')?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="make_unassigned" <?php echo isset($transfer_data['make_unassigned']) && $transfer_data['make_unassigned'] == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Make chat unassigned. Assigned operator will be unassigned')?></label>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="Update" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>">
    </div>

</form>