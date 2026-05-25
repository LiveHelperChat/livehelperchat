<?php

namespace LiveHelperChat\Models\Statistic;

class PerformanceWidgets
{
    const VALID_UPDATE_INTERVALS = [300, 600, 900, 1200, 1800, 3600];

    const UPDATE_INTERVAL_LABELS = [
        300  => '5 min',
        600  => '10 min',
        900  => '15 min',
        1200 => '20 min',
        1800 => '30 min',
        3600 => '1 hour',
    ];

    /**
     * Build the op_performance widget data.
     *
     * @param array $params {
     *   int   limit_list             Rows to return (0 = unlimited)
     *   bool  all_departments        $userData->all_departments == 1
     *   bool  can_list_online_all    $canListOnlineUsersAll
     *   int   current_user_id        $currentUser->getUserID()
     *   int   cache_version          $userData->cache_version
     *   mixed start_time             microtime() recorded before processing
     *   array stored_performance_config  erLhcoreClassModelChatConfig::fetch('statistic_performance_op')->data_value
     * }
     * @return array
     */
    public static function getOpPerformance(array $params): array {
        $limitList              = (int)($params['limit_list'] ?? 10);
        $allDepartments         = (bool)($params['all_departments'] ?? false);
        $canListOnlineUsersAll  = (bool)($params['can_list_online_all'] ?? false);
        $list_read_write        = (bool)($params['list_read_write'] ?? false);
        $currentUserId          = (int)($params['current_user_id'] ?? 0);
        $cacheVersion           = (int)($params['cache_version'] ?? 0);
        $startTimeRequestItem   = $params['start_time'] ?? microtime();
        $storedPerformanceConfig = $params['stored_performance_config'] ?? [];

        $defaultPerformanceColumns = ['toff', 'ton', 'ca', 'frt', 'aart', 'tup', 'tdown'];
        $defaultPerformanceColumnOrder = array_flip($defaultPerformanceColumns);

        $performanceColumns = array_values(array_intersect(
            isset($storedPerformanceConfig['columns']) && is_array($storedPerformanceConfig['columns'])
                ? $storedPerformanceConfig['columns']
                : $defaultPerformanceColumns,
            $defaultPerformanceColumns
        ));

        if (empty($performanceColumns)) {
            $performanceColumns = $defaultPerformanceColumns;
        }

        $columnPositionsRaw = isset($storedPerformanceConfig['positions']) ? $storedPerformanceConfig['positions'] : [];
        $columnPositions = is_array($columnPositionsRaw) ? $columnPositionsRaw : [];

        usort($performanceColumns, function ($columnA, $columnB) use ($columnPositions, $defaultPerformanceColumnOrder) {
            $positionA = isset($columnPositions[$columnA]) ? (int)$columnPositions[$columnA] : ($defaultPerformanceColumnOrder[$columnA] + 1);
            $positionB = isset($columnPositions[$columnB]) ? (int)$columnPositions[$columnB] : ($defaultPerformanceColumnOrder[$columnB] + 1);

            if ($positionA === $positionB) {
                return $defaultPerformanceColumnOrder[$columnA] <=> $defaultPerformanceColumnOrder[$columnB];
            }

            return $positionA <=> $positionB;
        });

        $item = Performance::findOne(['limit' => 1, 'filter' => [
            'type' => Performance::OPERATOR
        ], 'sort' => 'id DESC']);

        $depPerformanceRows = [];
        $updatedAt = 'n/a';

        if (is_object($item)) {
            $depPerformanceRows = $item->data_array;
            $updatedAt = \erLhcoreClassChat::formatSeconds(time() - $item->created_at);
        }

        if (isset($params['user_id_filter']) && !empty($params['user_id_filter'])) {
            $depPerformanceRows = array_values(array_filter($depPerformanceRows, function ($row) use ($params) {
                return in_array($row['id'], $params['user_id_filter']);
            }));
        }

        if ($params['myself'] === true) {
            $depPerformanceRows = array_values(array_filter($depPerformanceRows, function ($row) use ($currentUserId) {
                return (int)$row['id'] === $currentUserId;
            }));
        } else if (!$allDepartments && !$canListOnlineUsersAll) {
            $userDepartaments = \erLhcoreClassUserDep::getUserDepartaments($currentUserId, $cacheVersion);

            if ($list_read_write === false) {
                $userReadDepartments = \erLhcoreClassUserDep::getUserReadDepartments($currentUserId, $cacheVersion);
                if (!empty($userReadDepartments) && is_array($userDepartaments)) {
                    $userDepartaments = array_values(array_diff($userDepartaments, $userReadDepartments));
                }
            }
            
            $index = array_search(-1, $userDepartaments);
            if ($index !== false) {
                unset($userDepartaments[$index]);
            }
            if (!empty($userDepartaments)) {
                $visibleOpIds = \erLhcoreClassModelUserDep::getCount(
                    ['filterin' => ['dep_id' => $userDepartaments], 'limit' => false],
                    'COUNT', false,
                    'DISTINCT `lh_userdep`.`user_id`',
                    false, 
                    true, 
                    true
                );
                
                $depPerformanceRows = array_values(array_filter($depPerformanceRows, function ($row) use ($visibleOpIds) {
                    return in_array($row['id'], $visibleOpIds);
                }));
            } else {
                $depPerformanceRows = [];
            }
        }

        $userIds = array_column($depPerformanceRows, 'id');
        $depPerformanceRowsById = array_column($depPerformanceRows, null, 'id');
        $depPerformanceRows = [];

        if (!empty($userIds)) {
            $usersQuery = [
                'select_columns' => ['id','name','surname','chat_nickname'],
                'ignore_fields' => ['all_columns'],
                'filterin' => ['id' => $userIds],
                'sort'     => 'name ASC, surname ASC',
                'limit'    => $limitList > 0 ? $limitList : false,
            ];
            $users = \erLhcoreClassModelUser::getList($usersQuery);
            foreach ($users as $user) {
                if (isset($depPerformanceRowsById[$user->id])) {
                    $row = $depPerformanceRowsById[$user->id];
                    $row['name'] = $user->name_official;
                    $depPerformanceRows[] = $row;
                }
            }
        }

        foreach ($depPerformanceRows as &$rowPerformance) {
            foreach ($performanceColumns as $columnPerformance) {
                if (!isset($rowPerformance[$columnPerformance])) {
                    $rowPerformance[$columnPerformance] = '';
                } elseif (in_array($columnPerformance,['ton','toff','aart','frt']) && $rowPerformance[$columnPerformance] !== '') {
                    $rowPerformance[$columnPerformance] = \erLhcoreClassChat::formatSeconds((int)$rowPerformance[$columnPerformance], false, true);
                }
            }
        }
        unset($rowPerformance);

        $performanceUpdateInterval = isset($storedPerformanceConfig['update_interval'])
            && in_array((int)$storedPerformanceConfig['update_interval'], self::VALID_UPDATE_INTERVALS)
            ? (int)$storedPerformanceConfig['update_interval']
            : 600;

        $dataReturn = [
            'list' => $depPerformanceRows,
            'cl'   => $performanceColumns,
            'ui'   => $performanceUpdateInterval,
            'up'   => $updatedAt,
            'tt_stat'   => \erLhcoreClassModule::getDifference($startTimeRequestItem, microtime()),
        ];

        if (isset($storedPerformanceConfig['wrap_headers']) && $storedPerformanceConfig['wrap_headers'] === true) {
            $dataReturn['wrap_h'] = true;
        }

        return $dataReturn;
    }

