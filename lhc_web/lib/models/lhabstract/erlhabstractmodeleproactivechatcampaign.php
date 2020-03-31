<?php

class erLhAbstractModelProactiveChatCampaign
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_campaign';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodeleproactivechatcampaign.php');
    }

    public function getModuleTranslations()
    {
        /**
         * Get's executed before permissions check.
         * It can redirect to frontpage throw permission exception etc
         */
        $metaData = array(
            'permission_delete' => array(
                'module' => 'lhchat',
                'function' => 'administratecampaigs'
            ),
            'permission' => array(
                'module' => 'lhchat',
                'function' => 'administratecampaigs'
            ),
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Pro active chat campaigns')
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_proactive', array(
            'object_meta_data' => & $metaData
        ));

        return $metaData;
    }

    public function __get($var)
    {
        switch ($var) {


            default:
                break;
        }
    }

    public $id = null;

    public $name = '';

    public $text = '';

    public $hide_add = false;

    public $hide_delete = false;
}

?>