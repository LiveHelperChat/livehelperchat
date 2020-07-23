<?php

class erLhcoreClassMailconvStatistic {

    public static function messagesPerInterval($filter, $params_execution) {

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
                $numberOfChats[$dateUnix]['normal'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED, 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                $numberOfChats[$dateUnix]['notrequired'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED, 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                $numberOfChats[$dateUnix]['send'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED, 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                $numberOfChats[$dateUnix]['pending'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_PENDING),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                $numberOfChats[$dateUnix]['active'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_ACTIVE),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
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
                    $numberOfChats[$dateUnix]['normal'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED, 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    $numberOfChats[$dateUnix]['notrequired'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED, 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    $numberOfChats[$dateUnix]['send'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED, 'response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    $numberOfChats[$dateUnix]['pending'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_PENDING),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    $numberOfChats[$dateUnix]['active'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelMailconvMessage::STATUS_ACTIVE),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                }
            }

            $numberOfChats = array_reverse($numberOfChats,true);

            return $numberOfChats;
        }
    }

}

?>