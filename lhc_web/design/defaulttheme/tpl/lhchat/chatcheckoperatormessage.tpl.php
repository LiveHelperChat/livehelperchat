<?php if ($visitor->has_message_from_operator == true) : ?>
lh_inst.stopCheckNewMessage();
lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>');
<?php endif; ?>