<?php
/**
 * Run depending how frequent you wan those stats. Every 5 minutes should be fine
 * 
 * php cron.php -s site_admin -c cron/stats/performance
 * 
 * Force regenerate
 * 
 * php cron.php -s site_admin -c cron/stats/performance -p force
 *
 * */

echo "-=Starting performance stats aggregation=-\n\n";

$regenerate = false;

if (isset($cronjobPathOption) && $cronjobPathOption->value == 'force') {
    $regenerate = true;
}

try {
    $dt = new DateTime();
    $offset = $dt->format("P");
    $db = ezcDbInstance::get();
    $db->query("SET sql_mode=''");                      // Some of the reports require it
    $db->query("SET LOCAL time_zone='" . $offset ."'"); // Adjust time zone to match system
} catch (Exception $e) {
    // Ignore
}

class PerformanceStats {
    public static function departmentStats($regenerate = false) {
        $statisticOptions = erLhcoreClassModelChatConfig::fetch('statistic_performance');
        $configuration = (array)$statisticOptions->data;

        echo 'Date: ',date('Y-m-d H:i:s'),', TZ: ',date_default_timezone_get(), "\n";

        if (isset($configuration['update_interval']) && $configuration['update_interval'] > 0 && !empty($configuration['columns'])) {
            $currentMinute = (int)date('i');
            $updateInterval = (int)$configuration['update_interval'];

            // Check if current minute is aligned with update interval (e.g., 0, 10, 20, 30 for 10-minute interval)
            if ($regenerate === true || $currentMinute % ($updateInterval/60) === 0) {
                $lastUpdateTime = isset($configuration['last_update_time']) ? (int)$configuration['last_update_time'] : 0;
                $now = time();

                // Prevent multiple updates within same interval window (allow 30 seconds variation)
                if ($regenerate === true || $now - $lastUpdateTime >= $updateInterval - 30) {
                    $updateStartedAt = microtime(true);
                    echo "Collecting performance stats at " . date('Y-m-d H:i:s') . "\n";

                    $performanceData = array();
                    $days = isset($configuration['days']) ? (int)$configuration['days'] : 1;
                    $todayStart = strtotime('today');

                    $departments = erLhcoreClassModelDepartament::getList(array('limit' => false, 'filter' => array('archive' => 0, 'disabled' => 0)));

                    $filter = [
                        'limit' => false,
                        'filterin' => [
                            'dep_id' => array_keys($departments)
                        ],
                        'filtergte' => ['time' => $todayStart]
                    ];

                    /* Example row structure:
                        'name'  => 'Department or User name',
                        'id'    => 1,                 // Department or User ID
                        'cr'    => 100,               // Chats received (total started)
                        'ca'    => 100,               // Chats answered (handled by agents)
                        'wt'    => '00:00:06',        // Average wait time (HH:MM:SS)
                        'frt'   => '00:00:06',        // Avg first response time (agent)
                        'aart'  => '00:00:06',        // Avg agent response time
                        'tup'   => 10,                // Total thumbs up (positive feedback)
                        'tdown' => 5                  // Total thumbs down (negative feedback)
                    */

                    $result = [];

                    foreach ($configuration['columns'] as $column) {
                        switch ($column) {
                            case 'cr':
                                $filterAART = $filter;
                                $filterAART['group'] = 'dep_id';
                                $result['cr'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'dep_id, count(`lh_chat`.`id`) AS number_of_chats',             // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'ca':
                                // Chats abandoned we calculate first
                                // After a cycle chats answered is just cr - ca
                                $abandoned_sql = (string)erLhcoreClassModelChatConfig::fetch('abandoned_sql')->current_value;
                                $filterAART = array_merge($filter, ['customfilter' => [$abandoned_sql != '' ? $abandoned_sql : '((`lsync` < (`pnd_time` + `wait_time`) AND `wait_time` > 1) OR  (`lsync` > (`pnd_time` + `wait_time`) AND `wait_time` > 1 AND `user_id` = 0))']]);
                                $filterAART['group'] = 'dep_id';
                                $result['ca'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                    // $params
                                    '',                                   // $operation
                                    false,                                    // $field
                                    'dep_id, count(`lh_chat`.`id`) AS number_of_chats',             // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'aart':
                                // getCount($params = array(), $operation = 'COUNT', $field = false, $rawSelect = false, $fetchColumn = true, $fetchAll = false, $fetchColumnAll = false, $groupedCount = false)
                                $filterAART = $filter;
                                $filterAART['group'] = 'dep_id';
                                $filterAART['filtergt']['user_id'] = 0;
                                $filterAART['filtergt']['aart'] = 0;
                                $filterAART['filter']['status'] = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
                                $result['aart'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'dep_id, AVG(aart) as aart',                // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'wt':
                                $filterAART = $filter;
                                $filterAART['group'] = 'dep_id';
                                $filterAART['filterlt']['wait_time'] = 600;
                                $filterAART['filtergt']['user_id'] = 0;
                                $result['wt'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'dep_id, AVG(wait_time) as wt',             // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'frt':
                                $filterAART = $filter;
                                $filterAART['filtergt']['user_id'] = 0;
                                $filterAART['filtergt']['frt'] = 0;
                                $filterAART['filter']['status'] = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
                                $filterAART['group'] = 'dep_id';
                                $result['frt'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'dep_id, AVG(frt) as frt',                  // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'tup':
                                $filterAART = $filter;
                                $filterAART['group'] = 'dep_id';
                                $filterAART['filter']['fbst'] = 1;
                                $result['tup'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'dep_id, count(id) as tup',                 // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'tdown':
                                $filterAART = $filter;
                                $filterAART['group'] = 'dep_id';
                                $filterAART['filter']['fbst'] = 2;
                                $result['tdown'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'dep_id, count(id) as tdown',               // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                        }
                    }

                    $depPerformanceRows = array();
                    $crByDep = array();

                    if (isset($result['cr']) && is_array($result['cr'])) {
                        foreach ($result['cr'] as $rowCR) {
                            if (!isset($rowCR['dep_id']) || !isset($rowCR['number_of_chats'])) {
                                continue;
                            }
                            $depId = (int)$rowCR['dep_id'];
                            $crByDep[$depId] = $rowCR['number_of_chats'];
                        }
                    }

                    foreach ($crByDep as $depId => $crValue) {
                        if (!isset($departments[$depId])) {
                            continue;
                        }

                        $depPerformanceRows[$depId] = array(
                            'id' => $depId,
                            'cr' => $crValue,
                        );

                        foreach ($configuration['columns'] as $column) {
                            if (!isset($depPerformanceRows[$depId][$column])) {
                                $depPerformanceRows[$depId][$column] = 0;
                            }
                        }
                    }

                    foreach ($result as $metric => $rowsByMetric) {
                        if ($metric === 'cr' || !is_array($rowsByMetric)) {
                            continue;
                        }

                        foreach ($rowsByMetric as $rowMetric) {
                            if (!isset($rowMetric['dep_id'])) {
                                continue;
                            }

                            $depId = (int)$rowMetric['dep_id'];

                            // Skip departments that have no CR metric row.
                            if (!isset($depPerformanceRows[$depId])) {
                                continue;
                            }

                            if (isset($rowMetric[$metric])) {
                                $depPerformanceRows[$depId][$metric] = $rowMetric[$metric];
                            } elseif (isset($rowMetric['number_of_chats'])) {
                                $depPerformanceRows[$depId][$metric] = $rowMetric['number_of_chats'];
                            }
                        }
                    }

                    if (in_array('ca', $configuration['columns'])) {
                        foreach ($depPerformanceRows as $depId => $row) {
                            $crValue = isset($row['cr']) ? (int)$row['cr'] : 0;
                            $abandonedValue = isset($row['ca']) ? (int)$row['ca'] : 0;
                            $depPerformanceRows[$depId]['ca'] = max(0, $crValue - $abandonedValue);
                        }
                    }

                    $performanceData = array_values($depPerformanceRows);

                    // Save performance data
                    $performance = new \LiveHelperChat\Models\Statistic\Performance();
                    $performance->type = \LiveHelperChat\Models\Statistic\Performance::DEPARTMENT;
                    $performance->created_at = $now;
                    $performance->data = json_encode($performanceData);
                    $performance->saveThis();

                    echo "Saved " . count($performanceData) . " departments records\n";

                    // Update last update time
                    $configuration['last_update_time'] = $now;
                    $statisticOptions->value = serialize($configuration);
                    $statisticOptions->saveThis();

                    $updateDuration = microtime(true) - $updateStartedAt;
                    echo 'Update took ' . number_format($updateDuration, 3, '.', '') . " seconds\n";
                    echo "Performance stats aggregation completed\n";
                } else {
                    echo "Skipping update - last update was " . (($now - $lastUpdateTime) / 60) . " minutes ago\n";
                }

            } else {
                echo "Minute missmatch\n";
            }
        } else {
            echo "Not fully configured `statistic_performance`!\n";
        }
    }

    public static function operatorsStats($regenerate = false) {
        $statisticOptions = erLhcoreClassModelChatConfig::fetch('statistic_performance_op');
        $configuration = (array)$statisticOptions->data;

        echo 'Date: ',date('Y-m-d H:i:s'),', TZ: ',date_default_timezone_get(), "\n";

        if (isset($configuration['update_interval']) && $configuration['update_interval'] > 0 && !empty($configuration['columns'])) {
            $currentMinute = (int)date('i');
            $updateInterval = (int)$configuration['update_interval'];

            // Check if current minute is aligned with update interval (e.g., 0, 10, 20, 30 for 10-minute interval)
            if ($regenerate === true || $currentMinute % ($updateInterval/60) === 0) {
                $lastUpdateTime = isset($configuration['last_update_time']) ? (int)$configuration['last_update_time'] : 0;
                $now = time();

                // Prevent multiple updates within same interval window (allow 30 seconds variation)
                if ($regenerate === true || $now - $lastUpdateTime >= $updateInterval - 30) {
                    $updateStartedAt = microtime(true);
                    echo "Collecting performance stats at " . date('Y-m-d H:i:s') . "\n";

                    $days = isset($configuration['days']) ? (int)$configuration['days'] : 1;
                    $todayStart = strtotime('today');

                    $filterUsers = [];
                    $filterUsers['filter']['disabled'] = 0;
                    $filterUsers['limit'] = 10000;
                    $userList = erLhcoreClassModelUser::getUserList($filterUsers);

                    // Users filter is not present because we won't find any data for those users in any case
                    $filter = [
                        'limit' => false,
                        'filtergte' => ['time' => $todayStart]
                    ];

                    $result = [];

                    /*$columnTranslations = array(
                        +'toff' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Total Online Time'),
                        +'ton' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Total Offline Time'),
                        +'ca' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats answered'),
                        +'frt' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','First response time (Agent)'),
                        +'aart' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Average response time (Agent)'),
                        +'tup' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs Up'),
                        +'tdown' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs Down')
                    );*/

                    $db = ezcDbInstance::get();

                    foreach ($configuration['columns'] as $column) {
                        switch ($column) {
                            case 'ton':
                                $filterAART = $filter;
                                $filterAART['group'] = 'user_id';
                                $rangeStart = (int)$filterAART['filtergte']['time'];
                                unset($filterAART['filtergte']['time']);
                                $filterAART['filtergte']['lactivity'] = $rangeStart;
                                $result['ton'] = erLhcoreClassModelUserOnlineSession::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'user_id, SUM(lactivity - GREATEST(time, ' . $filter['filtergte']['time'] . ')) as ton',               // $rawSelect
                                    false,                                      // $fetchColumn
                                    true);
                                break;
                            case 'toff':
                                $stmt = $db->prepare('WITH all_sessions AS (
                                    SELECT user_id, time, lactivity
                                    FROM lh_users_online_session
                                    WHERE time < UNIX_TIMESTAMP()
                                    AND lactivity >= :from_ts_1
                                    UNION ALL
                                    SELECT s.user_id, s.time, s.lactivity
                                    FROM lh_users_online_session s
                                    INNER JOIN (
                                        SELECT user_id, MAX(time) AS max_time
                                        FROM lh_users_online_session
                                        WHERE lactivity < :from_ts_2
                                        GROUP BY user_id
                                    ) pre ON s.user_id = pre.user_id AND s.time = pre.max_time
                                ),
                                with_next AS (
                                    SELECT
                                        user_id,
                                        lactivity,
                                        LEAD(time) OVER (PARTITION BY user_id ORDER BY time) AS next_start
                                    FROM all_sessions
                                ),
                                gaps AS (
                                    SELECT
                                        user_id,
                                        next_start,
                                        GREATEST(lactivity, :from_ts_3)                                        AS gap_start,
                                        LEAST(COALESCE(next_start, UNIX_TIMESTAMP()), UNIX_TIMESTAMP())      AS gap_end
                                    FROM with_next
                                )
                                SELECT
                                    user_id,
                                    SUM(gap_end - gap_start) AS toff
                                FROM gaps
                                WHERE gap_end > gap_start
                                AND (gap_end - gap_start) <= 18000
                                AND (next_start IS NOT NULL OR (gap_end - gap_start) >= 30)  -- min 30s for the open-ended (last) gap
                                GROUP BY user_id
                                ORDER BY user_id;');
                                $stmt->bindValue(':from_ts_1', $filter['filtergte']['time'], PDO::PARAM_INT);
                                $stmt->bindValue(':from_ts_2', $filter['filtergte']['time'], PDO::PARAM_INT);
                                $stmt->bindValue(':from_ts_3', $filter['filtergte']['time'], PDO::PARAM_INT);
                                $stmt->execute();
                                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $result['toff'] = $rows;
                                break;
                            case 'ca':
                                $filterAART = $filter;
                                $filterAART['group'] = 'user_id';
                                $result['ca'] = \LiveHelperChat\Models\LHCAbstract\ChatParticipant::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'user_id, count(`lh_chat_participant`.`id`) AS number_of_chats',               // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'aart':
                                $filterAART = $filter;
                                $filterAART['group'] = 'user_id';
                                $filterAART['filtergt']['aart'] = 0;
                                $result['aart'] = \LiveHelperChat\Models\LHCAbstract\ChatParticipant::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'user_id, AVG(aart) as aart',               // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'frt':
                                $filterAART = $filter;
                                $filterAART['group'] = 'user_id';
                                $filterAART['filtergt']['frt'] = 0;
                                $result['frt'] = \LiveHelperChat\Models\LHCAbstract\ChatParticipant::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'user_id, AVG(frt) as frt',                  // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                            break;
                            case 'tup':
                                $filterAART = $filter;
                                $filterAART['group'] = 'user_id';
                                $filterAART['filter']['fbst'] = 1;
                                $result['tup'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'user_id, count(id) as tup',                 // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                            case 'tdown':
                                $filterAART = $filter;
                                $filterAART['group'] = 'user_id';
                                $filterAART['filter']['fbst'] = 2;
                                $result['tdown'] = erLhcoreClassModelChat::getCount(
                                    $filterAART,                                // $params
                                    '',                                         // $operattion
                                    false,                                      // $field
                                    'user_id, count(id) as tdown',              // $rawSelect
                                    false,                                      // $fetchColumn
                                    true                                        // $fetchAll
                                );
                                break;
                        }
                    }

                    $userPerformanceRows = array();
                    $caByUser = array();
                    $tonByUser = array();

                    if (isset($result['ca']) && is_array($result['ca'])) {
                        foreach ($result['ca'] as $rowCa) {
                            if (!isset($rowCa['user_id']) || !isset($rowCa['number_of_chats'])) {
                                continue;
                            }
                            $caByUser[(int)$rowCa['user_id']] = (int)$rowCa['number_of_chats'];
                        }
                    }

                    if (isset($result['ton']) && is_array($result['ton'])) {
                        foreach ($result['ton'] as $rowTon) {
                            if (!isset($rowTon['user_id'])) {
                                continue;
                            }
                            $tonByUser[(int)$rowTon['user_id']] = (float)($rowTon['ton'] ?? 0);
                        }
                    }

                    // Collect all user IDs that appear in either ca or ton
                    $allUserIds = array_unique(array_merge(array_keys($caByUser), array_keys($tonByUser)));

                    // Exclude users where both ca == 0 AND ton == 0
                    foreach ($allUserIds as $userId) {
                        $caValue  = $caByUser[$userId] ?? 0;
                        $tonValue = $tonByUser[$userId] ?? 0;

                        if ($caValue <= 0 && $tonValue <= 0) {
                            continue;
                        }

                        if (!isset($userList[$userId])) {
                            continue;
                        }

                        $userPerformanceRows[$userId] = array(
                            'id' => $userId,
                            'ca' => $caValue,
                        );

                        foreach ($configuration['columns'] as $column) {
                            if (!isset($userPerformanceRows[$userId][$column])) {
                                $userPerformanceRows[$userId][$column] = 0;
                            }
                        }
                    }

                    // Fill other metrics for users that passed the filter
                    foreach ($result as $metric => $rowsByMetric) {
                        if ($metric === 'ca' || !is_array($rowsByMetric)) {
                            continue;
                        }

                        foreach ($rowsByMetric as $rowMetric) {
                            if (!isset($rowMetric['user_id'])) {
                                continue;
                            }

                            $userId = (int)$rowMetric['user_id'];

                            // Skip users not included (i.e., ca == 0 or not present)
                            if (!isset($userPerformanceRows[$userId])) {
                                continue;
                            }

                            if (isset($rowMetric[$metric])) {
                                $userPerformanceRows[$userId][$metric] = $rowMetric[$metric];
                            } elseif (isset($rowMetric['number_of_chats'])) {
                                $userPerformanceRows[$userId][$metric] = $rowMetric['number_of_chats'];
                            }
                        }
                    }

                    $performanceData = array_values($userPerformanceRows);

                    // Save performance data
                    $performance = new \LiveHelperChat\Models\Statistic\Performance();
                    $performance->type = \LiveHelperChat\Models\Statistic\Performance::OPERATOR;
                    $performance->created_at = $now;
                    $performance->data = json_encode($performanceData);
                    $performance->saveThis();

                    echo "Saved " . count($performanceData) . " operator records\n";

                    // Update last update time
                    $configuration['last_update_time'] = $now;
                    $statisticOptions->value = serialize($configuration);
                    $statisticOptions->saveThis();

                    $updateDuration = microtime(true) - $updateStartedAt;
                    echo 'Update took ' . number_format($updateDuration, 3, '.', '') . " seconds\n";
                    echo "Performance stats aggregation completed\n";
                } else {
                    echo "Skipping update - last update was " . (($now - $lastUpdateTime) / 60) . " minutes ago\n";
                }

            } else {
                echo "Minute missmatch\n";
            }
        } else {
            echo "Not fully configured `statistic_performance_op`!\n";
        }
    }
}

PerformanceStats::departmentStats($regenerate);
echo "\n";
PerformanceStats::operatorsStats($regenerate);

echo "-=Deleting older records than 30 days=-\n\n";
try {
    $db = ezcDbInstance::get();
    $days = 30;
    $cutoff = time() - ($days * 86400);
    $table = 'lh_abstract_performance';

    $stmt = $db->prepare('DELETE FROM ' . $table . ' WHERE created_at < :cutoff');
    $stmt->bindValue(':cutoff', $cutoff, PDO::PARAM_INT);
    $stmt->execute();

    $deleted = $stmt->rowCount();
    echo "Deleted " . $deleted . " performance records older than " . $days . " days from table " . $table . "\n\n";

} catch (Exception $e) {
    echo "Error deleting old performance records: " . $e->getMessage() . "\n\n";
}

echo "\n-=Finished performance stats aggregation=-\n";