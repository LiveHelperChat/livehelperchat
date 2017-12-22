<?php

class erLhcoreClassModelSpeechLanguage {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_speech_language';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassSpeech::getSession';

    public static $dbSortOrder = 'DESC';


	public function getState()
	{
		return array(
				'id'         		=> $this->id,
				'name'   	 	    => $this->name				
		);
	}

	public function __toString()
    {
        return $this->name;
    }

	public function __get($var) {
		switch ($var) {							
			case 'mtime_front':
					return date('Ymd') == date('Ymd',$this->mtime) ? date(erLhcoreClassModule::$dateHourFormat,$this->mtime) : date(erLhcoreClassModule::$dateDateHourFormat,$this->mtime); 
				break;
				
			default:
				;
			break;
		}
	}

	public $id = NULL;
	public $name = NULL;
}

?>