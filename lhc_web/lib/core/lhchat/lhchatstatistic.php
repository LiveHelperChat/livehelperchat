<?php

class erLhcoreClassChatStatistic {


    /**
     * Gets pending chats
     */
    public static function getTopTodaysOperators($limit = 100, $offset = 0)
    {
    	$time = (time()-(24*3600));

    	$SQL = 'SELECT lh_chat.user_id,count(lh_chat.id) as assigned_chats FROM lh_chat WHERE `time` > :time GROUP BY user_id';

    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare($SQL);
    	$stmt->bindValue( ':time',$time);
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
    	}

    	return $usersReturn;
    }

}

?>