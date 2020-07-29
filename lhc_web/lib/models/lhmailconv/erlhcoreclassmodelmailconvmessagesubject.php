<?php
/**
 *
 * user_id - administartor user_id,
 * If 0 web user
 *
 * */

class erLhcoreClassModelMailconvMessageSubject
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_msg_subject';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'message_id' => $this->message_id,
            'conversation_id' => $this->conversation_id
        );

        return $stateArray;
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
    public $message_id = 0;
    public $conversation_id = 0;
}

?>