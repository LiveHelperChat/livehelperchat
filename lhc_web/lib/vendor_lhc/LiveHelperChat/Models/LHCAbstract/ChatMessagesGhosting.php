<?php

namespace LiveHelperChat\Models\LHCAbstract;
#[\AllowDynamicProperties]
class ChatMessagesGhosting {

    use \erLhcoreClassDBTrait;

    private static $maskRulesByType = [];

    /**
     * Entities matched by the last HTML masking call, e.g. EMAIL_ADDRESS or URL_NOT_IN_ALLOW_LIST
     */
    private static $matchedEntities = [];

    /**
     * Returns entities masked by the last maskMessage()/maskMessageHtml() call
     */
    public static function getMatchedEntities()
    {
        return array_values(array_unique(self::$matchedEntities));
    }

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
            'name'  => $this->name,
            'languages' => $this->languages,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'muser_id' => $this->muser_id,
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
        $locale = isset($chat->chat_locale) ? $chat->chat_locale : '';
        
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

        if ($locale != '') {
            $maskRule = clone $maskRule;
            $maskRule->translateByChat($locale);
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
        self::$matchedEntities = [];

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

        $maskedMessage = !empty($params['is_html'])
            ? self::maskHtmlWithRule($message, $maskRule)
            : $maskRule->getMasked($message);
        
        if ($returnArray) {
            return [
                'message' => $maskedMessage,
                'warning' => ($maskedMessage !== $message) ? $maskRule->v_warning : ''
            ];
        }
        
        return $maskedMessage;
    }

    /**
     * Masks HTML content, e.g. rendered mail bodies.
     *
     * Mask rules are applied only to text outside tags. When a rule matches an
     * image source or a link target, the whole attribute is neutralized instead
     * of inserting [mask]...[/mask] markers into the markup, which would break
     * the HTML. Remaining markers in visible text are rendered as text.
     */
    public static function maskMessageHtml($message, $params = array())
    {
        $params['is_html'] = true;

        return self::maskMessage($message, $params);
    }

    /**
     * Applies a mask rule to HTML while keeping the markup intact.
     * Text nodes are masked, tags are not touched except for img src and a href
     * attributes which are neutralized when the rule matches their value.
     */
    private static function maskHtmlWithRule($html, $maskRule)
    {
        if (!is_string($html) || $html === '') {
            return $html;
        }

        $parts = preg_split('~(<(?:"[^"]*"|\'[^\']*\'|[^\'">])*>)~i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return $html;
        }

        $maskedHtml = '';

        foreach ($parts as $index => $part) {
            $maskedHtml .= $index % 2 === 1
                ? self::maskHtmlTag($part, $maskRule)
                : self::maskHtmlText($part, $maskRule);
        }

        return $maskedHtml;
    }

