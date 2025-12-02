<?php

namespace LiveHelperChat\Models\LHCAbstract;
#[\AllowDynamicProperties]
class ChatMessagesGhosting {

    use \erLhcoreClassDBTrait;

    private static $maskRulesByType = [];

    public static $dbTable = 'lh_abstract_msg_protection';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = '`has_dep` DESC, `id` ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'pattern' => $this->pattern,
            'enabled' => $this->enabled,
            'remove' => $this->remove,
            'v_warning' => $this->v_warning,
            'rule_type' => $this->rule_type,
            'has_dep' => $this->has_dep,
            'dep_ids' => $this->dep_ids,
            'name'  => $this->name
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
           lh_rolefunction.type = 0 AND
           (
               (lh_rolefunction.module = '*' AND lh_rolefunction.function = '*') OR 
               (lh_rolefunction.module = 'lhchat' AND lh_rolefunction.function = '*') OR
               (lh_rolefunction.module = 'lhchat' AND lh_rolefunction.function = 'see_sensitive_information')
           ) AND NOT EXISTS (
                SELECT 1
                FROM lh_rolefunction
                INNER JOIN lh_role ON lh_role.id = lh_rolefunction.role_id
                INNER JOIN lh_grouprole ON lh_role.id = lh_grouprole.role_id
                INNER JOIN lh_groupuser ON lh_groupuser.group_id = lh_grouprole.group_id
                INNER JOIN lh_group ON lh_grouprole.group_id = lh_group.id
                WHERE 
                    lh_groupuser.user_id = :user_id_exclude AND
                    lh_group.disabled = 0 AND
                    lh_rolefunction.type = 1 AND
                    lh_rolefunction.module = 'lhchat' AND 
                    lh_rolefunction.function = 'see_sensitive_information'
            )
       ");

