<h3 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','MS Auth Options')?></h3>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','Tenant ID')?></label>
        <input class="form-control form-control-sm" type="text" name="ms_tenant_id" value="<?php (isset($ms_options['ms_tenant_id'])) ? print htmlspecialchars($ms_options['ms_tenant_id']) : print ''?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','Client ID')?></label>
        <input class="form-control form-control-sm" type="text" name="ms_client_id" value="<?php (isset($ms_options['ms_client_id'])) ? print htmlspecialchars($ms_options['ms_client_id']) : print ''?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','Client Secret')?></label>
        <input class="form-control form-control-sm" type="text" name="ms_secret" value="<?php (isset($ms_options['ms_secret'])) ? print htmlspecialchars($ms_options['ms_secret']) : print ''?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','Login URL to set in APP settings')?></label>
        <input readonly type="text" class="form-control form-control-sm" value="https://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('mailconvoauth/msoauth')?>">
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/msauth','Set this URL as Authentication Redirect URLs under Web platform.')?></p>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
