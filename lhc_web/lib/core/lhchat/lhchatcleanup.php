<?php

/**
 * Class erLhcoreClassChatCleanup
 *
 * Various static cleanup functions
 */

class erLhcoreClassChatCleanup {

    public static function onlineOperatorsCleanup()
    {
        $lastCleanup = erLhcoreClassModelChatConfig::fetch('uonline_sessions_last');

        // Do not clean more often that once per hour
        if ((int)$lastCleanup->current_value < time()-3600) {

            $db = ezcDbInstance::get();

            $lastCleanup->identifier = 'uonline_sessions_last';
            $lastCleanup->type = 0;
            $lastCleanup->explain = 'Track last online operators cleanup';
            $lastCleanup->hidden = 1;
            $lastCleanup->value = time();
            $lastCleanup->saveThis();

            $timeoutCleanup = erLhcoreClassModelChatConfig::fetch('uonline_sessions')->current_value;

            if ($timeoutCleanup > 0)
            {
                for ($i = 0; $i < 100; $i++)
                {
                    $stmt = $db->prepare("SELECT id, `time` FROM lh_users_online_session ORDER BY id ASC LIMIT 1 OFFSET 1000");
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (isset($data['time']) && $data['time'] < (int)(time() - ($timeoutCleanup * 24 * 3600))) {
                        $stmt = $db->prepare('DELETE FROM lh_users_online_session WHERE id < :id');
                        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        // No more records found to remove
                        break;
                    }
                }
            }
        }
    }

    public static function cleanupAuditLog()
    {
        $lastCleanup = erLhcoreClassModelChatConfig::fetch('audit_cleanup_last');

        // Do not clean more often that once per hour
        if ((int)$lastCleanup->current_value < time()-3600) {

            $lastCleanup->identifier = 'audit_cleanup_last';
            $lastCleanup->type = 0;
            $lastCleanup->explain = 'Track last audit cleanup';
            $lastCleanup->hidden = 1;
            $lastCleanup->value = time();
            $lastCleanup->saveThis();

            $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
            $data = (array)$auditOptions->data;

            if (isset($data['days_log']) && $data['days_log'] > 0) {

                $timeout = $data['days_log'];

                $db = ezcDbInstance::get();

                for ($i = 0; $i < 100; $i++)
                {
                    $stmt = $db->prepare("SELECT `id`, `time` FROM lh_audits ORDER BY id ASC LIMIT 1 OFFSET 500");
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (isset($data['time']) && strtotime($data['time']) < (int)(time() - ($timeout * 24 * 3600))) {
                        $stmt = $db->prepare('DELETE FROM lh_audits WHERE id < :id');
                        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        // No more records found to remove
                        break;
                    }
                }
            }
        }
    }

    /**
     * @desc Department availability cleanup
     */
    public static function departmentAvailabilityCleanup()
    {
        $lastCleanup = erLhcoreClassModelChatConfig::fetch('departament_availability_last');

        // Do not clean more often that once per hour
        if ((int)$lastCleanup->current_value < time()-3600) {

            $lastCleanup->identifier = 'departament_availability_last';
            $lastCleanup->type = 0;
            $lastCleanup->explain = 'Track last department availability cleanup';
            $lastCleanup->hidden = 1;
            $lastCleanup->value = time();
            $lastCleanup->saveThis();

            $timeout = erLhcoreClassModelChatConfig::fetch('departament_availability')->current_value;

            if ($timeout >= 0) {

                $db = ezcDbInstance::get();

                for ($i = 0; $i < 100; $i++)
                {
                    $stmt = $db->prepare("SELECT `id`, `time` FROM lh_departament_availability ORDER BY id ASC LIMIT 1 OFFSET 1000");
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (isset($data['time']) && $data['time'] < (int)(time() - ($timeout * 24 * 3600))) {
                        $stmt = $db->prepare('DELETE FROM lh_departament_availability WHERE id < :id');
                        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        // No more records found to remove
                        break;
                    }
                }
            }
        }
    }

