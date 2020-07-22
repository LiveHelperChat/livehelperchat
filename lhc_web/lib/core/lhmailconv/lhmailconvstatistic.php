<?php

class erLhcoreClassMailconvStatistic {

    public static function messagesPerInterval($filter) {

        $numberOfChats = array();
        $departmentFilter = array();
        $departmentMsgFilter = array();

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

            $numberOfChats[$dateUnix]['normal'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
            $numberOfChats[$dateUnix]['notrequired'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
            $numberOfChats[$dateUnix]['send'] = (int)erLhcoreClassModelMailconvMessage::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL),'customfilter' =>  array('FROM_UNIXTIME(udate,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
        }

        return $numberOfChats;
    }


    public static function getNumberOfChatsPerMonth($filter = array(), $paramsExecution = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatspermonth',array('params_execution' => $paramsExecution, 'filter' => $filter));

        if ($statusWorkflow === false) {
            $numberOfChats = array();
            $departmentFilter = array();
            $departmentMsgFilter = array();

            // Message filter
            $msgFilter = $filter;

            /**
             * If department filter provided we have to use strict filter with table names
             * */
            $departmentMsgFilter['innerjoin']['lh_chat'] = array('lh_msg.chat_id','lh_chat.id');

            /**
             * If user ID provided only provided user chat's has to take effect
             * */
            if (isset($msgFilter['filter']['user_id'])){
                unset($msgFilter['filter']['user_id']);
                $msgFilter['filter']['lh_chat.user_id'] = $filter['filter']['user_id'];
            }

            if (isset($msgFilter['filterin']['user_id'])){
                unset($msgFilter['filterin']['user_id']);
                $msgFilter['filterin']['lh_chat.user_id'] = $filter['filterin']['user_id'];
            }

            if (isset($msgFilter['filtergte']['time'])){
                unset($msgFilter['filtergte']['time']);
                $msgFilter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
            }

            if (isset($msgFilter['filtergt']['user_id'])) {
                unset($msgFilter['filtergt']['user_id']);
                $msgFilter['filtergt']['lh_chat.user_id'] = $filter['filtergt']['user_id'];
            }

            $yearStart = date('y');
            $monthStart = date('m');

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
                $yearStart = date('y',$filter['filterlte']['time']);
                $monthStart = date('m',$filter['filterlte']['time']);
            }

            for ($i = 0; $i < 12;$i++) {
                $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);

                if (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))
                {

                    $numberOfChats[$dateUnix] = array ();

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('active',$paramsExecution['charttypes'])){
                        $numberOfChats[$dateUnix]['closed'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['active'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['operators'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['pending'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('unanswered',$paramsExecution['charttypes'])){
                        $numberOfChats[$dateUnix]['unanswered'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('proactivevsdefault',$paramsExecution['charttypes'])){
                        $numberOfChats[$dateUnix]['chatinitdefault'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['chatinitproact'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('msgtype',$paramsExecution['charttypes'])) {
                        $numberOfChats[$dateUnix]['msg_user'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filter' 	=> array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                        $numberOfChats[$dateUnix]['msg_operator'] = (int)erLhcoreClassChat::getCount(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix)))+$msgFilter+$departmentMsgFilter,'lh_msg','count(lh_msg.id)');
                        $numberOfChats[$dateUnix]['msg_system'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                        $numberOfChats[$dateUnix]['msg_bot'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                    }
                }
            }

            $numberOfChats = array_reverse($numberOfChats,true);

            return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }
}

?>