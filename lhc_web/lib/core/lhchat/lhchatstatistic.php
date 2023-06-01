<?php

class erLhcoreClassChatStatistic {

    /**
     * Gets pending chats
     */
    public static function getTopTodaysOperators($limit = 1000, $offset = 0, $filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.gettoptodaysoperators',array('limit' => $limit, 'offset' => $offset, 'filter' => $filter));
         
        if ($statusWorkflow === false) {
                    
            $db = ezcDbInstance::get();
            
            if (empty($filter)) {      
            	$time = (time()-(24*3600));
            	$SQL = 'SELECT lh_chat.user_id,count(lh_chat.id) as assigned_chats FROM lh_chat WHERE time > :time AND user_id > 0 GROUP BY user_id';    	
            	$stmt = $db->prepare($SQL);
            	$stmt->bindValue( ':time', $time, PDO::PARAM_INT);
            	$filter['filtergte']['time'] = $time;
            } else {
                
                $paramsFilter = array();
                $bindFields = array();
                
                if (isset($filter['filtergte']['time'])) {   
                    $paramsFilter['time >= :timegte'] = $filter['filtergte']['time'];
                    $bindFields[] = ':timegte';
                }
                
                if (isset($filter['filterlte']['time'])){
                    $paramsFilter['time <= :timelte'] = $filter['filterlte']['time'];
                    $bindFields[] = ':timelte';
                }
    
                $appendUsers = '';
                if (isset($filter['filterin']['lh_chat.user_id'])){
                    $appendUsers .= 'AND user_id IN (' . implode(',', $filter['filterin']['lh_chat.user_id']) . ')';
                }
                
                if (isset($filter['filterin']['lh_chat.dep_id'])){
                    $appendUsers .= 'AND dep_id IN (' . implode(',', $filter['filterin']['lh_chat.dep_id']) . ')';
                }
                
                $SQL = 'SELECT lh_chat.user_id,count(lh_chat.id) as assigned_chats FROM lh_chat WHERE '.implode(' AND ', array_keys($paramsFilter)).' AND user_id > 0 ' . $appendUsers .' GROUP BY user_id';
                
                $stmt = $db->prepare($SQL);
                         
                $i = 0;
                foreach ($paramsFilter as $filterItemValue) {
                    $stmt->bindValue( $bindFields[$i], $filterItemValue, PDO::PARAM_INT);                                
                    $i++;
                }
            }
        	
        	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        	$stmt->execute();
        	$rows = $stmt->fetchAll();
        	    	
        	$usersID = array();
        	foreach ($rows as $item) {
        		$usersID[] = $item['user_id'];
        	}
    
        	if ( !empty($usersID) ) {
        		$users = erLhcoreClassModelUser::getUserList(array('limit' => $limit,'filterin' => array('id' => $usersID)));
        	}
        	
        	$filterStatsMsg = $filterStats = $filter;
        	
        	if (isset($filterStats['filterin']['lh_chat.user_id'])){
        	    unset($filterStats['filterin']['lh_chat.user_id']);
        	    unset($filterStatsMsg['filterin']['lh_chat.user_id']);
        	}
        	
        	if (isset($filterStatsMsg['filtergte']['time'])) {
        	    $filterStatsMsg['filtergte']['lh_msg.time'] = $filterStatsMsg['filtergte']['time'];
        	    unset($filterStatsMsg['filtergte']['time']);
        	}
        	
        	if (isset($filterStatsMsg['filterlte']['time'])) {
        	    $filterStatsMsg['filterlte']['lh_msg.time'] = $filterStatsMsg['filterlte']['time'];
        	    unset($filterStatsMsg['filterlte']['time']);
        	}
    
        	$usersReturn = array();
        	foreach ($rows as $row) {
    
        	    $user = null;
        	    if (isset($users[$row['user_id']])) {
        	        $user = $users[$row['user_id']];
        	    } else {
        	        $user = new erLhcoreClassModelUser();
        	        $user->id = $row['user_id'];
        	        $user->username = 'Not found user - ' . $row['user_id'];
        	    }
    
        		$usersReturn[$row['user_id']] = $user;
        		$usersReturn[$row['user_id']]->statistic_total_chats = $row['assigned_chats'];
        		$usersReturn[$row['user_id']]->statistic_total_messages = erLhcoreClassChat::getCount(array_merge_recursive($filterStatsMsg,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id')),'filter' => array('lh_msg.user_id' => $row['user_id']))),'lh_msg','count(lh_msg.id)');
        		$usersReturn[$row['user_id']]->statistic_upvotes = erLhcoreClassChat::getCount(array_merge_recursive($filterStats,array('filter' => array('fbst' => 1,'user_id' => $row['user_id']))));
        		$usersReturn[$row['user_id']]->statistic_downvotes = erLhcoreClassChat::getCount(array_merge_recursive($filterStats,array('filter' => array('fbst' => 2,'user_id' => $row['user_id']))));
        	}

        	return $usersReturn;

        } else {
            return $statusWorkflow['list'];
        }
    }
    
    /*
     * Returns last 12 month chats statistic
     * */
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

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && (in_array('total_chats',$paramsExecution['charttypes']) || in_array('active',$paramsExecution['charttypes']))) {
                        $numberOfChats[$dateUnix]['closed'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['active'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['operators'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['pending'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['bot'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_BOT_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['total_chats'] = $numberOfChats[$dateUnix]['pending'] + $numberOfChats[$dateUnix]['operators'] + $numberOfChats[$dateUnix]['active'] + $numberOfChats[$dateUnix]['closed'];
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('unanswered',$paramsExecution['charttypes'])) {
                        $numberOfChats[$dateUnix]['unanswered'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('proactivevsdefault',$paramsExecution['charttypes'])) {
                        $numberOfChats[$dateUnix]['chatinitdefault'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['chatinitproact'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filtergt' => array('invitation_id' => 0), 'filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                        $numberOfChats[$dateUnix]['chatinitmanualinv'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array( 'invitation_id' => 0, 'chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('msgtype',$paramsExecution['charttypes'])) {

                        $filterOur = array_merge_recursive(array('filter' 	=> array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter);
                        $filterOur['innerjoin'] = array_reverse($filterOur['innerjoin'],true);
                        $numberOfChats[$dateUnix]['msg_user'] = (int)erLhcoreClassChat::getCount($filterOur,'lh_msg','count(lh_msg.id)');

                        $filterOur =array_merge_recursive(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter);
                        $filterOur['innerjoin'] = array_reverse($filterOur['innerjoin'],true);
                        $numberOfChats[$dateUnix]['msg_operator'] = (int)erLhcoreClassChat::getCount($filterOur,'lh_msg','count(lh_msg.id)');

                        $filterOur = array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter);
                        $filterOur['innerjoin'] = array_reverse($filterOur['innerjoin'],true);
                        $numberOfChats[$dateUnix]['msg_system'] = (int)erLhcoreClassChat::getCount($filterOur,'lh_msg','count(lh_msg.id)');

                        $filterOur = array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter);
                        $filterOur['innerjoin'] = array_reverse($filterOur['innerjoin'],true);
                        $numberOfChats[$dateUnix]['msg_bot'] = (int)erLhcoreClassChat::getCount($filterOur,'lh_msg','count(lh_msg.id)');
                    }
                }
        	}
        	
        	$numberOfChats = array_reverse($numberOfChats,true);

        	return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function getNumberOfChatsPerWeekDay($filter = array(), $paramsExecution = array())
    {
         $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatsperweekday',array('params_execution' => $paramsExecution, 'filter' => $filter));

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
            if (isset($msgFilter['filter']['user_id'])) {
                unset($msgFilter['filter']['user_id']);
                $msgFilter['filter']['lh_chat.user_id'] = $filter['filter']['user_id'];
            }

            if (isset($msgFilter['filterin']['user_id'])) {
                unset($msgFilter['filterin']['user_id']);
                $msgFilter['filterin']['lh_chat.user_id'] = $filter['filterin']['user_id'];
            }

            if (isset($msgFilter['filtergte']['time'])) {
                unset($msgFilter['filtergte']['time']);
                $msgFilter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
            }

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            if (isset($msgFilter['filtergt']['user_id'])) {
                unset($msgFilter['filtergt']['user_id']);
                $msgFilter['filtergt']['lh_chat.user_id'] = $filter['filtergt']['user_id'];
            }

            for ($day = 1; $day < 8; $day++) {
                $i = $day;
                if ($i == 7) {
                    $i = 0;
                }

                $numberOfChats[$i] = array();

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && (in_array('active',$paramsExecution['charttypes']) || in_array('total_chats',$paramsExecution['charttypes']))) {
                    $numberOfChats[$i]['closed'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['active'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['operators'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['pending'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['bot'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_BOT_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['total_chats'] = $numberOfChats[$i]['closed'] + $numberOfChats[$i]['active'] + $numberOfChats[$i]['operators'] + $numberOfChats[$i]['pending'] + $numberOfChats[$i]['bot'];
                }

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('unanswered',$paramsExecution['charttypes'])){
                    $numberOfChats[$i]['unanswered'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                }

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('proactivevsdefault',$paramsExecution['charttypes'])){
                    $numberOfChats[$i]['chatinitdefault'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['chatinitproact'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                    $numberOfChats[$i]['chatinitmanualinv'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array( 'invitation_id' => 0, 'chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' => array('FROM_UNIXTIME(time,\'%w\') = '. $i))));
                }

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('msgtype',$paramsExecution['charttypes'])) {
                    $numberOfChats[$i]['msg_user'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filter' 	=> array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%w\') = '. $i)),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                    $numberOfChats[$i]['msg_operator'] = (int)erLhcoreClassChat::getCount(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%w\') = '. $i))+$msgFilter+$departmentMsgFilter,'lh_msg','count(lh_msg.id)');
                    $numberOfChats[$i]['msg_system'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%w\') = '. $i)),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                    $numberOfChats[$i]['msg_bot'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%w\') = '. $i)),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                }
            }

            return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function getNumberOfChatsPerWeek($filter = array(), $paramsExecution = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatsperweek',array('params_execution' => $paramsExecution, 'filter' => $filter));

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
                $msgFilter['filter']['`lh_chat`.`user_id`'] = $filter['filter']['user_id'];
            }

            if (isset($msgFilter['filtergt']['user_id'])){
                unset($msgFilter['filtergt']['user_id']);
                $msgFilter['filtergt']['`lh_chat`.`user_id`'] = $filter['filtergt']['user_id'];
            }

            if (isset($msgFilter['filterin']['user_id'])){
                unset($msgFilter['filterin']['user_id']);
                $msgFilter['filterin']['`lh_chat`.`user_id`'] = $filter['filterin']['user_id'];
            }

            if (isset($msgFilter['filtergte']['time'])){
                unset($msgFilter['filtergte']['time']);
                $msgFilter['filtergte']['`lh_msg`.`time`'] = $filter['filtergte']['time'];
            }

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['`lh_msg`.`time`'] = $filter['filterlte']['time'];
            }

            $startTimestamp = time()-(42*7*24*3600);

            $limitDays = 42;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 42;
                $startTimestamp = $filter['filterlte']['time']-(42*7*24*3600);
            }

            $weekStarted = false;
            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+($i*7),date('y',$startTimestamp));

                if ($weekStarted == false) {
                    $weekStarted = true;

                    if (date('N', $dateUnix) != 1) {
                        // Adjust start time to be it monday
                        $startTimestamp = $startTimestamp - ((date('N', $startTimestamp)-1)*24*3600);

                        continue; // First day is not a monday, skip to next week
                    }
                }

                // This week has not ended, so exclude it
                if (date('YW') == date('YW',$dateUnix) || time() < $dateUnix) {
                    continue;
                }

                if (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))
                {
                    $numberOfChats[$dateUnix] = array();

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && (in_array('total_chats',$paramsExecution['charttypes']) || in_array('active',$paramsExecution['charttypes']))){
                        $numberOfChats[$dateUnix]['closed'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                        $numberOfChats[$dateUnix]['active'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                        $numberOfChats[$dateUnix]['operators'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                        $numberOfChats[$dateUnix]['pending'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                        $numberOfChats[$dateUnix]['total_chats'] = $numberOfChats[$dateUnix]['pending'] + $numberOfChats[$dateUnix]['operators'] + $numberOfChats[$dateUnix]['active'] + $numberOfChats[$dateUnix]['closed'];
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('unanswered',$paramsExecution['charttypes'])){
                        $numberOfChats[$dateUnix]['unanswered'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('proactivevsdefault',$paramsExecution['charttypes'])){
                        $numberOfChats[$dateUnix]['chatinitdefault'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                        $numberOfChats[$dateUnix]['chatinitproact'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                        $numberOfChats[$dateUnix]['chatinitmanualinv'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array( 'invitation_id' => 0, 'chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' => array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))));
                    }

                    if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('msgtype',$paramsExecution['charttypes'])) {
                        $numberOfChats[$dateUnix]['msg_user'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filter' 	=> array('`lh_msg`.`user_id`' => 0),'customfilter' =>  array('FROM_UNIXTIME(`lh_msg`.`time`,\'%Y%v\') = '. date('YW',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                        $numberOfChats[$dateUnix]['msg_operator'] = (int)erLhcoreClassChat::getCount(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%v\') = '. date('YW',$dateUnix)))+$msgFilter+$departmentMsgFilter,'lh_msg','count(lh_msg.id)');
                        $numberOfChats[$dateUnix]['msg_system'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%v\') = '. date('YW',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                        $numberOfChats[$dateUnix]['msg_bot'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%v\') = '. date('YW',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                    }

                }
            }

            return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function getNumberOfChatsWaitTimePerWeekDay($filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatswaittimeperweekday', array('filter' => $filter));

        if ($statusWorkflow === false) {

            $numberOfChats = array();

            $weekStarted = false;

            for ($day = 1; $day < 8; $day++) {
                $i = $day;
                if ($i == 7) {
                    $i = 0;
                }

                $numberOfChats[$i] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array(' (wait_time > 0 AND wait_time < 600 AND FROM_UNIXTIME(time,\'%w\') = '. $i . ') '))),'lh_chat','AVG(wait_time)');
            }

            return $numberOfChats;

        } else {
            return $statusWorkflow['list'];
        }
    }


    public static function getNumberOfChatsWaitTimePerWeek($filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatswaittimeperweek', array('filter' => $filter));

        if ($statusWorkflow === false) {

            $startTimestamp = time()-(42*7*24*3600);

            $limitDays = 42;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }

            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 42;
                $startTimestamp = $filter['filterlte']['time']-(42*7*24*3600);
            }

            $numberOfChats = array();

            $weekStarted = false;

            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+($i*7),date('y',$startTimestamp));

                if ($weekStarted == false) {
                    $weekStarted = true;

                    if (date('N', $dateUnix) != 1) {
                        // Adjust start time to be it monday
                        $startTimestamp = $startTimestamp - ((date('N', $startTimestamp)-1)*24*3600);
                        continue; // First day is not a monday, skip to next week
                    }
                }

                // This week has not ended, so exclude it
                if (date('YW') == date('YW',$dateUnix) || time() < $dateUnix) {
                    continue;
                }

                $numberOfChats[$dateUnix] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array('(wait_time < 600 AND wait_time > 0 AND FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix) . ')'))),'lh_chat','AVG(wait_time)');
            }

            return $numberOfChats;

        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function getLast24HStatistic($filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getlast24hstatistic',array('filter' => $filter));
         
        if ($statusWorkflow === false) {
            $returnArray = array();
            $returnArray['totalchats'] = erLhcoreClassChat::getCount($filter); 
            $returnArray['totalpendingchats'] = erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))));
            $returnArray['total_active_chats'] = erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))));
            $returnArray['total_closed_chats'] = erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))));
            $returnArray['total_unanswered_chat'] = erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('unanswered_chat' => 1))));
            $returnArray['chatbox_chats'] = erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT))));
                    
            // Total messages (including visitors, system and operators messages)
            $filterMsg = array_merge_recursive($filter,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id'))));
            
            if (isset($filterMsg['filtergte']['time'])) {
                $filterMsg['filtergte']['lh_msg.time'] = $filterMsg['filtergte']['time'];
                unset($filterMsg['filtergte']['time']);
            }
            
            if (isset($filterMsg['filterlte']['time'])) {
                $filterMsg['filterlte']['lh_msg.time'] = $filterMsg['filterlte']['time'];
                unset($filterMsg['filterlte']['time']);
            }
            
            $returnArray['ttmall'] = erLhcoreClassChat::getCount($filterMsg,'lh_msg','count(lh_msg.id)');
            $returnArray['ttmvis'] = erLhcoreClassChat::getCount(array_merge_recursive($filterMsg,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id')),'filter' => array('lh_msg.user_id' => 0))),'lh_msg','count(lh_msg.id)');
            $returnArray['ttmsys'] = erLhcoreClassChat::getCount(array_merge_recursive($filterMsg,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id')), 'filterin' => array('lh_msg.user_id' => array(-1,-2)))),'lh_msg','count(lh_msg.id)');
            $returnArray['ttmop'] = $returnArray['ttmall'] - $returnArray['ttmvis'] - $returnArray['ttmsys'];

            return $returnArray;
            
        } else {
            return $statusWorkflow['list'];
        }
    }
    
    public static function getNumberOfChatsPerDay($filter = array(), $paramsExecution = array())
    {	
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatsperday', array('params_execution' => $paramsExecution, 'filter' => $filter));
         
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
        	
        	if (isset($msgFilter['filtergte']['time'])) {
        	    unset($msgFilter['filtergte']['time']);
        	    $msgFilter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
        	}
        		
        	if (isset($msgFilter['filterlte']['time'])) {
        	    unset($msgFilter['filterlte']['time']);
        	    $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
        	}

            if (isset($msgFilter['filtergt']['user_id'])) {
                unset($msgFilter['filtergt']['user_id']);
                $msgFilter['filtergt']['lh_chat.user_id'] = $filter['filtergt']['user_id'];
            }

            if (isset($msgFilter['filterin']['user_id'])) {
                unset($msgFilter['filterin']['user_id']);
                $msgFilter['filterin']['`lh_chat`.`user_id`'] = $filter['filterin']['user_id'];
            }

        	$startTimestamp = time()-(31*24*3600);
        	
        	$limitDays = 31;
        	
        	if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
        	    $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600));
        	    if ($daysDifference <= 31 && $daysDifference > 0) {
        	        $limitDays = $daysDifference;
        	        $startTimestamp = $filter['filtergte']['time'];
        	    }
        	    
        	} elseif (isset($filter['filtergte']['time'])) {    	    
        	    $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600));
        	    if ($daysDifference <= 31 && $daysDifference > 0) {
        	        $limitDays = $daysDifference;
        	        $startTimestamp = $filter['filtergte']['time'];
        	    }    	        	    
        	} elseif (isset($filter['filterlte']['time'])) { 
        	        $limitDays = 31;
        	        $startTimestamp = $filter['filterlte']['time']-(31*24*3600);
        	}
    
        	for ($i = 0; $i < $limitDays;$i++) {
        		$dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && (in_array('total_chats',$paramsExecution['charttypes']) || in_array('active',$paramsExecution['charttypes']))){
                    $numberOfChats[$dateUnix]['closed'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                    $numberOfChats[$dateUnix]['active'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                    $numberOfChats[$dateUnix]['operators'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                    $numberOfChats[$dateUnix]['pending'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                    $numberOfChats[$dateUnix]['total_chats'] = $numberOfChats[$dateUnix]['closed'] + $numberOfChats[$dateUnix]['active']  + $numberOfChats[$dateUnix]['operators'] + $numberOfChats[$dateUnix]['pending'];
                }

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('unanswered',$paramsExecution['charttypes'])){
                    $numberOfChats[$dateUnix]['unanswered'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                }

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('proactivevsdefault',$paramsExecution['charttypes'])){
                    $numberOfChats[$dateUnix]['chatinitdefault'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                    $numberOfChats[$dateUnix]['chatinitproact'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                    $numberOfChats[$dateUnix]['chatinitmanualinv'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('invitation_id' => 0, 'chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))));
                }

                if (isset($paramsExecution['charttypes']) && is_array($paramsExecution['charttypes']) && in_array('msgtype',$paramsExecution['charttypes'])) {
                    $numberOfChats[$dateUnix]['msg_user'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filter' 	=> array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                    $numberOfChats[$dateUnix]['msg_operator'] = (int)erLhcoreClassChat::getCount(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))+$msgFilter+$departmentMsgFilter,'lh_msg','count(lh_msg.id)');
                    $numberOfChats[$dateUnix]['msg_system'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                    $numberOfChats[$dateUnix]['msg_bot'] = (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)');
                }

        	}
        	
        	return $numberOfChats;
        } else {
    	    return $statusWorkflow['list'];
    	}
    }
    
    public static function getNumberOfChatsWaitTime($filter = array())
    {	
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatswaittime',array('filter' => $filter));
         
        if ($statusWorkflow === false) {
        	$numberOfChats = array();
        	    	 	    	    	    
        	for ($i = 0; $i < 12;$i++) {
        		$dateUnix = mktime(0,0,0,date('m')-$i,0, date('y'));


        		$numberOfChats[$dateUnix] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array('(wait_time > 0 AND wait_time < 600 AND FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix). ' )' ))),'lh_chat','AVG(wait_time)');
        	}
    
        	$numberOfChats = array_reverse($numberOfChats,true);
        	
        	return $numberOfChats;
        	
        } else {
    	    return $statusWorkflow['list'];
    	}
    }
        
    public static function getNumberOfChatsWaitTimePerDay($filter = array())
    {	
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatswaittimeperday', array('filter' => $filter));
         
        if ($statusWorkflow === false) {
        
            $startTimestamp = time()-(31*24*3600);
             
            $limitDays = 31;
             
            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
                	
            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 31;
                $startTimestamp = $filter['filterlte']['time']-(31*24*3600);
            }
            
        	$numberOfChats = array();
        	    	 	    	    	    
        	for ($i = 0; $i < $limitDays;$i++) {
        		$dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));
        		$numberOfChats[$dateUnix] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array('(wait_time > 0 AND wait_time < 600 AND FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix). ' )'))),'lh_chat','AVG(wait_time)');
        	}
        	    	
        	return $numberOfChats;
        	
        } else {
    	    return $statusWorkflow['list'];
    	}
    }
    
    /**
     * @desc not used
     * 
     * @param unknown $filter
     * @return multitype:|unknown
     */
    public static function getNumberOfChatsPerMonthUnanswered($filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatspermonthunanswered', array('filter' => $filter));
         
        if ($statusWorkflow === false) {
            
            $startTimestamp = time()-(31*24*3600);
             
            $limitDays = 31;
             
            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
                 
            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 31;
                $startTimestamp = $filter['filterlte']['time']-(31*24*3600);
            }
            
            $numberOfChats = array();
            
            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));            
                $numberOfChats[$dateUnix] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array('unanswered_chat = 1 AND FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))),'lh_chat');
            }
                            
            return array_reverse($numberOfChats,true);
            
        } else {
            return $statusWorkflow['list'];
        }
    }
    
    public static function getWorkLoadStatistic($days = 30, $filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getworkloadstatistic',array('filter' => $filter, 'days' => $days));
         
        if ($statusWorkflow === false) {
        
        	$numberOfChats = array('total' => array(), 'byday' => array(), 'bydaymax' => array());

            if (!isset($filter['filtergte']['time'])) {
                $filter['filtergte']['time'] = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
            }

            $diffDays = ceil(((isset($filter['filterlte']['time']) ? $filter['filterlte']['time'] : time())-$filter['filtergte']['time'])/(24*3600));

        	for ($i = 0; $i < 24; $i++) {
        		$dateHour = str_pad($i , 2, '0' , STR_PAD_LEFT);
        		$numberOfChats['total'][$i] = erLhcoreClassModelChat::getCount(array_merge_recursive(array('customfilter' =>  array('FROM_UNIXTIME(time,\'%k\') = '. $dateHour)),$filter));
                $numberOfChats['byday'][$i] = $numberOfChats['total'][$i]/$diffDays;
                $numberOfChats['bydaymax'][$i] = erLhcoreClassModelChat::getCount(array_merge_recursive(array('sort' => 'total_records DESC', 'limit' => 1, 'group' => 'FROM_UNIXTIME(time,\'%Y%m%d\')', 'customfilter' =>  array('FROM_UNIXTIME(time,\'%k\') = '. $dateHour)),$filter),'',false,'max(time) as time, count(`lh_chat`.`id`) as total_records', false);

                if (!is_array($numberOfChats['bydaymax'][$i])) {
                    $numberOfChats['bydaymax'][$i] = array();
                    if (!isset($numberOfChats['bydaymax'][$i]['time'])){
                        $numberOfChats['bydaymax'][$i]['time'] = 0;
                    }

                    if (!isset($numberOfChats['bydaymax'][$i]['total_records'])) {
                        $numberOfChats['bydaymax'][$i]['total_records'] = 0;
                    }
                }
        	}

        	return $numberOfChats;
        	
        } else {
    	    return $statusWorkflow['list'];
    	}
    }
    
    public static function getAverageChatduration($days = 30, $filter = array()) {
    	
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getaveragechatduration',array('days' => $days, 'filter' => $filter));
         
        if ($statusWorkflow === false) {

            if (!isset($filter['filtergte']['time'])) {
                $filter['filtergte']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days, date('y'));
            }
            
            $filter['filtergt']['user_id'] = 0;
            
            $filterCombined = array_merge_recursive($filter,array('filtergt' => array('chat_duration' => 0),'filter' =>  array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)));
                    
        	return erLhcoreClassChat::getCount($filterCombined, 'lh_chat', 'AVG(chat_duration)');
        	
        } else {
    	    return $statusWorkflow['list'];
    	}
    }

    public static function cannedStatistic($days = 30, $filter = array(), $paramsExecution = []) {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getcannedstatistic',array('days' => $days, 'filter' => $filter));

        if ($statusWorkflow === false) {

            $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));

            if (isset($filter['filtergt']['user_id'])) {
                $filter['filtergt']['`lh_chat`.`user_id`'] = $filter['filtergt']['user_id'];
                unset($filter['filtergt']['user_id']);
            }

            if (isset($filter['filter']['user_id'])) {
                $filter['filter']['`lh_chat`.`user_id`'] = $filter['filter']['user_id'];
                unset($filter['filter']['user_id']);
            }

            $generalFilter = erLhcoreClassChatStatistic::formatFilter($filter);
            $generalJoin = erLhcoreClassChatStatistic::formatJoin($filter);

            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
            $appendFilterTime = '';
            if ($useTimeFilter == true) {
                $appendFilterTime = ' ctime > :time ';
                if (!empty($generalFilter)) {
                    $generalFilter = ' AND ' . $generalFilter;
                }
            }

            if ($paramsExecution['action'] == 'count') {
                $sql = "SELECT count(`id`)  FROM (SELECT `lh_canned_msg_use`.`canned_id` as `id` FROM lh_chat INNER JOIN `lh_canned_msg_use` ON `lh_canned_msg_use`.`chat_id` = `lh_chat`.`id` INNER JOIN `lh_canned_msg` ON `lh_canned_msg`.`id` = `lh_canned_msg_use`.`canned_id` {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY `lh_canned_msg_use`.`canned_id`) as total_stats";

                $db = ezcDbInstance::get();
                $stmt = $db->prepare($sql);

                if ($useTimeFilter == true) {
                    $stmt->bindValue(':time',$dateUnixPast);
                }

                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                return $stmt->fetchColumn();
            }

            $limit = " LIMIT 20 OFFSET " .  (isset($paramsExecution['offset']) ? $paramsExecution['offset'] : 20);

            if ($paramsExecution['action'] == 'export') {
                $limit = '';
            }

            $sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,`lh_canned_msg_use`.`canned_id` FROM lh_chat INNER JOIN `lh_canned_msg_use` ON `lh_canned_msg_use`.`chat_id` = `lh_chat`.`id` INNER JOIN `lh_canned_msg` ON `lh_canned_msg`.`id` = `lh_canned_msg_use`.`canned_id` {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY `lh_canned_msg_use`.`canned_id` ORDER BY number_of_chats DESC,`lh_canned_msg_use`.`canned_id` DESC  " . $limit;
             
            $db = ezcDbInstance::get();
            $stmt = $db->prepare($sql);

            if ($useTimeFilter == true) {
                $stmt->bindValue(':time',$dateUnixPast);
            }

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $stats = $stmt->fetchAll();

            if ($paramsExecution['action'] == 'export') {
                $filename = "report-canned-".date('Y-m-d').".csv";
                $fp = fopen('php://output', 'w');

                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename='.$filename);

                fputcsv($fp, ['ID','Title','Used']);
                foreach ($stats as $key => $data) {
                    fputcsv($fp,[
                        $data['canned_id'],
                        (string)erLhcoreClassModelCannedMsg::fetch($data['canned_id']),
                        $data['number_of_chats'],
                    ]);
                }
                exit;
            }

            return $stats;

        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function subjectsStatistic($days = 30, $filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getsubjectsstatistic',array('days' => $days, 'filter' => & $filter));

        if ($statusWorkflow === false) {

            $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));

            $generalFilter = self::formatFilter($filter);
            $generalJoin = self::formatJoin($filter);

            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
            $appendFilterTime = '';
            if ($useTimeFilter == true) {
                $appendFilterTime = ' time > :time ';
                if (!empty($generalFilter)) {
                    $generalFilter = ' AND ' . $generalFilter;
                }
            }

            $sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,`lh_abstract_subject_chat`.`subject_id` FROM lh_chat INNER JOIN `lh_abstract_subject_chat` ON `lh_abstract_subject_chat`.`chat_id` = `lh_chat`.`id` {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY `lh_abstract_subject_chat`.`subject_id` ORDER BY number_of_chats DESC LIMIT 40";

            $db = ezcDbInstance::get();
            $stmt = $db->prepare($sql);

            if ($useTimeFilter == true) {
                $stmt->bindValue(':time',$dateUnixPast);
            }

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $stats = $stmt->fetchAll();

            return $stats;

        } else {
            return $statusWorkflow['list'];
        }
    }
    
    public static function getTopChatsByCountry($days = 30, $filter = array()) 
    {
    	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
    	
    	$statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.gettopchatsbycountry',array('days' => $days, 'filter' => $filter));
    	
    	if ($statusWorkflow === false) {
        	$generalFilter = self::formatFilter($filter);
        	$generalJoin = self::formatJoin($filter);

        	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
        	$appendFilterTime = '';
        	 
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'time > :time ';
        	}
        	
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}
        	
        	$sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,country_name FROM lh_chat {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY country_code,country_name ORDER BY number_of_chats DESC LIMIT 40";
        	$db = ezcDbInstance::get();
        	$stmt = $db->prepare($sql);
        	
        	if ($useTimeFilter == true) {
        		$stmt->bindValue(':time',$dateUnixPast);
        	}
        	
        	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        	$stmt->execute();
        	return $stmt->fetchAll();
    	} else {
    	    return $statusWorkflow['list'];
    	}    	
    }

    public static function exportAverageOfChatsDialogsByUser($days = 30, $filter = array()) {
        
        $data = self::averageOfChatsDialogsByUser($days,$filter,5000);   
        
        include 'lib/core/lhform/PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Report');
         
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','User ID'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat average in seconds'));
         
        $i = 2;
        foreach ($data as $item) {
            
            $key = 0;      
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item['user_id']);

            $key++; 
            $obUser = erLhcoreClassModelUser::fetch($item['user_id'],true);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (is_object($obUser) ? $obUser->username : $item['user_id']));
         
            
            $key++;      
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item['avg_chat_duration']);
                     
            $i++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');
         
        // It will be called file.xls
        header('Content-Disposition: attachment; filename="report.xlsx"');
         
        // Write file to the browser
        $objWriter->save('php://output');
    }
    
    
    public static function averageOfChatsDialogsByUser($days = 30, $filter = array(), $limit = 40)
    {    	    
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.averageofchatsdialogsbyuser',array('days' => $days, 'filter' => $filter, 'limit' => $limit));
        
        if ($statusWorkflow === false) {
            $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
            
            $filter['filter']['status'] = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
            $filter['filtergt']['chat_duration'] = 0;
            $filter['filtergt']['user_id'] = 0;
            
            $generalFilter = self::formatFilter($filter);
            $generalJoin = self::formatJoin($filter);

            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
            $appendFilterTime = '';
             
            if ($useTimeFilter == true) {
                $appendFilterTime = 'time > :time ';
            }
            
            if ($generalFilter != '' && $useTimeFilter == true) {
                $generalFilter = ' AND '.$generalFilter;
            }
             
            $sql = "SELECT AVG(chat_duration) AS avg_chat_duration,user_id FROM lh_chat {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY avg_chat_duration DESC LIMIT ".$limit;
            $db = ezcDbInstance::get();
            $stmt = $db->prepare($sql);
            
            if ($useTimeFilter == true) {
                $stmt->bindValue(':time',$dateUnixPast);
            }
            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            return $statusWorkflow['list'];
        }    	
    }

    public static function numberOfChatsDialogsByDepartment($days = 30, $filter = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.numberofchatsdialogsbydepartment',array('days' => $days, 'filter' => $filter));

        if ($statusWorkflow === false) {
            $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));

            $generalFilter = self::formatFilter($filter);
            $generalJoin = self::formatJoin($filter);

            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
            $appendFilterTime = '';

            if ($useTimeFilter == true) {
                $appendFilterTime = 'time > :time ';
            }

            if ($generalFilter != '' && $useTimeFilter == true) {
                $generalFilter = ' AND '.$generalFilter;
            }

            $sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats, dep_id FROM lh_chat {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY dep_id ORDER BY number_of_chats DESC LIMIT 40";

            $db = ezcDbInstance::get();
            $stmt = $db->prepare($sql);

            if ($useTimeFilter == true) {
                $stmt->bindValue(':time',$dateUnixPast);
            }

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            return $stmt->fetchAll();

        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function numberOfChatsDialogsByUser($days = 30, $filter = array(), $groupField = 'user_id')
    {
        if ($groupField == 'transfer_uid') {
            if (isset($filter['filterin']['user_id'])) {
                $filter['filterin']['transfer_uid'] = $filter['filterin']['user_id'];
                unset($filter['filterin']['user_id']);
            }
            if (isset($filter['filterin']['lh_chat.user_id'])) {
                $filter['filterin']['lh_chat.transfer_uid'] = $filter['filterin']['lh_chat.user_id'];
                unset($filter['filterin']['lh_chat.user_id']);
            }
            if (isset($filter['filtergt']['user_id'])) {
                $filter['filtergt']['transfer_uid'] = $filter['filtergt']['user_id'];
            }
        }

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.numberofchatsdialogsbyuser',array('group_field' => $groupField, 'days' => $days, 'filter' => $filter));

        if ($statusWorkflow === false) {
        	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        	
        	$generalFilter = self::formatFilter($filter);
        	$generalJoin = self::formatJoin($filter);

        	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
        	$appendFilterTime = '';
        	
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'time > :time ';
        	}
        	 
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}

            $column = 'user_id';

            if ($groupField == 'transfer_uid') {
                $column = '`transfer_uid` AS `user_id`';
            }

        	$sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,{$column} FROM lh_chat {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY {$groupField} ORDER BY number_of_chats DESC LIMIT 40";
        	
        	$db = ezcDbInstance::get();
        	$stmt = $db->prepare($sql);
        	
        	if ($useTimeFilter == true) {
        		$stmt->bindValue(':time',$dateUnixPast);
        	}
        	
        	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        	$stmt->execute();
        	return $stmt->fetchAll();
        	
        } else {
            return $statusWorkflow['list'];
        }
    }
    public static function numberOfChatsDialogsByUserParticipant($days = 30, $filter = array(), $groupField = '`lh_chat_participant`.`user_id`')
    {
        if ($groupField == 'transfer_uid') {
            if (isset($filter['filterin']['user_id'])) {
                $filter['filterin']['transfer_uid'] = $filter['filterin']['user_id'];
                unset($filter['filterin']['user_id']);
            }
            if (isset($filter['filterin']['lh_chat.user_id'])) {
                $filter['filterin']['lh_chat.transfer_uid'] = $filter['filterin']['lh_chat.user_id'];
                unset($filter['filterin']['lh_chat.user_id']);
            }
            if (isset($filter['filtergt']['user_id'])) {
                $filter['filtergt']['transfer_uid'] = $filter['filtergt']['user_id'];
            }
        }

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.numberofchatsdialogsbyuserparticipant',array('group_field' => $groupField, 'days' => $days, 'filter' => $filter));

        if ($statusWorkflow === false) {
        	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));

            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);

            if (isset($filter['filter']['user_id'])){
                $filter['filter']['`lh_chat_participant`.`user_id`'] = $filter['filter']['user_id'];
                unset($filter['filter']['user_id']);
            }

            if (isset($filter['filter']['dep_id'])){
                $filter['filter']['`lh_chat_participant`.`dep_id`'] = $filter['filter']['dep_id'];
                unset($filter['filter']['dep_id']);
            }

            if ($useTimeFilter != true) {
                if (isset($filter['filtergte']['time'])){
                    $filter['filtergte']['`lh_chat_participant`.`time`'] = $filter['filtergte']['time'];
                    unset($filter['filtergte']['time']);
                }

                if (isset($filter['filterlte']['time'])){
                    $filter['filterlte']['`lh_chat_participant`.`time`'] = $filter['filterlte']['time'];
                    unset($filter['filterlte']['time']);
                }
            }

        	$generalFilter = self::formatFilter($filter);
        	$generalJoin = self::formatJoin($filter);

        	$appendFilterTime = '';

        	if ($useTimeFilter == true) {
        		$appendFilterTime = '`lh_chat_participant`.`time` > :time ';
        	}

        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}

            $generalJoin .= " INNER JOIN `lh_chat` ON `lh_chat`.`id` = `lh_chat_participant`.`chat_id`";

            $column = '`lh_chat_participant`.`user_id`';

            if ($groupField == 'transfer_uid') {
                $column = '`transfer_uid` AS `user_id`';
            }

        	$sql = "SELECT count(`lh_chat_participant`.`id`) AS number_of_chats,{$column} FROM lh_chat_participant {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY {$groupField} ORDER BY number_of_chats DESC LIMIT 40";

        	$db = ezcDbInstance::get();
        	$stmt = $db->prepare($sql);

        	if ($useTimeFilter == true) {
        		$stmt->bindValue(':time',$dateUnixPast);
        	}

        	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        	$stmt->execute();

            $rows =  $stmt->fetchAll();

        	return $rows;

        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function avgWaitTimeyUser($days = 30, $filter = array()) 
    {    	    
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.avgwaittimeuser',array('days' => $days, 'filter' => $filter));
        
        if ($statusWorkflow === false) {
        	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        	
        	$filter['filterlt']['wait_time'] = 600;
        	
        	$generalFilter = self::formatFilter($filter);
        	$generalJoin = self::formatJoin($filter);

        	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
        	$appendFilterTime = '';
        	
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'time > :time ';
        	}
        	 
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}
        	    	
        	$sql = "SELECT avg(wait_time) AS avg_wait_time,user_id FROM lh_chat {$generalJoin} WHERE {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY avg_wait_time DESC LIMIT 40";
        	        	
        	$db = ezcDbInstance::get();
        	$stmt = $db->prepare($sql);
        	
        	if ($useTimeFilter == true) {
        		$stmt->bindValue(':time',$dateUnixPast);
        	}
        	
        	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        	$stmt->execute();
        	return $stmt->fetchAll();
        } else {
            return $statusWorkflow['list'];
        }
    }
    
    public static function numberOfMessagesByUser($days = 30, $filter = array()) 
    {    	    
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.numberofmessagesbyuser',array('days' => $days, 'filter' => $filter));
        
        if ($statusWorkflow === false) {
        
        	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        	
        	if (isset($filter['filtergte']['time'])){
        	    $filter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
        	    unset($filter['filtergte']['time']);
        	}
        		
        	if (isset($filter['filterlte']['time'])){
        	    $filter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
        	    unset($filter['filterlte']['time']);
        	}
        	
        	if (isset($filter['filter']['user_id'])){
        	    $filter['filter']['lh_msg.user_id'] = $filter['filter']['user_id'];
        	    unset($filter['filter']['user_id']);
        	}

        	if (isset($filter['filterin']['user_id'])){
        	    $filter['filterin']['lh_msg.user_id'] = $filter['filterin']['user_id'];
        	    unset($filter['filterin']['user_id']);
        	}

            if (isset($filter['filtergt']['user_id'])) {
                $filter['filtergt']['lh_msg.user_id'] = $filter['filtergt']['user_id'];
                unset($filter['filtergt']['user_id']);
            }
        	
        	$generalFilter = self::formatFilter($filter);
        	$generalJoin = self::formatJoin($filter);

        	$useTimeFilter = !isset($filter['filtergte']['lh_msg.time']) && !isset($filter['filterlte']['lh_msg.time']);
        	$appendFilterTime = '';
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'lh_msg.time > :time ';
        	}
        	
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}
        	       	
        	$sql = "SELECT count(lh_msg.id) AS number_of_chats,lh_msg.user_id 
        	FROM lh_msg 
        	INNER JOIN lh_chat ON lh_chat.id = lh_msg.chat_id
        	{$generalJoin}
        	WHERE {$appendFilterTime} {$generalFilter} 
        	GROUP BY lh_msg.user_id 
        	ORDER BY number_of_chats DESC LIMIT 40";

        	$db = ezcDbInstance::get();
        	$stmt = $db->prepare($sql);
        
        	
        	if ($useTimeFilter == true) {
        		$stmt->bindValue(':time',$dateUnixPast);
        	}
        	
        	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        	$stmt->execute();
        	return $stmt->fetchAll();
        } else {
            return $statusWorkflow['list'];
        }
    }
    
    public static function formatJoin($params) {
        $returnFilter = array();
        foreach ($params as $type => $params) {
            foreach ($params as $field => $value) {
                if ($type == 'innerjoin') {
                    $returnFilter[] = ' INNER JOIN `'. $field . '` ON ' . $value[0] . ' = ' . $value[1];
                }
            }
        }

        return implode(' ', $returnFilter);
    }
    
    public static function formatFilter($params) {
    	
    	$db = ezcDbInstance::get();
    	
    	$returnFilter = array();
    	foreach ($params as $type => $params){
    		foreach ($params as $field => $value) {
    			if ($type == 'filter') {
    				$returnFilter[] = $field.' = '.$db->quote($value);
    			} elseif ($type == 'filterlte') {
    				$returnFilter[] = $field.' <= '.$db->quote($value);
    			} elseif ($type == 'filterlt') {
    				$returnFilter[] = $field.' < '.$db->quote($value);
    			} elseif ($type == 'filtergte') {
    				$returnFilter[] = $field.' >= '.$db->quote($value);    			
    			} elseif ($type == 'filtergt') {
    				$returnFilter[] = $field.' > '.$db->quote($value);
                } elseif ($type == 'filterlike') {
                    $returnFilter[] = $field.' LIKE (' . $db->quote('%'.$value.'%') . ')';
    			} elseif ($type == 'filterin') {
                    $valuesEscaped = [];
                    foreach ($value as $valueItem) {
                        $valuesEscaped[] = $db->quote($valueItem);
                    }
    				$returnFilter[] = $field.' IN ( '. implode(',', $valuesEscaped) . ')';
    			} elseif ($type == 'customfilter') {
    				$returnFilter[] = $value;
    			}
    		}    		
    	}

    	return implode(' AND ', $returnFilter);
    }
    
    public static function formatUserFilter(& $filterParams, $table = 'lh_chat', $column = 'user_id') {
        if (isset($filterParams['input']->group_id) && is_numeric($filterParams['input']->group_id) && $filterParams['input']->group_id > 0 ) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_groupuser WHERE group_id = :group_id');
            $stmt->bindValue( ':group_id', $filterParams['input']->group_id, PDO::PARAM_INT);
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($userIds)) {
                $filterParams['filter']['filterin'][$table . '.' . $column] = $userIds;
            }
        }

        if (isset($filterParams['input']->group_ids) && is_array($filterParams['input']->group_ids) && !empty($filterParams['input']->group_ids)) {

            erLhcoreClassChat::validateFilterIn($filterParams['input']->group_ids);

            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_groupuser WHERE group_id IN (' . implode(',',$filterParams['input']->group_ids) .')');
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($userIds)) {
                if (isset($filterParams['filter']['filterin'][$table . '.' . $column])) {
                    $filterParams['filter']['filterin'][$table . '.' . $column] = array_merge($filterParams['filter']['filterin'][$table . '.' . $column],$userIds);
                } else {
                    $filterParams['filter']['filterin'][$table . '.' . $column] = $userIds;
                }
            }
        }
        
        if (isset($filterParams['input']->department_group_id) &&  is_numeric($filterParams['input']->department_group_id) && $filterParams['input']->department_group_id > 0 ) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT dep_id FROM lh_departament_group_member WHERE dep_group_id = :group_id');
            $stmt->bindValue( ':group_id', $filterParams['input']->department_group_id, PDO::PARAM_INT);
            $stmt->execute();
            $depIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($depIds)) {
                $filterParams['filter']['filterin'][$table . '.dep_id'] = $depIds;
            }
        }

        if (isset($filterParams['input']->department_group_ids) &&  is_array($filterParams['input']->department_group_ids) && !empty($filterParams['input']->department_group_ids)) {

            erLhcoreClassChat::validateFilterIn($filterParams['input']->department_group_ids);

            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT dep_id FROM lh_departament_group_member WHERE dep_group_id IN (' . implode(',',$filterParams['input']->department_group_ids) . ')');
            $stmt->execute();
            $depIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($depIds)) {
                if (isset($filterParams['filter']['filterin'][$table . '.dep_id'])){
                    $filterParams['filter']['filterin'][$table . '.dep_id'] = array_merge($filterParams['filter']['filterin'][$table . '.dep_id'],$depIds);
                } else {
                    $filterParams['filter']['filterin'][$table . '.dep_id'] = $depIds;
                }
            }
        }

    }
    
    public static function getRatingByUser($days = 30, $filter = array()) 
    {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getratingbyuser',array('days' => $days, 'filter' => $filter));

        if ($statusWorkflow !== false) {
            return $statusWorkflow['list'];
        }

    	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));    	    	
    	$rating = array();    

    	$generalFilter = self::formatFilter($filter);
    	$generalJoin = self::formatJoin($filter);

    	if ($generalFilter != ''){
    		$generalFilter = ' AND '.$generalFilter;
    	}
    	
    	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
    	$appendFilterTime = '';
    	if ($useTimeFilter == true) {
    		$appendFilterTime = ' AND time > :time ';
    	}    	
    	
    	$sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,user_id FROM lh_chat {$generalJoin} WHERE fbst = 1 {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 40";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	$rating['thumbsup'] = $stmt->fetchAll();
    		
    	$sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,user_id FROM lh_chat {$generalJoin} WHERE fbst = 2 {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 40";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	$rating['thumbdown'] = $stmt->fetchAll();
    		
    	$sql = "SELECT count(`lh_chat`.`id`) AS number_of_chats,user_id FROM lh_chat {$generalJoin} WHERE fbst = 0 {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 40";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	$rating['unrated'] = $stmt->fetchAll();
    	
    	return $rating;
    }

    public static function exportAgentStatistic($days = 30, $filter = array()) {
        $data = self::getAgentStatistic($days,$filter);
        include 'lib/core/lhform/PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Report');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Agent'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chats'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, '');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Total number of chats'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Total number of chats (participation)'));

        $columnCounter = 1;
        if (isset($filter['filterin']['subject_id']) && !empty($filter['filterin']['subject_id'])) {
            foreach ($filter['filterin']['subject_id'] as $subjectId) {
                $columnCounter++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1 + $columnCounter, 2, (string)erLhAbstractModelSubject::fetch($subjectId));
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Number of chats while online'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Hours on chat (sum of chat duration)'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Hours on chat (sum of chat duration (participation))'));


        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Time online (sum of time spend online)'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','AVG number of chat per hour'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','AVG number of chats per hour (participation)'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average pick-up time'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9 + $columnCounter, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average chat length'));

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getagentstatistic_export_columns',array('xls' => & $objPHPExcel));

        $i = 3;
        foreach ($data as $item) {
            $key = 0;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->agentName);
            $key++;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->numberOfChats);
            $key++;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->numberOfChatsParticipant);
            $key++;

            if (isset($filter['filterin']['subject_id']) && !empty($filter['filterin']['subject_id'])) {
                foreach ($filter['filterin']['subject_id'] as $subjectId) {
                    $value = '';
                    foreach ($item->subject_stats as $subjectStat) {
                       if ($subjectStat['subject_id'] == $subjectId) {
                           $value = '=('.$subjectStat['number_of_chats'].'/'.$item->numberOfChats.')*100';
                       }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key,  $i, $value,PHPExcel_Cell_DataType::TYPE_FORMULA);
                    $key++;
                }
            }

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->numberOfChatsOnline);

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->totalHours/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->totalHoursParticipant/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');

            $key++;            
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->totalHoursOnline/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');
            
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->aveNumber);

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->aveNumberParticipant);
            
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->avgWaitTime/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');
            
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->avgChatLengthSeconds/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getagentstatistic_export_columns_value',array(
                'xls' => & $objPHPExcel,
                'key' => & $key,
                'i' => & $i,
                'item' => & $item,
            ));

            $i++;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');
        // It will be called file.xls
        header('Content-Disposition: attachment; filename="report.xlsx"');
        // Write file to the browser
        $objWriter->save('php://output');
    }

    public static function getMedian($objects, $attr, $exclude = 10) {

        $numberOfElements = 0;
        $totalValue = 0;

        $valuesArray = array();
        foreach ($objects as $object) {
            if ($object->$attr > 0) {
                $valuesArray[] = (int)$object->$attr;
            }
        }

        sort($valuesArray);

        $elementstoExclude = floor(count($valuesArray)*($exclude/100));

        $keyMin = $elementstoExclude;
        $keyMax = count($valuesArray) - $elementstoExclude;

        foreach ($valuesArray as $key => $value) {
                if ($key >= $keyMin && $key < $keyMax) {
                    $numberOfElements++;
                    $totalValue += $value;
                }
        }

        return round($totalValue/($numberOfElements > 0 ? $numberOfElements : 1),2);
    }

    public static function getAgentStatisticSummary($statistic) {

        $attrToAverage = array(
            'numberOfChats',
            'numberOfChatsOnline',
            'totalHours',
            'totalHoursOnline',
            'aveNumber',
            'avgWaitTime',
            'avgChatLengthSeconds',
        );

        $attrFrontAverage = array(
            'totalHours',
            'totalHoursOnline',
            'avgWaitTime',
            'avgChatLengthSeconds',
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getagentstatisticaveragefield',array('attr' => & $attrToAverage, 'attr_front' => & $attrFrontAverage));

        $stats = array();
        foreach ($attrToAverage as $attr) {
            $stats[$attr] = self::getMedian($statistic,$attr);
        }

        foreach ($stats as $attr => $value) {
            if (in_array($attr,$attrFrontAverage)) {
                $stats[$attr . '_front'] = erLhcoreClassChat::formatSeconds($stats[$attr]);
            }
        }

        return $stats;
    }

    public static function getAgentStatistic ($days = 30, $filtergte = array()) {
        $filter = array();
    
        if (isset($filtergte['filtergte']['time'])) {
            $filter['filtergte']['time'] = $filtergte['filtergte']['time'];
        } else {
            $filter['filtergte']['time'] = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }
    
        if (isset($filtergte['filterlte']['time'])) {
            $filter['filterlte']['time'] = $filtergte['filterlte']['time'];
        }
        
        $filterUsers = array();

        if (isset($filtergte['filterin']['id'])) {
            $filterUsers['filterin']['id'] = $filtergte['filterin']['id'];
        }

        $userIdGroup = array();

        // Explicit user filter
        $userIdFilter = array();

        // Department appended users filters
        $userIdGroupDep = array();

        if (isset($filtergte['filterin']['group_ids'])) {
            $groupId = $filtergte['filterin']['group_ids'];
            unset($filtergte['filterin']['group_ids']);

            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_groupuser WHERE group_id IN (' . implode(',',$groupId) . ')');
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($userIds)) {
                $userIdFilter = $userIdGroup = $userIds;
            } else {
                $userIdGroup = array(-1);
            }
        }

        if (isset($filtergte['filterin']['department_group_ids'])) {
            
            $depGroup = $filtergte['filterin']['department_group_ids'];
            unset($filtergte['filterin']['department_group_ids']);

            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_userdep WHERE dep_id IN (select dep_id FROM lh_departament_group_member WHERE dep_group_id IN (' . implode(',',$depGroup) . '))');
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $stmt = $db->prepare('select dep_id FROM lh_departament_group_member WHERE dep_group_id IN (' . implode(',',$depGroup) . ')');
            $stmt->execute();
            $depIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($depIds)) {
                $filter['filterin']['dep_id'] = $depIds;            
            } else {
                $filter['filterin']['dep_id'] = array(-1);
            }
            
            if (empty($userIds)) {
                $userIds = array(-1);
            }

            if (!empty($userIdGroupDep)) {
                $userIdGroupDep = array_unique(array_intersect($userIdGroupDep, $userIds));
            } else {
                $userIdGroupDep = $userIds;
            }

            if (!empty($userIdGroup)) {
                $userIdGroup = array_unique(array_intersect($userIdGroup,$userIds));

                if (empty($userIdGroup)) {
                    $userIdGroup = array(-1);
                }
                
            } else {
                $userIdGroup = $userIds;
            }            
        }
        
        if (isset($filtergte['filterin']['department_ids'])) {

            $depIDs = $filtergte['filterin']['department_ids'];
            if (isset($filter['filterin']['dep_id']) && !in_array(-1,$filter['filterin']['dep_id'])){

                $combinedDepartment = array_unique(array_intersect($filtergte['filterin']['department_ids'], $filter['filterin']['dep_id']));

                if (!empty($combinedDepartment)) {
                    $filter['filterin']['dep_id'] = $combinedDepartment;
                } else {
                    $filter['filterin']['dep_id'] = array(-1);
                }

            } elseif (!isset($filter['filterin']['dep_id'])) {
                $filter['filterin']['dep_id'] = $depIDs;
            }
            unset($filtergte['filterin']['department_ids']);

            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_userdep WHERE dep_id IN ('. implode(',',$depIDs).')');
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($userIdGroupDep)) {
                $userIdGroupDep = array_unique(array_intersect($userIdGroupDep, $userIds));
            } else {
                $userIdGroupDep = $userIds;
            }

            if (!empty($userIds)) {
                if (!empty($userIdGroup)) {
                    $userIdGroup = array_unique(array_intersect($userIdGroup,$userIds));
                    
                    if (empty($userIdGroup)) {
                        $userIdGroup = array(-1);
                    }
                    
                } else {
                    $userIdGroup = $userIds;
                }
            }
        }

        if (!empty($userIdGroup)) {
            if (isset($filterUsers['filterin']['id']) && !empty($filterUsers['filterin']['id'])) {
                $filterUsers['filterin']['id'] = array_unique(array_intersect($userIdGroup, $filterUsers['filterin']['id']));
            } else {
                $filterUsers['filterin']['id'] = $userIdGroup;
            }
        }

        if (isset($filterUsers['filterin']['id'])) {
            $userIdFilter = array_values($filterUsers['filterin']['id']);
        }

        $userList = erLhcoreClassModelUser::getUserList($filterUsers);
        
        if (empty($userList)) {
            return array();
        }

        $filterExtension = array('user_filter' => $userIdFilter, 'department_user_id' => $userIdGroupDep, 'user_list' => $userList, 'days' => $days, 'filter' => $filter, 'filter_original' => $filtergte);

        $list = array();

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getagentstatistic',$filterExtension);

        if ($statusWorkflow === false) {        
            foreach ($userList as $user) {
                $userInfo = erLhcoreClassModelUser::fetch($user->id,true);
                $filter['filter']['user_id'] = $user->id;     
                $agentName = trim($userInfo->name .' '. $userInfo->surname);
                
                $userChatsStats = erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filter);
                $numberOfChats = empty($userChatsStats) ? $numberOfChats = "0" : $userChatsStats[0]['number_of_chats'];

                $userChatsParticipantStats = erLhcoreClassChatStatistic::numberOfChatsDialogsByUserParticipant(30,$filter);
                $numberOfChatsParticipant = empty($userChatsParticipantStats) ? $numberOfChatsParticipant = "0" : $userChatsParticipantStats[0]['number_of_chats'];

                // Just chat's then operator accepted chat and he was online
                $filterOnline = $filter;
                $filterOnline['filter']['usaccept'] = 0;
                $userChatsStatsOnline = erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterOnline);
                $numberOfChatsOnline = empty($userChatsStatsOnline) ? 0 : $userChatsStatsOnline[0]['number_of_chats'];
    
                $filterOnlineHours = $filter;
                if (isset($filterOnlineHours['filter']['dep_id'])) {
                    unset($filterOnlineHours['filter']['dep_id']);
                }
                
                if (isset($filterOnlineHours['filterin']['dep_id'])) {
                    unset($filterOnlineHours['filterin']['dep_id']);
                }

                $totalHoursOnline = self::totalHoursOfOnlineDialogsByUser(30,$filterOnlineHours);
                $totalHoursOnlineCount = self::formatHours($totalHoursOnline);

                $totalHours = self::totalHoursOfChatsDialogsByUser(30,$filter);
                $totalHoursParticipant = self::totalHoursOfDialogsByUserParticipant(30,$filter);
                
                if ($totalHoursOnlineCount > 1) {
                    $aveNumber = round($numberOfChatsOnline / $totalHoursOnlineCount, 2);
                } else {
                    $aveNumber = $numberOfChatsOnline;
                }

                if ($totalHoursOnlineCount > 1) {
                    $aveNumberParticipant = round($numberOfChatsParticipant / $totalHoursOnlineCount, 2);
                } else {
                    $aveNumberParticipant = $numberOfChatsParticipant;
                }


                $userWaitTimeByOperator = self::avgWaitTimeyUser(30,$filter);
                $userWaitTimeByOperatorNumber = empty($userWaitTimeByOperator) ? 0 : $userWaitTimeByOperator[0]['avg_wait_time'];
                
                $avgWaitTime = empty($userWaitTimeByOperator) ? "0 s." : erLhcoreClassChat::formatSeconds($userWaitTimeByOperator[0]['avg_wait_time']);
                
                $avgDuration = self::getAverageChatduration(30,$filter);   
                $avgChatLength = $avgDuration ? erLhcoreClassChat::formatSeconds($avgDuration) : "0 s.";

                $subjectStats = array();

                if (isset($filtergte['filterin']['subject_id']) && !empty($filtergte['filterin']['subject_id'])) {
                    $filterSubject = $filter;
                    $filterSubject['filterin']['`lh_abstract_subject_chat`.`subject_id`'] = $filtergte['filterin']['subject_id'];
                    $subjectStats = self::subjectsStatistic(30, $filterSubject);

                    foreach ($subjectStats as $indexSubject => $subjectStat) {
                        $subjectStats[$indexSubject]['perc'] = round($subjectStat['number_of_chats']/$numberOfChats*10000) / 100;
                    }
                }

                $list[] = (object)array(
                    'agentName' => $agentName, 
                    'userId' => $user->id,

                    'numberOfChats' => $numberOfChats, 
                    'numberOfChatsParticipant' => $numberOfChatsParticipant,

                    'numberOfChatsOnline' => $numberOfChatsOnline,

                    'totalHours' => $totalHours,
                    'totalHours_front' => erLhcoreClassChat::formatSeconds($totalHours),

                    'totalHoursParticipant' => $totalHoursParticipant,
                    'totalHoursParticipant_front' => erLhcoreClassChat::formatSeconds($totalHoursParticipant),

                    'totalHoursOnline' => $totalHoursOnline,
                    'totalHoursOnline_front' => erLhcoreClassChat::formatSeconds($totalHoursOnline),

                    'aveNumber' => $aveNumber,
                    'aveNumberParticipant' => $aveNumberParticipant,

                    'avgWaitTime' => $userWaitTimeByOperatorNumber, 
                    'avgWaitTime_front' => $avgWaitTime, 
                    'avgChatLength' => $avgChatLength,
                    'avgChatLengthSeconds' => $avgDuration,
                    'subject_stats' => $subjectStats,
                );
            }
            
        } else {
            $list = $statusWorkflow['list'];
        }
        
        return $list;
    }
    
    public static function formatHours($seconds) {               
        return round($seconds/60/60,2);
    }
    
    public static function totalHoursOfOnlineDialogsByUser($days = 30, $filter = array(), $limit = 40)
    {
        if (empty($filter)) {
            $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }

        return erLhcoreClassChat::getCount($filter,'lh_users_online_session','SUM(duration)');
    }

    public static function totalHoursOfDialogsByUserParticipant($days = 30, $filter = array(), $limit = 40)
    {
        if (empty($filter)) {
            $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }

        return erLhcoreClassChat::getCount($filter,'lh_chat_participant','SUM(duration)');
    }
    
    public static function totalHoursOfChatsDialogsByUser($days = 30, $filter = array())
    {
        if (empty($filter)) {
            $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }
        $filter['filtergt']['user_id'] = 0;
        return erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filtergt' => array('chat_duration' => 0),'filter' =>  array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))),'lh_chat','SUM(chat_duration)');
    }

    public static function getDepartmentsStatistic($days = 30, $filter = array(), $input = null)
    {
        if (empty($filter)) {
            $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }

        if (
            isset($filter['filtergte']['hourminute']) &&
            isset($filter['filterlte']['hourminute']) &&
            $filter['filtergte']['hourminute'] > $filter['filterlte']['hourminute']
        ) {
            $sql = '(hourminute >= ' . (int)$filter['filtergte']['hourminute'] . ' OR hourminute <= ' . (int)$filter['filterlte']['hourminute'] .')';
            unset($filter['filtergte']['hourminute']);
            unset($filter['filterlte']['hourminute']);
            $filter['filter_custom'][] = $sql;
        }

        $filterNew = $filter;
        $filterNew['group'] = 'ymd, status';

        // Collect statistic by days
        $stats = erLhcoreClassModelDepartamentAvailability::getCount($filterNew,'count',false,'count(id) as records, status, ymd, min(time) as time',false,true);

        $statsByDay = array();

        foreach ($stats as $stat) {
            $statsByDay[$stat['ymd']]['stats'][$stat['status']] = $stat['records'];
            $statsByDay[$stat['ymd']]['time'] = $stat['time'];
        }

        foreach ($statsByDay as $key => $stat) {
            $statsByDay[$key]['total'] = array_sum($statsByDay[$key]['stats']);

            $statsByDay[$key]['stats_formated'] = array();
            foreach ($statsByDay[$key]['stats'] as $status => $records) {
                $statsByDay[$key]['stats_formated'][$status]['perc'] = round(floor($records/$statsByDay[$key]['total'] * 10000)/100,2, PHP_ROUND_HALF_DOWN);
                $statsByDay[$key]['stats_formated'][$status]['seconds'] = $records*60;
            }
        }

        $filterNew = $filter;
        $filterNew['group'] = 'hour, status';

        // Collect statistic by hour
        $stats = erLhcoreClassModelDepartamentAvailability::getCount($filterNew,'count',false,'count(id) as records, status, hour, min(time) as time',false,true);
        $statsByHour = array();

        foreach ($stats as $stat) {
            $statsByHour[self::convertUTFHourToLocal($stat['hour'])]['stats'][$stat['status']] = $stat['records'];
            $statsByHour[self::convertUTFHourToLocal($stat['hour'])]['time'] = $stat['time'];
        }

        ksort($statsByHour);

        foreach ($statsByHour as $key => $stat) {
            $statsByHour[$key]['total'] = array_sum($statsByHour[$key]['stats']);
            $statsByHour[$key]['stats_formated'] = array();
            foreach ($statsByHour[$key]['stats'] as $status => $records) {
                  $statsByHour[$key]['stats_formated'][$status]['perc'] = round(floor($records/$statsByHour[$key]['total'] * 10000)/100,2, PHP_ROUND_HALF_DOWN);
                  $statsByHour[$key]['stats_formated'][$status]['seconds'] = $records*60;
            }
        }

        $filterNew = $filter;
        $filterNew['group'] = 'status';
        $stats = erLhcoreClassModelDepartamentAvailability::getCount($filterNew,'count',false,'count(id) as records, status',false,true);

        $totalTime = 0;
        foreach ($stats as $stat) {
            $totalTime +=  $stat['records'];
        }

        $statGlobal = array();
        foreach ($stats as $stat) {
            $stat['perc'] = round(floor(($stat['records']/$totalTime) * 10000)/100,2, PHP_ROUND_HALF_DOWN);
            $statGlobal[] = $stat;
        }

        return array('day_stats' => $statsByDay, 'hour_stats' => $statsByHour, 'global_stats' => $statGlobal);
    }

    public static function convertUTFHourToLocal($hourUTF) {

        $dateTime = new DateTime("now");
        $hourUTF =  $hourUTF + ($dateTime->getOffset() / 60 / 60);

        if ($hourUTF < 0) {
            $hourUTF = 24 + $hourUTF;
        } elseif ($hourUTF > 23) {
            $hourUTF = $hourUTF - 24;
        }

        return $hourUTF;
    }

    public static function exportDepartmentStatistic($data) {

        include 'lib/core/lhform/PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Report');


        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Date'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Online'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Disabled'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Overloaded'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Offline'));


        $i = 2;
        foreach ($data['day_stats'] as $day) {

            $key = 0;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)date(erLhcoreClassModule::$dateFormat,$day['time']));

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][0]['perc']) ? $day['stats_formated'][0]['perc'] : 0) . '%');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][1]['perc']) ? $day['stats_formated'][1]['perc'] : 0) . '%');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][2]['perc']) ? $day['stats_formated'][2]['perc'] : 0) . '%');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][3]['perc']) ? $day['stats_formated'][3]['perc'] : 0) . '%');

            $i++;
        }

        $i++;
        $i++;

        $objPHPExcel->getActiveSheet()->getStyle("A{$i}:AW{$i}")->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Hour'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Online'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Disabled'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Overloaded'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Offline'));

        $i++;
        foreach ($data['hour_stats'] as $hour => $day) {

            $key = 0;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $hour);

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][0]['perc']) ? $day['stats_formated'][0]['perc'] : 0) . '%');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][1]['perc']) ? $day['stats_formated'][1]['perc'] : 0) . '%');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][2]['perc']) ? $day['stats_formated'][2]['perc'] : 0) . '%');

            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (isset($day['stats_formated'][3]['perc']) ? $day['stats_formated'][3]['perc'] : 0) . '%');

            $i++;
        }


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');

        // It will be called file.xls
        header('Content-Disposition: attachment; filename="report.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }



    public static function getRangeWaitTime()
    {
        return array(
            array(
                'from' => 0,
                'to' => 5,
                'tt' => '0-5 sec.'
            ),
            array(
                'from' => 6,
                'to' => 10,
                'tt' => '6-10 sec.'
            ),
            array(
                'from' => 11,
                'to' => 20,
                'tt' => '11-20 sec.'
            ),
            array(
                'from' => 21,
                'to' => 30,
                'tt' => '21-30 sec.'
            ),
            array(
                'from' => 31,
                'to' => 40,
                'tt' => '31-40 sec.'
            ),
            array(
                'from' => 41,
                'to' => 50,
                'tt' => '41-50 sec.'
            ),
            array(
                'from' => 51,
                'to' => 60,
                'tt' => '51-60 sec.'
            ),
            array(
                'from' => 61,
                'to' => 90,
                'tt' => '61-90 sec.'
            ),
            array(
                'from' => 91,
                'to' => 120,
                'tt' => '91-120 sec.'
            ),
            array(
                'from' => 121,
                'to' => 180,
                'tt' => '2-3 min.'
            ),
            array(
                'from' => 181,
                'to' => 240,
                'tt' => '3-4 min.'
            ),
            array(
                'from' => 241,
                'to' => 300,
                'tt' => '4-5 min.'
            ),
            array(
                'from' => 301,
                'to' => 600,
                'tt' => '5-10 min.'
            ),
            array(
                'from' => 601,
                'to' => false,
                'tt' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','more than 10 min.')
            )
        );
    }

    public static function getPerformanceStatistic($days = 30, $filter = array(), $filterParams = array())
    {
        $dateRange = self::getRangeWaitTime();

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getperformancestatistic',array('ranges' => $dateRange, 'days' => $days, 'filter' => $filter, 'filter_params' => $filterParams));

        if ($statusWorkflow === false) {
            $stats = array(
                'rows' => array(),
                'total_chats' => 0,
                'total_aband_chats' => 0
            );
            
            foreach ($dateRange as $rangeData) {
                
                $filterTimeout = array();
                
                if ($rangeData['from'] !== false) {
                    $filterTimeout['filtergte']['wait_time'] = $rangeData['from'];
                }
                
                if ($rangeData['to'] !== false) {
                    $filterTimeout['filterlte']['wait_time'] = $rangeData['to'];
                }

                if ((isset($filterParams['input']->timefrom_include_hours) && is_numeric($filterParams['input']->timefrom_include_hours)) && (isset($filterParams['input']->timeto_include_hours) && is_numeric($filterParams['input']->timeto_include_hours))) {  

                    if ($filterParams['input']->timefrom_include_hours <= $filterParams['input']->timeto_include_hours){
                        $filterTimeout['customfilter'][] = 'FROM_UNIXTIME(time,\'%k\') >= '. (int)$filterParams['input']->timefrom_include_hours;
                        $filterTimeout['customfilter'][] = 'FROM_UNIXTIME(time,\'%k\') <= '. (int)$filterParams['input']->timeto_include_hours;
                    } else {
                        $filterTimeout['customfilter'][] = '(FROM_UNIXTIME(time,\'%k\') >= '. (int)$filterParams['input']->timefrom_include_hours . ' OR FROM_UNIXTIME(time,\'%k\') <= '. (int)$filterParams['input']->timeto_include_hours . ')';
                    }
                        
                } elseif (isset($filterParams['input']->timeto_include_hours)) {
                    $filterTimeout['customfilter'][] = 'FROM_UNIXTIME(time,\'%k\') <= '. (int)$filterParams['input']->timeto_include_hours;
                } elseif (isset($filterParams['input']->timefrom_include_hours) && is_numeric($filterParams['input']->timefrom_include_hours)) {
                    $filterTimeout['customfilter'][] = 'FROM_UNIXTIME(time,\'%k\') >= '. (int)$filterParams['input']->timefrom_include_hours;
                }

                $chatStarted = erLhcoreClassChat::getCount(array_merge_recursive($filter, $filterTimeout), 'lh_chat', 'count(id)');

                // Abandoned chat is considered if
                // * There is no operator assigned to the chat and visitor has already left
                // * Last sync was before chat was accepted by the operator
                $abandonedStarted = erLhcoreClassModelChat::getCount(array_merge_recursive($filter, $filterTimeout, array(
                    'filter_custom' => array (
                        '((user_id = 0 AND status_sub IN ( ' . erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT . ',' . erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED .' ) ) OR (lsync < (pnd_time + wait_time)))'
                    ))));

                $stats['rows'][] = array(
                    'from' => $rangeData['from'],
                    'to' => $rangeData['to'],
                    'tt' => $rangeData['tt'],
                    'started' => $chatStarted,
                    'abandoned' => $abandonedStarted
                );
                
                $stats['total_chats'] += $chatStarted;
                $stats['total_aband_chats'] += $abandonedStarted;
            } 
        } else {
            $stats = $statusWorkflow['list'];
        }        
        
        return $stats;
    }

    public static function getProactiveStatistic($params)
    {
        $stats = array(
            // Send invitations
            'INV_SEND' => erLhAbstractModelProactiveChatCampaignConversion::getCount($params['filter']),

            // Invitations where widget was opened
            'INV_SHOWN' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('filterin' => array('invitation_status' => array(
                erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN,
                erLhAbstractModelProactiveChatCampaignConversion::INV_SEEN,
                erLhAbstractModelProactiveChatCampaignConversion::INV_CHAT_STARTED,
            ))))),

            // Invitations where it was shown but chat was not started
            'INV_SEEN' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'],array('filterin' => array('invitation_status' => array(
                erLhAbstractModelProactiveChatCampaignConversion::INV_SEEN,
            ))))),

            // Invitations where it was shown but chat was not started
            'INV_CHAT_STARTED' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'],array('filterin' => array('invitation_status' => array(
                erLhAbstractModelProactiveChatCampaignConversion::INV_CHAT_STARTED,
            ))))),

            'INV_CONVERSIONS' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('filtergt' => array('conv_int_time' => 0)))),

            'INV_CONVERSIONS_INIT' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('filternot' => array('conv_event' => '')))),

            'INV_UNIQ_CONVERSIONS' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('limit' => false,  'filtergt' => array('conv_int_time' => 0))),
                'count',
                false,
                'count(distinct vid_id)'),

            'INV_UNIQ_CONVERSIONS_INIT' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('limit' => false, 'filternot' => array('conv_event' => ''))),
                'count',
                false,
                'count(distinct vid_id)'),

            // Statistic based on unique_id field
            'INV_CUSTOM_CONVERSIONS' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('filternot' => array('unique_id' => ''), 'filtergt' => array('conv_int_time' => 0)))),

            'INV_CUSTOM_CONVERSIONS_INIT' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('filternot' => array('unique_id' => '','conv_event' => '')))),

            'INV_CUSTOM_UNIQ_CONVERSIONS' => erLhAbstractModelProactiveChatCampaignConversion::getCount(array_merge_recursive($params['filter'], array('limit' => false,  'filternot' => array('unique_id' => ''), 'filtergt' => array('conv_int_time' => 0))),
                'count',
                false,
                'count(distinct unique_id)'
            ),
            'INV_CUSTOM_UNIQ_CONVERSIONS_INIT' => erLhAbstractModelProactiveChatCampaignConversion::getCount(
                array_merge_recursive($params['filter'], array('limit' => false, 'filternot' => array('unique_id' => '', 'conv_event' => ''))),
                'count',
                false,
                'count(distinct unique_id)'
            ),
        );

        return $stats;
    }

    public static function nickGroupingDateWeekDay($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdateweekday',array('params_execution' => $filterParams, 'filter' => $filter));

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

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            $validGroupFields = array(
                'nick' => '`nick`',
                'uagent' => '`uagent`',
                'device_type' => '`device_type`',
                'department' => '`dep_id`',
                'user_id' => '`user_id`',
                'transfer_uid' => '`transfer_uid`',
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

            for ($day = 1; $day < 8; $day++) {
                $i = $day;
                if ($i == 7) {
                    $i = 0;
                }

                $groupField = '`nick`';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                }

                $numberOfChats[$i] = array ();
                $numberOfChats[$i]['unique'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('customfilter' =>  array('FROM_UNIXTIME(time,\'%w\') = '. $i))),'lh_chat', 'count(distinct ' . $groupField . ')' );
            }

            return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function nickGroupingDateWeek($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdateweek',array('params_execution' => $filterParams, 'filter' => $filter));

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

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            $startTimestamp = time()-(42*7*24*3600);

            $limitDays = 42;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 42;
                $startTimestamp = $filter['filterlte']['time']-(42*7*24*3600);
            }

            $validGroupFields = array(
                'nick' => '`nick`',
                'uagent' => '`uagent`',
                'device_type' => '`device_type`',
                'department' => '`dep_id`',
                'user_id' => '`user_id`',
                'transfer_uid' => '`transfer_uid`',
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

            $weekStarted = false;
            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+($i*7),date('y',$startTimestamp));

                if ($weekStarted == false) {
                    $weekStarted = true;

                    if (date('N', $dateUnix) != 1) {
                        // Adjust start time to be it monday
                        $startTimestamp = $startTimestamp - ((date('N', $startTimestamp)-1)*24*3600);

                        continue; // First day is not a monday, skip to next week
                    }
                }

                // This week has not ended, so exclude it
                if (date('YW') == date('YW',$dateUnix) || time() < $dateUnix) {
                    continue;
                }

                if (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))
                {
                    $numberOfChats[$dateUnix] = array();

                    $groupField = '`nick`';
                    if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                        $groupField = $validGroupFields[$filterParams['group_field']];
                    }

                    $numberOfChats[$dateUnix] = array ();
                    $numberOfChats[$dateUnix]['unique'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix)))),'lh_chat', 'count(distinct ' . $groupField . ')' );
                }
            }

            return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function nickGroupingDateNickWeekDay($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdatenickweekday',array('params_execution' => $filterParams, 'filter' => $filter));

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

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            $validGroupFields = array(
                'nick' => '`nick`',
                'uagent' => '`uagent`',
                'device_type' => '`device_type`',
                'department' => '`dep_id`',
                'user_id' => '`user_id`',
                'transfer_uid' => '`transfer_uid`',
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

            if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields) && $filterParams['group_field'] == 'transfer_uid') {
                if (isset($filter['filterin']['user_id'])) {
                    $filter['filterin']['transfer_uid'] = $filter['filterin']['user_id'];
                    unset($filter['filterin']['user_id']);
                }
                if (isset($filter['filterin']['lh_chat.user_id'])) {
                    $filter['filterin']['lh_chat.transfer_uid'] = $filter['filterin']['lh_chat.user_id'];
                    unset($filter['filterin']['lh_chat.user_id']);
                }
            }

            for ($day = 1; $day < 8; $day++) {
                $i = $day;
                if ($i == 7) {
                    $i = 0;
                }

                $numberOfChats[$i] = array();

                $groupField = '`nick`';
                $attr = 'nick';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                    $attr = $filterParams['group_field'];
                }

                $justDemo = array_values(erLhcoreClassModelChat::getList(array_merge_recursive($departmentFilter,$filter,array('sort' => 'nick_count DESC', 'select_columns' => 'count(id) as nick_count', 'group' => $groupField, 'limit' => (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10), 'customfilter' => array('FROM_UNIXTIME(time,\'%w\') = '. $i)))));

                $returnArray = array();

                foreach ($justDemo as $demoItem) {
                    $returnArray['color'][] = json_encode(self::colorFromString($demoItem->{$attr}));

                    if ($attr == 'device_type') {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr} == 0 ? 'PC' : ($demoItem->{$attr} == 1 ? 'Mobile' : 'Table'));
                    } elseif ($attr == 'user_id') {
                        $returnArray['nick'][] = json_encode((string)$demoItem->n_off_full.' ['. $demoItem->user_id.']');
                    } elseif ($attr == 'transfer_uid') {
                        $userTransferrer = erLhcoreClassModelUser::fetch($demoItem->{$attr});
                        if ($userTransferrer instanceof erLhcoreClassModelUser) {
                            $returnArray['nick'][] = json_encode($userTransferrer->name_official);
                        } else {
                            $returnArray['nick'][] = json_encode($demoItem->{$attr});
                        }
                    } else {
                        $returnArray['nick'][] = json_encode((string)$demoItem->{$attr});
                    }

                    $returnArray['data'][] = $demoItem->virtual_nick_count;
                }

                $numberOfChats[$i] = $returnArray;

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

        } else {
            return $statusWorkflow['list'];
        }
    }
    public static function nickGroupingDateNickWeek($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdatenickweek',array('params_execution' => $filterParams, 'filter' => $filter));

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

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            $startTimestamp = time()-(42*7*24*3600);

            $limitDays = 42;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600*7));
                if ($daysDifference <= 42 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 42;
                $startTimestamp = $filter['filterlte']['time']-(42*7*24*3600);
            }

            $validGroupFields = array(
                'nick' => '`nick`',
                'uagent' => '`uagent`',
                'device_type' => '`device_type`',
                'department' => '`dep_id`',
                'user_id' => '`user_id`',
                'transfer_uid' => '`transfer_uid`',
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

            if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields) && $filterParams['group_field'] == 'transfer_uid') {
                if (isset($filter['filterin']['user_id'])) {
                    $filter['filterin']['transfer_uid'] = $filter['filterin']['user_id'];
                    unset($filter['filterin']['user_id']);
                }
                if (isset($filter['filterin']['lh_chat.user_id'])) {
                    $filter['filterin']['lh_chat.transfer_uid'] = $filter['filterin']['lh_chat.user_id'];
                    unset($filter['filterin']['lh_chat.user_id']);
                }
            }

            $weekStarted = false;
            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+($i*7),date('y',$startTimestamp));

                if ($weekStarted == false) {
                    $weekStarted = true;

                    if (date('N', $dateUnix) != 1) {
                        // Adjust start time to be it monday
                        $startTimestamp = $startTimestamp - ((date('N', $startTimestamp)-1)*24*3600);

                        continue; // First day is not a monday, skip to next week
                    }
                }

                // This week has not ended, so exclude it
                if (date('YW') == date('YW',$dateUnix) || time() < $dateUnix) {
                    continue;
                }

                if (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))
                {
                    $numberOfChats[$dateUnix] = array();

                    $groupField = '`nick`';
                    $attr = 'nick';
                    if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                        $groupField = $validGroupFields[$filterParams['group_field']];
                        $attr = $filterParams['group_field'];
                    }

                    $justDemo = array_values(erLhcoreClassModelChat::getList(array_merge_recursive($departmentFilter,$filter,array('sort' => 'nick_count DESC', 'select_columns' => 'count(id) as nick_count', 'group' => $groupField, 'limit' => (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10), 'customfilter' => array('FROM_UNIXTIME(time,\'%Y%v\') = '. date('YW',$dateUnix))))));

                    $returnArray = array();

                    foreach ($justDemo as $demoItem) {
                        $returnArray['color'][] = json_encode(self::colorFromString($demoItem->{$attr}));

                        if ($attr == 'device_type') {
                            $returnArray['nick'][] = json_encode($demoItem->{$attr} == 0 ? 'PC' : ($demoItem->{$attr} == 1 ? 'Mobile' : 'Table'));
                        } elseif ($attr == 'user_id') {
                            $returnArray['nick'][] = json_encode((string)$demoItem->n_off_full . ' [' . $demoItem->user_id . ']');
                        } elseif ($attr == 'transfer_uid') {
                            $userTransferrer = erLhcoreClassModelUser::fetch($demoItem->{$attr});
                            if ($userTransferrer instanceof erLhcoreClassModelUser) {
                                $returnArray['nick'][] = json_encode($userTransferrer->name_official);
                            } else {
                                $returnArray['nick'][] = json_encode($demoItem->{$attr});
                            }
                        } else {
                            $returnArray['nick'][] = json_encode((string)$demoItem->{$attr});
                        }

                        $returnArray['data'][] = $demoItem->virtual_nick_count;
                    }

                    $numberOfChats[$dateUnix] = $returnArray;
                }
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

        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function nickGroupingDateDay($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdateday', array('filter' => $filter, 'params_execution' => $filterParams));

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

            if (isset($msgFilter['filtergte']['time'])){
                unset($msgFilter['filtergte']['time']);
                $msgFilter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
            }

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            $startTimestamp = time()-(31*24*3600);

            $limitDays = 31;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }

            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 31;
                $startTimestamp = $filter['filterlte']['time']-(31*24*3600);
            }

            $validGroupFields = array(
                'nick' => '`nick`',
                'uagent' => '`uagent`',
                'device_type' => '`device_type`',
                'department' => '`dep_id`',
                'user_id' => '`user_id`',
                'transfer_uid' => '`transfer_uid`',
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

                $groupField = '`nick`';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                }

                $numberOfChats[$dateUnix] = array ();
                $numberOfChats[$dateUnix]['unique'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))),'lh_chat', 'count(distinct ' . $groupField . ')' );
            }

            return $numberOfChats;
        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function nickGroupingDateNickDay($filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdatenickday', array('filter' => $filter, 'params_execution' => $filterParams));

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

            if (isset($msgFilter['filtergte']['time'])){
                unset($msgFilter['filtergte']['time']);
                $msgFilter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
            }

            if (isset($msgFilter['filterlte']['time'])){
                unset($msgFilter['filterlte']['time']);
                $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            }

            $startTimestamp = time()-(31*24*3600);

            $limitDays = 31;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }

            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600));
                if ($daysDifference <= 31 && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = 31;
                $startTimestamp = $filter['filterlte']['time']-(31*24*3600);
            }

            $validGroupFields = array(
                'nick' => '`nick`',
                'uagent' => '`uagent`',
                'device_type' => '`device_type`',
                'department' => '`dep_id`',
                'user_id' => '`user_id`',
                'transfer_uid' => '`transfer_uid`',
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

            if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields) && $filterParams['group_field'] == 'transfer_uid') {
                if (isset($filter['filterin']['user_id'])) {
                    $filter['filterin']['transfer_uid'] = $filter['filterin']['user_id'];
                    unset($filter['filterin']['user_id']);
                }
                if (isset($filter['filterin']['lh_chat.user_id'])) {
                    $filter['filterin']['lh_chat.transfer_uid'] = $filter['filterin']['lh_chat.user_id'];
                    unset($filter['filterin']['lh_chat.user_id']);
                }
            }

            for ($i = 0; $i < $limitDays;$i++) {
                $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

                $groupField = '`nick`';
                $attr = 'nick';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                    $attr = $filterParams['group_field'];
                }

                $justDemo = array_values(erLhcoreClassModelChat::getList(array_merge_recursive($departmentFilter,$filter,array('sort' => 'nick_count DESC', 'select_columns' => 'count(id) as nick_count', 'group' => $groupField, 'limit' => (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10), 'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))));

                $returnArray = array();

                foreach ($justDemo as $demoItem) {
                    $returnArray['color'][] = json_encode(self::colorFromString($demoItem->{$attr}));

                    if ($attr == 'device_type') {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr} == 0 ? 'PC' : ($demoItem->{$attr} == 1 ? 'Mobile' : 'Table'));
                    } elseif ($attr == 'user_id') {
                        $returnArray['nick'][] = json_encode((string)$demoItem->n_off_full . ' [' . $demoItem->user_id . ']');
                    } elseif ($attr == 'transfer_uid') {
                        $userTransferrer = erLhcoreClassModelUser::fetch($demoItem->{$attr});
                        if ($userTransferrer instanceof erLhcoreClassModelUser) {
                            $returnArray['nick'][] = json_encode($userTransferrer->name_official);
                        } else {
                            $returnArray['nick'][] = json_encode($demoItem->{$attr});
                        }
                    } else {
                        $returnArray['nick'][] = json_encode((string)$demoItem->{$attr});
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

        } else {
            return $statusWorkflow['list'];
        }
    }

    public static function nickGroupingDate($days = 30, $filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdate', array('filter' => $filter, 'params_execution' => $filterParams));

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

        $yearStart = date('y');
        $monthStart = date('m');

        if (isset($msgFilter['filterlte']['time'])){
            unset($msgFilter['filterlte']['time']);
            $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            $yearStart = date('y',$filter['filterlte']['time']);
            $monthStart = date('m',$filter['filterlte']['time']);
        }

        $validGroupFields = array(
            'nick' => '`nick`',
            'uagent' => '`uagent`',
            'device_type' => '`device_type`',
            'department' => '`dep_id`',
            'user_id' => '`user_id`',
            'transfer_uid' => '`transfer_uid`',
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

        for ($i = 0; $i < 12;$i++) {
            $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);

            if (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))
            {
                $groupField = '`nick`';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                }

                $numberOfChats[$dateUnix] = array ();
                $numberOfChats[$dateUnix]['unique'] = (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))),'lh_chat', 'count(distinct ' . $groupField . ')' );

            }
        }

        $numberOfChats = array_reverse($numberOfChats,true);

        return $numberOfChats;

        } else {
            return $statusWorkflow['list'];
        }
    }

    static function hsl2rgb($H, $S, $V) {
        $H *= 6;
        $h = intval($H);
        $H -= $h;
        $V *= 255;
        $m = $V*(1 - $S);
        $x = $V*(1 - $S*(1-$H));
        $y = $V*(1 - $S*$H);
        $a = [[$V, $x, $m], [$y, $V, $m],
            [$m, $V, $x], [$m, $y, $V],
            [$x, $m, $V], [$V, $m, $y]][$h];
        return sprintf("#%02X%02X%02X", $a[0], $a[1], $a[2]);
    }

    static function hue($tstr) {
        return unpack('L', hash('adler32', $tstr, true))[1];
    }


    public static function colorFromString($string)
    {
        $colors = ['#F44336',
            '#FFEBEE',
            '#FFCDD2',
            '#EF9A9A',
            '#E57373',
            '#EF5350',
            '#E53935',
            '#D32F2F',
            '#C62828',
            '#B71C1C',
            '#FF8A80',
            '#FF5252',
            '#FF1744',
            '#D50000',
            '#FCE4EC',
            '#F8BBD0',
            '#F48FB1',
            '#F06292',
            '#EC407A',
            '#E91E63',
            '#D81B60',
            '#C2185B',
            '#AD1457',
            '#880E4F',
            '#FF80AB',
            '#FF4081',
            '#F50057',
            '#C51162',
            '#F3E5F5',
            '#E1BEE7',
            '#CE93D8',
            '#BA68C8',
            '#AB47BC',
            '#9C27B0',
            '#8E24AA',
            '#7B1FA2',
            '#6A1B9A',
            '#4A148C',
            '#EA80FC',
            '#E040FB',
            '#D500F9',
            '#AA00FF',
            '#EDE7F6',
            '#D1C4E9',
            '#B39DDB',
            '#9575CD',
            '#7E57C2',
            '#673AB7',
            '#5E35B1',
            '#512DA8',
            '#4527A0',
            '#311B92',
            '#B388FF',
            '#7C4DFF',
            '#651FFF',
            '#6200EA',
            '#E8EAF6',
            '#C5CAE9',
            '#9FA8DA',
            '#7986CB',
            '#5C6BC0',
            '#3F51B5',
            '#3949AB',
            '#303F9F',
            '#283593',
            '#1A237E',
            '#8C9EFF',
            '#536DFE',
            '#3D5AFE',
            '#304FFE',
            '#E3F2FD',
            '#BBDEFB',
            '#90CAF9',
            '#64B5F6',
            '#42A5F5',
            '#2196F3',
            '#1E88E5',
            '#1976D2',
            '#1565C0',
            '#0D47A1',
            '#82B1FF',
            '#448AFF',
            '#2979FF',
            '#2962FF',
            '#E1F5FE',
            '#B3E5FC',
            '#81D4FA',
            '#4FC3F7',
            '#29B6F6',
            '#03A9F4',
            '#039BE5',
            '#0288D1',
            '#0277BD',
            '#01579B',
            '#80D8FF',
            '#40C4FF',
            '#00B0FF',
            '#0091EA',
            '#E0F7FA',
            '#B2EBF2',
            '#80DEEA',
            '#4DD0E1',
            '#26C6DA',
            '#00BCD4',
            '#00ACC1',
            '#0097A7',
            '#00838F',
            '#6064',
            '#84FFFF',
            '#18FFFF',
            '#00E5FF',
            '#00B8D4',
            '#E0F2F1',
            '#B2DFDB',
            '#80CBC4',
            '#4DB6AC',
            '#26A69A',
            '#9688',
            '#00897B',
            '#00796B',
            '#00695C',
            '#004D40',
            '#A7FFEB',
            '#64FFDA',
            '#1DE9B6',
            '#00BFA5',
            '#E8F5E9',
            '#C8E6C9',
            '#A5D6A7',
            '#81C784',
            '#66BB6A',
            '#4CAF50',
            '#43A047',
            '#388E3C',
            '#2E7D32',
            '#1B5E20',
            '#B9F6CA',
            '#69F0AE',
            '#00E676',
            '#00C853',
            '#F1F8E9',
            '#DCEDC8',
            '#C5E1A5',
            '#AED581',
            '#9CCC65',
            '#8BC34A',
            '#7CB342',
            '#689F38',
            '#558B2F',
            '#33691E',
            '#CCFF90',
            '#B2FF59',
            '#76FF03',
            '#64DD17',
            '#F9FBE7',
            '#F0F4C3',
            '#E6EE9C',
            '#DCE775',
            '#D4E157',
            '#CDDC39',
            '#C0CA33',
            '#AFB42B',
            '#9E9D24',
            '#827717',
            '#F4FF81',
            '#EEFF41',
            '#C6FF00',
            '#AEEA00',
            '#FFFDE7',
            '#FFF9C4',
            '#FFF59D',
            '#FFF176',
            '#FFEE58',
            '#FFEB3B',
            '#FDD835',
            '#FBC02D',
            '#F9A825',
            '#F57F17',
            '#FFFF8D',
            '#FFFF00',
            '#FFEA00',
            '#FFD600',
            '#FFF8E1',
            '#FFECB3',
            '#FFE082',
            '#FFD54F',
            '#FFCA28',
            '#FFC107',
            '#FFB300',
            '#FFA000',
            '#FF8F00',
            '#FF6F00',
            '#FFE57F',
            '#FFD740',
            '#FFC400',
            '#FFAB00',
            '#FFF3E0',
            '#FFE0B2',
            '#FFCC80',
            '#FFB74D',
            '#FFA726',
            '#FF9800',
            '#FB8C00',
            '#F57C00',
            '#EF6C00',
            '#E65100',
            '#FFD180',
            '#FFAB40',
            '#FF9100',
            '#FF6D00',
            '#FBE9E7',
            '#FFCCBC',
            '#FFAB91',
            '#FF8A65',
            '#FF7043',
            '#FF5722',
            '#F4511E',
            '#E64A19',
            '#D84315',
            '#BF360C',
            '#FF9E80',
            '#FF6E40',
            '#FF3D00',
            '#DD2C00',
            '#EFEBE9',
            '#D7CCC8',
            '#BCAAA4',
            '#A1887F',
            '#8D6E63',
            '#795548',
            '#6D4C41',
            '#5D4037',
            '#4E342E',
            '#3E2723',
            '#FAFAFA',
            '#F5F5F5',
            '#EEEEEE',
            '#E0E0E0',
            '#BDBDBD',
            '#9E9E9E',
            '#757575',
            '#616161',
            '#424242',
            '#212121',
            '#ECEFF1',
            '#CFD8DC',
            '#B0BEC5',
            '#90A4AE',
            '#78909C',
            '#607D8B',
            '#546E7A',
            '#455A64',
            '#37474F',
            '#263238'];

        // generate a partial hash of the string (a full hash is too long for the % operator)
        $hash = substr(sha1($string), 0, 10);

        // determine the color index
        $colorIndex = hexdec($hash) % count($colors);

        return $colors[$colorIndex];
    }

    public static function nickGroupingDateNick($days = 30, $filter = array(), $filterParams = array())
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.nickgroupingdatenick', array('filter' => $filter, 'params_execution' => $filterParams));

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

        $yearStart = date('y');
        $monthStart = date('m');

        if (isset($msgFilter['filterlte']['time'])){
            unset($msgFilter['filterlte']['time']);
            $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
            $yearStart = date('y',$filter['filterlte']['time']);
            $monthStart = date('m',$filter['filterlte']['time']);
        }

        $validGroupFields = array(
            'nick' => '`nick`',
            'uagent' => '`uagent`',
            'device_type' => '`device_type`',
            'department' => '`dep_id`',
            'user_id' => '`user_id`',
            'transfer_uid' => '`transfer_uid`',
        );

        if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields) && $filterParams['group_field'] == 'transfer_uid') {
            if (isset($filter['filterin']['user_id'])) {
                $filter['filterin']['transfer_uid'] = $filter['filterin']['user_id'];
                unset($filter['filterin']['user_id']);
            }
            if (isset($filter['filterin']['lh_chat.user_id'])) {
                $filter['filterin']['lh_chat.transfer_uid'] = $filter['filterin']['lh_chat.user_id'];
                unset($filter['filterin']['lh_chat.user_id']);
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.validgroupfields', array('type' => 'sql', 'fields' => & $validGroupFields));

        for ($i = 0; $i < 12;$i++) {
            $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);
            if (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))
            {
                $numberOfChats[$dateUnix] = array ();

                $groupField = '`nick`';
                $attr = 'nick';
                if (isset($filterParams['group_field']) && key_exists($filterParams['group_field'], $validGroupFields)) {
                    $groupField = $validGroupFields[$filterParams['group_field']];
                    $attr = $filterParams['group_field'];
                }

                $justDemo = array_values(erLhcoreClassModelChat::getList(array_merge_recursive($departmentFilter,$filter,array('sort' => 'nick_count DESC', 'select_columns' => 'count(`lh_chat`.`id`) as nick_count', 'group' => $groupField, 'limit' => (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10), 'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))));

                $returnArray = array();

                foreach ($justDemo as $demoItem) {
                    $returnArray['color'][] = json_encode(self::colorFromString($demoItem->{$attr}));

                    if ($attr == 'device_type') {
                        $returnArray['nick'][] = json_encode($demoItem->{$attr} == 0 ? 'PC' : ($demoItem->{$attr} == 1 ? 'Mobile' : 'Table'));
                    } elseif ($attr == 'user_id') {
                        $returnArray['nick'][] = json_encode((string)$demoItem->n_off_full . ' [' . $demoItem->user_id . ']');
                    } elseif ($attr == 'transfer_uid') {
                        $userTransferrer = erLhcoreClassModelUser::fetch($demoItem->{$attr});
                        if ($userTransferrer instanceof erLhcoreClassModelUser) {
                            $returnArray['nick'][] = json_encode($userTransferrer->name_official);
                        } else {
                            $returnArray['nick'][] = json_encode($demoItem->{$attr});
                        }
                    } else {
                        $returnArray['nick'][] = json_encode((string)$demoItem->{$attr});
                    }

                    $returnArray['data'][] = $demoItem->virtual_nick_count;
                }

                $numberOfChats[$dateUnix] = $returnArray;
            }
        }

        $numberOfChats = array_reverse($numberOfChats,true);

        $returnReversed = array();

        $limitReverse = (isset($filterParams['group_limit']) && is_numeric($filterParams['group_limit']) ? (int)$filterParams['group_limit'] : 10);

        foreach ($numberOfChats as $dateIndex => $returnData) {
            for ($i = 0; $i < $limitReverse; $i++) {
                    $returnReversed[$i]['data'][] = isset($returnData['data'][$i]) ? $returnData['data'][$i] : 0;
                    $returnReversed[$i]['color'][] = isset($returnData['color'][$i]) ? $returnData['color'][$i] : '""';
                    $returnReversed[$i]['nick'][] = isset($returnData['nick'][$i]) ? $returnData['nick'][$i] : '""';
            }
        }

        return array('labels' => $numberOfChats, 'data' => $returnReversed);
    } else {
            return $statusWorkflow['list'];
        }
    }

    public static function getVisitorsStatistic($filter, $params) {

        $statistic = array('visitors_new' => array(), 'visitors_returning' => array());

        $yearStart = date('y');
        $monthStart = date('m');

        if (isset($filter['filterlte']['time'])){
            $yearStart = date('y',$filter['filterlte']['time']);
            $monthStart = date('m',$filter['filterlte']['time']);
        }

        $daysGroupLimit = ($params['groupby'] == 2) ? 42 : 31;
        $multiplier = ($params['groupby'] == 2) ? 7 : 1;
        $limitDays = 12;

        if ($params['groupby'] != 0)
        {
            $startTimestamp = time()-($daysGroupLimit*$multiplier*24*3600);

            $limitDays = $daysGroupLimit;

            if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600*$multiplier));
                if ($daysDifference <= $daysGroupLimit && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filtergte']['time'])) {
                $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600*$multiplier));
                if ($daysDifference <= $daysGroupLimit && $daysDifference > 0) {
                    $limitDays = $daysDifference;
                    $startTimestamp = $filter['filtergte']['time'];
                }
            } elseif (isset($filter['filterlte']['time'])) {
                $limitDays = $daysGroupLimit;
                $startTimestamp = $filter['filterlte']['time']-($daysGroupLimit*$multiplier*24*3600);
            }

            $weekStarted = false;
        }

        $groupAttributes = array(
            0 => array('db' => '\'%Y%m\'', 'php' => 'Ym','front' => 'Y.m'), // Month
            1 => array('db' => '\'%Y%m%d\'', 'php' => 'Ymd','front' => 'Y.m.d'), // Day
            2 => array('db' => '\'%Y%v\'', 'php' => 'YW','front' => 'Y.m.d') // Week
        );

        for ($i = 0; $i < $limitDays;$i++) {

            // week
            if ($params['groupby'] == 2) {
                $startReturning = $dateUnix = mktime(0, 0, 0, date('m', $startTimestamp), date('d', $startTimestamp) + ($i * 7), date('y', $startTimestamp));

                if ($weekStarted == false) {
                    $weekStarted = true;

                    if (date('N', $dateUnix) != 1) {
                        // Adjust start time to be it monday
                        $startReturning = $startTimestamp = $startTimestamp - ((date('N', $startTimestamp) - 1) * 24 * 3600);

                        continue; // First day is not a monday, skip to next week
                    }
                }

                // This week has not ended, so exclude it
                if (date('YW') == date('YW', $dateUnix) || time() < $dateUnix) {
                    continue;
                }

            // Day
            } elseif ($params['groupby'] == 1) {
                $startReturning = $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

            // Month
            } else if ($params['groupby'] == 0) {
                $startReturning = $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);
            }

            if ((($params['groupby'] == 0 || $params['groupby'] == 2) && (!isset($filter['filtergte']['time']) || $filter['filtergte']['time'] <= $dateUnix || date('Ym',$filter['filtergte']['time']) == date('Ym',$dateUnix))) || $params['groupby'] == 1 )
            {
                // New visitors
                if (in_array('visitors_new', $params['charttypes'])) {
                    $filterNew = $filter;
                    if (isset($filterNew['filterlte']['time'])) {
                        $filterNew['filterlte']['first_visit'] = $filterNew['filterlte']['time'];
                        unset($filterNew['filterlte']['time']);
                    }

                    if (isset($filterNew['filtergte']['time'])) {
                        $filterNew['filtergte']['first_visit'] = $filterNew['filtergte']['time'];
                        unset($filterNew['filtergte']['time']);
                    }

                    $filterFormated = array_merge_recursive($filterNew,array('customfilter' =>  array('FROM_UNIXTIME(first_visit,' . $groupAttributes[$params['groupby']]['db'] .') = '. date($groupAttributes[$params['groupby']]['php'],$dateUnix))));

                    $statistic['visitors_new'][$dateUnix] = erLhcoreClassModelChatOnlineUser::getCount($filterFormated);
                }

                // Visitors all
                if (in_array('visitors_all', $params['charttypes'])) {
                    $filterNew = $filter;

                    if (isset($filterNew['filtergte']['time'])) {
                        $filterNew['filterlte']['first_visit'] = $filterNew['filtergte']['time'];
                        unset($filterNew['filtergte']['time']);
                    }

                    if (isset($filterNew['filterlte']['time'])) {
                        $filterNew['filterlte']['first_visit'] = $filterNew['filterlte']['time'];
                        unset($filterNew['filterlte']['time']);
                    }

                    $filterFormated = array_merge_recursive($filterNew,array('customfilter' =>  array('(FROM_UNIXTIME(first_visit,' . $groupAttributes[$params['groupby']]['db'] .') = '. date($groupAttributes[$params['groupby']]['php'],$dateUnix).' OR FROM_UNIXTIME(last_visit,' . $groupAttributes[$params['groupby']]['db'] .') = '. date($groupAttributes[$params['groupby']]['php'],$dateUnix).')')));

                    $statistic['visitors_all'][$dateUnix] = erLhcoreClassModelChatOnlineUser::getCount($filterFormated);
                }

                // Returning visitors
                if (in_array('visitors_returning', $params['charttypes'])) {
                    $filterNew = $filter;
                    if (isset($filterNew['filterlte']['time'])) {
                        $filterNew['filterlte']['last_visit'] = $filterNew['filterlte']['time'];
                        unset($filterNew['filterlte']['time']);
                    }

                    if (isset($filterNew['filtergte']['time'])) {
                        $filterNew['filtergte']['last_visit'] = $filterNew['filtergte']['time'];
                        unset($filterNew['filtergte']['time']);
                    }

                    $filterNew['filterlte']['first_visit'] = $startReturning;

                    $filterFormated = array_merge_recursive($filterNew,array('customfilter' =>  array('FROM_UNIXTIME(last_visit,' . $groupAttributes[$params['groupby']]['db'] .') = '. date($groupAttributes[$params['groupby']]['php'],$dateUnix))));

                    $statistic['visitors_returning'][$dateUnix] = erLhcoreClassModelChatOnlineUser::getCount($filterFormated);
                }

                // Visitors by country
                if (in_array('visitors_country', $params['charttypes'])) {
                    $filterNew = $filter;
                    if (isset($filterNew['filterlte']['time'])) {
                        $filterNew['filterlte']['last_visit'] = $filterNew['filterlte']['time'];
                        unset($filterNew['filterlte']['time']);
                    }

                    if (isset($filterNew['filtergte']['time'])) {
                        $filterNew['filtergte']['last_visit'] = $filterNew['filtergte']['time'];
                        unset($filterNew['filtergte']['time']);
                    }

                    $filterFormated = array_merge_recursive($filterNew,array('customfilter' =>  array('FROM_UNIXTIME(last_visit,' . $groupAttributes[$params['groupby']]['db'] .') = '. date($groupAttributes[$params['groupby']]['php'],$dateUnix))));
                    $filterFormated['sort'] = 'total_records DESC';
                    $filterFormated['group'] = 'user_country_name';
                    $filterFormated['limit'] = 5;

                    $justDemo = erLhcoreClassModelChatOnlineUser::getCount($filterFormated, '', false, 'user_country_name, count(id) as total_records', false, true);

                    $returnArray = array();

                    foreach ($justDemo as $demoItem) {
                        $returnArray['color'][] = json_encode(self::hsl2rgb(self::hue($demoItem['user_country_name'])/0xFFFFFFFF, 0.7, 1));
                        $returnArray['nick'][] = trim($demoItem['user_country_name'] ) != '' ? json_encode(trim(ucwords($demoItem['user_country_name']))) : json_encode('Unknown');
                        $returnArray['data'][] = $demoItem['total_records'];
                    }

                    $statistic['visitors_country'][$dateUnix] = $returnArray;
                }

                // Visitors by city
                if (in_array('visitors_city', $params['charttypes'])) {
                    $filterNew = $filter;
                    if (isset($filterNew['filterlte']['time'])) {
                        $filterNew['filterlte']['last_visit'] = $filterNew['filterlte']['time'];
                        unset($filterNew['filterlte']['time']);
                    }

                    if (isset($filterNew['filtergte']['time'])) {
                        $filterNew['filtergte']['last_visit'] = $filterNew['filtergte']['time'];
                        unset($filterNew['filtergte']['time']);
                    }

                    $filterFormated = array_merge_recursive($filterNew,array('customfilter' =>  array('city != \'\' AND FROM_UNIXTIME(last_visit,' . $groupAttributes[$params['groupby']]['db'] .') = '. date($groupAttributes[$params['groupby']]['php'],$dateUnix))));
                    $filterFormated['sort'] = 'total_records DESC';
                    $filterFormated['group'] = 'city';
                    $filterFormated['limit'] = 5;

                    $justDemo = erLhcoreClassModelChatOnlineUser::getCount($filterFormated, '', false, 'city, user_country_name, count(id) as total_records', false, true);

                    $returnArray = array();

                    foreach ($justDemo as $demoItem) {
                        $returnArray['color'][] = json_encode(self::hsl2rgb(self::hue($demoItem['city'] . $demoItem['user_country_name'])/0xFFFFFFFF, 0.65, 1));
                        $returnArray['nick'][] = trim($demoItem['user_country_name'] . $demoItem['city']) != '' ? json_encode(trim(ucwords($demoItem['user_country_name']) .' - '. ucwords(($demoItem['city'] != '' ? $demoItem['city'] : 'Unknown')))) : json_encode('Unknown');
                        $returnArray['data'][] = $demoItem['total_records'];
                    }

                    $statistic['visitors_city'][$dateUnix] = $returnArray;
                }

            }
        }

        foreach (array('visitors_city','visitors_country') as $statisticIdentifier) {
            if (!empty($statistic[$statisticIdentifier])) {
                if ($params['groupby'] == 0) {
                    $numberOfChats = array_reverse($statistic[$statisticIdentifier],true);
                } else {
                    $numberOfChats = $statistic[$statisticIdentifier];
                }

                $returnReversed = array();

                if ($limitDays < 5) {
                    $limitDays = 5;
                }

                foreach ($numberOfChats as $dateIndex => $returnData) {
                    for ($i = 0; $i < $limitDays; $i++) {
                        $returnReversed[$i]['data'][] = isset($returnData['data'][$i]) ? $returnData['data'][$i] : 0;
                        $returnReversed[$i]['color'][] = isset($returnData['color'][$i]) ? $returnData['color'][$i] : '""';
                        $returnReversed[$i]['nick'][] = isset($returnData['nick'][$i]) ? $returnData['nick'][$i] : '""';
                    }
                }

                $returnReversed = array_slice($returnReversed,0,5);

                $statistic[$statisticIdentifier] = array('group_date' => $groupAttributes[$params['groupby']]['front'], 'labels' => $numberOfChats, 'data' => $returnReversed);
            }
        }

        $statistic['group_date'] = $groupAttributes[$params['groupby']]['front'];

        if ($params['groupby'] == 0) {

            if (isset($statistic['visitors_new'])) {
                $statistic['visitors_new'] = array_reverse($statistic['visitors_new'], true);
            }

            if (isset($statistic['visitors_returning'])) {
                $statistic['visitors_returning'] = array_reverse($statistic['visitors_returning'],true);
            }

            if (isset($statistic['visitors_all'])) {
                $statistic['visitors_all'] = array_reverse($statistic['visitors_all'],true);
            }
        }

        return $statistic;
    }

    public static function byChannel($filter, $params) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatsperchannel',array('params_execution' => $params, 'filter' => $filter));

        if ($statusWorkflow === false) {

            $yearStart = date('y');
            $monthStart = date('m');

            if (isset($filter['filterlte']['time'])){
                $yearStart = date('y',$filter['filterlte']['time']);
                $monthStart = date('m',$filter['filterlte']['time']);
            }

            $daysGroupLimit = ($params['groupby'] == 2) ? 42 : 31;
            $multiplier = ($params['groupby'] == 2) ? 7 : 1;
            $limitDays = 12;

            if ($params['groupby'] != 0)
            {
                $startTimestamp = time()-($daysGroupLimit*$multiplier*24*3600);

                $limitDays = $daysGroupLimit;

                if (isset($filter['filterlte']['time']) && isset($filter['filtergte']['time'])) {
                    $daysDifference = ceil(($filter['filterlte']['time'] - $filter['filtergte']['time'])/(24*3600*$multiplier));
                    if ($daysDifference <= $daysGroupLimit && $daysDifference > 0) {
                        $limitDays = $daysDifference;
                        $startTimestamp = $filter['filtergte']['time'];
                    }
                } elseif (isset($filter['filtergte']['time'])) {
                    $daysDifference = ceil((time() - $filter['filtergte']['time'])/(24*3600*$multiplier));
                    if ($daysDifference <= $daysGroupLimit && $daysDifference > 0) {
                        $limitDays = $daysDifference;
                        $startTimestamp = $filter['filtergte']['time'];
                    }
                } elseif (isset($filter['filterlte']['time'])) {
                    $limitDays = $daysGroupLimit;
                    $startTimestamp = $filter['filterlte']['time']-($daysGroupLimit*$multiplier*24*3600);
                }

                $weekStarted = false;
            }

            $groupAttributes = array(
                0 => array('db' => '\'%Y%m\'', 'php' => 'Ym','front' => 'Y.m'), // Month
                1 => array('db' => '\'%Y%m%d\'', 'php' => 'Ymd','front' => 'Y.m.d'), // Day
                2 => array('db' => '\'%Y%v\'', 'php' => 'YW','front' => 'Y.m.d'), // Week
                3 => array('db' => '\'%w\'', 'php' => 'YW','front' => 'Y.m.d') // Week
            );

            $filter['sort'] = 'time DESC';
            $filter['group'] = 'FROM_UNIXTIME(time,' . $groupAttributes[$params['groupby']]['db'] . '), incoming_id';
            $filter['leftjoin']['lh_chat_incoming'] = array('`lh_chat_incoming`.`chat_id`','`lh_chat`.`id`');

            $numberOfChats = erLhcoreClassModelChat::getCount(array_merge_recursive($filter,array()),'',false,'incoming_id, FROM_UNIXTIME(time,'.$groupAttributes[$params['groupby']]['db'].') as day, time, count(`lh_chat`.`id`) as total_records',false, true);

            $webHooksDifference = [];
            $reformatResponse = [];
            // Reformat response to correctData
            foreach ($numberOfChats as $item){
                $reformatResponse[$item['day']][$item['incoming_id']] = $item['total_records'];
                if (!isset($webHooksDifference[$item['incoming_id']])) {
                    $webHooksDifference[$item['incoming_id']] = 0;
                }
            }

            if ($params['groupby'] == 3) {
                $responseReturn = [];
                for ($day = 1; $day < 8; $day++) {
                    $i = $day;
                    if ($i == 7) {
                        $i = 0;
                    }

                    $responseReturn[$i] = $webHooksDifference;

                    if (isset($reformatResponse[$i])) {
                        $responseReturn[$i] = array_replace($webHooksDifference, $reformatResponse[$i]);
                    }
                }
                return $responseReturn;
            }

            $responseReturn = [];

            for ($i = 0; $i < $limitDays;$i++) {
                if ($params['groupby'] == 2) {
                    $dateUnix = mktime(0, 0, 0, date('m', $startTimestamp), date('d', $startTimestamp) + ($i * 7), date('y', $startTimestamp));

                    if ($weekStarted == false) {
                        $weekStarted = true;

                        if (date('N', $dateUnix) != 1) {
                            // Adjust start time to be it monday
                            $startReturning = $startTimestamp = $startTimestamp - ((date('N', $startTimestamp) - 1) * 24 * 3600);

                            continue; // First day is not a monday, skip to next week
                        }
                    }

                    // This week has not ended, so exclude it
                    if (date('YW') == date('YW', $dateUnix) || time() < $dateUnix) {
                        continue;
                    }

                    // Day
                } elseif ($params['groupby'] == 1) {
                    $dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));

                    // Month
                } else if ($params['groupby'] == 0) {
                    $dateUnix = mktime(0,0,0,$monthStart - $i,1, $yearStart);
                }

                $responseReturn[$dateUnix] = $webHooksDifference;
                if (isset($reformatResponse[date($groupAttributes[$params['groupby']]['php'],$dateUnix)])) {
                    $responseReturn[$dateUnix] = array_replace($webHooksDifference, $reformatResponse[date($groupAttributes[$params['groupby']]['php'],$dateUnix)]);
                }
            }

            return $responseReturn;
        } else {
            return $statusWorkflow['list'];
        }
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

        if ($type == 'numberOfChatsPerMonth') {
            fputcsv($fp, ['date','closed','active','operators','pending','bot','total_chats']);
            foreach ($statistic['numberOfChatsPerMonth'] as $key => $data) {
                fputcsv($fp,[
                    date('Y-m-d H:i:s',$key),
                    $data['closed'],
                    $data['active'],
                    $data['operators'],
                    $data['pending'],
                    $data['bot'],
                    $data['total_chats'],
                ]);
            }
        } else if ($type == 'proactivevsdefault') {
            fputcsv($fp, ['date','Proactive','Visitors initiated']);
            foreach ($statistic['numberOfChatsPerMonth'] as $key => $data) {
                fputcsv($fp,[
                    date('Y-m-d H:i:s',$key),
                    $data['chatinitproact'],
                    $data['chatinitdefault']
                ]);
            }
        } else if ($type == 'country') {
            fputcsv($fp, ['Country name','Number of chats']);
            foreach ($statistic['countryStats'] as $data) {
                fputcsv($fp,[
                    (isset($data['country_name']) && !empty($data['country_name']) ? $data['country_name'] : 'not set'),
                    $data['number_of_chats']
                ]);
            }
        } else if ($type == 'waitmonth') {
            fputcsv($fp, ['Date','Value']);
            foreach ($statistic['numberOfChatsPerWaitTimeMonth'] as $date => $value) {
                fputcsv($fp,[
                   date('Y-m-d H:i:s',$date),
                    $value
                ]);
            }
        } else if ($type == 'waitbyoperator') {
            fputcsv($fp, ['User ID','Wait time','Name']);
            foreach ($statistic['userWaitTimeByOperator'] as $date => $value) {
                $obUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                fputcsv($fp,[
                   $value['user_id'],
                   $value['avg_wait_time'],
                   (is_object($obUser) ? $obUser->name_official : $value['user_id'])
                ]);
            }
        } else if ($type == 'cs_nickgroupingdatenick' || $type == 'nickgroupingdatenick') {

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

        } else if ($type == 'unanswered') {
            fputcsv($fp, ['Date','Chats number']);
            foreach ($statistic['numberOfChatsPerMonth'] as $date => $value) {
                fputcsv($fp,[
                    date('Y-m-d H:i:s',$date),
                    $value['unanswered']
                ]);
            }
        } else if ($type == 'canned') {
            fputcsv($fp, ['Canned ID', 'Title', 'Chats number']);
            foreach ($statistic['cannedStatistic'] as $value) {
                fputcsv($fp,[
                    $value['canned_id'],
                    (string)erLhcoreClassModelCannedMsg::fetch($value['canned_id'],true),
                    $value['number_of_chats']
                ]);
            }
        } else if ($type == 'subject') {
            fputcsv($fp, ['Subject ID','Subject','Chats number']);
            foreach ($statistic['subjectsStatistic'] as $value) {
                fputcsv($fp,[
                    $value['subject_id'],
                    (string)erLhAbstractModelSubject::fetch($value['subject_id'],true),
                    $value['number_of_chats']
                ]);
            }
        } else if ($type == 'usermsg') {
            fputcsv($fp, ['User','Chats number']);
            foreach ($statistic['numberOfMsgByUser'] as $value) {
                if ($value['user_id'] == 0) {
                    $operator = 'Visitor';
                } elseif ($value['user_id'] == -1) {
                    $operator = 'System assistant';
                } elseif ($value['user_id'] == -2) {
                    $operator = 'Virtual assistant';
                } else {
                    $operatorObj = erLhcoreClassModelUser::fetch($value['user_id'],true);
                    if (is_object($operatorObj) ) {
                        $operator = $operatorObj->name_official;
                    } else {
                        $operator = '['.$value['user_id'].']';
                    }
                }
                fputcsv($fp,[
                    $operator,
                    $value['number_of_chats']
                ]);
            }
        } else if ($type == 'chatbyuserparticipant') {
            fputcsv($fp, ['User','Chats number']);
            foreach ($statistic['userChatsParticipantStats'] as $value) {
                $obUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                $operator = (is_object($obUser) ? $obUser->name_official : $value['user_id']);
                fputcsv($fp,[
                    $operator,
                    $value['number_of_chats']
                ]);
            }
        } else if ($type == 'chatbyuser' || $type == 'chatbytransferuser') {
            fputcsv($fp, ['User','Chats number']);
            foreach ($statistic[$type == 'chatbyuser' ? 'userChatsStats' : 'userTransferChatsStats'] as $value) {
                $obUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                $operator = (is_object($obUser) ? $obUser->name_official : $value['user_id']);
                fputcsv($fp,[
                    $operator,
                    $value['number_of_chats']
                ]);
            }
        } else if ($type == 'msgtype') {
            fputcsv($fp, ['Date','Visitor','User','System','Bot']);
            foreach ($statistic['numberOfChatsPerMonth'] as $date => $value) {
                fputcsv($fp,[
                    date('Y-m-d H:i:s',$date),
                    $value['msg_user'],
                    $value['msg_operator'],
                    $value['msg_system'],
                    $value['msg_bot'],
                ]);
            }
        } else if ($type == 'thumbsup') {
            fputcsv($fp, ['User','User ID','Number of thumbs up']);
            foreach ($statistic['userStats']['thumbsup'] as $value) {
                $nameUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                $operator = (is_object($nameUser) ? $nameUser->name_official : '-');
                fputcsv($fp,[
                    $operator,
                    $value['user_id'],
                    $value['number_of_chats'],
                ]);
            }
        } else if ($type == 'thumbdown') {
            fputcsv($fp, ['User','User ID','Number of thumbsdown']);
            foreach ($statistic['userStats']['thumbdown'] as $value) {
                $nameUser = erLhcoreClassModelUser::fetch($value['user_id'],true);
                $operator = (is_object($nameUser) ? $nameUser->name_official : '-');
                fputcsv($fp,[
                    $operator,
                    $value['user_id'],
                    $value['number_of_chats'],
                ]);
            }
        } else if ($type == 'hourbyhour') {
            fputcsv($fp, ['Hour', 'Average number of chats', 'Max chats per hour', 'Peak date']);
            foreach ($statistic['numberOfChatsPerHour']['byday'] as $hour => $value) {
                fputcsv($fp,[
                    $hour,
                    $value,
                    (isset($statistic['numberOfChatsPerHour']['bydaymax'][$hour]['total_records']) ? $statistic['numberOfChatsPerHour']['bydaymax'][$hour]['total_records'] : ''),
                    (isset($statistic['numberOfChatsPerHour']['bydaymax'][$hour]['time']) ? date('Y-m-d H:i:s',$statistic['numberOfChatsPerHour']['bydaymax'][$hour]['time']) : '')
                ]);
            }
        } else if ($type == 'chatperhour') {
            fputcsv($fp, ['Hour','Total chats']);
            foreach ($statistic['numberOfChatsPerHour']['total'] as $hour => $value) {
                fputcsv($fp,[
                    $hour,
                    $value,
                ]);
            }
        } else if ($type == 'chatbydep') {
            fputcsv($fp, ['Department', 'Department ID', 'Number of chats']);
            foreach ($statistic['depChatsStats'] as $value) {
               $obUser = erLhcoreClassModelDepartament::fetch($value['dep_id'],true);
               $department = (is_object($obUser) ? $obUser->name : $value['dep_id']);
               fputcsv($fp,[
                    $department,
                    $value['dep_id'],
                    $value['number_of_chats'],
                ]);
            }
        } else if ($type == 'nickgroupingdate') {
            fputcsv($fp, ['Date', 'Number of records']);
            foreach ($statistic['nickgroupingdate'] as $date => $value) {
               fputcsv($fp,[
                    date('Y-m-d H:i:s', $date),
                    $value['unique'],
                ]);
            }
        }
        // Chats statistic tab exports
        else if ($type == 'cs_total_chats') {
            fputcsv($fp, ['Date', 'Number of chats']);
            foreach ($statistic['numberOfChatsPerMonth'] as $monthUnix => $value) {
               fputcsv($fp,[
                   ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $value['total_chats'],
                ]);
            }
        } else if ($type == 'cs_msgtype') {
            fputcsv($fp, ['Date','Visitor','User','System','Bot']);
            foreach ($statistic['numberOfChatsPerMonth'] as $monthUnix => $value) {
                fputcsv($fp,[
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $value['msg_user'],
                    $value['msg_operator'],
                    $value['msg_system'],
                    $value['msg_bot'],
                ]);
            }
        } else if ($type == 'cs_nickgroupingdate') {
            fputcsv($fp, ['Date','Unique records']);
            foreach ($statistic['nickgroupingdate'] as $monthUnix => $value) {
                fputcsv($fp,[
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $value['unique']
                ]);
            }
        } else if ($type == 'cs_by_channel') {

            $labels = ['date'];
            foreach (array_keys(current($statistic['by_channel'])) as $incomingId) {
                $webHook = erLhcoreClassModelChatIncomingWebhook::fetch($incomingId);
                $label = $webHook instanceof erLhcoreClassModelChatIncomingWebhook ? $webHook->name : $incomingId;
                if (empty($label)){
                    $label = 'Chat';
                }
                $labels[] = $label;
            }
            fputcsv($fp, $labels);

            foreach ($statistic['by_channel'] as $monthUnix => $data) {
                $itemData = [
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix])
                ];

                foreach ($data as $dataItem) {
                    $itemData[] = (int)$dataItem;
                }
                fputcsv($fp,$itemData);
            }

        } else if ($type == 'cs_active') {
            fputcsv($fp, ['date','closed','active','operators','pending','bot','total_chats']);
            foreach ($statistic['numberOfChatsPerMonth'] as $monthUnix => $data) {
                fputcsv($fp,[
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $data['closed'],
                    $data['active'],
                    $data['operators'],
                    $data['pending'],
                    (isset($data['bot']) ? $data['bot'] : 0),
                    $data['total_chats'],
                ]);
            }
        } else if ($type == 'cs_proactivevsdefault') {
            fputcsv($fp, ['Date','Proactive','Visitors initiated']);
            foreach ($statistic['numberOfChatsPerMonth'] as $monthUnix => $data) {
                fputcsv($fp,[
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $data['chatinitproact'],
                    $data['chatinitdefault']
                ]);
            }
        } else if ($type == 'cs_unanswered') {
            fputcsv($fp, ['Date','Chats number']);
            foreach ($statistic['numberOfChatsPerMonth'] as $monthUnix => $value) {
                fputcsv($fp,[
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $value['unanswered']
                ]);
            }
        } else if ($type == 'cs_waitmonth') {
            fputcsv($fp, ['Date','Value']);
            foreach ($statistic['numberOfChatsPerWaitTimeMonth'] as $monthUnix => $value) {
                fputcsv($fp,[
                    ($monthUnix > 10 ? date('Y-m-d H:i:s',$monthUnix) : $weekDays[(int)$monthUnix]),
                    $value
                ]);
            }
        } else {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.export_csv',array('fp' => $fp, 'type' => $type, 'data' => $statistic));
        }
        exit;
    }

}

?>