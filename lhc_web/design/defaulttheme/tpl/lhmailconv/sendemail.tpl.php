
<div class="d-flex flex-column m-0 p-0 h-100">
    <?php if ((!isset($Result['popup']) || $Result['popup'] === false) && isset($Result['path'])) :
        $pathElementCount = count($Result['path'])-1;
        if ($pathElementCount >= 0): ?>
            <div id="path-container" style="margin-left: -8px;margin-right: -7px" ng-non-bindable>
                <ul class="breadcrumb rounded-0 border-bottom p-2 mb-0" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
                    <li class="breadcrumb-item"><a rel="home" itemprop="url" href="<?php echo erLhcoreClassDesign::baseurl()?>"><span itemprop="title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Home')?></span></a></li>
                    <?php foreach ($Result['path'] as $key => $pathItem) : if (isset($pathItem['url']) && $pathElementCount != $key) { ?><li class="breadcrumb-item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="<?php echo $pathItem['url']?>" itemprop="url"><span itemprop="title"><?php echo htmlspecialchars(htmlspecialchars_decode($pathItem['title'],ENT_QUOTES))?></span></a></li><?php } else { ?><li class="breadcrumb-item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><?php echo htmlspecialchars(htmlspecialchars_decode($pathItem['title'], ENT_QUOTES))?></span></li><?php }; ?><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif;?>


<div>
    <?php if (isset($department) && $department instanceof erLhcoreClassModelDepartament) : ?>
        <span class="float-end badge mt-2 border border-secondary text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Active department used for default mailbox selection, guardrails (PII) checks, and response templates.');?>"><span class="material-icons">home</span><?php echo htmlspecialchars($department->name)?></span>
    <?php endif; ?>
<h1>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send an e-mail');?>
</h1>

</div>

<?php if (isset($updated)) :
    $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','E-mail was sent.');

    if (isset($outcome['copy']) && $outcome['copy']['success'] == true) {
        $msg .= ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Sent e-mail copy was created in a sent folder.');
    }
?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>

    <?php if (isset($outcome['copy'])) : ?>
        <?php if ($outcome['copy']['success'] === false) : ?>
            <?php $errors = [$outcome['copy']['reason']]; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : ?>
<div class="d-flex flex-wrap align-items-center">
<a class="btn btn-sm btn-outline-secondary me-2" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?><?php if (isset($uparams['var1'])) :?>/(var1)/<?php echo htmlspecialchars($uparams['var1'])?><?php endif;?><?php if (isset($uparams['var1'])) :?>/(var2)/<?php echo htmlspecialchars($uparams['var2'])?><?php endif;?><?php if (isset($chat) && $chat->id > 0) : ?>/(chat_id)/<?php echo $chat->id;?><?php endif; ?><?php if (isset($Result['popup']) && $Result['popup'] === true) : ?>/(layout)/popup<?php endif;?><?php if (isset($is_escalate) && $is_escalate === true) : ?>/(escalate)/1<?php endif;?>?new=1"><span class="material-icons">mail</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send a new e-mail');?></a>
<?php if (isset($copy_data)) : ?>
<form class="m-0" method="post" action="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?><?php if (isset($uparams['var1'])) :?>/(var1)/<?php echo htmlspecialchars($uparams['var1'])?><?php endif;?><?php if (isset($uparams['var1'])) :?>/(var2)/<?php echo htmlspecialchars($uparams['var2'])?><?php endif;?><?php if (isset($chat) && $chat->id > 0) : ?>/(chat_id)/<?php echo $chat->id;?><?php endif; ?><?php if (isset($Result['popup']) && $Result['popup'] === true) : ?>/(layout)/popup<?php endif;?><?php if (isset($is_escalate) && $is_escalate === true) : ?>/(escalate)/1<?php endif;?>">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <input type="hidden" name="copy_content" value="<?php echo htmlspecialchars($copy_data)?>" />
    <button type="submit" class="btn btn-sm btn-outline-secondary"><span class="material-icons">content_copy</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send a new e-mail, copy content');?></button>
</form>
<?php endif; ?>
<script>window.parent.postMessage('lhc_chat::mail_sent','*');</script>
</div>
<?php endif; ?>

