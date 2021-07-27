
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send e-mail settings SMTP');?></h5>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail');?></label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="mail" value="<?php echo htmlspecialchars($item->mail)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','From name');?></label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Host');?></label>
            <input type="text" placeholder="tls://smtp.gmail.com" maxlength="250" class="form-control form-control-sm" name="host" value="<?php echo htmlspecialchars($item->host)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Port');?></label>
            <input type="text" placeholder="587" maxlength="250" class="form-control form-control-sm" name="port" value="<?php echo htmlspecialchars($item->port)?>" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Username');?></label>
            <input type="text" placeholder="example@example.org" maxlength="250" class="form-control form-control-sm" name="username" value="<?php echo htmlspecialchars($item->username)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Password');?></label>
            <input type="password" maxlength="250" class="form-control form-control-sm" name="password" value="<?php echo htmlspecialchars($item->password)?>" />
        </div>
    </div>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Receive e-mail IMAP settings.');?></h5>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','IMAP Server address');?></label>
    <input type="text" placeholder="{imap.gmail.com:993/imap/ssl}" maxlength="250" class="form-control form-control-sm" name="imap" value="<?php echo htmlspecialchars($item->imap == '' ? '{imap.gmail.com:993/imap/ssl}' : $item->imap)?>" />
</div>


<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Active');?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="delete_mode" value="on" <?php $item->delete_mode == erLhcoreClassModelMailconvMailbox::DELETE_ALL ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','On message deletion delete it also on IMAP server');?></label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Check for new messages interval in seconds.');?></label>
            <input type="text" placeholder="60" maxlength="250" class="form-control form-control-sm" name="sync_interval" value="<?php echo htmlspecialchars($item->sync_interval)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Import since this unix timestamp.');?> <button type="button" class="btn btn-xs btn-secondary" onclick="$('#id_import_since').val(Math.floor(Date.now()/1000))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Set to now');?></button></label>
            <input type="number" maxlength="250" class="form-control form-control-sm" id="id_import_since" name="import_since" value="<?php echo htmlspecialchars($item->import_since)?>" />
        </div>
    </div>
</div>

