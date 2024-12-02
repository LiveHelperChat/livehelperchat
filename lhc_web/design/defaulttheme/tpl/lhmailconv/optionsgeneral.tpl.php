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
        <label>
            <input type="checkbox" name="mail_module_as_send" value="on" <?php if (isset($general_options['mail_module_as_send']) && ($general_options['mail_module_as_send'] == true)) : ?>checked="checked"<?php endif;?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Use mail module to send e-mail from chat')?>
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

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Send report of failing mailbox to')?></label>
        <input type="text" placeholder="example1@example.com,example2@example.com" class="form-control form-control-sm" value="<?php isset($general_options['report_email']) ? print htmlspecialchars($general_options['report_email']) : print ''?>" name="report_email" />
        <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','We will report to provided e-mail. You can enter multiple e-mails by separating by comma.')?></small>
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Other')?></h4>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Subject to add if mail ticket is closed from chat. Enter a subject ID')?></label>
        <input type="text" placeholder="Subject ID" class="form-control form-control-sm" value="<?php isset($general_options['subject_id']) ? print htmlspecialchars($general_options['subject_id']) : print ''?>" name="subject_id" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Matching rules to exclude mailbox being automatically closed. Defines matching rule for the mailbox.')?></label>
        <textarea class="form-control form-control-sm" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Each new matching rule per line.')?>" name="exclude_mailbox"><?php isset($general_options['exclude_mailbox']) ? print htmlspecialchars($general_options['exclude_mailbox']) : print ''?></textarea>
        <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','E.g.')?> /^example@*/i</small>
    </div>

    <input type="submit" class="btn btn-sm btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
