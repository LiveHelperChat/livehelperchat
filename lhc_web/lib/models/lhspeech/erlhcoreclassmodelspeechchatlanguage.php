<?php

class erLhcoreClassModelSpeechChatLanguage {

	public function getState()
	{
		return array(
				'id'         		=> $this->id,
				'chat_id'   	 	=> $this->chat_id,				
				'language_id'   	=> $this->language_id,				
				'dialect'   	 	=> $this->dialect				
		);
	}

	public function setState( array $properties )
	{
		foreach ( $properties as $key => $val )
		{
			$this->$key = $val;
		}
	}
	
	public static function getList($params)
	{
	    return erLhcoreClassSpeech::getList($params,'erLhcoreClassModelSpeechChatLanguage','lh_speech_chat_language');
	}
	
	public function __get($var) {
		switch ($var) {							
							
			default:
				;
			break;
		}
	}
		
	public static function fetch($id) {
		$item = erLhcoreClassSpeech::getSession()->load( 'erLhcoreClassModelSpeechChatLanguage', (int)$id );
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
	public $chat_id = NULL;
	public $language_id = NULL;
	public $dialect = NULL;
}

?>