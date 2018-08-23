<?php if (isset($reopen_chat)) : ?>
lh_inst.stopCheckNewMessage();
if (window.innerWidth > 700) {
	lh_inst.addCookieAttribute('hash','<?php echo $reopen_chat->id;?>_<?php echo $reopen_chat->hash?>');
	lh_inst.showStartWindow();
};
<?php elseif ($visitor->has_message_from_operator == true && (!isset($dynamic_everytime) || $dynamic_everytime == false)) : ?>
lh_inst.stopCheckNewMessage();

<?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && $visitor->invitation->show_on_mobile == 1) : ?>

    <?php if (($visitor->invitation_assigned == false && $visitor->invitation->delay > 0) || $visitor->invitation->delay_init > 0) : ?>
    setTimeout(function() {
    <?php endif; ?>
        lh_inst.isProactivePending = 1;
        lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
    <?php if ($visitor->invitation_assigned == false && $visitor->invitation->delay > 0) : ?>
    },<?php echo ($visitor->invitation_assigned == true ? $visitor->invitation->delay_init : $visitor->invitation->delay) * 1000?>);
    <?php endif; ?>

<?php else : ?>
        if (window.innerWidth > 700) {
            <?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && (($visitor->invitation_assigned == false && $visitor->invitation->delay > 0) || $visitor->invitation->delay_init > 0)) : ?>
                setTimeout(function() {
            <?php endif; ?>
                lh_inst.isProactivePending = 1;
                lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
            <?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && ($visitor->invitation_assigned == false && $visitor->invitation->delay > 0 || $visitor->invitation->delay_init > 0)) : ?>
                },<?php echo ($visitor->invitation_assigned == true ? $visitor->invitation->delay_init : $visitor->invitation->delay) * 1000?>);
            <?php endif; ?>
        }
<?php endif; ?>

<?php elseif (isset($dynamic)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/dynamic_events.tpl.php')); ?>	
<?php endif; ?>
<?php if (isset($operation)) : ?><?php echo $operation;?><?php endif;?>

<?php if ($visitor->next_reschedule > 0) : ?>
    setTimeout(function() {
        lh_inst.startNewMessageCheckSingle();
    },<?php echo (($visitor->next_reschedule + 1)*1000);?>);
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/chatcheckoperatormessage_multiinclude.tpl.php')); ?>	