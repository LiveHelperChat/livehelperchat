<?php
/**
 *
 * @author Remigijus Kiminas
 *
 * @desc Rest API Key
 *
 */
class erLhAbstractModelRestAPIKeyRemote {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_rest_api_key_remote';
    public static $dbTableId = 'id';
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';
    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array (
            'id'         => $this->id,
            'host'  	 => $this->host,
            'name'  	 => $this->name,
            'username'   => $this->username,
            'position'   => $this->position,
            'api_key'	 => $this->api_key,
            'active'     => $this->active
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->api_key;
    }

    public function getModuleTranslations()
    {
        $metaData = array('path' => array('url' => erLhcoreClassDesign::baseurl('restapi/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Rest API')), 'permission_delete' => array('module' => 'lhrestapi','function' => 'use_admin'), 'permission' => array('module' => 'lhrestapi','function' => 'use_admin'), 'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/product','Rest API Remote Keys'));
        /**
         * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
         * */
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_product', array('object_meta_data' => & $metaData));

        return $metaData;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'left_menu':
                $this->left_menu = '';
                return $this->left_menu;
                break;

            default:
                break;
        }
    }

    public $id = null;
    public $host = '';
    public $api_key = '';
    public $name = '';
    public $username = '';
    public $position = 0;
    public $active = 0;

    public $hide_add = false;
    public $hide_delete = false;
}
