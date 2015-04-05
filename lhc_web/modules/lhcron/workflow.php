<?php

/**
 * php cron.php -s site_admin -c cron/workflow
 *
 * Run every 10 minits or so. On this cron depends automatic chat transfer and unaswered chats callback.
 *
 * */

echo "Starting chat/workflow\n";

if ( erLhcoreClassModelChatConfig::fetch('run_departments_workflow')->current_value == 1 ) {
	echo "Starting departments workflow\n";

	$ts = time();

	foreach (erLhcoreClassChat::getList(array('limit' => 500, 'customfilter' => array('transfer_timeout_ts < ('.$ts.'-transfer_timeout_ac)'), 'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT, 'transfer_if_na' => 1))) as $chat){
		$canExecuteWorkflow = true;

		if (erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value >= 0) {
			if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
				$canExecuteWorkflow = erLhcoreClassChat::getPendingChatsCountPublic($chat->department->department_transfer_id) <= erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value;
			}
		}

		if ($canExecuteWorkflow == true) {
			erLhcoreClassChatWorkflow::transferWorkflow($chat);
			echo "executing department transfer workflow for - ",$chat->id,"\n";
		} else {
			echo "Skipping transfer because dedicated department queue is full\n";
		}
	}

	echo "Ended departments workflow\n";
}

// Unanswered chats callback
echo erLhcoreClassChatWorkflow::mainUnansweredChatWorkflow();

echo "Closed chats - ",erLhcoreClassChatWorkflow::automaticChatClosing(),"\n";

echo "Purged chats - ",erLhcoreClassChatWorkflow::automaticChatPurge(),"\n";

foreach (erLhcoreClassChat::getList(array('limit' => 500, 'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))) as $chat){
	erLhcoreClassChatWorkflow::autoAssign($chat,$chat->department);
	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.pending_process_workflow',array('chat' => & $chat));
}

echo "Ended chat/workflow\n";

?>