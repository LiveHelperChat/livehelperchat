<?php

class erLhcoreClassModelGenericBotBot {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_bot';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'nick' => $this->nick,
            'configuration' => $this->configuration,
            'attr_str_1' => $this->attr_str_1,
            'attr_str_2' => $this->attr_str_2,
            'attr_str_3' => $this->attr_str_3,
            'filepath' => $this->filepath,
            'filename' => $this->filename,
        );

        return $stateArray;
    }

    public function beforeRemove() {
        $q = ezcDbInstance::get()->createDeleteQuery();

        // Bot groups
        $q->deleteFrom( 'lh_generic_bot_group' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        // Bot payloads
        $q->deleteFrom( 'lh_generic_bot_payload' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        // Bot triggers
        $q->deleteFrom( 'lh_generic_bot_trigger' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        // Bot trigger event
        $q->deleteFrom( 'lh_generic_bot_trigger_event' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        $this->removeFile();
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

    public function getBotIds() {

        $ids = array($this->id);

        $configurationArray = $this->configuration_array;

        if (isset($configurationArray['bot_id']) && !empty($configurationArray['bot_id'])) {
            $ids = array_merge($ids,$configurationArray['bot_id']);
        }

        return $ids;
    }

    public function removeFile()
    {
        if ($this->filename != '') {
            if ( file_exists($this->filepath . $this->filename) ) {
                unlink($this->filepath . $this->filename);
            }

            if ($this->filepath != '') {
                erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/botphoto/',str_replace('var/botphoto/','',$this->filepath));
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
    public $configuration = '';
    public $attr_str_1 = '';
    public $attr_str_2 = '';
    public $attr_str_3 = '';
    public $filepath = '';
    public $filename = '';
}