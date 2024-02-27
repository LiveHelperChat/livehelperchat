<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Main settings');?></h5>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail');?>*</label>
            <input type="text" required maxlength="250" class="form-control form-control-sm" name="mail" value="<?php echo htmlspecialchars($item->mail)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','From name');?></label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Authentication method');?></label>
            <select name="auth_method" class="form-control form-control-sm" <?php if ($item->id == null) : ?>disabled<?php endif; ?> >
                <option <?php if ($item->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_NORMAL_PASSWORD) : ?>selected="selected"<?php endif; ?> value="<?php echo erLhcoreClassModelMailconvMailbox::AUTH_NORMAL_PASSWORD?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Normal password');?></option>
                <option <?php if ($item->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) : ?>selected="selected"<?php endif; ?> value="<?php echo erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','OAuth2');?></option>
            </select>
        </div>
    </div>
    <div class="col-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Finish OAuth authentication');?></label>
        <?php if ($item->id !== null) : ?>
        <div class="method-type-<?php echo erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2?>">
            <?php if (strpos($item->imap,'outlook.office365.com') !== false) : ?>
                <a href="<?php echo erLhcoreClassDesign::baseurl('mailconvoauth/mslogin')?>/<?php echo $item->id?>"><img src="<?php echo erLhcoreClassDesign::design('images/mailconv/oauth/ms-symbollockup_signin_light.png');?>"></a>
                <?php $oauth = LiveHelperChat\Models\mailConv\OAuthMS::findOne(['filter' => ['mailbox_id' => $item->id]]);
                    if (is_object($oauth)) : ?>
                       <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Token expires at');?> - <b><?php echo date('Y-m-d H:i:s',$oauth->dtExpires)?></b>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php elseif ($item->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Please save first initial account settings');?>
    <?php endif; ?>
</div>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send e-mail settings SMTP');?></h5>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Do not enter SMTP username and password if it is the same as IMAP')?></p>
<div class="row">
<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Username');?></label>
        <input type="text" placeholder="example@example.org" maxlength="250" autocomplete="new-password" class="form-control form-control-sm" name="username_smtp" value="<?php echo htmlspecialchars($item->username_smtp)?>" />
    </div>
</div>
<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Password');?></label>
        <input type="password" maxlength="250" class="form-control form-control-sm" autocomplete="new-password" name="password_smtp" value="<?php echo htmlspecialchars($item->password_smtp)?>" />
    </div>
</div>

<div class="col-12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','You can set custom from mail and name. If not set we will use the main settings. Reply-to always will be set to main settings mail.')?></div>

<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail');?></label>
        <input type="text" maxlength="250" class="form-control form-control-sm" name="mail_smtp" value="<?php echo htmlspecialchars($item->mail_smtp)?>" />
    </div>
</div>
<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','From name');?></label>
        <input type="text" maxlength="250" class="form-control form-control-sm" name="name_smtp" value="<?php echo htmlspecialchars($item->name_smtp)?>" />
    </div>
</div>
<div class="col-12">
    <div class="form-group">
        <label><input type="checkbox" name="no_pswd_smtp" value="on" <?php $item->no_pswd_smtp == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','No password required to send an e-mail.');?></label>
    </div>
</div>

<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Host');?>*</label>
        <input type="text" required placeholder="tls://smtp.gmail.com" maxlength="250" class="form-control form-control-sm" name="host" value="<?php echo htmlspecialchars($item->host)?>" />
    </div>
</div>
<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Port');?>*</label>
        <input type="text" placeholder="E.g 587" required maxlength="250" class="form-control form-control-sm" name="port" value="<?php echo htmlspecialchars($item->port)?>" />
    </div>
</div>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Receive e-mail IMAP settings.');?></h5>

<div class="row">
<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Username');?>*</label>
        <input type="text" placeholder="example@example.org" maxlength="250" autocomplete="new-password" class="form-control form-control-sm" name="username" value="<?php echo htmlspecialchars($item->username)?>" />
    </div>
</div>
<div class="col-6">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Password');?></label>
        <input type="password" maxlength="250" class="form-control form-control-sm" autocomplete="new-password" name="password" value="<?php echo htmlspecialchars($item->password)?>" />
    </div>
</div>
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','IMAP Server address');?>*</label>
<input type="text" required placeholder="{imap.gmail.com:993/imap/ssl}" maxlength="250" class="form-control form-control-sm" name="imap" value="<?php echo htmlspecialchars($item->imap == '' ? '{imap.gmail.com:993/imap/ssl}' : $item->imap)?>" />
</div>




