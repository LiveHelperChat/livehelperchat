
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

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send an e-mail');?></h1>

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
<a class="btn btn-sm btn-outline-secondary" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?><?php if (isset($uparams['var1'])) :?>/(var1)/<?php echo htmlspecialchars($uparams['var1'])?><?php endif;?><?php if (isset($uparams['var1'])) :?>/(var2)/<?php echo htmlspecialchars($uparams['var2'])?><?php endif;?><?php if (isset($chat) && $chat->id > 0) : ?>/(chat_id)/<?php echo $chat->id;?><?php endif; ?><?php if (isset($Result['popup']) && $Result['popup'] === true) : ?>/(layout)/popup<?php endif;?>?new=1"><span class="material-icons">mail</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send a new e-mail');?></a>
<script>window.parent.postMessage('lhc_chat::mail_sent','*');</script>
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

<form class="d-flex flex-column flex-grow-1 overflow-scroll position-relative" action="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?><?php if (isset($uparams['var1'])) :?>/(var1)/<?php echo htmlspecialchars($uparams['var1'])?><?php endif;?><?php if (isset($uparams['var1'])) :?>/(var2)/<?php echo htmlspecialchars($uparams['var2'])?><?php endif;?><?php if (isset($chat) && $chat->id > 0) : ?>/(chat_id)/<?php echo $chat->id;?><?php endif; ?><?php if (isset($Result['popup']) && $Result['popup'] === true) : ?>/(layout)/popup<?php endif;?>" id="sendemail-form" ng-non-bindable method="post" autocomplete="new-password">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <input type="text" id="new-mailbox-id" autocomplete="new-password" value="<?php echo htmlspecialchars((string)$item->mailbox_front)?>" class="form-control form-control-sm" name="mailbox_id" list="mailbox_list">
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
                <?php if (isset($chat) && !erLhcoreClassUser::instance()->hasAccessTo('lhchat','chat_see_unhidden_email')) : ?>
                    <input type="text" readonly="readonly" class="form-control form-control-sm" name="from_address" value="<?php echo htmlspecialchars(\LiveHelperChat\Helpers\Anonymizer::maskEmail($item->from_address))?>" />
                <?php else : ?>
                    <input type="text" class="form-control form-control-sm" name="from_address" value="<?php echo htmlspecialchars($item->from_address)?>" />
                <?php endif; ?>
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
                <input list="mailbox_list" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','If not filled we will use mailbox e-mail.');?> <?php if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','reply_to_all')) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Only defined mailbox are allowed.');?><?php endif;?>" class="form-control form-control-sm" name="to_data" value="<?php echo htmlspecialchars($item->to_data)?>" />
            </div>
        </div>
        <div class="col-6 pe-0">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to name');?></label>
                <input type="text" placeholder="If not filled we will use mailbox name" class="form-control form-control-sm" name="reply_to_data" value="<?php echo htmlspecialchars($item->reply_to_data)?>" />
            </div>
        </div>
    </div>

    <div class="flex-grow-1 position-relative">
        <?php $tinyMceOptions = ['hide_form_group' => true,'height' => '\'100%\'']; ?>
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
<?php endif; ?>
</div>
