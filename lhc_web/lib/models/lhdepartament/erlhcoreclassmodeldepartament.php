<?php

class erLhcoreClassModelDepartament {

    use erLhcoreClassDBTrait;
   
    public static $dbTable = 'lh_departament';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';
    
    public static $dbSortOrder = 'ASC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'xmpp_recipients' => $this->xmpp_recipients,
            'xmpp_group_recipients' => $this->xmpp_group_recipients,
            'priority' => $this->priority,
            'sort_priority' => $this->sort_priority,
            'department_transfer_id' => $this->department_transfer_id,
            'transfer_timeout' => $this->transfer_timeout,
            'identifier' => $this->identifier,
            'mod_start_hour' => $this->mod_start_hour,
            'mod_end_hour' => $this->mod_end_hour,
            'tud_start_hour' => $this->tud_start_hour,
            'tud_end_hour' => $this->tud_end_hour,
            'wed_start_hour' => $this->wed_start_hour,
            'wed_end_hour' => $this->wed_end_hour,
            'thd_start_hour' => $this->thd_start_hour,
            'thd_end_hour' => $this->thd_end_hour,
            'frd_start_hour' => $this->frd_start_hour,
            'frd_end_hour' => $this->frd_end_hour,
            'sad_start_hour' => $this->sad_start_hour,
            'sad_end_hour' => $this->sad_end_hour,
            'sud_start_hour' => $this->sud_start_hour,
            'sud_end_hour' => $this->sud_end_hour,
            'inform_options' => $this->inform_options,
            'inform_delay' => $this->inform_delay,
            'inform_close' => $this->inform_close,
            'inform_close_all' => $this->inform_close_all,
            'inform_close_all_email' => $this->inform_close_all_email,
            'online_hours_active' => $this->online_hours_active,
            'disabled' => $this->disabled,
            'hidden' => $this->hidden,
            'visible_if_online' => $this->visible_if_online,
            'delay_lm' => $this->delay_lm,
            'inform_unread' => $this->inform_unread,
            'inform_unread_delay' => $this->inform_unread_delay,
            'na_cb_execute' => $this->na_cb_execute,
            'nc_cb_execute' => $this->nc_cb_execute,
            'active_balancing' => $this->active_balancing,
            'max_active_chats' => $this->max_active_chats,
            'max_timeout_seconds' => $this->max_timeout_seconds,
            'exclude_inactive_chats' => $this->exclude_inactive_chats,
            'max_ac_dep_chats' => $this->max_ac_dep_chats,
            'attr_int_1' => $this->attr_int_1,
            'attr_int_2' => $this->attr_int_2,
            'attr_int_3' => $this->attr_int_3,
            'active_chats_counter' => $this->active_chats_counter,
            'pending_chats_counter' => $this->pending_chats_counter,
            'closed_chats_counter' => $this->closed_chats_counter,
            'product_configuration' => $this->product_configuration,
            'pending_max' => $this->pending_max,
            'pending_group_max' => $this->pending_group_max,
            'delay_before_assign' => $this->delay_before_assign,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function beforeRemove()
    {        
        // Delete user assigned departaments
        $q = ezcDbInstance::get()->createDeleteQuery();
        $q->deleteFrom('lh_userdep')->where($q->expr->eq('dep_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
        
        // Delete departament products
        $q = ezcDbInstance::get()->createDeleteQuery();
        $q->deleteFrom('lh_abstract_product_departament')->where($q->expr->eq('departament_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
        
        // Delete member from department limit group
        $q = ezcDbInstance::get()->createDeleteQuery();
        $q->deleteFrom('lh_departament_limit_group_member')->where($q->expr->eq('dep_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
        
        // Delete member from department group
        $q = ezcDbInstance::get()->createDeleteQuery();
        $q->deleteFrom('lh_departament_group_member')->where($q->expr->eq('dep_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
    }

    public function __get($var) {
	   	switch ($var) {
	   		case 'is_online':
	   			$this->is_online = erLhcoreClassChat::isOnline($this->id);
	   			return $this->is_online;
	   		break;

	   		case 'is_online_exclipic':
	   			$this->is_online_exclipic = erLhcoreClassChat::isOnline($this->id,true);
	   			return $this->is_online_exclipic;
	   		break;

	   		case 'inform_options_array':
	   			$this->inform_options_array = $this->inform_options != '' ? unserialize($this->inform_options) : array();
	   			return $this->inform_options_array;
	   		break;

	   		case 'can_delete':
	   			$this->can_delete = erLhcoreClassChat::getCount(array('filter' => array('dep_id' => $this->id))) == 0;
	   			return $this->can_delete;
	   		break;

	   		case 'is_overloaded':
	   		    $this->is_overloaded = ($this->pending_max > 0 && $this->pending_chats_counter >= $this->pending_max) || ($this->pending_group_max > 0 && $this->pending_chats_counter >= $this->pending_group_max);
	   		    return $this->is_overloaded;
	   	    break;
	   		
	   		case 'department_transfer':

	   			$this->department_transfer = false;

	   			if ($this->department_transfer_id > 0) {
	   				try {
	   					$this->department_transfer = self::fetch($this->department_transfer_id,true);
	   				} catch (Exception $e) {

	   				}
	   			}

	   			return $this->department_transfer;
	   		break;

	   		case 'start_hour_front':
	   				return floor($this->start_hour/100);
	   			break;

	   		case 'start_minutes_front':
	   				return $this->start_hour - ($this->start_hour_front * 100);
	   			break;

	   		case 'end_hour_front':
	   				return floor($this->end_hour/100);
	   			break;
	   			
	   		case 'product_configuration_array':
	   		        $this->product_configuration_array = array();
	   		        if ($this->product_configuration != '') {
	   		            $this->product_configuration_array = json_decode($this->product_configuration,true);
	   		        }
	   		        return $this->product_configuration_array;
	   		    break;
	   		    
	   		case 'end_minutes_front':
	   				return $this->end_hour - ($this->end_hour_front * 100);
	   			break;
            case 'mod_start_hour_front':
            case 'tud_start_hour_front':
            case 'wed_start_hour_front':
            case 'thd_start_hour_front':
            case 'frd_start_hour_front':
            case 'sad_start_hour_front':
            case 'sud_start_hour_front':
                $startHourName = str_replace('_start_hour_front', '', $var).'_start_hour';
                return $this->{$startHourName} != -1 ? floor($this->{$startHourName}/100) : '';
                break;

            case 'mod_start_minutes_front':
            case 'tud_start_minutes_front':
            case 'wed_start_minutes_front':
            case 'thd_start_minutes_front':
            case 'frd_start_minutes_front':
            case 'sad_start_minutes_front':
            case 'sud_start_minutes_front':
                $startHourName = str_replace('_start_minutes_front', '', $var).'_start_hour';
                $startHourFrontName = str_replace('_start_minutes_front', '', $var).'_start_hour_front';
                return $this->{$startHourName} != -1 ? $this->{$startHourName} - ($this->{$startHourFrontName} * 100) : '';
                break;

            case 'mod_end_hour_front':
            case 'tud_end_hour_front':
            case 'wed_end_hour_front':
            case 'thd_end_hour_front':
            case 'frd_end_hour_front':
            case 'sad_end_hour_front':
            case 'sud_end_hour_front':
                $endHourName = str_replace('_end_hour_front', '', $var).'_end_hour';
                return $this->{$endHourName} != -1 ? floor($this->{$endHourName}/100) : '';
                break;

            case 'mod_end_minutes_front':
            case 'tud_end_minutes_front':
            case 'wed_end_minutes_front':
            case 'thd_end_minutes_front':
            case 'frd_end_minutes_front':
            case 'sad_end_minutes_front':
            case 'sud_end_minutes_front':
                $endHourName = str_replace('_end_minutes_front', '', $var).'_end_hour';
                $endHourFrontName = str_replace('_end_minutes_front', '', $var).'_end_hour_front';
                return $this->$endHourName != -1 ? $this->{$endHourName} - ($this->{$endHourFrontName} * 100) : '';
                break;

	   		default:
	   		break;
	   	}
    }
   
    public $id = null;
    public $name = '';
    public $email = '';
    public $priority = 0;
    public $sort_priority = 0;
    public $department_transfer_id = 0;
    public $transfer_timeout = 0;
    public $identifier = '';    
    public $xmpp_recipients = '';    
    public $xmpp_group_recipients = '';
    public $mod_start_hour = -1;
    public $mod_end_hour = -1;
    public $tud_start_hour = -1;
    public $tud_end_hour = -1;
    public $wed_start_hour = -1;
    public $wed_end_hour = -1;
    public $thd_start_hour = -1;
    public $thd_end_hour = -1;
    public $frd_start_hour = -1;
    public $frd_end_hour = -1;
    public $sad_start_hour = -1;
    public $sad_end_hour = -1;
    public $sud_start_hour = -1;
    public $sud_end_hour = -1;
    public $inform_delay = 0;
    public $inform_options = '';    
    public $inform_close = 0;    
    public $online_hours_active = 0;
    public $disabled = 0;
    public $hidden = 0;
    public $inform_unread = 0;
    public $inform_unread_delay = 0;
    public $na_cb_execute = 0;
    public $nc_cb_execute = 0;
    public $active_balancing = 0;
    public $max_active_chats = 0;
    public $max_timeout_seconds = 0;
    public $attr_int_1 = 0;
    public $attr_int_2 = 0;
    public $attr_int_3 = 0;
    public $visible_if_online = 0;
    public $inform_close_all = 0;
    public $inform_close_all_email = '';
    public $pending_max = 0;
    public $pending_group_max = 0;
    public $exclude_inactive_chats = 0;
    public $max_ac_dep_chats = 0;

    public $active_chats_counter = 0;
    public $delay_before_assign = 0;
    public $pending_chats_counter = 0;
    public $closed_chats_counter = 0;
    public $product_configuration = '';


    // 0 - disabled
    // > 0 - delay in seconds
    public $delay_lm = 0;
}

?>