    /**
     * Masks a single HTML tag. Only img src and a href are neutralized, all
     * other tags and attributes are returned untouched.
     */
    private static function maskHtmlTag($tag, $maskRule)
    {
        if (!preg_match('~^<\s*(img|a)\b~i', $tag, $tagMatches)) {
            return $tag;
        }

        $attribute = strtolower($tagMatches[1]) === 'img' ? 'src' : 'href';
        $attributePattern = '~\b' . $attribute . '\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i';

        if (!preg_match($attributePattern, $tag, $valueMatches)) {
            return $tag;
        }

        $value = '';
        foreach (array(1, 2, 3) as $index) {
            if (isset($valueMatches[$index]) && $valueMatches[$index] !== '') {
                $value = $valueMatches[$index];
                break;
            }
        }

        $maskedValue = $maskRule->getMasked($value);

        if ($maskedValue === $value) {
            return $tag;
        }

        // Remember what was masked, markers carry the entity name
        self::$matchedEntities[] = preg_match('~\[mask\](.*?)\[\\\\?/mask\]~s', $maskedValue, $entityMatch) ? $entityMatch[1] : 'URL';

        if ($attribute === 'src') {
            $replacement = 'src="' . htmlspecialchars(\erLhcoreClassDesign::design('images/general/denied.png'), ENT_QUOTES, 'UTF-8') . '"';
        } else {
            $reason = preg_match('~\[mask\](.*?)\[\\\\?/mask\]~s', $maskedValue, $reasonMatches) ? html_entity_decode($reasonMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'URL';
            $url = \erLhcoreClassDesign::baseurl('mailconv/maskedlink') . '?reason=' . rawurlencode($reason);
            $replacement = 'href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" data-lhc-masked-link="1"';
        }

        return preg_replace_callback('~\b' . $attribute . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)~i', function () use ($replacement) {
            return $replacement;
        }, $tag, 1);
    }

    /**
     * Masks a single HTML text node and renders markers left in the text.
     */
    private static function maskHtmlText($text, $maskRule)
    {
        if ($text === '') {
            return $text;
        }

        $text = $maskRule->getMasked($text);

        // Remember what was masked, markers carry the entity name
        if (preg_match_all('~\[mask\]([^<]*?)\[\\\\?/mask\]~s', $text, $entityMatches)) {
            foreach ($entityMatches[1] as $entity) {
                self::$matchedEntities[] = $entity;
            }
        }

        return preg_replace_callback('~\[mask\]([^<]*?)\[\\\\?/mask\]~s', function ($matches) {
            return \LiveHelperChat\Helpers\BBCode\Mask::render($matches[1]);
        }, $text);
    }

    /**
     * Masks a rendered HTML body (body_front) of a mail message.
     *
     * Message type is detected from response_type, so operator replies and
     * visitor messages are masked with their respective mask rules.
     */
    public static function maskBodyFront($message, $dep_id = null)
    {
        $maskParams = [
            'dep_id' => $dep_id !== null ? (int)$dep_id : (isset($message->dep_id) ? (int)$message->dep_id : 0),
            'type' => (isset($message->response_type) && $message->response_type == \erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL)
                ? self::MSG_TYPE_OPERATOR_EMAIL
                : self::MSG_TYPE_VISITOR_EMAIL,
        ];

        $message->body_front = self::maskMessageHtml($message->body_front, $maskParams);

        return $message->body_front;
    }

    /**
     * Neutralizes masked markup inside already rendered HTML.
     *
     * Masked images get a placeholder source, masked links point to the
     * explanation page and the remaining [mask]...[/mask] markers are rendered
     * as text, so the masked values cannot be recovered from the HTML.
     */
    public static function maskHtml($message)
    {
        if (!is_string($message) || strpos($message, '[mask]') === false) {
            return $message;
        }

        // Masked image sources are replaced with a placeholder image
        $message = preg_replace_callback('~<img\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*>~i', function ($image) {
            return preg_replace_callback('~(?:"[^"]*"|\'[^\']*\')(*SKIP)(*F)|(\s+src\s*=\s*)(?:"\[mask\][^"]*"|\'\[mask\][^\']*\'|\[mask\][^\s>]*)~i', function ($source) {
                return $source[1] . '"' . htmlspecialchars(\erLhcoreClassDesign::design('images/general/denied.png'), ENT_QUOTES, 'UTF-8') . '"';
            }, $image[0]);
        }, $message);

        // Masked links point to an explanation page instead of the original URL
        $message = preg_replace_callback('~<a\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*>~i', function ($link) {
            return preg_replace_callback('~(?:"[^"]*"|\'[^\']*\')(*SKIP)(*F)|(\s+href\s*=\s*)(?:"(\[mask\][^"]*)"|\'(\[mask\][^\']*)\'|(\[mask\][^\s>]*))~i', function ($href) {
                $value = $href[2] ?: ($href[3] ?: $href[4]);
                $reason = preg_match('~^\[mask\](.*?)\[/mask\]~s', $value, $match) ? html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'URL';
                $url = \erLhcoreClassDesign::baseurl('mailconv/maskedlink') . '?reason=' . rawurlencode($reason);
                return $href[1] . '"' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" data-lhc-masked-link="1"';
            }, $link[0]);
        }, $message);

        // Remaining mask markers in the text itself
        $message = preg_replace_callback('~<(?:"[^"]*"|\'[^\']*\'|[^\'">])*> (*SKIP)(*F)|\[mask\]([^<]*?)\[/mask\]~sx', function ($matches) {
            return \LiveHelperChat\Helpers\BBCode\Mask::render($matches[1]);
        }, $message);

        return $message;
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
        return '<script type="module" src="'. \erLhcoreClassDesign::designJSStatic('js/svelte/public/build/departments.js').'"></script><script type="module" src="' . \erLhcoreClassDesign::designJSStatic('js/svelte/public/build/masking.js').'"></script><script type="module" src="' . \erLhcoreClassDesign::designJSStatic('js/svelte/public/build/languages.js').'"></script>';
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

            case 'material_icon':
                switch ($this->rule_type) {
                    case self::MSG_TYPE_OPERATOR_TO_VISITOR:
                        return 'support_agent';

                    case self::MSG_TYPE_REST:
                        return 'api';

                    case self::MSG_TYPE_VISITOR_EMAIL:
                        return 'mail';

                    case self::MSG_TYPE_OPERATOR_EMAIL:
                        return 'forward_to_inbox';

                    case self::MSG_TYPE_VISITOR_TO_OPERATOR:
                    default:
                        return 'person';
                }

            default:
                ;
                break;
        }
    }

    public function setTranslationData($data)
    {
        if (isset($data['message']) && $data['message'] != '') {
            $this->v_warning = $data['message'];
        }
    }

    public function translateByChat($locale)
    {
        if ($locale == '' || $this->languages == '') {
            return;
        }

        $languages = json_decode($this->languages, true);

        if (!is_array($languages)) {
            return;
        }

        foreach ($languages as $data) {
            if (isset($data['languages']) && is_array($data['languages']) && in_array($locale, $data['languages'])) {
                $this->setTranslationData($data);
                return;
            }
        }

        $localeShort = explode('-', $locale)[0];
        foreach ($languages as $data) {
            if (isset($data['languages']) && is_array($data['languages']) && in_array($localeShort, $data['languages'])) {
                $this->setTranslationData($data);
                return;
            }
        }
    }

    public function validateInput($params = array())
    {
        $definition = array(
            'languages' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY),
            'message_lang' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY),
        );

        $form = new \ezcInputForm(INPUT_POST, $definition);

        $languagesData = [];

        if ($form->hasValidData('languages') && !empty($form->languages)) {
            foreach ($form->languages as $index => $languages) {
                $languagesData[] = [
                    'message' => isset($form->message_lang[$index]) ? $form->message_lang[$index] : '',
                    'languages' => $languages,
                ];
            }
        }

        $this->languages = json_encode($languagesData, JSON_HEX_APOS);
        $this->languages_array = $languagesData;
    }

    public function beforeSave()
    {
        $this->has_dep = $this->dep_ids == '' || $this->dep_ids == '[]' ? 0 : 1;
    }

    public function beforeUpdate()
    {
        $this->has_dep = $this->dep_ids == '' || $this->dep_ids == '[]' ? 0 : 1;    
    }

    const MSG_TYPE_VISITOR_TO_OPERATOR = 0;
    const MSG_TYPE_OPERATOR_TO_VISITOR = 1;
    const MSG_TYPE_REST = 2;
    const MSG_TYPE_VISITOR_EMAIL = 3;
    const MSG_TYPE_OPERATOR_EMAIL = 4;

    public $id = null;
    public $pattern = '';
    public $v_warning = '';
    public $enabled = 1;
    public $remove = 0;
    public $rule_type = self::MSG_TYPE_VISITOR_TO_OPERATOR;
    public $has_dep = 0;
    public $dep_ids = '';
    public $name = '';
    public $languages = '';

    public $created_at = 0;
    public $updated_at = 0;
    public $muser_id = 0;
}