        $stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id_exclude', $user_id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchColumn() == 0; // Assigned operator does not have permission to see sensitive information
    }

    public static function maskVisitorMessages(& $messages, \erLhcoreClassModelChat $chat, $type = self::MSG_TYPE_VISITOR_TO_OPERATOR) {
        $user_id = $chat->user_id;
        $dep_id = $chat->dep_id;
        
        // Create a cache key based on type and department
        $cacheKey = $type . '_' . $dep_id;
        
        // Load rule for this type and department if not already loaded
        // Only one rule can be selected at a time (limit = 1)
        // Rules with has_dep = 1 are prioritized first
        if (!isset(self::$maskRulesByType[$cacheKey])) {
            $maskRule = self::findOne([
                'filter' => ['rule_type' => $type, 'enabled' => 1],
                'customfilter' => [
                    "((has_dep = 1 AND dep_ids != '' AND dep_ids != '[]' AND JSON_CONTAINS(dep_ids, '" . (int)$dep_id . "', '$')) OR has_dep = 0)"
                ],
                'sort' => '`has_dep` DESC, `id` ASC'
            ]);

            self::$maskRulesByType[$cacheKey] = $maskRule !== false ? $maskRule : null;
        }

        $maskRule = self::$maskRulesByType[$cacheKey];

        // If no rule exists, return early - no need to check permissions
        if ($maskRule === null) {
            return $messages;
        }

        // Only check permissions if user is assigned and we have a rule to apply
        if ($user_id > 0 && !self::shouldMask($user_id)) {
            return $messages;
        }

        foreach ($messages as & $message) {
            if ($message['user_id'] == 0) {
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


        return $messages;
    }
    
    public static function maskMessage($message, $params = array())
    {
        $dep_id = isset($params['dep_id']) ? (int)$params['dep_id'] : 0;
        $type = isset($params['type']) ? $params['type'] : self::MSG_TYPE_VISITOR_TO_OPERATOR;
        $returnArray = isset($params['return_array']) && $params['return_array'] === true;
        
        // Create a cache key based on type and department
        $cacheKey = 'mask_' . $type . '_' . $dep_id;
        
        // Load rule for this type and department if not already loaded
        // Only one rule can be selected at a time (limit = 1)
        // Rules with has_dep = 1 are prioritized first
        if (!isset(self::$maskRulesByType[$cacheKey])) {
            $maskRule = self::findOne([
                'filter' => ['rule_type' => $type, 'enabled' => 1],
                'customfilter' => [
                    "((has_dep = 1 AND dep_ids != '' AND dep_ids != '[]' AND JSON_CONTAINS(dep_ids, '" . (int)$dep_id . "', '$')) OR has_dep = 0)"
                ],
                'sort' => '`has_dep` DESC, `id` ASC'
            ]);
            
            self::$maskRulesByType[$cacheKey] = $maskRule !== false ? $maskRule : null;
        }

        $maskRule = self::$maskRulesByType[$cacheKey];

        // If no rule exists, return message unchanged
        if ($maskRule === null) {
            if ($returnArray) {
                return ['message' => $message, 'warning' => ''];
            }
            return $message;
        }

        $maskedMessage = $maskRule->getMasked($message);
        
        if ($returnArray) {
            return [
                'message' => $maskedMessage,
                'warning' => ($maskedMessage !== $message) ? $maskRule->v_warning : ''
            ];
        }
        
        return $maskedMessage;
    }

    public function getMasked($message)
    {
        $patterns = trim($this->pattern);

        if (empty($patterns)) {
            return $message;
        }

        $jsonData = json_decode($patterns, true);

        if ($jsonData !== null && json_last_error() === JSON_ERROR_NONE) {
            $urlsRules = [];
            $otherRules = [];
            foreach ($jsonData as $rule) {
                if (isset($rule['type']) && $rule['type'] == 'urls') {
                    $urlsRules[] = $rule;
                } else {
                    $otherRules[] = $rule;
                }
            }
            $jsonData = array_merge($urlsRules, $otherRules);

            $magoo = null;
            foreach ($jsonData as $rule) {
                if ($rule['type'] == 'regex' || $rule['type'] == 'email' || $rule['type'] == 'credit_card') {
                    if ($magoo === null) {
                        $magoo = new \Pachico\Magoo\Magoo();
                    }
                    if ($rule['type'] == 'email') {
                        if (isset($rule['replacement']) && isset($rule['replacement_domain'])) {
                            $magoo->pushEmailMask($rule['replacement'], $rule['replacement_domain']);
                        } elseif (isset($rule['replacement'])) {
                            $magoo->pushEmailMask($rule['replacement']);
                        } else {
                            $magoo->pushEmailMask();
                        }
                    } elseif ($rule['type'] == 'credit_card') {
                        $magoo->pushCreditCardMask(isset($rule['replacement']) ? $rule['replacement'] : '*');
                    } elseif (isset($rule['pattern']) && !empty($rule['pattern'])) {
                        // Legacy support for old format
                        if ($rule['pattern'] == '__email__') {
                            if (isset($rule['replacement']) && isset($rule['replacement_domain'])) {
                                $magoo->pushEmailMask($rule['replacement'], $rule['replacement_domain']);
                            } elseif (isset($rule['replacement'])) {
                                $magoo->pushEmailMask($rule['replacement']);
                            } else {
                                $magoo->pushEmailMask();
                            }
                        } elseif ($rule['pattern'] == '__credit_card__') {
                            $magoo->pushCreditCardMask(isset($rule['replacement']) ? $rule['replacement'] : '*');
                        } else {
                            // If replacement is empty, use custom regex check to return [mask]REGEX:Name[/mask]
                            if (!isset($rule['replacement']) || $rule['replacement'] === '') {
                                if ($magoo !== null) {
                                    $message = $magoo->getMasked($message);
                                    $magoo = null;
                                }
                                $regexName = isset($rule['name']) && $rule['name'] !== '' ? $rule['name'] : 'Pattern';
                                $result = \LiveHelperChat\Validators\Guardrails\PII::checkCustomRegex($message, [
                                    'customRegex' => [['name' => 'REGEX:' . $regexName, 'value' => $rule['pattern']]]
                                ]);
                                if ($result['tripwireTriggered'] === true && isset($result['info']['maskEntities'])) {
                                    foreach ($result['info']['maskEntities'] as $entity => $matches) {
                                        foreach ($matches as $match) {
                                            $message = str_replace($match, '[mask]'.$entity.'[/mask]', $message);
                                        }
                                    }
                                }
                                $magoo = new \Pachico\Magoo\Magoo();
                            } else {
                                $magoo->pushByRegexMask($rule['pattern'], $rule['replacement']);
                            }
                        }
                    }
                } else {
                    if ($magoo !== null) {
                        $message = $magoo->getMasked($message);
                        $magoo = null;
                    }
                    if ($rule['type'] == 'pii') {
                        $result = \LiveHelperChat\Validators\Guardrails\PII::check($message, $rule);
                        if ($result['tripwireTriggered'] === true && isset($result['info']['maskEntities'])) {
                            $replacement = isset($rule['replacement']) && $rule['replacement'] !== '' ? $rule['replacement'] : null;
                            foreach ($result['info']['maskEntities'] as $entity => $matches) {
                                foreach ($matches as $match) {
                                    if ($replacement !== null) {
                                        $message = str_replace($match, str_repeat($replacement, mb_strlen($match)), $message);
                                    } else {
                                        $message = str_replace($match, '[mask]'.$entity.'[/mask]', $message);
                                    }
                                }
                            }
                        }
                    } elseif ($rule['type'] == 'secret_keys') {
                        $result = \LiveHelperChat\Validators\Guardrails\SecretKeys::check($message, $rule);
                        if ($result['tripwireTriggered'] === true && isset($result['info']['maskEntities'])) {
                            $replacement = isset($rule['replacement']) && $rule['replacement'] !== '' ? $rule['replacement'] : null;
                            foreach ($result['info']['maskEntities'] as $entity => $matches) {
                                foreach ($matches as $match) {
                                    if ($replacement !== null) {
                                        $message = str_replace($match, str_repeat($replacement, mb_strlen($match)), $message);
                                    } else {
                                        $message = str_replace($match, '[mask]'.$entity.'[/mask]', $message);
                                    }
                                }
                            }
                        }
                    } elseif ($rule['type'] == 'urls') {
                        $result = \LiveHelperChat\Validators\Guardrails\URLs::urls($message, $rule);
                        if ($result['tripwireTriggered'] === true && isset($result['info']['blocked'])) {                        
                            $replacement = isset($rule['replacement']) && $rule['replacement'] !== '' ? $rule['replacement'] : null;
                            foreach ($result['info']['blocked'] as $indexURL => $url) {
                                if ($replacement !== null) {
                                    $message = str_replace($url, str_repeat($replacement, mb_strlen($url)), $message);
                                } else {
                                    $message = str_replace($url, '[mask]'.($result['info']['blockedReasons'][$indexURL] ?? 'URL').'[/mask]', $message);
                                }
                            }
                        }
                    }
                }
            }
            if ($magoo !== null) {
                $message = $magoo->getMasked($message);
            }
        } else {
            $patterns = explode("\n", $patterns);
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

        return $message;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelchatmessagesghosting.php');
    }

    public function dependFooterJs()
    {
        return '<script type="module" src="'. \erLhcoreClassDesign::designJSStatic('js/svelte/public/build/departments.js').'"></script><script type="module" src="'. \erLhcoreClassDesign::designJSStatic('js/svelte/public/build/masking.js').'"></script>';
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
                'function' => 'messagecontentprotection'
            ),
            'permission' => array(
                'module' => 'lhsystem',
                'function' => 'messagecontentprotection'
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

    public function beforeUpdate()
    {
        $this->has_dep = $this->dep_ids == '' || $this->dep_ids == '[]' ? 0 : 1;    
    }

    const MSG_TYPE_VISITOR_TO_OPERATOR = 0;
    const MSG_TYPE_OPERATOR_TO_VISITOR = 1;
    const MSG_TYPE_REST = 2;

    public $id = null;
    public $pattern = '';
    public $v_warning = '';
    public $enabled = 1;
    public $remove = 0;
    public $rule_type = self::MSG_TYPE_VISITOR_TO_OPERATOR;
    public $has_dep = 0;
    public $dep_ids = '';
    public $name = '';
}