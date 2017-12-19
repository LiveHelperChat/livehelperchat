<?php

class erLhcoreClassModelSpeechLanguageDialect {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_speech_language_dialect';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassSpeech::getSession';

    public static $dbSortOrder = 'DESC';

	public function getState()
	{
		return array(
				'id'         		=> $this->id,
				'language_id'   	=> $this->language_id,				
				'lang_name'   	 	=> $this->lang_name,				
				'lang_code'   	 	=> $this->lang_code				
		);
	}

	public function __get($var) {
		switch ($var) {							
			case 'dialect_name':
					return $this->lang_name.' ('.$this->lang_code.')'; 
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