<?php if (isset($reopen_chat)) : ?>
lh_inst.stopCheckNewMessage();
if (window.innerWidth > 1023) {
	lh_inst.addCookieAttribute('hash','<?php echo $reopen_chat->id;?>_<?php echo $reopen_chat->hash?>');
	lh_inst.showStartWindow();
};
<?php elseif ($visitor->has_message_from_operator == true) : ?>
lh_inst.stopCheckNewMessage();

<?php if ($visitor->show_on_mobile == 1) : ?>
    lh_inst.isProactivePending = 1;
	lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
<?php else : ?>
if (window.innerWidth > 1023) {
    lh_inst.isProactivePending = 1;
    lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
}
<?php endif; ?>

<?php elseif (isset($dynamic)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/dynamic_events.tpl.php')); ?>	
<?php endif; ?>
<?php if (isset($operation)) : ?><?php echo $operation;?><?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/chatcheckoperatormessage_multiinclude.tpl.php')); ?>	