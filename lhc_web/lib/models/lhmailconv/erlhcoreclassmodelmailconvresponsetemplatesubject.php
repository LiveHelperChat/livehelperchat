<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvResponseTemplateSubject
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_response_template_subject';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'template_id' => $this->template_id,
            'subject_id' => $this->subject_id
        );
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

    public $id = NULL;
    public $template_id = 0;
    public $subject_id = 0;
}

?>