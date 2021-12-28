<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Mail general options')?></h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label>
            <input type="checkbox" name="active_lang_detect" value="on" <?php if (isset($general_options['active_lang_detect']) && ($general_options['active_lang_detect'] == true)) : ?>checked="checked"<?php endif;?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Enable language detection')?>
        </label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','URL of API endpoint')?></label>
        <input type="text" placeholder="https://hub.docker.com/r/antoinefinkelstein/language-detection-api" rows="10" class="form-control form-control-sm" value="<?php isset($general_options['lang_url']) ? print htmlspecialchars($general_options['lang_url']) : print 'https://hub.docker.com/r/antoinefinkelstein/language-detection-api'?>" name="lang_url" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Language detection provider')?></label>
        <select name="lang_provider" class="form-control form-control-sm">
            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Choose provider')?></option>
            <option value="antoinefinkelsteinlang" <?php if (isset($general_options['lang_provider']) && ($general_options['lang_provider'] == 'antoinefinkelsteinlang')) : ?>selected="selected"<?php endif;?> >https://hub.docker.com/r/antoinefinkelstein/language-detection-api</option>
        </select>
    </div>

    <input type="submit" class="btn btn-sm btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
