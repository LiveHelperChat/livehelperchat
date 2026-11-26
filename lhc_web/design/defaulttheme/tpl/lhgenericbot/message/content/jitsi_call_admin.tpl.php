<?php
$jitsiStatus = $metaMessage['status'] ?? erLhcoreClassJitsi::STATUS_INVITED;
$jitsiMode = ($metaMessage['mode'] ?? erLhcoreClassJitsi::MODE_VIDEO) === erLhcoreClassJitsi::MODE_AUDIO ? 'audio' : 'video';
$jitsiMsgId = (int)$msg['id'];
$jitsiChatId = isset($chat) && $chat instanceof erLhcoreClassModelChat ? (int)$chat->id : (int)($msg['chat_id'] ?? 0);
$jitsiDuration = erLhcoreClassJitsi::getDurationSeconds($metaMessage);
$jitsiStateKey = erLhcoreClassJitsi::getStateKey($metaMessage);
$jitsiIsActive = in_array($jitsiStatus, [erLhcoreClassJitsi::STATUS_INVITED, erLhcoreClassJitsi::STATUS_ACCEPTED], true);
?>
<div class="msg-body text-dark bg-white rounded p-3 shadow-sm border me-2 my-1" style="width: 270px" id="jitsi-admin-status-<?php echo $jitsiMsgId; ?>" data-state-key="<?php echo htmlspecialchars($jitsiStateKey); ?>">
    <h6 class="text-center mb-2">
        <i class="material-icons fs16 align-middle"><?php echo $jitsiMode === 'video' ? 'videocam' : 'call'; ?></i>
        <?php if ($jitsiMode === 'video') : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Jitsi Video Call'); ?>
        <?php else : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Jitsi Voice Call'); ?>
        <?php endif; ?>
    </h6>

    <?php if ($jitsiStatus === erLhcoreClassJitsi::STATUS_INVITED) : ?>
        <p class="text-center text-muted fs13 mb-2">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Waiting for visitor to accept...'); ?>
        </p>
        <div class="d-grid gap-1">
            <button type="button" onclick="lhinst.jitsiJoinOperator(<?php echo $jitsiChatId; ?>, <?php echo $jitsiMsgId; ?>)" class="btn btn-sm btn-outline-primary">
                <i class="material-icons fs14 align-middle"><?php echo $jitsiMode === 'video' ? 'videocam' : 'call'; ?></i>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Join room'); ?>
            </button>
            <button type="button" onclick="lhinst.jitsiCancel(<?php echo $jitsiChatId; ?>, <?php echo $jitsiMsgId; ?>, 'cancel')" class="btn btn-sm btn-warning">
                <i class="material-icons fs14 align-middle">phone_disabled</i>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Cancel invitation'); ?>
            </button>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_ACCEPTED) : ?>
        <div class="<?php echo !empty($metaMessage['op_joined']) ? 'bg-success text-white' : 'bg-warning text-dark'; ?> rounded p-2 text-center fs13 mb-2">
            <i class="material-icons fs14 align-middle"><?php echo $jitsiMode === 'video' ? 'videocam' : 'call'; ?></i>
            <?php if (!empty($metaMessage['op_joined']) && !empty($metaMessage['vi_joined'])) : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'In call'); ?>
            <?php elseif (!empty($metaMessage['op_joined'])) : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'You joined — waiting for visitor'); ?>
            <?php else : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Visitor accepted — join call to start'); ?>
            <?php endif; ?>
        </div>
        <div class="d-grid gap-1">
            <button type="button" onclick="lhinst.jitsiJoinOperator(<?php echo $jitsiChatId; ?>, <?php echo $jitsiMsgId; ?>)" class="btn btn-sm btn-primary">
                <i class="material-icons fs14 align-middle"><?php echo $jitsiMode === 'video' ? 'videocam' : 'call'; ?></i>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Join call'); ?>
            </button>
            <button type="button" onclick="lhinst.jitsiCancel(<?php echo $jitsiChatId; ?>, <?php echo $jitsiMsgId; ?>, 'end')" class="btn btn-sm btn-outline-danger">
                <i class="material-icons fs14 align-middle">call_end</i>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'End call'); ?>
            </button>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_DENIED) : ?>
        <div class="bg-light text-danger rounded p-2 text-center fs13">
            <i class="material-icons fs14 align-middle">phone_missed</i>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Visitor declined the call'); ?>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_CANCELED) : ?>
        <div class="bg-light text-muted rounded p-2 text-center fs13">
            <i class="material-icons fs14 align-middle">phone_disabled</i>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Call was canceled by operator'); ?>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_ENDED) : ?>
        <div class="bg-secondary text-white rounded p-2 text-center fs13">
            <i class="material-icons fs14 align-middle">call_end</i>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Call ended'); ?>
            <?php if ($jitsiDuration > 0) : ?>
                (<?php echo erLhcoreClassChat::formatSeconds($jitsiDuration); ?>)
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php if ($jitsiIsActive && $jitsiChatId > 0 && $jitsiMsgId > 0) : ?>
<script>if (typeof lhinst !== 'undefined' && typeof lhinst.jitsiMonitorAdmin === 'function') { lhinst.jitsiMonitorAdmin(<?php echo $jitsiChatId; ?>, <?php echo $jitsiMsgId; ?>, <?php echo json_encode($jitsiStateKey); ?>); }</script>
<?php endif; ?>
