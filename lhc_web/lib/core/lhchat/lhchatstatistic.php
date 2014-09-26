<?php

class erLhcoreClassChatStatistic {


    /**
     * Gets pending chats
     */
    public static function getTopTodaysOperators($limit = 100, $offset = 0)
    {
    	$time = (time()-(24*3600));

    	$SQL = 'SELECT lh_chat.user_id,count(lh_chat.id) as assigned_chats FROM lh_chat WHERE time > :time AND user_id > 0 GROUP BY user_id';

    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($SQL);
    	$stmt->bindValue( ':time',$time,PDO::PARAM_INT);
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

    	$usersReturn = array();
    	foreach ($rows as $row) {
    		$usersReturn[$row['user_id']] = $users[$row['user_id']];
    		$usersReturn[$row['user_id']]->statistic_total_chats = $row['assigned_chats'];
    		$usersReturn[$row['user_id']]->statistic_total_messages = erLhcoreClassChat::getCount(array('filtergte' => array('time' => $time),'filter' => array('user_id' => $row['user_id'])),'lh_msg');
    		
    		$usersReturn[$row['user_id']]->statistic_upvotes = erLhcoreClassChat::getCount(array('filtergte' => array('time' => $time),'filter' => array('fbst' => 1,'user_id' => $row['user_id'])));
    		$usersReturn[$row['user_id']]->statistic_downvotes = erLhcoreClassChat::getCount(array('filtergte' => array('time' => $time),'filter' => array('fbst' => 2,'user_id' => $row['user_id'])));
    	}

    	return $usersReturn;
    }
    
    /*
     * Returns last 12 month chats statistic
     * */
    public static function getNumberOfChatsPerMonth($filter = array())
    {	
    	$numberOfChats = array();
    	    	
    	for ($i = 0; $i < 12;$i++) {
    		$dateUnix = mktime(0,0,0,date('m')-$i,1,date('y'));
    		$numberOfChats[$dateUnix] = array (
    				'closed' 			=> erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
    				'active' 			=> erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
    				'operators' 		=> erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
    				'pending' 			=> erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
    				
    				'msg_user' 			=> erLhcoreClassChat::getCount(array('filter' 	=> array('user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))+$filter,'lh_msg'),    		
    				'msg_operator' 		=> erLhcoreClassChat::getCount(array('filtergt' => array('user_id' => 0),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))+$filter,'lh_msg'),    		
    				'msg_system' 		=> erLhcoreClassChat::getCount(array('filter' 	=> array('user_id' => -1),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix)))+$filter,'lh_msg'),    		
    				
    				'chatinitdefault' 	=> erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
    				'chatinitproact' 	=> erLhcoreClassChat::getCount(array_merge_recursive($filter,array('filter' => array('chat_initiator' => erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE),'customfilter' =>  array('FROM_UNIXTIME(time,\'%Y%m\') = '. date('Ym',$dateUnix))))),    		
    		);
    	}
    	return $numberOfChats;
    }
    
    public static function getWorkLoadStatistic($filter = array()) 
    {
    	$numberOfChats = array();
    	
    	for ($i = 0; $i < 24; $i++) {
    		$dateHour = str_pad($i , 2, '0' , STR_PAD_LEFT);
    		$numberOfChats[$i] = erLhcoreClassChat::getCount(array_merge(array('customfilter' =>  array('FROM_UNIXTIME(time,\'%k\') = '. $dateHour)),$filter));
    	}
    	
    	return $numberOfChats;
    }
    
    public static function getAverageChatduration($days = 30) {
    	$dateUnixPast = mktime(0,0,0,date('m'),date('d')-$days,date('y'));
    	return erLhcoreClassChat::getCount(array('filtergt' => array('time' => $dateUnixPast,'chat_duration' => 0),'filter' =>  array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)),'lh_chat','AVG(chat_duration)');
    }
    
    public static function getTopChatsByCountry($days = 30, $filter = array()) 
    {
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
    	
    	$sql = "SELECT count(id) AS number_of_chats,country_name FROM lh_chat WHERE {$appendFilterTime} {$generalFilter} GROUP BY country_code ORDER BY number_of_chats DESC LIMIT 20";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	return $stmt->fetchAll();
    }
    
    public static function numberOfChatsDialogsByUser($days = 30, $filter = array()) 
    {    	    
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
    }
    
    public static function numberOfMessagesByUser($days = 30, $filter = array()) 
    {    	    
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
    	
    	$sql = "SELECT count(id) AS number_of_chats,user_id FROM lh_msg WHERE {$appendFilterTime} {$generalFilter} GROUP BY user_id ORDER BY number_of_chats DESC LIMIT 20";
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($sql);
    	
    	if ($useTimeFilter == true) {
    		$stmt->bindValue(':time',$dateUnixPast);
    	}
    	
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	$stmt->execute();
    	return $stmt->fetchAll();
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
    			} elseif ($type == 'filtergte') {
    				$returnFilter[] = $field.' >= '.$db->quote($value);
    			}
    		}    		
    	}
    	return implode(' AND ', $returnFilter);
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

    
    
}

?>