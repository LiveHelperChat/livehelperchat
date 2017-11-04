<?php

/**
 * php cron.php -s site_admin -c cron/workflow
 *
 * Run every 10 minits or so. On this cron depends automatic chat transfer and unaswered chats callback.
 *
 * */
echo "Starting chat/workflow\n";

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.started',array());

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

$db = ezcDbInstance::get();

try {
    $assignWorkflowTimeout = erLhcoreClassModelChatConfig::fetch('assign_workflow_timeout')->current_value;

    if ($assignWorkflowTimeout > 0) {
        foreach (erLhcoreClassChat::getList(array('sort' => 'id ASC', 'limit' => 500, 'filterlt' => array('time' => (time() - $assignWorkflowTimeout)),'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))) as $chat){
            $db->beginTransaction();
            $chat = erLhcoreClassModelChat::fetchAndLock($chat->id);
            if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                erLhcoreClassChatWorkflow::autoAssign($chat, $chat->department, array('cron_init' => true));
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.pending_process_workflow',array('chat' => & $chat));
            }
            $db->commit();
        }
    }

    foreach (erLhcoreClassChat::getList(array('sort' => 'priority DESC, id ASC', 'limit' => 500, 'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))) as $chat){
        $db->beginTransaction();
            $chat = erLhcoreClassModelChat::fetchAndLock($chat->id);
            if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                erLhcoreClassChatWorkflow::autoAssign($chat, $chat->department, array('cron_init' => true));
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.pending_process_workflow',array('chat' => & $chat));
            }
        $db->commit();
    }

} catch (Exception $e) {
    $db->rollback();
    throw $e;
}
    
// Inform visitors about unread messages
erLhcoreClassChatWorkflow::autoInformVisitor(erLhcoreClassModelChatConfig::fetch('inform_unread_message')->current_value);

echo "Ended chat/workflow\n";

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow',array());

?>