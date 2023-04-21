<?php

namespace LiveHelperChat\Models\Abstract;

class ChatMessagesGhosting {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_msg_protection';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'pattern' => $this->pattern,
            'enabled' => $this->enabled,
            'remove' => $this->remove
        );

        return $stateArray;
    }

    public function getMasked($message)
    {
        $patterns = explode("\n",trim($this->pattern));
        $magoo = new \Pachico\Magoo\Magoo();

        foreach ($patterns as $pattern) {
            $patternParams = explode('|||',$pattern);
            if ($patternParams[0] === '__email__') {
                if (isset($patternParams[1]) && isset($patternParams[2])) {
                    $magoo->pushEmailMask(trim($patternParams[1]), trim($patternParams[2]));
                } elseif (isset($patternParams[1])) {
                    $magoo->pushEmailMask(trim($patternParams[1]));
                } else {
                    $magoo->pushEmailMask();
                }
            } else if ($patternParams[0] === '__credit_card__') {
                if (isset($patternParams[1])) {
                    $magoo->pushCreditCardMask($patternParams[1]);
                } else {
                    $magoo->pushCreditCardMask();
                }
            } else {
                if (isset($patternParams[1])) {
                    $magoo->pushByRegexMask($patternParams[0],$patternParams[1]);
                } else {
                    $magoo->pushByRegexMask($patternParams[0]);
                }
            }
        }

        return $magoo->getMasked($message);
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelchatmessagesghosting.php');
    }

    public function getModuleTranslations()
    {
        /**
         * Get's executed before permissions check.
         * It can redirect to frontpage throw permission exception etc
         */
        $metaData = array(
            'permission_delete' => array(
                'module' => 'lhsystem',
                'function' => 'auditlog'
            ),
            'permission' => array(
                'module' => 'lhsystem',
                'function' => 'auditlog'
            ),
            'table_class' => 'table-condensed table-small',
            'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Messages content protection')
        );

        return $metaData;
    }

    public function __toString()
    {
        return $this->pattern;
    }

    public function customForm()
    {
        return 'message_content_protection.tpl.php';
    }

    public function __get($var)
    {
        switch ($var) {

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $pattern = '';
    public $enabled = 1;
    public $remove = 0;
}