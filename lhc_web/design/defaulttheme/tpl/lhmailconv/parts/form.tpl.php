<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Main settings');?></h5>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail');?>*</label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="mail" value="<?php echo htmlspecialchars($item->mail)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','From name');?>*</label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
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
            <input type="text" placeholder="tls://smtp.gmail.com" maxlength="250" class="form-control form-control-sm" name="host" value="<?php echo htmlspecialchars($item->host)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Port');?>*</label>
            <input type="text" placeholder="587" maxlength="250" class="form-control form-control-sm" name="port" value="<?php echo htmlspecialchars($item->port)?>" />
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
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Password');?>*</label>
            <input type="password" maxlength="250" class="form-control form-control-sm" autocomplete="new-password" name="password" value="<?php echo htmlspecialchars($item->password)?>" />
        </div>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','IMAP Server address');?>*</label>
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
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="create_a_copy" value="on" <?php $item->create_a_copy == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Create a copy in a send folder.');?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="assign_parent_user" value="on" <?php $item->assign_parent_user == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Assign follow-up e-mail to the previous thread owner');?></label>
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
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Timeout in days after last response before we create a new issue');?></label>
            <input type="number" maxlength="250" class="form-control form-control-sm" name="reopen_timeout" value="<?php echo htmlspecialchars($item->reopen_timeout)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Import priority. If mail is send to two mailbox and we handle both, only mail with higher mailbox priority will be processed.');?></label>
            <input type="number" maxlength="250" class="form-control form-control-sm" name="import_priority" value="<?php echo htmlspecialchars($item->import_priority)?>" />
        </div>
    </div>
</div>

