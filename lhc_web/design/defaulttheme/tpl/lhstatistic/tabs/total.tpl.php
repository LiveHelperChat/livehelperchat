 <h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total statistic');?></h2>
<table class="table">
	<thead>
		<tr>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
		</tr>
	</thead>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount()?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total pending chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total active chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total closed chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chatbox chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT)))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (including visitors, system and operators messages)');?></td>
		<td><?php $totalMessagesCount = erLhcoreClassChat::getCount(array(),'lh_msg');
		echo $totalMessagesCount?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only visitors)');?></td>
		<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => 0)),'lh_msg');
		echo $totalVisitorsMessagesCount;?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only system messages)');?></td>
		<td><?php
		$systemMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => -1)),'lh_msg');
		echo $systemMessagesCount; ?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only operators)');?></td>
		<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
	</tr>
</table>