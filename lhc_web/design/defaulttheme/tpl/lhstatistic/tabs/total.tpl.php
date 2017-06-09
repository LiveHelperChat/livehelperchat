 <h2><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_statistic.tpl.php'));?></h2>
<table class="table">
	<thead>
		<tr>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
		</tr>
	</thead>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount()?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_pending_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_active_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_closed_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('unanswered_chat' => 1)))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chatbox_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_including_v_s_o_m.tpl.php'));?></td>
		<td><?php $totalMessagesCount = erLhcoreClassChat::getCount(array(),'lh_msg');
		echo $totalMessagesCount?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_visitors.tpl.php'));?></td>
		<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => 0)),'lh_msg');
		echo $totalVisitorsMessagesCount;?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_system_messages.tpl.php'));?></td>
		<td><?php
		$systemMessagesCount = erLhcoreClassChat::getCount(array('filterin' => array('user_id' => array(-1,-2))),'lh_msg');
		echo $systemMessagesCount; ?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_operators.tpl.php'));?></td>
		<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
	</tr>
</table>