<?php if (isset($updated) && isset($outcome['copy']['success']) && $outcome['copy']['success'] == true && isset($outcome['copy']['message_id'])) : ?>
    <div class="py-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Ticket');?>: <span id="ticket-progress"><span class="material-icons lhc-spin">cached</span><span id="ticket-progress-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Working');?>...</span></span></div>
    <script>
        (function intervalStarter() {
                var counter = 0;
                var scheduled = 0;
                var interval = setInterval(function () {
                $.postJSON(WWW_DIR_JAVASCRIPT + 'mailconv/geticketbymessageid/',{'copy_id' : <?php echo json_encode($outcome['copy']['copy_id'] ?: 0)?>, 'scheduled': scheduled, 'counter': counter, 'mailbox_id': <?php echo $item->mailbox_id?>, 'message_id': <?php echo json_encode($outcome['copy']['message_id'])?>}, function (data) {
                    if (data.found == true) {
                        $('#ticket-progress').html(data.conversation);
                        clearInterval(interval);
                    } else {
                        if (data.scheduled == 1) {
                            scheduled = 1;
                        }
                        $('#ticket-progress-text').html(data.progress);
                    }
                });
                counter = counter + 1;
            }, 2000);
        })();
    </script>
<?php endif; ?>

<?php if (!isset($updated)) : ?>

