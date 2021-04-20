<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $items = [];

    $filters = [];

    $timefrom = strtotime('-30 days');
    if (isset($_GET['from']) && !empty($_GET['from'])) {
        $t = strtotime($_GET['from']);
        if ($t) {
            $timefrom = $t;
            $filters['filtergte']['time'] = $timefrom;
        }
    }

    $timeto = time();
    if (isset($_GET['to']) && !empty($_GET['to'])) {
        $t = strtotime($_GET['to']);
        if ($t) {
            $timeto = $t;
            $filters['filterlte']['time'] = $timeto;
        }
    }

    if (isset($_GET['group_ids']) && is_array($_GET['group_ids']) && !empty($_GET['group_ids'])) {
        $filters['filterin']['group_ids'] = $_GET['group_ids'];
    }

    if (isset($_GET['department_ids']) && is_array($_GET['department_ids']) && !empty($_GET['department_ids'])) {
        $filters['filterin']['department_ids'] = $_GET['department_ids'];
    }

    if (isset($_GET['department_group_ids']) && is_array($_GET['department_group_ids']) && !empty($_GET['department_group_ids'])) {
        $filters['filterin']['department_group_ids'] = $_GET['department_group_ids'];
    }

    if (isset($_GET['subject_ids']) && is_array($_GET['subject_ids']) && !empty($_GET['subject_ids'])) {
        $filters['filterin']['subject_ids'] = $_GET['subject_ids'];
    }

    if (isset($_GET['user_ids']) && is_array($_GET['user_ids']) && !empty($_GET['user_ids'])) {
        $filters['filterin']['id'] = $_GET['user_ids'];
    }

    $days = round(($timeto - $timefrom) / (60 * 60 * 24));

    $items = erLhcoreClassChatStatistic::getAgentStatistic($days, $filters);

    erLhcoreClassRestAPIHandler::outputResponse(
        array(
            'error'  => false,
            'result' => array_values($items)
        )
    );
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();



