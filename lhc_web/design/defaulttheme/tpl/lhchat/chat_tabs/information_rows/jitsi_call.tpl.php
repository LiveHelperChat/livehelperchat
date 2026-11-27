<?php if (isset($orderInformation['jitsi_call']['enabled']) && $orderInformation['jitsi_call']['enabled'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhjitsi', 'use')) : ?>
<?php $jitsiConfig = erLhcoreClassJitsi::getConfig(); if (erLhcoreClassJitsi::isEnabled($jitsiConfig)) : ?>
<?php $jitsiConfirmMsg = !empty($jitsiConfig['confirm_before_invite']) ? erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Send Jitsi call invitation to visitor?') : ''; ?>
<tr>
    <td colspan="2">
        <h6 class="fw-bold"><i class="material-icons"><?php echo !empty($jitsiConfig['video_enabled']) ? 'videocam' : 'call'; ?></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Jitsi Call'); ?></h6>
        <?php if (!empty($jitsiConfig['video_enabled'])) : ?>
            <a href="#" class="text-muted me-3" data-confirm="<?php echo $jitsiConfirmMsg; ?>" onclick="return lhinst.jitsiInvite(<?php echo (int)$chat->id; ?>, 'video', this)"><i class="material-icons">videocam</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Invite to Video Call'); ?></a>
            <a href="#" class="text-muted" data-confirm="<?php echo $jitsiConfirmMsg; ?>" onclick="return lhinst.jitsiInvite(<?php echo (int)$chat->id; ?>, 'audio', this)"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Invite to Voice Call'); ?></a>
        <?php else : ?>
            <a href="#" class="text-muted" data-confirm="<?php echo $jitsiConfirmMsg; ?>" onclick="return lhinst.jitsiInvite(<?php echo (int)$chat->id; ?>, 'audio', this)"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Invite to Voice Call'); ?></a>
        <?php endif; ?>
    </td>
</tr>
<?php endif; endif; ?>
