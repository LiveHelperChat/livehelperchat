<?php

#[\AllowDynamicProperties]
class erLhcoreClassModelChatFile
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_file';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'upload_name' => $this->upload_name,
            'size' => $this->size,
            'type' => $this->type,
            'file_path' => $this->file_path,
            'extension' => $this->extension,
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id,
            'online_user_id' => $this->online_user_id,
            'date' => $this->date,
            'persistent' => $this->persistent,
            'width' => $this->width,
            'height' => $this->height,
            'meta_msg' => $this->meta_msg,
            'tmp' => $this->tmp,
        );
    }

    public static function deleteByChatId($chat_id)
    {
        foreach (self::getList(array('filter' => array('chat_id' => $chat_id))) as $file) {
            $file->removeThis();
        }
    }

    public function beforeRemove()
    {
        if (file_exists($this->file_path_server)) {
            unlink($this->file_path_server);
        }

        if ($this->file_path != '') {
            erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/', str_replace('var/', '', $this->file_path));
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.remove_file', array('chat_file' => & $this));
    }


    public function __get($var)
    {

        switch ($var) {
            case 'file_path_server':
                $this->file_path_server = $this->file_path . $this->name;
                return $this->file_path_server;;
                break;

            case 'security_hash':
                // AWS plugin changes file name, but we always use original name
                $parts = explode('/', $this->name);
                end($parts);
                $name = end($parts);
                $this->security_hash = md5($name . '_' . $this->chat_id);
                return $this->security_hash;


            case 'chat':
                $this->chat = false;
                if ($this->chat_id > 0) {
                    try {
                        $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                    } catch (Exception $e) {
                        $this->chat = new erLhcoreClassModelChat();
                    }
                }
                return $this->chat;

            case 'user':
                $this->user = false;
                if ($this->user_id > 0) {
                    try {
                        $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                    } catch (Exception $e) {
                        $this->user = false;
                    }
                }
                return $this->user;


            case 'date_front':
                $this->date_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->date);
                return $this->date_front;

            case 'meta_msg_array':
                $this->meta_msg_array = array();
                if ($this->meta_msg != '') {
                    $jsonData = json_decode($this->meta_msg, true);
                    if ($jsonData !== null) {
                        $this->meta_msg_array = $jsonData;
                    }
                }
                return $this->meta_msg_array;

            case 'file_body':
                return 'data:'.$this->type.';base64,'.base64_encode(file_get_contents($this->file_path_server));

            case 'file_body_embed':
                return '[chatfilebody='.$this->id . '_' . $this->security_hash . ']';

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $name = null;
    public $upload_name = null;
    public $type = null;
    public $file_path = null;
    public $size = null;
    public $extension = null;
    public $date = 0;
    public $user_id = 0;
    public $chat_id = 0;
    public $online_user_id = 0;
    public $persistent = 0;
    public $width = 0;
    public $height = 0;
    public $tmp = 0;
    public $meta_msg = '';

}

?>