<div class="row">
    <div class="<?php if (!(isset($sendNoticeParams['mode']) && $sendNoticeParams['mode'] == 'mass')) : ?>col-12<?php else : ?>col-8<?php endif; ?>">
        <textarea required class="form-control form-group" name="Message" id="sendMessageContent" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the user');?>"><?php if (isset($visitor)) : ?><?php echo htmlspecialchars($visitor->operator_message) ?><?php endif; ?></textarea>
    </div>
    <?php if (isset($sendNoticeParams['mode']) && $sendNoticeParams['mode'] == 'mass') : ?>
    <div class="col-4">
        <textarea class="form-control form-group" id="sendToUsernames" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','You can paste usernames separated by a new line.');?>" name="SendToUsernames"></textarea>
    </div>
    <?php endif;?>
</div>


<div class="row">
    <div class="col-6">
        <label><input type="checkbox" name="FullWidget" value="on" <?php (isset($visitor->online_attr_system_array['lhc_full_widget']) && $visitor->online_attr_system_array['lhc_full_widget'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Open full widget for the visitor')?></label>
    </div>
    <div class="col-6">
        <label><input type="checkbox" name="IgnoreBot" value="on" <?php (isset($visitor->online_attr_system_array['lhc_ignore_bot']) && $visitor->online_attr_system_array['lhc_ignore_bot'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Skip bot')?></label>
    </div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/send_order.tpl.php'));?>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Invitation expire time, after that period of time invitation will be hidden.');?><?php if (isset($visitor->online_attr_system_array['lhcinv_exp']) && $visitor->online_attr_system_array['lhcinv_exp'] > 0) : ?>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Last sent invitation expires in');?><br/><span class="badge bg-secondary"><?php echo erLhcoreClassChat::formatSeconds( (int)$visitor->online_attr_system_array['lhcinv_exp'] - time());?></span><?php endif;?>

        <?php if (isset($visitor) && $visitor->message_seen == 1) : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','last invitation was seen');?> <span class="badge bg-success"><?php echo erLhcoreClassChat::formatSeconds( time() - (int)$visitor->message_seen_ts);?></span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','ago');?>.
        <?php endif; ?>

        <?php if (isset($visitor) && $visitor->has_message_from_operator) : ?>
            <span class="badge bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','has active invitation');?></span>
        <?php else : ?>
            <span class="badge bg-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','do not have any active invitation');?></span>
        <?php endif; ?>

    </label>

    <select class="form-control form-control-sm" name="InvitationExpire">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Permanent (visitor has to close invitation)');?></option>
        <option value="60">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
        <option value="300">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="600">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="1800">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="3600">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
        <option value="7200">2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="14400">4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="28800">8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="57600">16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="86400">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
    </select>
</div>

<ul class="nav nav-tabs mb-2" role="tablist">
    <?php foreach ($sendMessageOrder as $inviteType => $inviteOption) : ?>
        <?php if ($inviteType == 'invite') : ?>
            <li role="presentation" class="nav-item" ><a class="<?php if ($inviteOption['active'] === true) :?>active<?php endif;?> nav-link" href="#panel1" aria-controls="panel1" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Invitation')?></a></li>
        <?php elseif ($inviteType == 'chat') : ?>
            <li role="presentation" class="nav-item" ><a class="<?php if ($inviteOption['active'] === true) :?>active<?php endif;?> nav-link" href="#panel2" aria-controls="panel2" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat')?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane<?php if ($sendMessageOrder['invite']['active'] == true) : ?> active<?php endif; ?>" id="panel1">
        <div class="row form-group">
            <div class="col-6"><label><input type="checkbox" name="AssignToMe" value="on" <?php (isset($visitor->online_attr_system_array['lhc_assign_to_me']) && $visitor->online_attr_system_array['lhc_assign_to_me'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Assign the chat to me if the visitor replies')?></label></div>
            <div class="col-6"><label><input type="checkbox" name="IgnoreAutoresponder" value="on" <?php (isset($visitor->online_attr_system_array['lhc_ignore_autoresponder']) && $visitor->online_attr_system_array['lhc_ignore_autoresponder'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Do not send automated messages if the customer replies')?></label></div>
            <div class="col-6"><label><input type="checkbox" name="RequiresEmail" value="on" <?php isset($visitor) && $visitor->requires_email == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires e-mail')?></label></div>
            <div class="col-6"><label><input type="checkbox" name="RequiresUsername" value="on" <?php isset($visitor) && $visitor->requires_username == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires name')?></label></div>
            <div class="col-6"><label><input type="checkbox" name="RequiresPhone" value="on" <?php isset($visitor) && $visitor->requires_phone == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires phone')?></label></div>
        </div>

        <div class="row">
            <div class="col-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Campaign')?></label>
                <select name="CampaignId" class="form-control form-control-sm" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').attr('data-msg') : '');">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select campaign')?></option>
                    <?php foreach (erLhAbstractModelProactiveChatCampaign::getList() as $item) : ?>
                        <option value="<?php echo $item->id?>" data-msg="<?php echo htmlspecialchars(str_replace('{nick}', (isset($chat) ? $chat->nick : ''), $item->text))?>"><?php echo htmlspecialchars($item->name)?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Canned message')?></label>
                <select class="form-control form-control-sm" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').text() : '');">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message')?></option>
                    <?php

                    $grouped = erLhcoreClassModelCannedMsg::groupItems(erLhcoreClassModelCannedMsg::getCannedMessages((isset($visitor) ? $visitor->dep_id : 0), erLhcoreClassUser::instance()->getUserID()), (isset($visitor) ? $visitor : null), erLhcoreClassUser::instance()->getUserData(true));
                    $itemsCanned = ezcQuery::arrayFlatten($grouped);

                    foreach ($itemsCanned as $item) : ?>
                        <option value="<?php echo $item->id?>"><?php echo htmlspecialchars($item->msg_to_user)?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <input type="hidden" id="id_SendMessage" name="SendMessage" value="1" />
        <hr>
        <?php if (!(isset($sendNoticeParams['mode']) && $sendNoticeParams['mode'] == 'mass')) : ?>
            <input type="submit" class="btn btn-secondary btn-sm modal-submit-disable" name="SendMessage" onclick="$('#id_SendMessage').val(1)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message');?>" />
        <?php else : ?>
            <button type="submit" class="btn btn-secondary btn-sm modal-submit-disable" onclick="$('#id_SendMessage').val(1)" name="updateBotSettings"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send message to')?> (<span id="mass-receiver-count"></span>) <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','visitors')?></button>
        <?php endif;?>

    </div>
    <div role="tabpanel" class="tab-pane<?php if ($sendMessageOrder['chat']['active'] == true) : ?> active<?php endif; ?>" id="panel2">

        <?php if (!(isset($sendNoticeParams['mode']) && $sendNoticeParams['mode'] == 'mass')) : ?>
        <?php if (isset($visitor) && $visitor->chat instanceof erLhcoreClassModelChat) : ?>
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
        <?php endif; ?>


        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <?php if (isset($sendNoticeParams['mode']) && $sendNoticeParams['mode'] == 'mass') : ?>
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default department if visitor does not have assigned one');?></label>
                    <?php else : ?>
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
                    <?php endif; ?>

                    <?php $params = array (
                        'input_name'     => 'DepartmentID',
                        'display_name'   => 'name',
                        'css_class'      => 'form-control form-control-sm',
                        'selected_id'    => (isset($visitor) ? $visitor->dep_id : 0),
                        'list_function'  => 'erLhcoreClassModelDepartament::getList',
                        'list_function_params'  => array_merge(array('limit' => '1000000'),(isset($limitDepartments) ? $limitDepartments : $departmentParams))
                    );
                    echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
                </div>
            </div>
        </div>

        <?php if (!(isset($sendNoticeParams['mode']) && $sendNoticeParams['mode'] == 'mass')) : ?>
            <input type="submit" class="btn btn-sm btn-secondary modal-submit-disable" name="SendMessageStart" onclick="$('#id_SendMessage').val(2)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message and start a chat');?>" />
        <?php else : ?>
            <button type="submit" class="btn btn-secondary btn-sm modal-submit-disable" onclick="$('#id_SendMessage').val(2)" name="updateBotSettings"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send the message and start a chat')?> (<span id="mass-receiver-count-chat"></span>) <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','visitors')?></button>
        <?php endif; ?>

    </div>
</div>