<?php

class erLhcoreClassMailconvStatistic {

    public static function messagesPerInterval($filter, $params_execution) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.messagesperinterval',array('params_execution' => $params_execution, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        self::formatFilterMail($filter);

        if ($params_execution['group_by'] == 1) {
            $numberOfChats = array();

            $startTimestamp = time()-(31*24*3600);

            $limitDays = 31;

            if (isset($filter['filterlte']['udate']) && isset($filter['filtergte']['udate'])) {
                $daysDifference = ceil(($filter['filterlte']['udate'] - $filter['filtergte']['udate'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['udate'];
                }

            } elseif (isset($filter['filtergte']['udate'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['udate'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['udate'];
                }
            } elseif (isset($filter['filterlte']['udate'])) {
                $limitDays = 31;
                $startTimestamp = $filter['filterlte']['udate']-(31*24*3600);
            }

            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

                if (in_array('mmsgperinterval',$params_execution['chart_type'])) {
                    $numberOfChats[$dateUnix]['normal'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter, array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL), 'customfilter' => array('FROM_UNIXTIME(udate,\'%Y%m%d\') = ' . date('Ymd', $dateUnix)))));
                    $numberOfChats[$dateUnix]['notrequired'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter, array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED), 'customfilter' => array('FROM_UNIXTIME(udate,\'%Y%m%d\') = ' . date('Ymd', $dateUnix)))));
                    $numberOfChats[$dateUnix]['send'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter, array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL), 'customfilter' => array('FROM_UNIXTIME(udate,\'%Y%m%d\') = ' . date('Ymd', $dateUnix)))));
                    $numberOfChats[$dateUnix]['unresponded'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter, array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED), 'customfilter' => array('FROM_UNIXTIME(udate,\'%Y%m%d\') = ' . date('Ymd', $dateUnix)))));
                }

                if (in_array('mavgwaittime',$params_execution['chart_type'])) {
                    $numberOfChats[$dateUnix]['avg_wait_time'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter, array('filtergt' => array('wait_time' => 0),'filterlt' => array('wait_time' => 600), 'customfilter' => array('FROM_UNIXTIME(udate,\'%Y%m%d\') = ' . date('Ymd', $dateUnix)))), 'AVG', 'wait_time');
                }
            }

            return $numberOfChats;

        } else {
            $numberOfChats = array();

            $yearStart = date('y');
            $monthStart = date('m');

            if (isset($filter['filterlte']['udate'])) {
                $yearStart = date('y',$filter['filterlte']['udate']);
                $monthStart = date('m',$filter['filterlte']['udate']);
            }

            for ($i = 0; $i < 12;$i++) {
                $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);
                if (!isset($filter['filtergte']['udate']) || $filter['filtergte']['udate'] <= $dateUnix || date('Ym',$filter['filtergte']['udate']) == date('Ym',$dateUnix))
                {
                    $numberOfChats[$dateUnix] = array ();

                    if (in_array('mmsgperinterval',$params_execution['chart_type'])) {
                        $numberOfChats[$dateUnix]['normal'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array( 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['notrequired'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array( 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['send'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array( 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['unresponded'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter, array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED), 'customfilter' => array('FROM_UNIXTIME(udate,\'%Y%m\') = ' . date('Ym', $dateUnix)))));
                    }

                    if (in_array('mavgwaittime',$params_execution['chart_type'])) {
                        $numberOfChats[$dateUnix]['avg_wait_time'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filtergt' => array('wait_time' => 0),'filterlt' => array('wait_time' => 600), 'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))),'AVG','wait_time');
                    }
                }
            }

            $numberOfChats = array_reverse($numberOfChats,true);

            return $numberOfChats;
        }
    }

    public static function formatFilterMail(& $filter, $table = '`lhc_mailconv_msg`') {

        if (isset($filter['filterin']['lh_chat.dep_id'])) {
            $filter['filterin'][$table.'.`dep_id`'] = $filter['filterin']['lh_chat.dep_id'];
            unset($filter['filterin']['lh_chat.dep_id']);
        }
        
        if (isset($filter['filterin']['lh_chat.user_id'])) {
            $filter['filterin'][$table.'.`user_id`'] = $filter['filterin']['lh_chat.user_id'];
            unset($filter['filterin']['lh_chat.user_id']);
        }

        if (isset($filter['filterin']['lh_chat.conv_user_id'])) {
            $filter['filterin'][$table.'.`conv_user_id`'] = $filter['filterin']['lh_chat.conv_user_id'];
            unset($filter['filterin']['lh_chat.conv_user_id']);
        }

    }

    public static function messagesPerUser($filter, $params_execution = []) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.messagesperuser',array('params_execution' => $params_execution, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        if (!isset($filter['filtergte']['udate'])) {
            $filter['filtergte']['udate'] = mktime(0,0,0,date('m'),date('d')-31,date('y'));
        }

        self::formatFilterMail($filter);

        $items = erLhcoreClassModelMailconvMessage::getCount(
            array_merge(array('sort' => 'total_records DESC', 'limit' => 50, 'group' => 'user_id'),$filter),
            '',
            false,
            'user_id, count(`lhc_mailconv_msg`.`id`) as total_records',
            false,
            true
        );

        return $items;
    }

    public static function messagesPerDep($filter, $params_execution = []) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.messagesperdep',array('params_execution' => $params_execution, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        if (!isset($filter['filtergte']['udate'])) {
            $filter['filtergte']['udate'] = mktime(0,0,0,date('m'),date('d')-31,date('y'));
        }

        self::formatFilterMail($filter);

        $items = erLhcoreClassModelMailconvMessage::getCount(
            array_merge(array('sort' => 'total_records DESC', 'limit' => 20, 'group' => 'dep_id'),$filter),
            '',
            false,
            'dep_id, count(`lhc_mailconv_msg`.`id`) as total_records',
            false,
            true
        );

        return $items;
    }

    public static function avgInteractionPerDep($filter, $params_execution = []) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.avginteractionperdep',array('params_execution' => $params_execution, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        if (!isset($filter['filtergte']['udate'])) {
            $filter['filtergte']['udate'] = mktime(0,0,0,date('m'),date('d')-31,date('y'));
        }

        $filter['filtergt']['interaction_time'] = 0;
        $filter['filterlt']['interaction_time'] = 600;

        self::formatFilterMail($filter);

        $items = erLhcoreClassModelMailconvMessage::getCount(
            array_merge(array('sort' => 'interaction_time DESC', 'limit' => 20, 'group' => 'dep_id'),$filter),
            '',
            false,
            'dep_id, avg(interaction_time) as interaction_time',
            false,
            true
        );

        return $items;
    }

    public static function avgInteractionPerUser($filter, $params_execution = []) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.avginteractionperuser',array('params_execution' => $params_execution, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        self::formatFilterMail($filter);

        if (!isset($filter['filtergte']['udate'])) {
            $filter['filtergte']['udate'] = mktime(0,0,0,date('m'),date('d')-31,date('y'));
        }

        $filter['filtergt']['interaction_time'] = 0;
        $filter['filterlt']['interaction_time'] = 600;

        $items = erLhcoreClassModelMailconvMessage::getCount(
            array_merge(array('sort' => 'interaction_time DESC', 'limit' => 20, 'group' => 'user_id'),$filter),
            '',
            false,
            'user_id, avg(interaction_time) as interaction_time',
            false,
            true
        );

        return $items;
    }

    public static function messagesPerHour($filter = array(), $params_execution = [])
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.messagesperhour', array('params_execution' => $params_execution, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        self::formatFilterMail($filter);

        $numberOfChats = array('total' => array(), 'byday' => array(), 'bydaymax' => array());

        if (!isset($filter['filtergte']['udate'])) {
            $filter['filtergte']['udate'] = mktime(0,0,0,date('m'),date('d')-30,date('y'));
        }

        $diffDays = ceil(((isset($filter['filterlte']['udate']) ? $filter['filterlte']['udate'] : time())-$filter['filtergte']['udate'])/(24*3600));

        for ($i = 0; $i < 24; $i++) {
            $dateHour = str_pad($i , 2, '0' , STR_PAD_LEFT);
            $numberOfChats['total'][$i] = erLhcoreClassModelMailconvMessage::getCount(array_merge(array('customfilter' =>  array('FROM_UNIXTIME(udate,\'%k\') = '. $dateHour)),$filter));
            $numberOfChats['byday'][$i] = $numberOfChats['total'][$i]/$diffDays;
            $numberOfChats['bydayavgresponse'][$i] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge(array('filterlt' => ['response_time' => 600], 'filtergt'=> ['response_time' => 0], 'customfilter' =>  array('FROM_UNIXTIME(udate,\'%k\') = '. $dateHour)),$filter),'',false,'avg(response_time) as response_time');
        }

        return $numberOfChats;
    }

    public static function attrByPerIntervalDay($filter = array(), $filterParams = array())
    {
        self::formatFilterMail($filter);

        $numberOfChats = array();
        $departmentFilter = array();

        $startTimestamp = time()-(31*24*3600);

        $limitDays = 31;

        if (isset($filter['filterlte']['udate']) && isset($filter['filtergte']['udate'])) {
            $daysDifference = ceil(($filter['filterlte']['udate'] - $filter['filtergte']['udate'])/(24*3600));
            if ($daysDifference <= 31 && $daysDifference > 0) {
                $limitDays = $daysDifference;
                $startTimestamp = $filter['filtergte']['udate'];
            }

        } elseif (isset($filter['filtergte']['udate'])) {
            $daysDifference = ceil((time() - $filter['filtergte']['udate'])/(24*3600));
            if ($daysDifference <= 31 && $daysDifference > 0) {
                $limitDays = $daysDifference;
                $startTimestamp = $filter['filtergte']['udate'];
            }
        } elseif (isset($filter['filterlte']['udate'])) {
            $limitDays = 31;
            $startTimestamp = $filter['filterlte']['udate']-(31*24*3600);
        }

        $validGroupFields = array(
            'user_id' => '`user_id`',
            'dep_id' => '`dep_id`',
            'mailbox_id' => '`mailbox_id`',
            'response_type' => '`response_type`',
        );

        $responseTypes = array(
            erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Unresponded'),
            erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No reply required'),
            erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Send messages'),
            erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responded by e-mail'),
        );

        for ($i = 0; $i < $limitDays;$i++) {
            $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

            $groupField = '`nick`';
            $attr = 'nick';
            if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                $groupField = $validGroupFields[$filterParams['group_field']];
                $attr = $filterParams['group_field'];
            }

            $justDemo = array_values(erLhcoreClassModelMailconvMessage::getList(array_merge_recursive($departmentFilter,$filter,array('sort' => 'nick_count DESC', 'select_columns' => 'count(id) as nick_count', 'group' => $groupField, 'limit' => (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10), 'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))));

            $returnArray = array();

            foreach ($justDemo as $demoItem) {
                $returnArray['color'][] = json_encode(erLhcoreClassChatStatistic::colorFromString($demoItem->{$attr}));

                if ($attr == 'user_id') {
                    $returnArray['nick'][] = json_encode($demoItem->{$attr} > 0 ? erLhcoreClassModelUser::fetch($demoItem->{$attr},true)->name_official : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Not assigned'));
                } else if ($attr == 'dep_id') {
                    $returnArray['nick'][] = json_encode($demoItem->{$attr} > 0 ? (string)$demoItem->department : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Not assigned'));
                } else if ($attr == 'mailbox_id') {
                    $returnArray['nick'][] = json_encode($demoItem->{$attr} > 0 ? (string)$demoItem->mailbox : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Not assigned'));
                } else if ($attr == 'response_type') {
                    $returnArray['nick'][] = json_encode($responseTypes[$demoItem->{$attr}]);
                } else {
                    $returnArray['nick'][] = json_encode($demoItem->{$attr});
                }

                $returnArray['data'][] = $demoItem->virtual_nick_count;
            }

            $numberOfChats[$dateUnix] = $returnArray;
        }

        $returnReversed = array();

        $limitDays = (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10);

        foreach ($numberOfChats as $dateIndex => $returnData) {
            for ($i = 0; $i < $limitDays; $i++) {
                $returnReversed[$i]['data'][] = isset($returnData['data'][$i]) ? $returnData['data'][$i] : 0;
                $returnReversed[$i]['color'][] = isset($returnData['color'][$i]) ? $returnData['color'][$i] : '""';
                $returnReversed[$i]['nick'][] = isset($returnData['nick'][$i]) ? $returnData['nick'][$i] : '""';
            }
        }

        return array('labels' => $numberOfChats, 'data' => $returnReversed);
    }

    public static function attrByPerInterval($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.statistic.attrbyperinterval', array('params_execution' => $filterParams, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

        if ($filterParams['group_by'] == 1) {
            return self::attrByPerIntervalDay($filter,$filterParams);
        } else {
            return self::attrByPerIntervalMonth($filter,$filterParams);
        }
    }

    public static function attrByPerIntervalMonth($filter = array(), $filterParams = array())
    {
        self::formatFilterMail($filter);

        $numberOfChats = array();
        $departmentFilter = array();

        $yearStart = date('y');
        $monthStart = date('m');

        if (isset($filter['filterlte']['udate'])){
            $yearStart = date('y',$filter['filterlte']['udate']);
            $monthStart = date('m',$filter['filterlte']['udate']);
        }

        $validGroupFields = array(
            'user_id' => '`user_id`',
            'dep_id' => '`dep_id`',
            'mailbox_id' => '`mailbox_id`',
            'response_type' => '`response_type`',
        );

        $responseTypes = array(
            erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Unresponded'),
            erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No reply required'),
            erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Send messages'),
            erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responded by e-mail'),
        );

        for ($i = 0; $i < 12;$i++) {
            $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);
            if (!isset($filter['filtergte']['udate']) || $filter['filtergte']['udate'] <= $dateUnix || date('Ym',$filter['filtergte']['udate']) == date('Ym',$dateUnix))
            {
                $numberOfChats[$dateUnix] = array ();

                $groupField = '`user_id`';
                $attr = 'user_id';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                    $attr = $filterParams['group_field'];
                }

                $justDemo = array_values(erLhcoreClassModelMailconvMessage::getList(array_merge_recursive($departmentFilter, $filter, array('sort' => 'nick_count DESC', 'select_columns' => 'count(`lhc_mailconv_msg`.`id`) as nick_count', 'group' => $groupField, 'limit' => (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10), 'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix))))));

                $returnArray = array();

                foreach ($justDemo as $demoItem) {
                    $returnArray['color'][] = json_encode(erLhcoreClassChatStatistic::colorFromString($demoItem->{$attr}));

                    if ($attr == 'user_id') {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr} > 0 && ($userStat = erLhcoreClassModelUser::fetch($demoItem->{$attr},true)) ? $userStat->name_official : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Not assigned'));
                    } else if ($attr == 'dep_id') {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr} > 0 ? (string)$demoItem->department : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Not assigned'));
                    } else if ($attr == 'mailbox_id') {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr} > 0 ? (string)$demoItem->mailbox : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Not assigned'));
                    } else if ($attr == 'response_type') {
                        $returnArray['nick'][] = json_encode($responseTypes[$demoItem->{$attr}]);
                    } else {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr});
                    }

                    $returnArray['data'][] = $demoItem->virtual_nick_count;
                }

                $numberOfChats[$dateUnix] = $returnArray;
            }
        }

        $numberOfChats = array_reverse($numberOfChats,true);

        $returnReversed = array();

        $limitDays = (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10);

        foreach ($numberOfChats as $dateIndex => $returnData) {
            for ($i = 0; $i < $limitDays; $i++) {
                $returnReversed[$i]['data'][] = isset($returnData['data'][$i]) ? $returnData['data'][$i] : 0;
                $returnReversed[$i]['color'][] = isset($returnData['color'][$i]) ? $returnData['color'][$i] : '""';
                $returnReversed[$i]['nick'][] = isset($returnData['nick'][$i]) ? $returnData['nick'][$i] : '""';
            }
        }

        return array('labels' => $numberOfChats, 'data' => $returnReversed);
    }
    
    public static function getResponseTypes()
    {
        $items = [];

        foreach (array(
            erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Unresponded'),
            erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No reply required'),
            erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','We have send this message as reply or forward'),
            erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responded by e-mail'),
        ) as $id => $item) {
            $itemSTD = new stdClass();
            $itemSTD->id = $id;
            $itemSTD->name = $item;
            $items[] = $itemSTD;
        }

        return $items;
    }

    public static function exportCSV($statistic, $type) {
        $filename = "report-" . $type . "-".date('Y-m-d').".csv";
        $fp = fopen('php://output', 'w');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);

        $weekDays = array(
            0 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Sunday'),
            1 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Monday'),
            2 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Tuesday'),
            3 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Wednesday'),
            4 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Thursday'),
            5 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Friday'),
            6 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Saturday'),
        );

        if ($type == 'cs_mmsgperinterval') {
            fputcsv($fp, ['date',
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responded by e-mail'),
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No reply required'),
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','We have send this message as reply or forward'),
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Unresponded')
            ]);
            foreach ($statistic['mmsgperinterval'] as $key => $data) {
                fputcsv($fp,[
                    date('Y-m-d H:i:s',$key),
                    $data['normal'],
                    $data['notrequired'],
                    $data['send'],
                    $data['unresponded']
                ]);
            }
        } else if ($type == 'cs_mmintperdep') {
            fputcsv($fp, ['department','department_id','interaction_time']);
            foreach ($statistic['mmintperdep'] as $key => $data) {
                fputcsv($fp,[
                    ((string)erLhcoreClassModelDepartament::fetch($data['dep_id'])),
                    $data['dep_id'],
                    $data['interaction_time'],
                ]);
            }
        } else if ($type == 'cs_mattrgroupvert') {

            $groupField = 'nickgroupingdatenick';

            $dates = ['Entity'];
            foreach ($statistic[$groupField]['labels'] as $date => $value) {
                $dates[] = date('Y-m-d H:i:s',$date);
            }

            fputcsv($fp, array_merge($dates,['Total']));
            $agents = [];
            foreach ($statistic[$groupField]['labels'] as $date => $value) {
                $agents = array_unique(array_merge($agents,(isset($value['nick']) ? $value['nick'] : [])));
            }

            foreach ($agents as $agent) {
                $agentRow = [];
                foreach ($statistic[$groupField]['labels'] as $date => $value) {
                    $index = array_search($agent, (isset($value['nick']) ? $value['nick'] : []));
                    if ($index !== false) {
                        $agentRow[] = $value['data'][$index];
                    } else {
                        $agentRow[] = 0;
                    }
                }
                fputcsv($fp,array_merge([(string)json_decode($agent)],$agentRow,[array_sum($agentRow)]));
            }

        } else if ($type == 'cs_mattrgroup') {
            $counter = 0;
            foreach ($statistic['nickgroupingdatenick']['labels'] as $date => $value) {
                fputcsv($fp,array_merge(array('Date'),(isset($value['nick']) ? $value['nick'] : [])));
                fputcsv($fp,array_merge(array(date('Y-m-d H:i:s',$date)),(isset($value['data']) ? $value['data'] : [])));
                $counter++;
            }
        } else if ($type == 'cs_mmsgperuser') {
            fputcsv($fp, ['user','user_id','total_records']);
            foreach ($statistic['mmsgperuser'] as $date => $value) {
                $obUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                fputcsv($fp,[
                    (string)$obUser,
                    $value['user_id'],
                    $value['total_records'],
                ]);
            }
        } else if ($type == 'cs_mmintperuser') {
            fputcsv($fp, ['user','user_id','interaction_time']);
            foreach ($statistic['mmintperuser'] as $date => $value) {
                $obUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                fputcsv($fp,[
                    (string)$obUser,
                    $value['user_id'],
                    $value['interaction_time'],
                ]);
            }
        } else if ($type == 'cs_msgperhour') {
            fputcsv($fp, ['hour','total','avg','avg_response_time']);
            foreach ($statistic['msgperhour']['total'] as $hour => $value) {
                fputcsv($fp,[
                    $hour,
                    $value,
                    $statistic['msgperhour']['byday'][$hour],
                    $statistic['msgperhour']['bydayavgresponse'][$hour],
                ]);
            }
        } else if ($type == 'cs_mmsgperdep') {
            fputcsv($fp, ['department','department_id','total_records']);
            foreach ($statistic['mmsgperdep'] as $value) {
                fputcsv($fp,[
                    ((string)erLhcoreClassModelDepartament::fetch($value['dep_id'])),
                    $value['dep_id'],
                    $value['total_records']
                ]);
            }
        } else if ($type == 'cs_mavgwaittime') {
            fputcsv($fp, ['date', 'avg_wait_time']);
            foreach ($statistic['mmsgperinterval'] as $date => $value) {
                fputcsv($fp,[
                    date('Y-m-d H:i:s',$date),
                    $value['avg_wait_time']
                ]);
            }
        } else {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail_statistic.export_csv',array('fp' => $fp, 'type' => $type, 'data' => $statistic));
        }
        exit;
    }

    public static function getAgentStatistic(& $itemState, $filter, $user) {

        if (isset($filter['filtergte']['time'])) {
            $filter['filtergte']['udate'] = $filter['filtergte']['time'];
            unset($filter['filtergte']['time']);
        }

        if (isset($filter['filterlte']['time'])) {
            $filter['filterlte']['udate'] = $filter['filterlte']['time'];
            unset($filter['filterlte']['time']);
        }

        // In statistic there will be option to search by mail message owner or conversation owner
        /*if (isset($filter['filter']['user_id'])){
            $filter['filter']['conv_user_id'] = $filter['filter']['user_id'];
            unset($filter['filter']['user_id']);
        }*/

        $items = erLhcoreClassModelMailconvMessage::getCount(
            array_merge(array('limit' => 50, 'group' => 'response_type'),$filter),
            '',
            false,
            'response_type, count(id) as total_records',
            false,
            true
        );

        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED] =
        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED] =
        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL] =
        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL] = 0;

        foreach ($items as $item) {
            $itemState['mail_statistic_'.$item['response_type']] = $item['total_records'];
        }

        $itemState['mail_statistic_total'] = $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED]+
        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED] +
        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL] +
        $itemState['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL];
    }

}

?>