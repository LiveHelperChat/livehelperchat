<?php

class erLhcoreClassModelSpeechLanguage {

	public function getState()
	{
		return array(
				'id'         		=> $this->id,
				'name'   	 	    => $this->name				
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
	    return erLhcoreClassSpeech::getList($params);
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
		
	public static function fetch($id) {
		$item = erLhcoreClassSpeech::getSession()->load( 'erLhcoreClassModelSpeechLanguage', (int)$id );
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