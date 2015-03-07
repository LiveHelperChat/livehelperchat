<?php

class erLhcoreClassModelSpeechLanguageDialect {

	public function getState()
	{
		return array(
				'id'         		=> $this->id,
				'language_id'   	=> $this->language_id,				
				'lang_name'   	 	=> $this->lang_name,				
				'lang_code'   	 	=> $this->lang_code				
		);
	}

	public function setState( array $properties )
	{
		foreach ( $properties as $key => $val )
		{
			$this->$key = $val;
		}
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
	
	public static function getList($params)
	{
	    return erLhcoreClassSpeech::getList($params,'erLhcoreClassModelSpeechLanguageDialect','lh_speech_language_dialect');
	}
		
	public static function fetch($id) {
		$item = erLhcoreClassSpeech::getSession()->load( 'erLhcoreClassModelSpeechLanguageDialect', (int)$id );
		return $item;
	}

	public function saveThis()
	{
		erLhcoreClassSpeech::getSession()->saveOrUpdate($this);
	}

	public function removeThis() {
		erLhcoreClassSpeech::getSession()->delete( $this );
	}

	public $id = NULL;
	public $name = NULL;
}

?>