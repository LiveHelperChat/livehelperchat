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
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Ticket owner workflow');?></label>
            <select name="owner_logic" class="form-control form-control-sm" onchange="$(this).val() == <?php echo erLhcoreClassModelMailconvMailingCampaign::OWNER_USER?> ? $('#id_owner_user_id').show() : $('#id_owner_user_id').hide()">
                <option <?php if ($item->owner_logic == erLhcoreClassModelMailconvMailingCampaign::OWNER_CREATOR) : ?>selected="selected"<?php endif; ?> value="<?php echo erLhcoreClassModelMailconvMailingCampaign::OWNER_CREATOR?>">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Campaign creator will be an owner of the ticket');?>
                    <?php if ($item->user_id > 0) : ?>
                        <?php if (erLhcoreClassUser::instance()->getUserID() == $item->user_id) : ?>&nbsp;(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Me');?>)<?php else : ?> <?php echo ' '.htmlspecialchars($item->user)?><?php endif; ?>
                    <?php endif;?>
                </option>
                <option <?php if ($item->owner_logic == erLhcoreClassModelMailconvMailingCampaign::OWNER_DEFAULT) : ?>selected="selected"<?php endif; ?> value="<?php echo erLhcoreClassModelMailconvMailingCampaign::OWNER_DEFAULT?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Ticket will follow standard mailbox rules');?></option>
                <option <?php if ($item->owner_logic == erLhcoreClassModelMailconvMailingCampaign::OWNER_USER) : ?>selected="selected"<?php endif; ?> value="<?php echo erLhcoreClassModelMailconvMailingCampaign::OWNER_USER?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Selected user will be assigned as ticket owner');?></option>
            </select>
        </div>
    </div>
    <div class="col-6" id="id_owner_user_id" <?php if ($item->owner_logic != erLhcoreClassModelMailconvMailingCampaign::OWNER_USER) : ?>style="display: none"<?php endif; ?> >
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Dedicated ticket user');?></label>
        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
            'input_name'     => 'owner_user_id',
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose user'),
            'selected_id'    => $item->owner_user_id,
            'type'           => 'radio',
            'data_prop'      => 'data-limit="1"',
            'css_class'      => 'form-control',
            'display_name'   => 'name_official',
            'show_optional'  => false,
            'no_selector'  => true,
            'list_function_params' => array('limit' => false,'sort' => '`name` ASC'),
            'list_function'  => 'erLhcoreClassModelUser::getList',
        )); ?>
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
            <div class="fw-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending, campaign has not started yet.');?></div>
        <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_IN_PROGRESS) : ?>
            <div class="fw-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress');?></div>
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
<script>
    $(function() {
        $('.btn-block-department').makeDropdown();
    });
</script>

<?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/body.tpl.php'));?>

<p><small><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/mailingcampaign'});" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Replaceable variables?');?></a></small></p>

