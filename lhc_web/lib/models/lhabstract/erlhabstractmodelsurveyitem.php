<?php
/**
 * 
 * @author Remigijus Kiminas
 * 
 * @desc Stores surveys themself
 *
 */

class erLhAbstractModelSurveyItem {

    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_abstract_survey_item';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';
    
    public static $dbSortOrder = 'DESC';
    
	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'survey_id'  	=> $this->survey_id,
			'chat_id'		=> $this->chat_id,			
			'user_id'		=> $this->user_id,
			'dep_id'		=> $this->dep_id,		
			'status'	    => $this->status,		
			'ftime'		    => $this->ftime, // Then user was completed by visitor
		);

		for($i = 1; $i <= 5; $i++) {
			$stateArray['max_stars_' . $i] = $this->{'max_stars_' . $i};
			$stateArray['question_options_' . $i] = $this->{'question_options_' . $i};
			$stateArray['question_plain_' . $i] = $this->{'question_plain_' . $i};		
		}

		return $stateArray;
	}

	public function __toString()
	{
		return $this->survey;
	}

	public function __construct()
	{
		$fields = array();
		
		for($i = 1; $i <= 5; $i++) {
			$this->{'max_stars_' . $i} = 0;
			$this->{'question_options_' . $i} = 0;
			$this->{'question_plain_' . $i} = '';			
		}		
	}
	
	public function __get($var)
	{
	   switch ($var) {
	       
	   	case 'survey':
	   	       $this->survey = false;
	   	       
	   	       if ($this->survey_id > 0) {
	   	           $this->survey = erLhAbstractModelSurvey::fetch($this->survey_id);
	   	       }
	   	       
	   		   return $this->survey;
	   		break;
	       
	   	case 'ftime_front':
	   	       $this->ftime_front = date('Ymd') == date('Ymd',$this->ftime) ? date(erLhcoreClassModule::$dateHourFormat,$this->ftime) : date(erLhcoreClassModule::$dateDateHourFormat,$this->ftime);
	   		   return $this->ftime_front;
	   		break;
	   		
	   	case 'user':
	   	       try {
	   	           $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
	   	       } catch (Exception $e) {
	   	           $this->user = false;
	   	       }
	   		   return $this->user;
	   		break;

	   	case 'is_filled':	   	   	   	       
	   	       return !is_null($this->id) && $this->status == self::STATUS_PERSISTENT;
	   	    break;	
	   	    
   	    case 'department':
   	        $this->department = false;
   	        if ($this->dep_id > 0) {
   	            try {
   	           					$this->department = erLhcoreClassModelDepartament::fetch($this->dep_id,true);
   	            } catch (Exception $e) {
   	    
   	            }
   	        }
   	    
   	        return $this->department;
   	        break;
   	    
   	    case 'department_name':
   	        return $this->department_name = (string)$this->department;
   	        break;
   	        
   	    case 'average_stars':
   	            return round($this->virtual_total_stars/$this->virtual_chats_number,2);
   	        break;
   	                
	   	default:	   		
	   		break;
	   }
	}

	public static function getInstance(erLhcoreClassModelChat $chat, erLhAbstractModelSurvey $survey) 
	{
	    $items = self::getList(array('filter' => array('chat_id' => $chat->id, 'survey_id' => $survey->id)));
	    
	    if (!empty($items)){
	        return array_shift($items);
	    } else {
	        $surveyItem = new self();
	        $surveyItem->chat_id = $chat->id;
	        $surveyItem->survey_id = $survey->id;
	        $surveyItem->user_id = $chat->user_id;
	        $surveyItem->dep_id = $chat->dep_id;
	        $surveyItem->ftime = time();
	        return $surveyItem;
	    }
	}
		
	const STATUS_PERSISTENT = 0;
	const STATUS_TEMP = 1;
	
   	public $id = NULL;
	public $survey_id = NULL;
	public $chat_id = NULL;
	public $user_id = 0;
	public $ftime = 0;
	public $status = self::STATUS_PERSISTENT;
	
	public $hide_add = false;
	public $hide_delete = false;

}

?>