    public static function cleanupOnlineUsers($params = array())
    {
        $cleanupCronjob = erLhcoreClassModelChatConfig::fetch('cleanup_cronjob')->current_value;

        if ($cleanupCronjob == 0 || (isset($params['cronjob']) && $params['cronjob'] == 1))
        {
            $lastCleanup = erLhcoreClassModelChatConfig::fetch('tracked_users_cleanup_last');

            // Do not clean more often that once per hour
            if ((int)$lastCleanup->current_value < time()-3600) {

                $timeoutCleanup = erLhcoreClassModelChatConfig::fetch('tracked_users_cleanup')->current_value;

                $timeoutCleanupFootprint = erLhcoreClassModelChatConfig::fetch('tracked_footprint_cleanup')->current_value;

                $lastCleanup->identifier = 'tracked_users_cleanup_last';
                $lastCleanup->type = 0;
                $lastCleanup->explain = 'Track last cleanup';
                $lastCleanup->hidden = 1;
                $lastCleanup->value = time();
                $lastCleanup->saveThis();

                $db = ezcDbInstance::get();

                if ($timeoutCleanup > 0) {
                    // Proactive events cleanup
                    $stmt = $db->prepare('DELETE T2 FROM lh_abstract_proactive_chat_event as T2 INNER JOIN lh_chat_online_user as T1 ON T1.id = T2.vid_id WHERE last_visit < :last_visit');
                    $stmt->bindValue(':last_visit', (int)(time() - ($timeoutCleanup * 24 * 3600)), PDO::PARAM_INT);
                    $stmt->execute();

                    // Online user cleanup
                    $stmt = $db->prepare('DELETE FROM lh_chat_online_user WHERE last_visit < :last_activity');
                    $stmt->bindValue(':last_activity', (int)(time() - ($timeoutCleanup * 24 * 3600)), PDO::PARAM_INT);
                    $stmt->execute();

                    // Cleanup proactive statistic. As it's related to online visitors cleanup it with same workflow
                    $stmt = $db->prepare('DELETE FROM lh_abstract_proactive_chat_campaign_conv WHERE ctime < :ctime');
                    $stmt->bindValue(':ctime', (int)(time() - ($timeoutCleanup * 24 * 3600)), PDO::PARAM_INT);
                    $stmt->execute();
                }

                if ($timeoutCleanupFootprint > 0) {
                    self::cleanupFootprint($timeoutCleanupFootprint);
                }
            }
        }
    }

    /**
     * @desc refactor footprint cleanup so it will use indexes all the time
     *
     * @param $timeout
     * @throws ezcDbHandlerNotFoundException
     */
    public static function cleanupFootprint($timeout) {
        $db = ezcDbInstance::get();

        for ($i = 0; $i < 100; $i++)
        {
            $stmt = $db->prepare("SELECT id, vtime FROM lh_chat_online_user_footprint ORDER BY id ASC LIMIT 1 OFFSET 1000");
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($data['vtime']) && $data['vtime'] < (int)(time() - ($timeout * 24 * 3600))) {
                $stmt = $db->prepare('DELETE FROM lh_chat_online_user_footprint WHERE id < :id');
                $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // No more records found to remove
                break;
            }
        }
    }

    public static function updateFootprintBackground()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT * FROM lh_chat_online_user_footprint_update ORDER BY ctime ASC LIMIT 1000 ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete instantly, because commands can take longer.
        // These commands are not dead must so they can fail time from time
        foreach ($rows as $row) {
            $stmt = $db->prepare('DELETE FROM lh_chat_online_user_footprint_update WHERE online_user_id = :online_user_id AND ctime = :ctime');
            $stmt->bindValue(':ctime',(int)$row['ctime'],PDO::PARAM_INT);
            $stmt->bindValue(':online_user_id',(int)$row['online_user_id'],PDO::PARAM_INT);
            $stmt->execute();
        }

        foreach ($rows as $row) {
            if ($row['command'] == 'set_chat') {
                $stmt = $db->prepare('UPDATE LOW_PRIORITY lh_chat_online_user_footprint SET chat_id = :chat_id WHERE online_user_id = :online_user_id');
                $stmt->bindValue(':chat_id',(int)$row['args'],PDO::PARAM_INT);
                $stmt->bindValue(':online_user_id',(int)$row['online_user_id'],PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
}

?>
