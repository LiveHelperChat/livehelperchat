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

        if (!$allDepartments && !$canListOnlineUsersAll) {
            $userDepartaments = \erLhcoreClassUserDep::getUserDepartaments($currentUserId, $cacheVersion);
            $index = array_search(-1, $userDepartaments);
            if ($index !== false) {
                unset($userDepartaments[$index]);
            }
            if (!empty($userDepartaments)) {
                $visibleOpIds = \erLhcoreClassModelUserDep::getCount(
                    ['filterin' => ['dep_id' => $userDepartaments], 'limit' => false],
                    'COUNT', false,
                    'DISTINCT `lh_userdep`.`user_id`',
                    false, true, true
                );
                $depPerformanceRows = array_values(array_filter($depPerformanceRows, function ($row) use ($visibleOpIds) {
                    return in_array($row['id'], $visibleOpIds);
                }));
            } else {
                $depPerformanceRows = [];
            }
        }

        foreach ($depPerformanceRows as &$rowPerformance) {
            $rowPerformance['name'] = ($user = \erLhcoreClassModelUser::fetch($rowPerformance['id'], true)) ? $user->name_official : '';
            foreach ($performanceColumns as $columnPerformance) {
                if (!isset($rowPerformance[$columnPerformance])) {
                    $rowPerformance[$columnPerformance] = '';
                } elseif (($columnPerformance === 'ton' || $columnPerformance === 'toff') && $rowPerformance[$columnPerformance] !== '') {
                    $rowPerformance[$columnPerformance] = \erLhcoreClassChat::formatSeconds((int)$rowPerformance[$columnPerformance]);
                }
            }
        }

        usort($depPerformanceRows, function ($rowA, $rowB) {
            $nameA = isset($rowA['name']) ? (string)$rowA['name'] : '';
            $nameB = isset($rowB['name']) ? (string)$rowB['name'] : '';
            return strcasecmp($nameA, $nameB);
        });

        if ($limitList > 0) {
            $depPerformanceRows = array_slice($depPerformanceRows, 0, $limitList);
        }

        $performanceUpdateInterval = isset($storedPerformanceConfig['update_interval'])
            && in_array((int)$storedPerformanceConfig['update_interval'], self::VALID_UPDATE_INTERVALS)
            ? (int)$storedPerformanceConfig['update_interval']
            : 600;

        return [
            'list' => $depPerformanceRows,
            'cl'   => $performanceColumns,
            'ui'   => $performanceUpdateInterval,
            'up'   => $updatedAt,
            'tt'   => \erLhcoreClassModule::getDifference($startTimeRequestItem, microtime()),
        ];
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

        if ($allowedDepIds !== null) {
            $depPerformanceRows = array_values(array_filter($depPerformanceRows, function ($row) use ($allowedDepIds) {
                return isset($row['id']) && in_array($row['id'], $allowedDepIds);
            }));
        }

        foreach ($depPerformanceRows as &$rowPerformance) {
            $rowPerformance['name'] = (string)\erLhcoreClassModelDepartament::fetch($rowPerformance['id'], true);
            foreach ($performanceColumns as $columnPerformance) {
                if (!isset($rowPerformance[$columnPerformance])) {
                    $rowPerformance[$columnPerformance] = '';
                }
            }
        }

        usort($depPerformanceRows, function ($rowA, $rowB) {
            $nameA = isset($rowA['name']) ? (string)$rowA['name'] : '';
            $nameB = isset($rowB['name']) ? (string)$rowB['name'] : '';
            return strcasecmp($nameA, $nameB);
        });

        if ($limitList > 0) {
            $depPerformanceRows = array_slice($depPerformanceRows, 0, $limitList);
        }

        $performanceUpdateInterval = isset($storedPerformanceConfig['update_interval'])
            && in_array((int)$storedPerformanceConfig['update_interval'], self::VALID_UPDATE_INTERVALS)
            ? (int)$storedPerformanceConfig['update_interval']
            : 600;

        return [
            'list' => $depPerformanceRows,
            'cl'   => $performanceColumns,
            'ui'   => $performanceUpdateInterval,
            'up'   => $updatedAt,
            'tt'   => \erLhcoreClassModule::getDifference($startTimeRequestItem, microtime()),
        ];
    }
}
