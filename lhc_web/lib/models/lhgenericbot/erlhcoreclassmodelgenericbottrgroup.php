<?php

class erLhcoreClassModelGenericBotTrGroup {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_tr_group';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'nick' => $this->nick,
            'filepath' => $this->filepath,
            'filename' => $this->filename,
            'configuration' => $this->configuration,
        );
        return $stateArray;
    }

    public function __get($var) {

        switch ($var) {
            case 'configuration_array':
                $this->configuration_array = array();
                if ($this->configuration != ''){
                    $jsonData = json_decode($this->configuration,true);
                    if ($jsonData !== null) {
                        $this->configuration_array = $jsonData;
                    } else {
                        $this->configuration_array = array();
                    }
                }
                return $this->configuration_array;
                break;

            case 'name_support':
                return $this->name_support = $this->nick;
                break;

            case 'has_photo':
                return $this->filename != '';
                break;

            case 'photo_path':
                $this->photo_path = ($this->filepath != '' ? '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'. $this->filepath . $this->filename;
                return $this->photo_path;
                break;

            case 'file_path_server':
                return $this->filepath . $this->filename;
                break;

            default:
                break;
        }
    }
    public function beforeRemove() {
        $q = ezcDbInstance::get()->createDeleteQuery();

        // Bot groups
        $q->deleteFrom( 'lh_generic_bot_tr_item' )->where( $q->expr->eq( 'group_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        $this->removeFile();
    }
    public function removeFile()
    {
        if ($this->filename != '') {
            if ( file_exists($this->filepath . $this->filename) ) {
                unlink($this->filepath . $this->filename);
            }

            if ($this->filepath != '') {
                erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/bottrphoto/',str_replace('var/bottrphoto/','',$this->filepath));
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.remove_photo', array('user' => & $this));

            $this->filepath = '';
            $this->filename = '';
            $this->saveThis();
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $nick = '';
    public $filepath = '';
    public $filename = '';
    public $configuration = '';
}