<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Simplified output')?></h5>

<ul>

    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Message seen')?>: <strong><?php echo (int)$online_user->message_seen?></strong> (1 = already seen, 0 = not seen)</li>
    
    
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Message seen ago')?>: <strong><?php echo $online_user->message_seen_ts > 0 ? erLhcoreClassChat::formatSeconds(time() - $online_user->message_seen_ts) : '-';?></strong></li>

    <?php
    $canReshow = false;
    if ($online_user->message_seen == 0) {
        $canReshow = true;
    } elseif ($online_user->message_seen_ts > 0) {
        $msgTimeout = (int)erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value;
        $resetByTimeout = ($msgTimeout > 0 && $online_user->message_seen_ts < (time() - ($msgTimeout * 3600)));
        $resetByNextIvt = (isset($online_user->online_attr_system_array['lhcnxt_ivt']) && $online_user->online_attr_system_array['lhcnxt_ivt'] > 0 && $online_user->message_seen_ts < (time() - $online_user->online_attr_system_array['lhcnxt_ivt']));
        $canReshow = ($resetByTimeout || $resetByNextIvt);
    }
    $invExpired = (isset($online_user->online_attr_system_array['lhcinv_exp']) && $online_user->online_attr_system_array['lhcinv_exp'] > 0 && $online_user->online_attr_system_array['lhcinv_exp'] < time());
    $canShow = ($canReshow && $online_user->operator_message != '' && !$invExpired);

    // Predict whether the invitation will be triggered on the next run
    if ($invExpired) {
        // Expiry handler (has_message_from_operator) will clear operator_message + reset state on next access;
        // processProActiveInvitation will then run but message_seen resets to 1 – depends on reshow timeout
        $nextRunTrigger = false;
        $nextRunReason = 'invitation expired – state will be reset on next access, reshow depends on timeout';
    } elseif ($online_user->message_seen == 0 && $online_user->operator_message != '') {
        // Invitation is queued and not yet seen – widget will display it immediately
        $nextRunTrigger = true;
        $nextRunReason = 'invitation already queued, will be shown to visitor';
    } elseif ($online_user->message_seen == 1 && $canReshow) {
        // Visitor already saw it; timeout has passed – processProActiveInvitation will pass the
        // invitation_was_seen gate and reshow the invitation
        $nextRunTrigger = true;
        $nextRunReason = 'timeout passed – eligible for reshow on next run';
    } elseif ($online_user->message_seen == 1 && !$canReshow) {
        // Timeout has NOT passed – processProActiveInvitation will skip via invitation_was_seen check
        $nextRunTrigger = false;
        $nextRunReason = 'timeout not passed – will be skipped (invitation_was_seen)';
    } else {
        // No invitation assigned yet – outcome depends on whether eligible invitations exist in the system
        $nextRunTrigger = true;
        $nextRunReason = 'no invitation assigned – depends on eligible invitations in the system';
    }
    ?>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Invitation can be shown')?>:
        <strong>
        <?php if ($canShow) : ?>
            <span class="text-success">YES</span>
        <?php else: ?>
            <span class="text-danger">NO</span>
        <?php endif; ?>
        </strong>
        <small class="text-muted">(msg_seen=<?php echo (int)$online_user->message_seen?>, can_reshow=<?php echo $canReshow ? 'Y' : 'N'?>, operator_message=<?php echo $online_user->operator_message != '' ? 'Y' : 'N'?>, expired=<?php echo $invExpired ? 'Y' : 'N'?>)</small>
    </li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Will trigger on next run')?>:
        <strong>
        <?php if ($nextRunTrigger) : ?>
            <span class="text-success">YES</span>
        <?php else: ?>
            <span class="text-danger">NO</span>
        <?php endif; ?>
        </strong>
        <small class="text-muted">(<?php echo htmlspecialchars($nextRunReason)?>)</small>
    </li>


    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Has active message from operator')?>: <strong><?php echo $online_user->has_message_from_operator ? 'Y' : 'N';?></strong></li>

    <?php if (erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value != 1) : ?>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Proactive invitations is')?>&nbsp;<span class="badge bg-danger">disabled</span>&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/listchatconfig')?>#onlinetracking"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','change it.')?></a></li>
    <?php endif; ?>

    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Valid invitations found')?> - <?php echo count($debug_invitation['rows']); ?> [<?php echo implode(',',array_keys($debug_invitation['rows']))?>]</li>

    <?php if (isset($debug_invitation['no_messages'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','No valid messages were found from candidates')?></li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['no_online_ops'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of no online operators')?> - [<?php echo implode(',',array_keys($debug_invitation['no_online_ops']))?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['invitation_was_seen'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because invitation was seen')?> - <span class="badge bg-secondary">[<?php echo implode(',',$debug_invitation['invitation_was_seen'])?>]</span></li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['last_visit_prev'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Last time seen on website ago')?>.</span> - [<?php echo implode(',',array_keys($debug_invitation['last_visit_prev']))?>], <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','conditions')?> - [<?php echo implode(',',$debug_invitation['last_visit_prev_cond'])?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['last_chat_time'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Last time had chat n minutes ago')?>.</span> - [<?php echo implode(',',array_keys($debug_invitation['last_chat_time']))?>], <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','conditions')?> - [<?php echo implode(',',$debug_invitation['last_chat_time_cond'])?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['conditions_not_valid'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Attributes conditions not valid')?></span> - [<?php echo implode(',',array_keys($debug_invitation['conditions_not_valid']))?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['message_selected'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Message selected')?> - <?php echo $debug_invitation['message_selected']->id?>
            <ul>
                <?php if (isset($debug_invitation['conditions_unsatisfied'])) : ?>
                    <li><span class="badge bg-danger">conditions_unsatisfied</span></li>
                <?php endif; ?>

                <?php if (isset($debug_invitation['message_approved'])) : ?>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Attributes online visitor')?> - <?php echo json_encode($debug_invitation['message_approved'])?></li>
                <?php endif; ?>

                <?php if (isset($debug_invitation['time_on_site_missmatch'])) : ?>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Time on site')?></span> - [<?php echo implode(',',array_keys($debug_invitation['time_on_site_missmatch']))?>], <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','conditions')?> - [<?php echo implode(',',$debug_invitation['time_on_site_missmatch_cond'])?>]</li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
</ul>

<div class="mx550">
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Online user output')?></h5>
    <pre class="fs11">
    <?php echo json_encode($online_user,JSON_PRETTY_PRINT); ?>
    </pre>
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Raw output debug')?></h5>
    <pre class="fs11">
    <?php echo json_encode($debug_invitation,JSON_PRETTY_PRINT); ?>
    </pre>
</div>