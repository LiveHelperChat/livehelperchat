<?php
$jitsiConfig = erLhcoreClassJitsi::getConfig();
$jitsiStatus = $metaMessage['status'] ?? erLhcoreClassJitsi::STATUS_INVITED;
$jitsiMode = ($metaMessage['mode'] ?? erLhcoreClassJitsi::MODE_VIDEO) === erLhcoreClassJitsi::MODE_AUDIO ? 'audio' : 'video';
$jitsiUseWidget = erLhcoreClassJitsi::shouldUseWidgetForVisitor($jitsiConfig, $jitsiMode);
$jitsiUseMinimalAudio = erLhcoreClassJitsi::shouldUseMinimalWidgetAudio($jitsiConfig, $jitsiMode);
$jitsiOpJoined = !empty($metaMessage['op_joined']);
$jitsiViJoined = !empty($metaMessage['vi_joined']);
$jitsiMsgId = (int)$msg['id'];
$jitsiJoinUrl = isset($chat) && $chat instanceof erLhcoreClassModelChat
    ? erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurl('jitsi/join') . '/' . (int)$chat->id . '/' . rawurlencode((string)$chat->hash) . '/' . $jitsiMsgId
    : '#';
$jitsiDuration = erLhcoreClassJitsi::getDurationSeconds($metaMessage);
$jitsiStateKey = erLhcoreClassJitsi::getStateKey($metaMessage);
$jitsiChatInvalid = isset($chat) && $chat instanceof erLhcoreClassModelChat && !erLhcoreClassJitsi::isVisitorChatValid($chat, (string)$chat->hash, false);
$jitsiIsActive = !$jitsiChatInvalid && in_array($jitsiStatus, [erLhcoreClassJitsi::STATUS_INVITED, erLhcoreClassJitsi::STATUS_ACCEPTED], true);
?>
<div class="msg-body text-dark msg-body-widget bg-white rounded p-3 my-2 shadow-sm border" id="jitsi-widget-status-<?php echo $jitsiMsgId; ?>" data-state-key="<?php echo htmlspecialchars($jitsiStateKey); ?>">
    <div class="text-center mb-2">
        <div id="jitsi-minimal-title-live-<?php echo $jitsiMsgId; ?>" class="fw-semibold fs13" style="display:none;"></div>
        <div id="jitsi-minimal-title-server-<?php echo $jitsiMsgId; ?>" class="fw-semibold fs13"><i class="material-icons fs14 me-1 align-text-bottom">&#xf117;</i><?php if ($jitsiMode === 'video') : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Video call invitation'); ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Voice call invitation'); ?><?php endif; ?></div>
    </div>

    <?php if ($jitsiChatInvalid && in_array($jitsiStatus, [erLhcoreClassJitsi::STATUS_INVITED, erLhcoreClassJitsi::STATUS_ACCEPTED], true)) : ?>
        <div class="bg-secondary text-white rounded p-2 text-center fs13">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call ended'); ?>
            <?php if ($jitsiDuration > 0) : ?>
                (<?php echo erLhcoreClassChat::formatSeconds($jitsiDuration); ?>)
            <?php endif; ?>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_INVITED) : ?>
        <div id="jitsi-minimal-live-<?php echo $jitsiMsgId; ?>"></div>
        <div class="d-flex gap-2 justify-content-center" id="jitsi-invite-actions-<?php echo $jitsiMsgId; ?>">
            <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"accept","msg_id":"<?php echo $jitsiMsgId; ?>","mode":"<?php echo $jitsiMode; ?>","op_joined":<?php echo $jitsiOpJoined ? 'true' : 'false'; ?>}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" id="jitsi-accept-btn-<?php echo $jitsiMsgId; ?>" class="btn btn-sm btn-success flex-fill"><i class="material-icons fs14 me-1">&#xf117;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Accept'); ?></button>
            <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"deny","msg_id":"<?php echo $jitsiMsgId; ?>"}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" id="jitsi-deny-btn-<?php echo $jitsiMsgId; ?>" class="btn btn-sm btn-outline-danger flex-fill"><i class="material-icons fs14 me-1">&#xf10a;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Deny'); ?></button>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_ACCEPTED) : ?>
        <?php if ($jitsiUseMinimalAudio) : ?>
            <div id="jitsi-minimal-live-<?php echo $jitsiMsgId; ?>"></div>
            <div id="jitsi-minimal-server-<?php echo $jitsiMsgId; ?>">
                <?php if (!$jitsiOpJoined) : ?>
                    <div class="bg-info text-dark rounded p-2 text-center fs13 mb-2"><i class="material-icons lhc-spin fs14 me-1">&#xf113;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Waiting for operator to join...'); ?></div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"end","msg_id":"<?php echo $jitsiMsgId; ?>"}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" class="btn btn-sm btn-outline-danger flex-fill"><i class="material-icons fs14 me-1">&#xf10a;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'End call'); ?></button>
                    </div>
                <?php else : ?>
                    <div class="<?php echo $jitsiViJoined ? 'bg-success text-white' : 'bg-light text-dark border'; ?> rounded p-2 text-center fs13 mb-2"><i class="material-icons fs14 me-1">&#xf117;</i><?php if ($jitsiViJoined) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call in progress'); ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Operator has joined the call'); ?><?php endif; ?></div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"continue_minimal","msg_id":"<?php echo $jitsiMsgId; ?>"}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" class="btn btn-sm btn-success flex-fill"><i class="material-icons fs14 me-1">&#xf111;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Continue'); ?></button>
                        <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"end","msg_id":"<?php echo $jitsiMsgId; ?>"}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" class="btn btn-sm btn-outline-danger"><i class="material-icons fs14 me-1">&#xf10a;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'End'); ?></button>
                    </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="<?php echo $jitsiViJoined ? 'bg-success text-white' : 'bg-light text-dark border'; ?> rounded p-2 text-center fs13 mb-2">
                <?php if ($jitsiViJoined) : ?>
                    <i class="material-icons fs14 me-1">&#xf117;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call in progress'); ?>
                <?php elseif ($jitsiOpJoined) : ?>
                    <i class="material-icons fs14 me-1">&#xf117;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Operator joined — ready to connect'); ?>
                <?php else : ?>
                    <i class="material-icons lhc-spin fs14 me-1">&#xf113;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call accepted — waiting for operator to join...'); ?>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2 justify-content-center">
                <?php if ($jitsiUseWidget) : ?>
                    <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"open","msg_id":"<?php echo $jitsiMsgId; ?>","mode":"<?php echo $jitsiMode; ?>"}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" class="btn btn-sm btn-primary flex-fill"><i class="material-icons fs14 me-1">&#xf117;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Open call'); ?></button>
                    <a href="<?php echo htmlspecialchars($jitsiJoinUrl); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Open in new window'); ?>"><i class="material-icons fs14 me-0">&#xf106;</i></a>
                <?php else : ?>
                    <a href="<?php echo htmlspecialchars($jitsiJoinUrl); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary flex-fill"><i class="material-icons fs14 me-1">&#xf117;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Open call'); ?></a>
                <?php endif; ?>
                <button type="button" data-no-change="true" data-bot-action="execute-js" data-bot-args='{"method":"end","msg_id":"<?php echo $jitsiMsgId; ?>"}' data-bot-extension="jitsi-call" onclick="lhinst.executeJS()" class="btn btn-sm btn-outline-secondary"><i class="material-icons fs14 me-1">&#xf10a;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'End'); ?></button>
            </div>
        <?php endif; ?>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_DENIED) : ?>
        <div class="bg-light text-danger rounded p-2 text-center fs13">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call declined'); ?>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_CANCELED) : ?>
        <div class="bg-light text-secondary rounded p-2 text-center fs13">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call invitation was canceled'); ?>
        </div>

    <?php elseif ($jitsiStatus === erLhcoreClassJitsi::STATUS_ENDED) : ?>
        <div class="bg-secondary text-white rounded p-2 text-center fs13">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call ended'); ?>
            <?php if ($jitsiDuration > 0) : ?>
                (<?php echo erLhcoreClassChat::formatSeconds($jitsiDuration); ?>)
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ($jitsiIsActive) : ?>
        <script data-bot-always="true" class="meta-message-<?php echo $jitsiMsgId; ?>" data-bot-action="execute-js" data-bot-args='{"method":"monitor","msg_id":"<?php echo $jitsiMsgId; ?>","state_key":"<?php echo htmlspecialchars($jitsiStateKey); ?>"}' data-bot-extension="jitsi-call"></script>
    <?php endif; ?>
</div>
