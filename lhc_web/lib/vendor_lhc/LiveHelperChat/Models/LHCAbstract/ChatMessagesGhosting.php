<?php

namespace LiveHelperChat\Models\LHCAbstract;

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
            'remove' => $this->remove,
            'v_warning' => $this->v_warning
        );

        return $stateArray;
    }

    public static function shouldMask($user_id) {

        $db = \ezcDbInstance::get();

        $stmt = $db->prepare("SELECT count(lh_rolefunction.id)     

       FROM lh_rolefunction
       
       INNER JOIN lh_role ON lh_role.id = lh_rolefunction.role_id
       INNER JOIN lh_grouprole ON lh_role.id = lh_grouprole.role_id
       INNER JOIN lh_groupuser ON lh_groupuser.group_id = lh_grouprole.group_id       
       INNER JOIN lh_group ON lh_grouprole.group_id = lh_group.id
           
       WHERE 
           lh_groupuser.user_id = :user_id AND 
           lh_group.disabled = 0 AND
           (
               (lh_rolefunction.module = '*' AND lh_rolefunction.function = '*') OR 
               (lh_rolefunction.module = 'lhchat' AND lh_rolefunction.function = '*') OR
               (lh_rolefunction.module = 'lhchat' AND lh_rolefunction.function = 'see_sensitive_information')
           )
       ");

        $stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchColumn() == 0; // Assigned operator does not have permission to see sensitive information
    }

    public static function maskVisitorMessages(& $messages) {
        static $maskRules = null;

        if ($maskRules === null) {
            $maskRules = self::getList(['filter' => ['enabled' => 1]]);
        }

        foreach ($messages as & $message) {
            if ($message['user_id'] == 0) {
                foreach ($maskRules as $maskRule) {
                    $msgMasked = $maskRule->getMasked($message['msg']);
                    if ($msgMasked != $message['msg'] && $maskRule->v_warning != '') {
                        $message['msg'] = $message['msg'] .'';

                        $metaMsg = [];
                        if (!empty($message['meta_msg'])) {
                            $metaMsg = json_decode($message['meta_msg'],true);
                        }

                        $metaMsg['content'] = [
                            'text_conditional' => [
                                'msg_body_class' => 'sub-message',
                                'intro_us' => $maskRule->v_warning,
                                'full_us' => '',
                                'readmore_us' => '',
                                'intro_op' => '',
                                'full_op' => '',
                                'readmore_op' => '',
                            ]
                        ];
                        $message['meta_msg'] = json_encode($metaMsg);
                    }
                }
            }
        }


        return $messages;
    }
    
    public static function maskMessage($message)
    {
        static $maskRules = null;

        if ($maskRules === null) {
            $maskRules = self::getList(['filter' => ['enabled' => 1]]);
        }

        foreach ($maskRules as $maskRule) {
            $message = $maskRule->getMasked($message);
        }

        return $message;
    }

    public function getMasked($message)
    {
        $patterns = explode("\n",trim($this->pattern));
        $magoo = new \Pachico\Magoo\Magoo();

        foreach ($patterns as $pattern) {
            $patternParams = explode('|||',trim($pattern));
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
                    $magoo->pushCreditCardMask(trim($patternParams[1]));
                } else {
                    $magoo->pushCreditCardMask();
                }
            } else {
                if (isset($patternParams[1])) {
                    $magoo->pushByRegexMask(trim($patternParams[0]),trim($patternParams[1]));
                } else {
                    $magoo->pushByRegexMask(trim($patternParams[0]));
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
    public $v_warning = '';
    public $enabled = 1;
    public $remove = 0;
}