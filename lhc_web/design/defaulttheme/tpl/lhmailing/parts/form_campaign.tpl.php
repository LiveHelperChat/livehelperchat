<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?>*</label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailbox');?>*</label>
            <input type="text" id="new-mailbox-id" autocomplete="new-password" value="<?php echo htmlspecialchars((string)$item->mailbox_front)?>" class="form-control form-control-sm" name="mailbox_id" list="mailbox_list">
            <datalist id="mailbox_list" autocomplete="new-password">
                <?php foreach (erLhcoreClassModelMailconvMailbox::getList(array('filter' => array('active' => 1))) as $mailbox) : ?>
                    <option value="<?php echo htmlspecialchars($mailbox->mail)?>"><?php echo htmlspecialchars($mailbox->name)?></option>
                <?php endforeach; ?>
            </datalist>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" <?php if ($item->id == null || erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['status' => erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING,'campaign_id' => $item->id]]) == 0) : $disabledCampaign = true;?>disabled<?php endif;?> name="enabled" value="on" <?php $item->enabled == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Activate campaign');?></label>
            <?php if (isset($disabledCampaign) && $disabledCampaign == true) : ?><div class="text-danger"><small><i>You will be able to activate campaign once you have at-least one recipient</i></small></div><?php endif; ?>
            <div><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Only once the campaign is activated we will start sending e-mails. Progress you can see in statistic tab.');?></i></small></div>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="as_active" value="on" <?php $item->as_active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','As active');?></label>
            <div><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Created ticket will be created as active one');?></i></small></div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label class="<?php ($item->starts_at > 0 && $item->starts_at < time()) ? print 'text-danger' : ''?> "><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Start sending at');?> <b><?php print date_default_timezone_get()?></b>, Current time - <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></label>
            <input class="form-control form-control-sm" name="starts_at" type="datetime-local" value="<?php echo date('Y-m-d\TH:i', $item->starts_at > 0 ? $item->starts_at : time())?>">
        </div>
        <?php if ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_PENDING) : ?>
            <div class="font-weight-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending, campaign has not started yet.');?></div>
        <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_IN_PROGRESS) : ?>
            <div class="font-weight-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress');?></div>
        <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_FINISHED) : ?>
            <label><input type="checkbox" name="activate_again" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Set campaign status to pending. E.g You can activate it again if you have added more recipients.');?></label>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Subject');?></label>
    <input type="text" class="form-control form-control-sm" name="subject" value="<?php echo htmlspecialchars($item->subject)?>" />
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to e-mail');?></label>
            <input type="text" placeholder="If not filled we will use mailbox e-mail" class="form-control form-control-sm" name="reply_email" value="<?php echo htmlspecialchars($item->reply_email)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to name');?></label>
            <input type="text" placeholder="If not filled we will use mailbox name" class="form-control form-control-sm" name="reply_name" value="<?php echo htmlspecialchars($item->reply_name)?>" />
        </div>
    </div>
</div>


<?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/body.tpl.php'));?>

<p><small><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/mailingcampaign'});" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Replaceable variables?');?></a></small></p>

