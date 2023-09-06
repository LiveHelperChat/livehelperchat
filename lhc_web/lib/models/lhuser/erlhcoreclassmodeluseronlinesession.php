<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelUserOnlineSession
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_users_online_session';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'time' => $this->time,
            'lactivity' => $this->lactivity,
            'duration' => $this->duration
        );
    }
    
    public function __get($var)
    {
        switch ($var) {
            case 'time_front':
                $this->time_front = date('Ymd') == date('Ymd',$this->time) ? date(erLhcoreClassModule::$dateHourFormat,$this->time) : date(erLhcoreClassModule::$dateDateHourFormat,$this->time);
                return $this->time_front;
            ;
            break;
            
            case 'lactivity_front':
                $this->lactivity_front = date('Ymd') == date('Ymd',$this->lactivity) ? date(erLhcoreClassModule::$dateHourFormat,$this->lactivity) : date(erLhcoreClassModule::$dateDateHourFormat,$this->lactivity);
                return $this->lactivity_front;
            ;
            break;
            
            case 'duration_front':
                $this->duration_front = erLhcoreClassChat::formatSeconds($this->duration);
                return $this->duration_front;
            ;
            break;
            
            case 'user':
       		   $this->user = false;
       		   if ($this->user_id > 0) {
       		   		try {
       		   			$this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
       		   		} catch (Exception $e) {
       		   			$this->user = false;
       		   		}
       		   }
       		   return $this->user;
       		break;
            
            case 'user_name':
       			return $this->user_name = (string)$this->user;
       		break;	
            
            default:
                ;
            break;
        }
    }

    public static function setChatsBySessions(& $sessions, $filterList)
    {

        if (empty($sessions) || !isset($filterList['filterin']['user_id']) || count($filterList['filterin']['user_id']) > 1) {
            return;
        }

        $sessionsCloned = array_reverse($sessions, true);

        $ranges = array_keys($sessions);

        $start = $sessions[min($ranges)]->time;
        $end = $sessions[max($ranges)]->lactivity;

        $filter['filtergte']['time'] = $start;
        $filter['filterlte']['time'] = $end;
        $filter['limit'] = false;

        if (isset($filterList['filterin']['user_id'])) {
            $filter['filterin']['user_id'] = $filterList['filterin']['user_id'];
        }

        $filter['select_columns'] = ['time'];
        $filter['ignore_fields'] = ['all_columns'];

        $chats = erLhcoreClassModelChat::getList($filter);

        foreach ($chats as $chat) {
            $chatProcessed = false;
            foreach ($sessionsCloned as $key => $session) {
                if (($session->time <= $chat->time) && ($chat->time <= $session->lactivity)) {
                    $sessions[$key]->chatsOnline++;
                    $chatProcessed = true;
                    break;
                }
            }

            if ($chatProcessed == false) {
                foreach ($sessions as $key => $session) {
                    if ($session->lactivity < $chat->time) {
                        if ($key > 0) {
                            $sessions[$key]->chatsOffline++;
                        }
                        break;
                    }
                }
            }
        }
    }

    public $id = null;

    public $user_id = null;

    public $time = null;

    public $lactivity = null;

    public $duration = null;

    public $chatsOnline = 0;

    public $chatsOffline = 0;
}

?>