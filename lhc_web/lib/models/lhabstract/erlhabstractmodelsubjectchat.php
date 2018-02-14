<?php

class erLhAbstractModelSubjectChat {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_subject_chat';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'chat_id' => $this->chat_id
        );

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->subject;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'subject':
                $this->subject = erLhAbstractModelSubject::fetch($this->subject_id);
                return $this->subject;
            default:
                ;
                break;
        }
    }

    public $id = null;
    public $subject_id = 0;
    public $chat_id = 0;
}