    /**
     * Build the dep_performance widget data.
     *
     * @param array $params {
     *   int   limit_list              Rows to return (0 = unlimited)
     *   bool  all_departments         $userData->all_departments == 1
     *   int   current_user_id         $currentUser->getUserID()
     *   int   cache_version           $userData->cache_version
     *   mixed start_time              microtime() recorded before processing
     *   array stored_performance_config  erLhcoreClassModelChatConfig::fetch('statistic_performance')->data_value
     * }
     * @return array
     */
    public static function getDepPerformance(array $params): array {
        $limitList               = (int)($params['limit_list'] ?? 10);
        $allDepartments          = (bool)($params['all_departments'] ?? false);
        $currentUserId           = (int)($params['current_user_id'] ?? 0);
        $cacheVersion            = (int)($params['cache_version'] ?? 0);
        $startTimeRequestItem    = $params['start_time'] ?? microtime();
        $storedPerformanceConfig = $params['stored_performance_config'] ?? [];

        $defaultPerformanceColumns = ['cr', 'ca', 'wt', 'frt', 'aart', 'tup', 'tdown'];
        $defaultPerformanceColumnOrder = array_flip($defaultPerformanceColumns);

        $performanceColumns = array_values(array_intersect(
            isset($storedPerformanceConfig['columns']) && is_array($storedPerformanceConfig['columns'])
                ? $storedPerformanceConfig['columns']
                : $defaultPerformanceColumns,
            $defaultPerformanceColumns
        ));

        if (empty($performanceColumns)) {
            $performanceColumns = $defaultPerformanceColumns;
        }

        $columnPositionsRaw = isset($storedPerformanceConfig['positions']) ? $storedPerformanceConfig['positions'] : [];
        $columnPositions = is_array($columnPositionsRaw) ? $columnPositionsRaw : [];

        usort($performanceColumns, function ($columnA, $columnB) use ($columnPositions, $defaultPerformanceColumnOrder) {
            $positionA = isset($columnPositions[$columnA]) ? (int)$columnPositions[$columnA] : ($defaultPerformanceColumnOrder[$columnA] + 1);
            $positionB = isset($columnPositions[$columnB]) ? (int)$columnPositions[$columnB] : ($defaultPerformanceColumnOrder[$columnB] + 1);

            if ($positionA === $positionB) {
                return $defaultPerformanceColumnOrder[$columnA] <=> $defaultPerformanceColumnOrder[$columnB];
            }

            return $positionA <=> $positionB;
        });

        $item = Performance::findOne(['limit' => 1, 'filter' => [
            'type' => Performance::DEPARTMENT
        ], 'sort' => 'id DESC']);

        $depPerformanceRows = [];
        $updatedAt = 'n/a';

        if (is_object($item)) {
            $depPerformanceRows = $item->data_array;
            $updatedAt = \erLhcoreClassChat::formatSeconds(time() - $item->created_at);
        }

        $allowedDepIds = null;

        if (!$allDepartments) {
            $userDepartaments = \erLhcoreClassUserDep::getUserDepartaments($currentUserId, $cacheVersion);
            if (!empty($userDepartaments)) {
                $allowedDepIds = $userDepartaments;
            } else {
                $allowedDepIds = [-1];
            }
        }

        $depIds = array_column($depPerformanceRows, 'id');
        if ($allowedDepIds !== null) {
            $depIds = array_values(array_intersect($depIds, $allowedDepIds));
        }
        $depPerformanceRowsById = array_column($depPerformanceRows, null, 'id');
        $depPerformanceRows = [];

        if (!empty($depIds)) {
            $departments = \erLhcoreClassModelDepartament::getList([
                'select_columns' => ['id','name'],
                'ignore_fields' => ['all_columns'],
                'filterin' => ['id' => $depIds],
                'sort'     => 'name ASC',
                'limit'    => $limitList > 0 ? $limitList : false,
            ]);

            foreach ($departments as $department) {
                if (isset($depPerformanceRowsById[$department->id])) {
                    $row = $depPerformanceRowsById[$department->id];
                    $row['name'] = $department->name;
                    $depPerformanceRows[] = $row;
                }
            }
        }

        foreach ($depPerformanceRows as &$rowPerformance) {
            foreach ($performanceColumns as $columnPerformance) {
                if (!isset($rowPerformance[$columnPerformance])) {
                    $rowPerformance[$columnPerformance] = '';
                } elseif (in_array($columnPerformance,['aart','frt','wt']) && $rowPerformance[$columnPerformance] !== '') {
                    $rowPerformance[$columnPerformance] = \erLhcoreClassChat::formatSeconds((int)$rowPerformance[$columnPerformance], false, true);
                }
            }
        }
        unset($rowPerformance);

        $performanceUpdateInterval = isset($storedPerformanceConfig['update_interval'])
            && in_array((int)$storedPerformanceConfig['update_interval'], self::VALID_UPDATE_INTERVALS)
            ? (int)$storedPerformanceConfig['update_interval']
            : 600;

        $dataReturn = [
            'list' => $depPerformanceRows,
            'cl'   => $performanceColumns,
            'ui'   => $performanceUpdateInterval,
            'up'   => $updatedAt,
            'tt_stat'   => \erLhcoreClassModule::getDifference($startTimeRequestItem, microtime()),
        ];

        if (isset($storedPerformanceConfig['wrap_headers']) && $storedPerformanceConfig['wrap_headers'] === true) {
            $dataReturn['wrap_h'] = true;
        }

        return $dataReturn;
    }
}
