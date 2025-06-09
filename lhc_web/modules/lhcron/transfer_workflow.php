<?php
/**
 * php cron.php -s site_admin -c cron/transfer_workflow
 *
 * Run every 1/2 seconds. On this cron depends automatic chat transfer between departments
 * 
 * */
if (erLhcoreClassModelChatConfig::fetch('run_departments_workflow')->current_value == 1) {
    echo "Starting departments workflow\n";

    $db = ezcDbInstance::get();

    $isOnlineCache = [];

    foreach (erLhcoreClassChat::getList(array('limit' => 500, 'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT, 'transfer_if_na' => 1))) as $chat) {

        // Fix misleading message
        if (isset($offlineDepartmentOperators)) {
            unset($offlineDepartmentOperators);
        }

        try {
            $db->beginTransaction();

            $chat = erLhcoreClassModelChat::fetchAndLock($chat->id);

            // Verify that chat is still in pending mode
            if ($chat->status != erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                continue;
            }

            $ts = time();

            if (
                (
                    $chat->transfer_timeout_ts < ($ts - (int)$chat->transfer_timeout_ac)
                ) || (
                    ($department = $chat->department) && $offlineDepartmentOperators = true && $department !== false && isset($department->bot_configuration_array['off_op_exec']) && $department->bot_configuration_array['off_op_exec'] == 1 &&
                        (
                            (
                                (isset($isOnlineCache[$chat->dep_id]) && $isOnlineCache[$chat->dep_id] === false)
                                ||
                                (erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true, 'include_users' => true, 'exclude_online_hours' => true)) === false && ($isOnlineCache[$chat->dep_id] = false) === false)
                            )
                            ||
                            (isset($department->bot_configuration_array['off_op_work_hours']) && $department->bot_configuration_array['off_op_work_hours'] == 1 && erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true, 'ignore_user_status' => true)) === false)
                        )
                )
            ) {
                $canExecuteWorkflow = true;

                if (erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value >= 0) {
                    if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
                        $canExecuteWorkflow = erLhcoreClassChat::getPendingChatsCountPublic($chat->department->department_transfer_id) <= erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value;
                    }
                }

                if ($canExecuteWorkflow == true) {
                    // Check if destination department has online operators if option is enabled
                    
                    $skipTransferNoOperators = false;
                    if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
                        if (isset($chat->department->bot_configuration_array['no_transfer_no_operators']) && $chat->department->bot_configuration_array['no_transfer_no_operators'] == 1) {
                            if (!isset($isOnlineCache[$chat->department->department_transfer_id])) {
                                $isOnlineCache[$chat->department->department_transfer_id] = erLhcoreClassChat::isOnline($chat->department->department_transfer_id, false, array('exclude_bot' => true, 'include_users' => true, 'exclude_online_hours' => true));
                            }
                            if ($isOnlineCache[$chat->department->department_transfer_id] === false) {
                                $skipTransferNoOperators = true;
                                echo "Skipping transfer to department {$chat->department->department_transfer_id} - no online operators\n";
                            }
                        }
                    }
                    
                    if (!$skipTransferNoOperators) {
                        erLhcoreClassChatWorkflow::transferWorkflow($chat, array('offline_operators' => isset($offlineDepartmentOperators)));
                        echo "executing department transfer workflow for - ", $chat->id, "\n";
                    }

                } else {
                    echo "Skipping transfer because dedicated department queue is full\n";
                }
            }

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    echo "Ended departments workflow\n";
}
