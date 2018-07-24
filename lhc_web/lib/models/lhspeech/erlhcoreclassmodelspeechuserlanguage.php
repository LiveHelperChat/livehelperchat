<?php

class erLhcoreClassModelSpeechUserLanguage {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_speech_user_language';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassSpeech::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'         		=> $this->id,
            'user_id'   	 	=> $this->user_id,
            'language'   	 	=> $this->language,
        );
    }

    public function __toString()
    {
        return $this->language;
    }

    public $id = NULL;
    public $user_id = NULL;
    public $language = '';
}

?>