<form class="d-flex flex-column flex-grow-1 overflow-scroll position-relative" action="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?><?php if (isset($uparams['var1'])) :?>/(var1)/<?php echo htmlspecialchars($uparams['var1'])?><?php endif;?><?php if (isset($uparams['var1'])) :?>/(var2)/<?php echo htmlspecialchars($uparams['var2'])?><?php endif;?><?php if (isset($chat) && $chat->id > 0) : ?>/(chat_id)/<?php echo $chat->id;?><?php endif; ?><?php if (isset($Result['popup']) && $Result['popup'] === true) : ?>/(layout)/popup<?php endif;?><?php if (isset($is_escalate) && $is_escalate === true) : ?>/(escalate)/1<?php endif;?>" id="sendemail-form" ng-non-bindable method="post" autocomplete="new-password"
      data-msg-mailbox-required="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Please select a mailbox first');?>"
      data-msg-signature-failed="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Failed to fetch signature');?>"
      data-msg-signature-error="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Error fetching signature');?>">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <div class="input-group input-group-sm">
            <input type="text" id="new-mailbox-id" autocomplete="new-password" value="<?php echo htmlspecialchars((string)$item->mailbox_front)?>" class="form-control form-control-sm" name="mailbox_id" list="mailbox_list">
            <button class="btn btn-outline-secondary" type="button" id="append-signature-btn" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Append mailbox signature');?>">
                <span class="material-icons me-0">draw</span>
            </button>
        </div>
        <datalist id="mailbox_list" autocomplete="new-password">
            <?php foreach (erLhcoreClassModelMailconvMailbox::getList(array('filter' => array('active' => 1))) as $mailbox) : ?>
                <option value="<?php echo htmlspecialchars($mailbox->mail)?>"><?php echo htmlspecialchars($mailbox->name)?></option>
            <?php endforeach; ?>
        </datalist>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Subject');?></label>
        <input type="text" class="form-control form-control-sm" name="subject" value="<?php echo htmlspecialchars($item->subject)?>" />
    </div>

    <div class="row me-0 ms-0">
        <div class="col-6 ps-0">
            <div class="form-group">
                <label><?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/recipient_email_title.tpl.php')); ?> </label>
                <div class="input-group input-group-sm">
                    <?php if (isset($chat) && (!isset($is_escalate) || $is_escalate === false) && !erLhcoreClassUser::instance()->hasAccessTo('lhchat','chat_see_unhidden_email')) : ?>
                        <input type="text" readonly="readonly" class="form-control form-control-sm" name="from_address" value="<?php echo htmlspecialchars(\LiveHelperChat\Helpers\Anonymizer::maskEmail($item->from_address))?>" />
                    <?php else : ?>
                        <input type="text" class="form-control form-control-sm" name="from_address" value="<?php echo htmlspecialchars($item->from_address)?>" />
                    <?php endif; ?>
                    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','send_mail_bcc_cc')) : ?>
                        <button type="button" class="btn btn-outline-secondary border-secondary-control btn-add-recipient" data-target="#cc-container"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Add CC');?></button>
                        <button type="button" class="btn btn-outline-secondary border-secondary-control btn-add-recipient" data-target="#bcc-container"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Add BCC');?></button>
                    <?php else : ?>
                        <span class="d-inline-block" tabindex="0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','You do not have permission to add custom CC/BCC recipients');?>">
                            <button type="button" class="btn btn-outline-secondary border-secondary-control" style="pointer-events:none" disabled><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Add CC');?></button>
                        </span>
                        <span class="d-inline-block" tabindex="0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','You do not have permission to add custom CC/BCC recipients');?>">
                            <button type="button" class="btn btn-outline-secondary border-secondary-control" style="pointer-events:none" disabled><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Add BCC');?></button>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-6 pe-0">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Recipient Name');?></label>
                <input type="text" class="form-control form-control-sm" name="from_name" value="<?php echo htmlspecialchars($item->from_name)?>" />
            </div>
        </div>
    </div>

    <div class="row me-0 ms-0">
        <div class="col-6 ps-0">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to e-mail');?></label>
                <input autocomplete="new-password" list="mailbox_list" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','If not filled we will use mailbox e-mail.');?> <?php if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','reply_to_all')) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Only defined mailbox are allowed.');?><?php endif;?>" class="form-control form-control-sm" name="to_data" value="<?php echo htmlspecialchars($item->to_data)?>" />
            </div>
        </div>
        <div class="col-6 pe-0">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to name');?></label>
                <input autocomplete="new-password" type="text" placeholder="If not filled we will use mailbox name" class="form-control form-control-sm" name="reply_to_data" value="<?php echo htmlspecialchars($item->reply_to_data)?>" />
            </div>
        </div>
    </div>

    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','send_mail_bcc_cc')) : ?>
    <div class="row me-0 ms-0">
        <div class="col-6 ps-0">
            <?php $ccRecipients = $item->cc_data_array; ?>
            <div class="form-group recipient-section<?php if (empty($ccRecipients)) : ?> d-none<?php endif; ?>">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Cc');?></label>
                <div id="cc-container" data-field="cc">
                    <?php foreach ($ccRecipients as $recipient) : ?>
                        <div class="input-group input-group-sm mb-1 recipient-row">
                            <input type="text" class="form-control form-control-sm" name="cc[]" value="<?php echo htmlspecialchars($recipient['email'])?>" />
                            <button type="button" class="btn btn-remove-recipient" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Remove recipient');?>"><span class="material-icons me-0">delete</span></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-6 pe-0">
            <?php $bccRecipients = $item->bcc_data_array; ?>
            <div class="form-group recipient-section<?php if (empty($bccRecipients)) : ?> d-none<?php endif; ?>">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Bcc');?></label>
                <div id="bcc-container" data-field="bcc">
                    <?php foreach ($bccRecipients as $recipient) : ?>
                        <div class="input-group input-group-sm mb-1 recipient-row">
                            <input type="text" class="form-control form-control-sm" name="bcc[]" value="<?php echo htmlspecialchars($recipient['email'])?>" />
                            <button type="button" class="btn btn-remove-recipient" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Remove recipient');?>"><span class="material-icons me-0">delete</span></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex-grow-1 position-relative">
        <?php $tinyMceOptions = ['hide_form_group' => true,'height' => '\'100%\'']; if (isset($chat) && $chat->id > 0) { $tinyMceOptions['chat_id'] = (int)$chat->id; } ?>
        <?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/body.tpl.php'));?>
    </div>

    <div>
        <input type="hidden" name="send_status" id="id_send_status" value="0">
        <div class="btn-group mt-2">
            <button name="SendEmail" onclick="$('.send-buttons').attr('disabled','disabled').text('Sending...');$('#sendemail-form').submit()" class="send-buttons btn btn-sm btn-primary" type="submit"><i class="material-icons">send</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send as closed');?></button>
            <button name="SendEmailActive" onclick="$('.send-buttons').attr('disabled','disabled').text('Sending...');$('#id_send_status').val(1);$('#sendemail-form').submit()" class="send-buttons btn btn-sm btn-outline-secondary" type="submit"><i class="material-icons text-success">send</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send as Active');?></button>
        </div>
    </div>

</form>

<div class="d-none" id="recipient-row-prototype">
    <div class="input-group input-group-sm mb-1 recipient-row">
        <input type="text" class="form-control form-control-sm" name="cc[]" value="" />
        <button type="button" class="btn btn-remove-recipient" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Remove recipient');?>"><span class="material-icons me-0">delete</span></button>
    </div>
</div>

<?php endif; ?>
</div>
