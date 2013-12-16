<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic');?></h1>

<div class="row">
	<div class="columns large-6">
		<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total statistic');?></h2>
		<table class="small-12">
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
	</div>
	<div class="columns large-6">
		<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last 24h statistic');?></h2>
		<table class="small-12">
			<thead>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
				</tr>
			</thead>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
				<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600)))))?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total pending chats');?></td>
				<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT)))?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total active chats');?></td>
				<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total closed chats');?></td>
				<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)))?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chatbox chats');?></td>
				<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT)))?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (including visitors, system and operators messages)');?></td>
				<td><?php $totalMessagesCount = erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600)))),'lh_msg');
				echo $totalMessagesCount?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only visitors)');?></td>
				<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => 0),'filtergte' => array('time' => (time()-(24*3600)))),'lh_msg');
				echo $totalVisitorsMessagesCount;?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only system messages)');?></td>
				<td><?php
				$systemMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => -1),'filtergte' => array('time' => (time()-(24*3600)))),'lh_msg');
				echo $systemMessagesCount; ?></td>
			</tr>
			<tr>
				<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only operators)');?></td>
				<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
			</tr>
		</table>
	</div>
</div>

<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators last 24h statistic, top 100 by chats number');?></h2>
<?php $operators = erLhcoreClassChatStatistic::getTopTodaysOperators(100); ?>
<table class="small-12">
	<thead>
		<tr>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Votes');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last activity');?></td>
		</tr>
	</thead>
	<?php foreach ($operators as $operator) : ?>
	<tr>
		<td><?php echo htmlspecialchars((string)$operator)?></td>
		<td><?php echo $operator->statistic_total_chats?></td>
		<td><?php echo $operator->statistic_total_messages?></td>
		<td>
		
		<i class="icon-thumbs-up up-voted"><?php echo $operator->statistic_upvotes?></i>
		<i class="icon-thumbs-down down-voted"><?php echo $operator->statistic_downvotes?></i>
		
		</td>
		<td><?php echo $operator->lastactivity_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','ago');?></td>
	</tr>
	<?php endforeach;?>
</table>
