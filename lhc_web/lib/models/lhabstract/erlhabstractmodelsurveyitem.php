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
			'online_user_id'=> $this->online_user_id
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

	   	case 'chat':
	   	       $this->chat = null;

	   	       if ($this->chat_id > 0) {
	   	           $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                   if (!($this->chat instanceof erLhcoreClassModelChat)){
                      $this->chat = null;
                   }
	   	       }

	   		   return $this->chat;
	   		break;
	       
	   	case 'ftime_front':
	   	       $this->ftime_front = date('Ymd') == date('Ymd',$this->ftime) ? date(erLhcoreClassModule::$dateHourFormat,$this->ftime) : date(erLhcoreClassModule::$dateDateHourFormat,$this->ftime);
	   		   return $this->ftime_front;
	   		break;
	   		
	   	case 'user':
	   	           $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);

                   if (!($this->user instanceof erLhcoreClassModelUser)){
                       $this->user = new erLhcoreClassModelUser();
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
	    
        foreach ([
            'max_stars_1_title',
            'max_stars_2_title',
            'max_stars_3_title',
            'max_stars_4_title',
            'max_stars_5_title',
            'question_options_1',
            'question_options_1_items',
            'question_options_2',
            'question_options_2_items',
            'question_options_3',
            'question_options_3_items',
            'question_options_4',
            'question_options_4_items',
            'question_options_5',
            'question_options_5_items',
            'question_plain_1',
            'question_plain_2',
            'question_plain_3',
            'question_plain_4',
            'question_plain_5'] as $fieldTranslate) {
            if (isset($survey->{$fieldTranslate}) && $survey->{$fieldTranslate} != '') {
                $survey->{$fieldTranslate} = erLhcoreClassGenericBotWorkflow::translateMessage($survey->{$fieldTranslate}, array('chat' => $chat));
            }
        }
        
	    if (!empty($items)){
            $surveyItem = array_shift($items);
            if ($surveyItem->online_user_id == 0 && $chat->online_user_id > 0) {
                $surveyItem->online_user_id = $chat->online_user_id;
                $surveyItem->updateThis(['update' => ['online_user_id']]);
            }
	        return $surveyItem;
	    } else {
	        $surveyItem = new self();
	        $surveyItem->chat_id = $chat->id;
	        $surveyItem->survey_id = $survey->id;
	        $surveyItem->user_id = $chat->user_id;
	        $surveyItem->dep_id = $chat->dep_id;
            $surveyItem->online_user_id = $chat->online_user_id;
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
	public $online_user_id = 0;
	public $status = self::STATUS_PERSISTENT;
	
	public $hide_add = false;
	public $hide_delete = false;

}

?>