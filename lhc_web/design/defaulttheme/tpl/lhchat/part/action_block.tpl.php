<?php if ($canEditChat == true && (!isset($writeRemoteDisabled) || $writeRemoteDisabled == false)) :  ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action.tpl.php')); ?>
<?php endif; ?>
