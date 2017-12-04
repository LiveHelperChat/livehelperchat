<?php

class erLhcoreClassUserUtils
{
    public static function updateStats($userlist)
    {
        $remap = array();
        
        if (!empty($userlist)) {
            $sql = "SELECT user_id, active_chats, pending_chats, inactive_chats FROM lh_userdep WHERE user_id IN (" . implode(',', array_keys($userlist)) . ') GROUP by user_id';
            $db = ezcDbInstance::get();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($stats as $stat) {
                $remap[$stat['user_id']] = array(
                    'ac' => $stat['active_chats'],
                    'pc' => $stat['pending_chats'],
                    'ic' => $stat['inactive_chats'],
                    'acrt' => erLhcoreClassModelChat::getCount(array(
                        'filter' => array(
                            'user_id' => $stat['user_id'],
                            'status' => 1
                        )
                    ))
                );
            }
        }
        
        return $remap;
    }
}

?>