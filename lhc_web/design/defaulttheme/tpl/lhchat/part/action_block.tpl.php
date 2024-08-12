<?php if ($canEditChat == true && (!isset($writeRemoteDisabled) || $writeRemoteDisabled == false) && erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use')) :  ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action.tpl.php')); ?>
<?php endif; ?>