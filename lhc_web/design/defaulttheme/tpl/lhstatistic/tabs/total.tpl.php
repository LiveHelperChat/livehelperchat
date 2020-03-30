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
		<td><?php echo erLhcoreClassModelChat::getCount($totalfilter)?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_pending_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassModelChat::getCount(array_merge_recursive($totalfilter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_active_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassModelChat::getCount(array_merge_recursive($totalfilter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_closed_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassModelChat::getCount(array_merge_recursive($totalfilter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassModelChat::getCount(array_merge_recursive($totalfilter,array('filter' => array('unanswered_chat' => 1))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chatbox_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassModelChat::getCount(array_merge_recursive($totalfilter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_including_v_s_o_m.tpl.php'));?></td>
		<td><?php $totalMessagesCount = erLhcoreClassModelmsg::getCount(array_merge_recursive($totalfilter,array('innerjoin' => array('lh_chat' => array('`lh_msg`.`chat_id`','`lh_chat`.`id`')))));
		echo $totalMessagesCount?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_visitors.tpl.php'));?></td>
		<td><?php $totalVisitorsMessagesCount = erLhcoreClassModelmsg::getCount(array_merge_recursive($totalfilter,array('innerjoin' => array('lh_chat' => array('`lh_msg`.`chat_id`','`lh_chat`.`id`')),'filter' => array('`lh_msg`.`user_id`' => 0))));
		echo $totalVisitorsMessagesCount;?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_system_messages.tpl.php'));?></td>
		<td><?php
		$systemMessagesCount = erLhcoreClassModelmsg::getCount(array_merge_recursive($totalfilter,array('innerjoin' => array('lh_chat' => array('`lh_msg`.`chat_id`','`lh_chat`.`id`')), 'filterin' => array('`lh_msg`.`user_id`' => array(-1,-2)))));
		echo $systemMessagesCount; ?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_operators.tpl.php'));?></td>
		<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
	</tr>
</table>