<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send a message to the user') ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Message was sent to the user'); ?>

<script>
<?php if (isset($start_chat)) : ?>
$('#myModal').modal('hide');
lhinst.startChat("<?php echo $chat->id?>",$('#tabs'),<?php echo json_encode((string)$chat->nick,JSON_HEX_APOS)?>);
<?php else : ?>
setTimeout(function() {
    $('#myModal').modal('hide');
},2000);
<?php endif; ?>

</script>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','If the message was already sent, this will mark it as not delivered and the user will be shown the chat message again.');?></p>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $visitor->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

	<textarea class="form-control form-group" name="Message" id="sendMessageContent" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the user');?>"><?php echo htmlspecialchars($visitor->operator_message) ?></textarea>

    <label><input type="checkbox" name="FullWidget" value="on" <?php (isset($visitor->online_attr_system_array['lhc_full_widget']) && $visitor->online_attr_system_array['lhc_full_widget'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Open full widget for the visitor')?></label>

    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/send_order.tpl.php'));?>

    <ul class="nav nav-tabs mb-2" role="tablist">
        <?php foreach ($sendMessageOrder as $inviteType => $inviteOption) : ?>
            <?php if ($inviteType == 'invite') : ?>
                <li role="presentation" class="nav-item" ><a class="<?php if ($inviteOption['active'] === true) :?>active<?php endif;?> nav-link" href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Invitation')?></a></li>
            <?php elseif ($inviteType == 'chat') : ?>
                <li role="presentation" class="nav-item" ><a class="<?php if ($inviteOption['active'] === true) :?>active<?php endif;?> nav-link" href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat')?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane<?php if ($sendMessageOrder['invite']['active'] == true) : ?> active<?php endif; ?>" id="panel1">
            <div class="row form-group">
                <div class="col-6"><label><input type="checkbox" name="AssignToMe" value="on" <?php (isset($visitor->online_attr_system_array['lhc_assign_to_me']) && $visitor->online_attr_system_array['lhc_assign_to_me'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Assign the chat to me if the visitor replies')?></label></div>
                <div class="col-6"><label><input type="checkbox" name="IgnoreAutoresponder" value="on" <?php (isset($visitor->online_attr_system_array['lhc_ignore_autoresponder']) && $visitor->online_attr_system_array['lhc_ignore_autoresponder'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Do not send automated messages if the customer replies')?></label></div>
                <div class="col-6"><label><input type="checkbox" name="RequiresEmail" value="on" <?php $visitor->requires_email == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires e-mail')?></label></div>
                <div class="col-6"><label><input type="checkbox" name="RequiresUsername" value="on" <?php $visitor->requires_username == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires name')?></label></div>
                <div class="col-6"><label><input type="checkbox" name="RequiresPhone" value="on" <?php $visitor->requires_phone == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires phone')?></label></div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Campaign')?></label>
                    <select name="CampaignId" id="id_CampaignMessage-<?php echo $chat->id?>" class="form-control" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').attr('data-msg') : '');">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select campaign')?></option>
                        <?php foreach (erLhAbstractModelProactiveChatCampaign::getList() as $item) : ?>
                            <option value="<?php echo $item->id?>" data-msg="<?php echo htmlspecialchars(str_replace('{nick}', (isset($chat) ? $chat->nick : ''), $item->text))?>"><?php echo htmlspecialchars($item->name)?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="col-6">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Canned message')?></label>
                    <select class="form-control" id="id_CannedMessage-<?php echo $chat->id?>" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').text() : '');">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message')?></option>
                        <?php foreach (erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,erLhcoreClassUser::instance()->getUserID()) as $item) : ?>
                            <option value="<?php echo $item->id?>"><?php echo htmlspecialchars(str_replace('{nick}', (isset($chat) ? $chat->nick : ''), $item->msg))?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <input type="hidden" id="id_SendMessage" name="SendMessage" value="1" />
            <hr>
            <input type="submit" class="btn btn-secondary btn-sm" name="SendMessage" onclick="$('#id_SendMessage').val(1)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message');?>" />
        </div>
        <div role="tabpanel" class="tab-pane<?php if ($sendMessageOrder['chat']['active'] == true) : ?> active<?php endif; ?>" id="panel2">

            <?php if ($visitor->chat instanceof erLhcoreClassModelChat) : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Visitor have assigned chat with id');?>: <?php echo $visitor->chat_id?></p>

                <?php if ($visitor->chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $visitor->chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Chat is in pending/active state.')?></p>
                <?php elseif ($visitor->chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT): ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Chat is in bot status.')?></p>
                <?php elseif ($visitor->chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT): ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Chat is closed, we will start a new chat.')?></p>
                <?php endif; ?>

                <?php if ($visitor->chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT && in_array($visitor->chat->status_sub,array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) : ?>
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','We will initiate a new chat, because visitor has ended previous chat already. Is in survey/closed chat/filling contact form')?></p>
                <?php endif; ?>

            <?php else : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Visitor does not have any chat. We will initiate a new chat.');?></p>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
                        <?php $params = array (
                            'input_name'     => 'DepartmentID',
                            'display_name'   => 'name',
                            'css_class'      => 'form-control form-control-sm',
                            'selected_id'    => 0,
                            'list_function'  => 'erLhcoreClassModelDepartament::getList',
                            'list_function_params'  => array_merge(array('limit' => '1000000'),$limitDepartments)
                        );
                        echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
                    </div>
                </div>
            </div>


            <input type="submit" class="btn btn-sm btn-secondary" name="SendMessageStart" onclick="$('#id_SendMessage').val(2)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message and start a chat');?>" />
        </div>
    </div>

</form>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>