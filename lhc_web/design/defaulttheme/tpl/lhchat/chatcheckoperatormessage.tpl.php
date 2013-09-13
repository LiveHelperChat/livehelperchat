<?php if ($visitor->has_message_from_operator == true) : ?>
lh_inst.stopCheckNewMessage();
lh_inst.showStartWindow('//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>/(vid)/<?php echo $vid;?>');
<?php endif; ?>