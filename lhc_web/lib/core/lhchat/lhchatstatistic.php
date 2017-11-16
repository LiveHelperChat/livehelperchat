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
        	
        	if (isset($msgFilter['filtergte']['time'])){
        	    unset($msgFilter['filtergte']['time']);
        	    $msgFilter['filtergte']['lh_msg.time'] = $filter['filtergte']['time'];
        	}
        		
        	if (isset($msgFilter['filterlte']['time'])){
        	    unset($msgFilter['filterlte']['time']);
        	    $msgFilter['filterlte']['lh_msg.time'] = $filter['filterlte']['time'];
        	}
        	        	
        	for ($i = 0; $i < 12;$i++) {
        		$dateUnix = mktime(0,0,0,date('m')-$i,1,date('y'));
        		$numberOfChats[$dateUnix] = array (
        				'closed' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        				'active' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        				'operators' 		=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        				'pending' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        				'unanswered' 		=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        		    
        				'msg_user' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filter' 	=> array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)'),    		
        				'msg_operator' 		=> (int)erLhcoreClassChat::getCount(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix)))+$msgFilter+$departmentMsgFilter,'lh_msg','count(lh_msg.id)'),    		
        				'msg_system' 		=> (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1,-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m\') = '. date('Ym',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)'),    		
        				
        				'chatinitdefault' 	=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        				'chatinitproact' 	=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
        		);    		    		
        	}
        	
        	$numberOfChats = array_reverse($numberOfChats,true);
        	
        	// @todo for future
        	if (isset($paramsExecution['comparetopast'])) {
        	    
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
    
    public static function getNumberOfChatsPerDay($filter = array())
    {	
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getnumberofchatsperday', array('filter' => $filter));
         
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
    
        	for ($i = 0; $i < $limitDays;$i++) {
        		$dateUnix = mktime(0,0,0,date('m',$startTimestamp),date('d',$startTimestamp)+$i,date('y',$startTimestamp));
        		$numberOfChats[$dateUnix] = array (
        				'closed' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),    		
        				'active' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),    		
        				'operators' 		=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),    		
        				'pending' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),    		
        		        'unanswered' 		=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('unanswered_chat' => 1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),
    
        				'msg_user' 			=> (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filter' 	=> array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)'),    		
        				'msg_operator' 		=> (int)erLhcoreClassChat::getCount(array('filtergt' => array('lh_msg.user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)))+$msgFilter+$departmentMsgFilter,'lh_msg','count(lh_msg.id)'),    		
        				'msg_system' 		=> (int)erLhcoreClassChat::getCount(array_merge_recursive(array('filterin' => array('lh_msg.user_id' => array(-1,-2)),'customfilter' =>  array('FROM_UNIXTIME(lh_msg.time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))),$msgFilter,$departmentMsgFilter),'lh_msg','count(lh_msg.id)'),    		
        				
        				'chatinitdefault' 	=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),    		
        				'chatinitproact' 	=> (int)erLhcoreClassChat::getCount(array_merge_recursive($departmentFilter,$filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix))))),    		
        		);
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
        		$dateUnix = mktime(0,0,0,date('m')-$i,1,date('y'));
        		$numberOfChats[$dateUnix] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)),'filterlt' =>  array('wait_time' => 600),'filtergt' =>  array('wait_time' => 0))),'lh_chat','AVG(wait_time)');
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
        		$numberOfChats[$dateUnix] = (int)erLhcoreClassChat::getCount(array_merge_recursive($filter,array('customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m%d\') = '. date('Ymd',$dateUnix)),'filterlt' =>  array('wait_time' => 600),'filtergt' =>  array('wait_time' => 0))),'lh_chat','AVG(wait_time)');
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
    
    public static function getWorkLoadStatistic($filter = array()) 
    {
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getworkloadstatistic',array('filter' => $filter));
         
        if ($statusWorkflow === false) {
        
        	$numberOfChats = array();
        	        	
        	for ($i = 0; $i < 24; $i++) {
        		$dateHour = str_pad($i , 2, '0' , STR_PAD_LEFT);
        		$numberOfChats[$i] = erLhcoreClassChat::getCount(array_merge(array('customfilter' =>  array('FROM_UNIXTIME(time,\'%k\') = '. $dateHour)),$filter));
        	}
        	
        	return $numberOfChats;
        	
        } else {
    	    return $statusWorkflow['list'];
    	}
    }
    
    public static function getAverageChatduration($days = 30, $filter = array()) {
    	
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getaveragechatduration',array('days' => $days, 'filter' => $filter));
         
        if ($statusWorkflow === false) {
        
            if (empty($filter)) {
                $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
            }
            
            $filter['filtergt']['user_id'] = 0;
            
            $filterCombined = array_merge_recursive($filter,array('filtergt' => array('chat_duration' => 0),'filter' =>  array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)));
                    
        	return erLhcoreClassChat::getCount($filterCombined, 'lh_chat', 'AVG(chat_duration)');
        	
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
        	 
        	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
        	$appendFilterTime = '';
        	 
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'time > :time ';
        	}
        	
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}
        	
        	$sql = "SELECT count(id) AS number_of_chats,country_name FROM lh_chat WHERE {$appendFilterTime} {$generalFilter} GROUP BY country_code,country_name ORDER BY number_of_chats DESC LIMIT 20";
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
    
    
    public static function averageOfChatsDialogsByUser($days = 30, $filter = array(), $limit = 20) 
    {    	    
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.averageofchatsdialogsbyuser',array('days' => $days, 'filter' => $filter, 'limit' => $limit));
        
        if ($statusWorkflow === false) {
            $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
            
            $filter['filter']['status'] = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
            $filter['filtergt']['chat_duration'] = 0;
            $filter['filtergt']['user_id'] = 0;
            
            $generalFilter = self::formatFilter($filter);
             
            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
            $appendFilterTime = '';
             
            if ($useTimeFilter == true) {
                $appendFilterTime = 'time > :time ';
            }
            
            if ($generalFilter != '' && $useTimeFilter == true) {
                $generalFilter = ' AND '.$generalFilter;
            }
             
            $sql = "SELECT AVG(chat_duration) AS avg_chat_duration,user_id FROM lh_chat WHERE {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY avg_chat_duration DESC LIMIT ".$limit;
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

            $useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
            $appendFilterTime = '';

            if ($useTimeFilter == true) {
                $appendFilterTime = 'time > :time ';
            }

            if ($generalFilter != '' && $useTimeFilter == true) {
                $generalFilter = ' AND '.$generalFilter;
            }

            $sql = "SELECT count(id) AS number_of_chats,dep_id FROM lh_chat WHERE {$appendFilterTime} {$generalFilter} GROUP BY dep_id ORDER BY number_of_chats DESC LIMIT 20";

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

    public static function numberOfChatsDialogsByUser($days = 30, $filter = array()) 
    {    	    
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.numberofchatsdialogsbyuser',array('days' => $days, 'filter' => $filter));

        if ($statusWorkflow === false) {
        	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        	
        	$generalFilter = self::formatFilter($filter);
        	
        	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
        	$appendFilterTime = '';
        	
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'time > :time ';
        	}
        	 
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}
        	    	
        	$sql = "SELECT count(id) AS number_of_chats,user_id FROM lh_chat WHERE {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 20";
        	
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
    
    public static function avgWaitTimeyUser($days = 30, $filter = array()) 
    {    	    
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.avgwaittimeuser',array('days' => $days, 'filter' => $filter));
        
        if ($statusWorkflow === false) {
        	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        	
        	$filter['filterlt']['wait_time'] = 600;
        	
        	$generalFilter = self::formatFilter($filter);
        	
        	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
        	$appendFilterTime = '';
        	
        	if ($useTimeFilter == true) {
        		$appendFilterTime = 'time > :time ';
        	}
        	 
        	if ($generalFilter != '' && $useTimeFilter == true) {
        		$generalFilter = ' AND '.$generalFilter;
        	}
        	    	
        	$sql = "SELECT avg(wait_time) AS avg_wait_time,user_id FROM lh_chat WHERE {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY avg_wait_time DESC LIMIT 20";
        	        	
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
        	
        	$generalFilter = self::formatFilter($filter);
        	    	 
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
        	WHERE {$appendFilterTime} {$generalFilter} 
        	GROUP BY lh_msg.user_id 
        	ORDER BY number_of_chats DESC LIMIT 20";
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
    			} elseif ($type == 'filterin') {
    				$returnFilter[] = $field.' IN ( '. implode(',', $value) . ')';
    			}
    		}    		
    	}

    	return implode(' AND ', $returnFilter);
    }
    
    public static function formatUserFilter(& $filterParams) {        
        if (isset($filterParams['input']->group_id) && is_numeric($filterParams['input']->group_id) && $filterParams['input']->group_id > 0 ) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_groupuser WHERE group_id = :group_id');
            $stmt->bindValue( ':group_id', $filterParams['input']->group_id, PDO::PARAM_INT);
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($userIds)) {
                $filterParams['filter']['filterin']['lh_chat.user_id'] = $userIds;
            }
        }
        
        if (isset($filterParams['input']->department_group_id) &&  is_numeric($filterParams['input']->department_group_id) && $filterParams['input']->department_group_id > 0 ) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT dep_id FROM lh_departament_group_member WHERE dep_group_id = :group_id');
            $stmt->bindValue( ':group_id', $filterParams['input']->department_group_id, PDO::PARAM_INT);
            $stmt->execute();
            $depIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($depIds)) {
                $filterParams['filter']['filterin']['lh_chat.dep_id'] = $depIds;
            }
        }
    }
    
    public static function getRatingByUser($days = 30, $filter = array()) 
    {    	    
    	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));    	    	
    	$rating = array();    

    	$generalFilter = self::formatFilter($filter);
    	if ($generalFilter != ''){
    		$generalFilter = ' AND '.$generalFilter;
    	}
    	
    	$useTimeFilter = !isset($filter['filtergte']['time']) && !isset($filter['filterlte']['time']);
    	$appendFilterTime = '';
    	if ($useTimeFilter == true) {
    		$appendFilterTime = ' AND time > :time ';
    	}    	
    	
    	$sql = "SELECT count(id) AS number_of_chats,user_id FROM lh_chat WHERE fbst = 1 {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 20";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	$rating['thumbsup'] = $stmt->fetchAll();
    		
    	$sql = "SELECT count(id) AS number_of_chats,user_id FROM lh_chat WHERE fbst = 2 {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 20";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	$rating['thumbdown'] = $stmt->fetchAll();
    		
    	$sql = "SELECT count(id) AS number_of_chats,user_id FROM lh_chat WHERE fbst = 0 {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 20";
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
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Number of chats while online'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Hours on chat (sum of chat duration)'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Time online (sum of time spend online)'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','AVG number of chat per hour'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average pick-up time'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, 2, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average chat length'));

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getagentstatistic_export_columns',array('xls' => & $objPHPExcel));

        $i = 3;
        foreach ($data as $item) {
            $key = 0;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->agentName);
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->numberOfChats);
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->numberOfChatsOnline);
            
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->totalHours/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');
            
            $key++;            
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($key, $i, $item->totalHoursOnline/(24*3600), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key, $i)->getNumberFormat()->setFormatCode('[HH]:MM:SS');
            
            $key++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->aveNumber);
            
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
    
    public static function getAgentStatistic ($days = 30, $filtergte) {
        $filter = array();
    
        if (isset($filtergte['filtergte']['time'])) {
            $filter['filtergte']['time'] = $filtergte['filtergte']['time'];
        } else {
            $filter['filtergte']['time'] = 0;
        }
    
        if (isset($filtergte['filterlte']['time'])) {
            $filter['filterlte']['time'] = $filtergte['filterlte']['time'];
        }
        
        $filterUsers = array();

        $userIdGroup = array();
        if (isset($filtergte['filter']['group_id'])) {
            $groupId = $filtergte['filter']['group_id'];
            unset($filtergte['filter']['group_id']);
            
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_groupuser WHERE group_id = :group_id');
            $stmt->bindValue( ':group_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($userIds)) {
                $userIdGroup = $userIds;
            } else {
                $userIdGroup = array(-1);
            }
        }

        
        if (isset($filtergte['filter']['department_group_id'])) {
            
            $depGroup = $filtergte['filter']['department_group_id'];
            unset($filtergte['filter']['department_group_id']);
                            
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_userdep WHERE dep_id IN (select dep_id FROM lh_departament_group_member WHERE dep_group_id = :dep_group_id)');
            $stmt->bindValue( ':dep_group_id', $depGroup, PDO::PARAM_INT);
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        
            $stmt = $db->prepare('select dep_id FROM lh_departament_group_member WHERE dep_group_id = :dep_group_id');
            $stmt->bindValue( ':dep_group_id', $depGroup, PDO::PARAM_INT);
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
            
            if (!empty($userIdGroup)) {                                
                $userIdGroup = array_unique(array_intersect($userIdGroup,$userIds));
                
                if (empty($userIdGroup)) {
                    $userIdGroup = array(-1);
                }
                
            } else {
                $userIdGroup = $userIds;
            }            
        }
        
        if (isset($filtergte['filter']['dep_id'])) {
                       
            $filter['filter']['dep_id'] = $filtergte['filter']['dep_id'];
            
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_userdep WHERE dep_id = :dep_id');
            $stmt->bindValue( ':dep_id', $filtergte['filter']['dep_id'], PDO::PARAM_INT);
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
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
            $filterUsers['filterin']['id'] = $userIdGroup;
        }
        
        $userList = erLhcoreClassModelUser::getUserList($filterUsers);
        
        if (empty($userList)) {
            return array();
        }
        
        $list = array();
        
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.getagentstatistic',array('user_list' => $userList, 'days' => $days, 'filter' => $filter));
        
        if ($statusWorkflow === false) {        
            foreach ($userList as $user) {
                $userInfo = erLhcoreClassModelUser::fetch($user->id,true);
                $filter['filter']['user_id'] = $user->id;     
                $agentName = $userInfo->name;
                
                $userChatsStats = erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filter);
                $numberOfChats = empty($userChatsStats) ? $numberOfChats = "0" : $userChatsStats[0]['number_of_chats'];
                
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
                            
                if ($totalHoursOnlineCount > 1) {
                    $aveNumber = round($numberOfChatsOnline / $totalHoursOnlineCount, 2);
                } else {
                    $aveNumber = $numberOfChatsOnline;
                }
                
                $userWaitTimeByOperator = self::avgWaitTimeyUser(30,$filter);
                $userWaitTimeByOperatorNumber = empty($userWaitTimeByOperator) ? 0 : $userWaitTimeByOperator[0]['avg_wait_time'];
                
                $avgWaitTime = empty($userWaitTimeByOperator) ? "0 s." : erLhcoreClassChat::formatSeconds($userWaitTimeByOperator[0]['avg_wait_time']);
                
                $avgDuration = self::getAverageChatduration(30,$filter);   
                $avgChatLength = $avgDuration ? erLhcoreClassChat::formatSeconds($avgDuration) : "0 s.";
                $list[] = (object)array(
                    'agentName' => $agentName, 
                    'userId' => $user->id,
                    'numberOfChats' => $numberOfChats, 
                    'numberOfChatsOnline' => $numberOfChatsOnline, 
                    'totalHours' => $totalHours,
                    'totalHours_front' => erLhcoreClassChat::formatSeconds($totalHours),
                    'totalHoursOnline' => $totalHoursOnline,
                    'totalHoursOnline_front' => erLhcoreClassChat::formatSeconds($totalHoursOnline),
                    'aveNumber' => $aveNumber, 
                    'avgWaitTime' => $userWaitTimeByOperatorNumber, 
                    'avgWaitTime_front' => $avgWaitTime, 
                    'avgChatLength' => $avgChatLength,
                    'avgChatLengthSeconds' => $avgDuration
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
    
    public static function totalHoursOfOnlineDialogsByUser($days = 30, $filter = array(), $limit = 20)
    {
        if (empty($filter)) {
            $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }

        return erLhcoreClassChat::getCount($filter,'lh_users_online_session','SUM(duration)');
    }
    
    public static function totalHoursOfChatsDialogsByUser($days = 30, $filter = array())
    {
        if (empty($filter)) {
            $filter['filtergt']['time'] = $dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
        }
        $filter['filtergt']['user_id'] = 0;
        return erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filtergt' => array('chat_duration' => 0),'filter' =>  array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))),'lh_chat','SUM(chat_duration)');
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
                $abandonedStarted = erLhcoreClassChat::getCount(array_merge_recursive($filter, $filterTimeout, array('filter' => array('user_id' => 0, 'status_sub' => erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT))), 'lh_chat', 'count(id)');
                
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
}

?>