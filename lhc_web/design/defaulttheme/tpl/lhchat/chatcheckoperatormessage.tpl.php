<?php if (isset($reopen_chat)) : ?>
lh_inst.stopCheckNewMessage();
if (window.innerWidth > 1023) {
	lh_inst.addCookieAttribute('hash','<?php echo $reopen_chat->id;?>_<?php echo $reopen_chat->hash?>');
	lh_inst.showStartWindow();
};
<?php elseif ($visitor->has_message_from_operator == true) : ?>
lh_inst.stopCheckNewMessage();
if (window.innerWidth > 1023) {
	lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>',true);
};
<?php endif; ?>
<?php if (isset($operation)) : ?><?php echo $operation;?><?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/chatcheckoperatormessage_multiinclude.tpl.php')); ?>	