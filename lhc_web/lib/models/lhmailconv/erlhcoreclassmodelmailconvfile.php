<?php

class erLhcoreClassModelMailconvFile
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_file';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'message_id' => $this->message_id,
            'size' => $this->size,
            'name' => $this->name,
            'description' => $this->description,
            'extension' => $this->extension,
            'type' => $this->type,
            'attachment_id' => $this->attachment_id,
            'file_path' => $this->file_path,
            'content_id' => $this->content_id,
            'disposition' => $this->disposition,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                return date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                break;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $message_id = null;
    public $size = 0;
    public $name = '';
    public $description = '';
    public $extension = '';
    public $type = '0';
    public $attachment_id = '0';
    public $file_path = '';
    public $content_id = '';
    public $disposition = '';
}

?>