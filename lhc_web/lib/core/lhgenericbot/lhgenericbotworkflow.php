<?php

class erLhcoreClassGenericBotWorkflow {

    public static $startChat = false;
    
    public static function findEvent($text, $botId, $type = 0, $paramsFilter = array(), $paramsExecution = array())
    {
        $bot = erLhcoreClassModelGenericBotBot::fetch($botId);

        if (!($bot instanceof erLhcoreClassModelGenericBotBot)) {
            return null;
        }

        // Avoid double execution
        if (self::$currentAlwaysEvent !== null) {
            $paramsFilter['filternot']['id'] = self::$currentAlwaysEvent->id;
        }

        $events = erLhcoreClassModelGenericBotTriggerEvent::getList(array_merge_recursive(array('sort' => 'priority ASC', 'filterin' => array('bot_id' => $bot->getBotIds()),'filter' => array('type' => $type),'filterlikeright' => array('pattern' => $text)),$paramsFilter));

        foreach ($events as $event) {
            $configurationMatching = $event->configuration_array;

            if (!is_array($configurationMatching)) {
                $configurationMatching = array();
            }

            $wordsFound = true;

            if (isset($paramsExecution['dep_id']) && is_numeric($paramsExecution['dep_id']) && isset($configurationMatching['dep_inc']) && !empty($configurationMatching['dep_inc'])) {
                $depIds = explode(',', str_replace(' ', '', $configurationMatching['dep_inc']));
                if (!in_array($paramsExecution['dep_id'], $depIds)) {
                    $wordsFound = false;
                }
            }

            if ($wordsFound == true && isset($paramsExecution['dep_id']) && is_numeric($paramsExecution['dep_id']) && isset($configurationMatching['dep_exc']) && !empty($configurationMatching['dep_exc'])) {
                $depIds = explode(',', str_replace(' ', '', $configurationMatching['dep_exc']));
                if (in_array($paramsExecution['dep_id'], $depIds)) {
                    $wordsFound = false;
                }
            }

            if ($wordsFound == true) {
                return $event;
            }
        }

        return null;
    }

    public static function findTextMatchingEvent($messageText, $botId, $paramsFilter = array(), $paramsExecution = array())
    {
        $bot = erLhcoreClassModelGenericBotBot::fetch($botId);

        if (!($bot instanceof erLhcoreClassModelGenericBotBot)) {
            return null;
        }

        // Avoid double execution
        if (self::$currentAlwaysEvent !== null) {
            $paramsFilter['filternot']['id'] = self::$currentAlwaysEvent->id;
        }

        $rulesMatching = erLhcoreClassModelGenericBotTriggerEvent::getList(array_merge_recursive(array('sort' => 'priority ASC', 'filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => array('type' => 2)), $paramsFilter));

        // We want always want to return trigger with lowest number of typos
        $validRules = []; // trigger id => typos detected

        foreach ($rulesMatching as $ruleMatching) {

            if ($ruleMatching->pattern != '') {

                $configurationMatching = $ruleMatching->configuration_array;

                if (!is_array($configurationMatching)) {
                    $configurationMatching = array();
                }

                $wordsFound = true;

                if (isset($paramsExecution['dep_id']) && is_numeric($paramsExecution['dep_id']) && isset($configurationMatching['dep_inc']) && !empty($configurationMatching['dep_inc'])) {
                    $depIds = explode(',', str_replace(' ', '', $configurationMatching['dep_inc']));
                    if (!in_array($paramsExecution['dep_id'], $depIds)) {
                        $wordsFound = false;
                    }
                }

                if ($wordsFound == true && isset($paramsExecution['dep_id']) && is_numeric($paramsExecution['dep_id']) && isset($configurationMatching['dep_exc']) && !empty($configurationMatching['dep_exc'])) {
                    $depIds = explode(',', str_replace(' ', '', $configurationMatching['dep_exc']));
                    if (in_array($paramsExecution['dep_id'], $depIds)) {
                        $wordsFound = false;
                    }
                }

                $wordsTypo = isset($configurationMatching['words_typo']) && is_numeric($configurationMatching['words_typo']) ? (int)$configurationMatching['words_typo'] : 0;
                $wordsTypoExc = isset($configurationMatching['exc_words_typo']) && is_numeric($configurationMatching['exc_words_typo']) ? (int)$configurationMatching['exc_words_typo'] : 0;

                $typosUsed = 0;

                // // We should include at-least one word from group
                if ($wordsFound == true && isset($configurationMatching['only_these']) && $configurationMatching['only_these'] == true) {
                    $words = explode(' ', $messageText);
                    $mustCombinations = explode('&&', $ruleMatching->pattern);
                    foreach ($words as $messageWord) {
                        foreach ($mustCombinations as $mustCombination) {
                            $presenceData = self::checkPresence(explode(',', $mustCombination), $messageWord, $wordsTypo, ['stats' => true]);
                            if (!$presenceData['valid']) {
                                $wordsFound = false;
                                break;
                            } else {
                                $typosUsed += $presenceData['number'];
                            }
                        }
                    }
                } else if (isset($ruleMatching->pattern) && $ruleMatching->pattern != '') {
                    $mustCombinations = explode('&&', $ruleMatching->pattern);
                    foreach ($mustCombinations as $mustCombination) {
                        $presenceData = self::checkPresence(explode(',', $mustCombination), $messageText, $wordsTypo, ['stats' => true]);
                        if (!$presenceData['valid']) {
                            $wordsFound = false;
                            break;
                        } else {
                            $typosUsed += $presenceData['number'];
                        }
                    }
                }

                // We should NOT include any of these words
                if ($wordsFound == true && isset($ruleMatching->pattern_exc) && $ruleMatching->pattern_exc != '') {
                    $mustCombinations = explode('&&', $ruleMatching->pattern_exc);
                    foreach ($mustCombinations as $mustCombination) {
                        $presenceData = self::checkPresence(explode(',', $mustCombination), $messageText, $wordsTypoExc, ['stats' => true]);
                        if ($presenceData['valid'] == true) {
                            $wordsFound = false;
                            break;
                        }
                    }
                }

                if ($wordsFound == true) {
                    $validRules[$ruleMatching->id] = $typosUsed;
                }
            }
        }

        if (!empty($validRules)) {
            // Sort to get matching rules with lowest typo number
            asort($validRules);

            // Flip id with values
            $flipped = array_keys($validRules);

            // Take ID
            $item = array_shift($flipped);

            // return element by id
            return $rulesMatching[$item];
        }
    }

    public static $currentEvent = null;

    public static $currentAlwaysEvent = null;

    public static function userMessageAdded(& $chat, $msg) {

        // Execute rest workflow if chat is in full bot mode
        if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT)
        {
            $response = self::sendAlwaysDefault($chat, $chat->gbot_id, $msg);

            if ($response === true) {

                $chatEvent = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $chat->id)));
                if ($chatEvent instanceof erLhcoreClassModelGenericBotChatEvent) {
                    $chatEvent->removeThis();
                }
                return;
            }
        }

        // Try to find current callback handler just
        $chatEvent = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $chat->id)));
        if ($chatEvent instanceof erLhcoreClassModelGenericBotChatEvent) {
            self::$currentEvent = $chatEvent;
            self::processEvent($chatEvent, $chat, array('msg' => $msg));
            return;
        }

        // Try to find current workflow
        $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
        if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
            self::processWorkflow($workflow, $chat, array('msg' => $msg));
            return;
        }

        // Execute rest workflow if chat is in full bot mode
        if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT)
        {
            // There is no current workflow in progress
            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_message', array(
                'chat' => & $chat,
                'msg' => $msg,
                'payload' => $msg->msg,
            ));

            if ($handler !== false) {
                $event = $handler['event'];
            } else {
                // There is no current workflow in progress
                $event = self::findEvent($msg->msg, $chat->gbot_id, 0, array(), array('dep_id' => $chat->dep_id));
            }

            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)){
                $event = self::findTextMatchingEvent($msg->msg, $chat->gbot_id, array(), array('dep_id' => $chat->dep_id));
            }

            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                $responseTrigger = self::processTrigger($chat, $event->trigger, false, array('args' => array('msg' => $msg)));
                if (!is_array($responseTrigger) || !isset($responseTrigger['ignore_trigger']) || $responseTrigger['ignore_trigger'] === false) {
                    return;
                }
            }

            self::sendDefault($chat, $chat->gbot_id, $msg);
        }
    }

    public static function getDefaultNick($chat)
    {
        $nameSupport = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');

        if ($chat->gbot_id > 0) {

            $department = $chat->department;
            $nameSet = false;
            if ($department !== false) {
                $configuration = $department->bot_configuration_array;
                if (isset($configuration['bot_tr_id']) && $configuration['bot_tr_id'] > 0) {
                    $trGroup = erLhcoreClassModelGenericBotTrGroup::fetch($configuration['bot_tr_id']);
                    if ($trGroup instanceof erLhcoreClassModelGenericBotTrGroup && $trGroup->nick != '') {
                        $nameSupport = $trGroup->nick;
                        $nameSet = true;
                    }
                }
            }

            if ($nameSet == false) {
                $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);
                if ($bot instanceof erLhcoreClassModelGenericBotBot && $bot->nick != '') {
                    $nameSupport = $bot->nick;
                }
            }
        }

        return $nameSupport;
    }

    /**
     *
     * @desc Overrides bot frontend attributes by chat.
     *
     * @param $chat
     * @param $bot
     */
    public static function setDefaultPhotoNick($chat, $bot)
    {
        $department = $chat->department;
        if ($department !== false) {
            $configuration = $department->bot_configuration_array;
            if (isset($configuration['bot_tr_id']) && $configuration['bot_tr_id'] > 0) {
                $trGroup = erLhcoreClassModelGenericBotTrGroup::fetch($configuration['bot_tr_id']);
                if ($trGroup instanceof erLhcoreClassModelGenericBotTrGroup) {

                    if ($trGroup->nick != '') {
                        $bot->name_support = $trGroup->nick;
                    }

                    if ($trGroup->has_photo == true) {
                        $bot->has_photo = true;
                        $bot->photo_path = $trGroup->photo_path;
                    }
                }
            }
        }
    }

    // Send default message if there is any
    public static function sendDefault(& $chat, $botId, $msg = null)
    {
        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_default_message', array(
            'chat' => & $chat,
            'bot_id' => $botId
        ));

        if ($handler !== false) {
            $trigger = $handler['trigger'];
        } else {
            $bot = erLhcoreClassModelGenericBotBot::fetch($botId);
            $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => array('default_unknown' => 1)));
        }

        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
            $message = erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, false, array('args' => array('msg' => $msg)));

            if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                self::setLastMessageId($chat, $message->id, true);
            }
        }
    }

    // Send default always message if there is any
    public static function sendAlwaysDefault(& $chat, $botId, $msg = null)
    {
        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_send_always', array(
            'chat' => & $chat,
            'bot_id' => $botId,
            'msg' => $msg
        ));

        if ($handler !== false) {
            return;
        }

        $bot = erLhcoreClassModelGenericBotBot::fetch($botId);

        $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => array('default_always' => 1)));

        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {

            $event = self::findTextMatchingEvent($msg->msg, $chat->gbot_id, array('filter' => array('trigger_id' => $trigger->id)), array('dep_id' => $chat->dep_id));

            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)){
                $event = self::findEvent($msg->msg, $chat->gbot_id, 0, array('filter' => array('trigger_id' => $trigger->id)), array('dep_id' => $chat->dep_id));
            }

            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                
                self::processTrigger($chat, $event->trigger, true, array('args' => array('msg' => $msg)));

                self::$currentAlwaysEvent = $event;

                if ($event->on_start_type == 2) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    public static function getWordParams($word){
        $matches = array();
        preg_match('/\{[0-9]\}/',$word,$matches);

        $numberTypos = null;
        foreach ($matches as $match) {
            $numberTypos = str_replace(array('{','}'),'',$match);
            $word = str_replace($match,'',$word);
        }

        $noEndTypo = false;
        if (preg_match('/\$$/is',$word)) {
            $noEndTypo = true;
            $word = str_replace('$','',$word);
        }

        return array('typos' => $numberTypos, 'word' => $word, 'noendtypo' => $noEndTypo);
    }

    public static function checkPresence($words, $text, $mistypeLetters = 1, $paramsExecution = []) {

        $textLetters = self::splitWord($text);

        foreach ($words as $word) {

            $word = trim($word);
            
            if ($word == '') {
                continue;
            }

            $wordSettings = self::getWordParams(trim($word));

            $wordLetters = self::splitWord($wordSettings['word']);
            $currentWordLetterIndex = 0;
            $mistypedCount = 0;

            // allow word to have custom mistype number configuration
            $mistypeLettersWord = $wordSettings['typos'] !== null ? $wordSettings['typos'] : $mistypeLetters;

            foreach ($textLetters as $indexLetter => $letter) {

                $lastLetterMatch = true;
                if ($letter != $wordLetters[$currentWordLetterIndex]) {
                    $mistypedCount++;
                    $lastLetterMatch = false;
                }

                // We do not allow first letter to be mistaken
                if ($mistypedCount > $mistypeLettersWord || $mistypedCount == 1 && $currentWordLetterIndex == 0) {
                    $currentWordLetterIndex = 0;
                    $mistypedCount = 0;
                } else {
                    // It has to be not the end of previous word
                    if ($currentWordLetterIndex > 0 || $mistypedCount == 0 && $currentWordLetterIndex == 0 && !isset($textLetters[$indexLetter-1]) || in_array($textLetters[$indexLetter-1],array('"',',',' ',"'",':','.','?','!'))){
                        $currentWordLetterIndex++;
                    } else {
                        $currentWordLetterIndex = 0;
                        $mistypedCount = 0;
                    }
                }

                if (count($wordLetters) == $currentWordLetterIndex) {
                    if (!isset($textLetters[$indexLetter+1]) || in_array($textLetters[$indexLetter+1],array('"',',',' ',"'",':','.','?','!'))){

                        if ($wordSettings['noendtypo'] == true && $lastLetterMatch == false) {
                            $currentWordLetterIndex = 0;
                            $mistypedCount = 0;
                        } else {
                            if (isset($paramsExecution['stats']) && $paramsExecution['stats'] == true) {
                                return ['valid' => true, 'number' => $mistypedCount];
                            } else {
                                return true;
                            }
                        }
                    } else {
                        $currentWordLetterIndex = 0;
                        $mistypedCount = 0;
                    }
                }
            }
        }

        if (isset($paramsExecution['stats']) && $paramsExecution['stats'] == true) {
            return ['valid' => false];
        } else {
            return false;
        }
    }

    public static function splitWord($word){
        $word = str_replace(array("\r\n","\n")," ",$word);
        $len = mb_strlen($word, 'UTF-8');
        $result = [];
        for ($i = 0; $i < $len; $i++) {
            $result[] = mb_strtolower(mb_substr($word, $i, 1, 'UTF-8'));
        }
        return $result;
    }

    public static function processEvent($chatEvent, & $chat, $params = array()) {

        if (isset($params['msg'])) {
            $payload = $params['msg']->msg;
        } else {
            $payload = $params['payload'];
        }

        $db = ezcDbInstance::get();

        try {

            // Cancel workflow
            if ($payload == 'cancel_workflow') {

                $chatEvent->removeThis();

                if (isset($chatEvent->content_array['callback_list'][0]['content']['attr_options']['collection_callback_cancel']) && is_numeric($chatEvent->content_array['callback_list'][0]['content']['attr_options']['collection_callback_cancel'])) {
                       $trigger = erLhcoreClassModelGenericBotTrigger::fetch($chatEvent->content_array['callback_list'][0]['content']['attr_options']['collection_callback_cancel']);
                        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                            $paramsTrigger = array();
                            erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $paramsTrigger);
                        }
                } elseif (isset($chatEvent->content_array['callback_list'][0]['content']['cancel_message']) && $chatEvent->content_array['callback_list'][0]['content']['cancel_message'] != '') {
                    throw new Exception($chatEvent->content_array['callback_list'][0]['content']['cancel_message']);
                } else {
                    throw new Exception('Canceled!');
                }

                return;
            }

            $keepEvent = false;

            // Event was processed we can remove it now
            foreach ($chatEvent->content_array['callback_list'] as $eventData) {

                $handler = false;

                // Perhaps there is extension which listens for a specific event
                if (isset($eventData['content']['event']) && !empty($eventData['content']['event']) && (!isset($eventData['content']['type']) || $eventData['content']['type'] != 'intent')) {

                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_event_handler', array(
                        'render' => $eventData['content']['event'],
                        'render_args' => (isset($eventData['content']['event_args']) ? $eventData['content']['event_args'] : array()),
                        'chat' => & $chat,
                        'event' => & $chatEvent,
                        'event_data' => $eventData,
                        'payload' => & $payload,
                    ));

                    if (isset($handler['keep_event']) && $handler['keep_event'] == true) {
                        $keepEvent = true;
                    }
                }
                
                if (isset($handler) && $handler !== false && isset($handler['render']) && is_callable($handler['render'])){

                    // Extension itself has to update chat
                    $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                    if (isset($dataProcess['valid']) && $dataProcess['valid'] == false) {
                        if (isset($dataProcess['message']) && !empty($dataProcess['message'])) {
                            throw new erLhcoreClassGenericBotException($dataProcess['message'], 0, null, (isset($dataProcess['params_exception']) ? $dataProcess['params_exception'] : array()));
                        } else {
                            throw new erLhcoreClassGenericBotException('Your message does not match required format!', 0, null, (isset($dataProcess['params_exception']) ? $dataProcess['params_exception'] : array()));
                        }
                    } elseif (!isset($dataProcess['valid'])) {
                        throw new erLhcoreClassGenericBotException('Returned format is incorrect and data could not be validated!', 0, null, (isset($dataProcess['params_exception']) ? $dataProcess['params_exception'] : array()));
                    }

                } else {
                    
                    if (isset($eventData['content']['type']) && $eventData['content']['type'] == 'intent') {

                        $args = array();
                        if (isset($eventData['content']['replace_array'])) {
                            $args['args']['replace_array'] = $eventData['content']['replace_array'];
                        }

                        $args['args']['msg_text'] = $payload;

                        $responseTrigger = null;

                        if (isset($eventData['content']['event_args']['callback_match']) && is_numeric($eventData['content']['event_args']['callback_match'])) {
                            $trigger = erLhcoreClassModelGenericBotTrigger::fetch($eventData['content']['event_args']['callback_match']);
                            if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                $responseTrigger = erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $args);
                            }
                        }

                        if ($responseTrigger === null) {

                            $renderArgs = (isset($eventData['content']['event_args']) ? $eventData['content']['event_args'] : array());

                            // Extract arguments if any
                            if (isset($eventData['content']['validation']['validation_args']) && $eventData['content']['validation']['validation_args'] != '') {
                                $validationArgs = array();
                                $rule = str_replace("\r","\n",$eventData['content']['validation']['validation_args']);
                                $rules = array_filter(explode("\n",$rule));
                                foreach ($rules as $ruleItem) {
                                    $ruleItemData = explode('==>',$ruleItem);
                                    $matches = array();
                                    preg_match($ruleItemData[0], $payload,$matches);
                                    if (!empty($matches) && isset($matches[$ruleItemData[1]]) && trim($matches[$ruleItemData[1]]) != '') {
                                        $validationArgs[$ruleItemData[2]] = trim($matches[$ruleItemData[1]]);
                                    }
                                }

                                if (!empty($validationArgs)) {
                                    if (isset($renderArgs['validation_args'])) {
                                        $renderArgs['validation_args'] = array_merge($renderArgs['validation_args'],$validationArgs);
                                    } else {
                                        $renderArgs['validation_args'] = $validationArgs;
                                    }
                                }
                            }

                            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_event_handler', array(
                                'render' => $eventData['content']['event'],
                                'render_args' => $renderArgs,
                                'chat' => & $chat,
                                'event' => & $chatEvent,
                                'event_data' => $eventData,
                                'payload' => & $payload,
                            ));

                            if (isset($handler['keep_event']) && $handler['keep_event'] == true) {
                                $keepEvent = true;
                            }

                            if (isset($eventData['content']['validation']['words']) && $eventData['content']['validation']['words'] != '') {

                                $pregMatchValid = false;

                                // First letter is /, means we are in preg match mode
                                if ($eventData['content']['validation']['words'][0] == '/' && preg_match($eventData['content']['validation']['words'], mb_strtolower($payload)) === 1) {
                                    $pregMatchValid = true;
                                } else {
                                    $words = explode(',',$eventData['content']['validation']['words']);
                                }

                                $wordsExc = array();
                                if (isset($eventData['content']['validation']['words_exc']) && $eventData['content']['validation']['words_exc'] != '') {
                                    $wordsExc = explode(',',$eventData['content']['validation']['words_exc']);
                                }

                                if (
                                    ($pregMatchValid === true || self::checkPresence($words,mb_strtolower($payload),(isset($eventData['content']['validation']['typos']) ? (int)$eventData['content']['validation']['typos'] : 0)) === true) &&
                                    (empty($wordsExc) || self::checkPresence($wordsExc,mb_strtolower($payload),(isset($eventData['content']['validation']['typos_exc']) ? (int)$eventData['content']['validation']['typos_exc'] : 0)) === false)
                                ) {
                                     if (isset($eventData['content']['event_args']['valid']) && is_numeric($eventData['content']['event_args']['valid'])){
                                         $trigger = erLhcoreClassModelGenericBotTrigger::fetch($eventData['content']['event_args']['valid']);
                                         if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                             erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $args);
                                         }
                                     }
                                } else {

                                    $trigger = null;
                                    if (isset($eventData['content']['event_args']['check_default']) && $eventData['content']['event_args']['check_default'] == true) {
                                        $triggerEvent = self::findTextMatchingEvent(mb_strtolower($payload), $chat->gbot_id, array(), array('dep_id' => $chat->dep_id));
                                        if ($triggerEvent instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                                            $trigger = $triggerEvent->trigger;
                                        }
                                    }

                                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                        erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $args);
                                    } else {
                                        if (isset($eventData['content']['validation']['words_alt']) && self::checkPresence(explode(',',$eventData['content']['validation']['words_alt']),mb_strtolower($payload),(isset($eventData['content']['validation']['typos']) ? (int)$eventData['content']['validation']['typos'] : 0)) === true){
                                            if (isset($eventData['content']['event_args']['valid_alt']) && is_numeric($eventData['content']['event_args']['valid_alt'])) {
                                                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($eventData['content']['event_args']['valid_alt']);
                                                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $args);
                                                }
                                            }
                                        } else if (isset($eventData['content']['event_args']['invalid']) && is_numeric($eventData['content']['event_args']['invalid'])){
                                            $trigger = erLhcoreClassModelGenericBotTrigger::fetch($eventData['content']['event_args']['invalid']);
                                            if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $args);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else if (isset($eventData['content']['type']) && $eventData['content']['type'] == 'default_actions') {

                        $args = array();
                        $args['args']['msg_text'] = $payload;

                        $filter = array();

                        if (isset($eventData['content']['event_args']['on_start_type']) && is_numeric($eventData['content']['event_args']['on_start_type']) && $eventData['content']['event_args']['on_start_type'] != 5) {
                            $filter = array('filter' => array('on_start_type' => $eventData['content']['event_args']['on_start_type']));
                        }

                        $event = self::findTextMatchingEvent($payload, $chat->gbot_id, $filter, array('dep_id' => $chat->dep_id));

                        if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)) {
                            $event = self::findEvent($payload, $chat->gbot_id,0, $filter, array('dep_id' => $chat->dep_id));
                        }

                        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                            erLhcoreClassGenericBotWorkflow::processTrigger($chat, $event->trigger, true, $args);
                        } elseif (isset($eventData['content']['event_args']['alternative_callback']) && is_numeric($eventData['content']['event_args']['alternative_callback'])) {
                            $trigger = erLhcoreClassModelGenericBotTrigger::fetch($eventData['content']['event_args']['alternative_callback']);
                            if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $args);
                            }
                        }

                    } else if (isset($eventData['content']['type']) && $eventData['content']['type'] == 'chat') {
                        if ($eventData['content']['field'] == 'email') {
                            if (filter_var($payload, FILTER_VALIDATE_EMAIL)) {
                                $q = $db->createUpdateQuery();
                                $q->update( 'lh_chat' )
                                    ->set( 'email', $q->bindValue($payload) )
                                    ->where( $q->expr->eq( 'id', $chat->id ) );
                                $stmt = $q->prepare();
                                $stmt->execute();
                                $chat->email = $payload;

                            } else {
                                throw new Exception('Invalid e-mail address');
                            }
                        } else if ($eventData['content']['field'] == 'phone') {

                            if ($payload == '' || mb_strlen($payload) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value) {
                                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone'));
                            }

                            if (mb_strlen($payload) > 100)
                            {
                                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters for phone'));
                            }

                            $q = $db->createUpdateQuery();
                            $q->update( 'lh_chat' )
                                ->set( 'phone', $q->bindValue($payload) )
                                ->where( $q->expr->eq( 'id', $chat->id ) );
                            $stmt = $q->prepare();
                            $stmt->execute();
                            $chat->phone = $payload;
                        }
                    } elseif (isset($eventData['content']['type']) && $eventData['content']['type'] == 'chat_attr') {

                        // Make sure field is not empty
                        if (empty($payload)) {
                            if (isset($eventData['content']['validation_error']) && !empty($eventData['content']['validation_error'])){
                                throw new erLhcoreClassGenericBotException($eventData['content']['validation_error']);
                            } else {
                                throw new erLhcoreClassGenericBotException('Your message does not match required format!');
                            }
                        }

                        // Preg match validation
                        if (isset($eventData['content']['preg_match']) && !empty($eventData['content']['preg_match']))
                        {
                            if (!preg_match('/' . $eventData['content']['preg_match'] . '/',$payload)) {

                                $metaMessage = array();

                                if (isset($eventData['content']['attr_options']['cancel_button_enabled']) && $eventData['content']['attr_options']['cancel_button_enabled'] == true) {
                                    $metaMessage = array('content' =>
                                        array (
                                            'quick_replies' =>
                                                array (
                                                    0 =>
                                                        array (
                                                            'type' => 'button',
                                                            'content' =>
                                                                array (
                                                                    'name' => (($eventData['content']['cancel_button'] && $eventData['content']['cancel_button'] != '') ? $eventData['content']['cancel_button'] : 'Cancel?'),
                                                                    'payload' => 'cancel_workflow',
                                                                ),
                                                        )
                                                ),
                                        ));
                                }

                                if (isset($eventData['content']['validation_error']) && !empty($eventData['content']['validation_error'])){
                                    throw new erLhcoreClassGenericBotException($eventData['content']['validation_error'], 0, null, $metaMessage);
                                } else {
                                    throw new erLhcoreClassGenericBotException('Your message does not match required format!', 0, null, $metaMessage);
                                }
                            }
                        }

                        if (!empty($chat->additional_data)){
                            $chatAttributes = (array)json_decode($chat->additional_data,true);
                        } else {
                            $chatAttributes = array();
                        }

                        $attrIdToUpdate = (isset($eventData['content']['attr_options']['identifier']) ? $eventData['content']['attr_options']['identifier'] : $eventData['content']['attr_options']['name']);

                        foreach ($chatAttributes as $key => $attr) {
                            if ($attr['identifier'] == $attrIdToUpdate) {
                                unset($chatAttributes[$key]);
                            }
                        }

                        if ($attrIdToUpdate == 'lhc.email') {
                            $chat->email = $payload;
                        } elseif ($attrIdToUpdate == 'lhc.nick') {
                            $chat->nick = $payload;
                        } elseif ($attrIdToUpdate == 'lhc.phone') {
                            $chat->phone = $payload;
                        } else {
                            $chatAttributes[] = array('key' => $eventData['content']['attr_options']['name'], 'identifier' => $attrIdToUpdate, 'value' => $payload);
                        }

                        $chat->additional_data = json_encode(array_values($chatAttributes));

                        $q = $db->createUpdateQuery();
                        $q->update( 'lh_chat' )
                            ->set( 'additional_data', $q->bindValue($chat->additional_data) )
                            ->set( 'email', $q->bindValue($chat->email) )
                            ->set( 'nick', $q->bindValue($chat->nick) )
                            ->set( 'phone', $q->bindValue($chat->phone) )
                            ->where( $q->expr->eq( 'id', $chat->id ) );
                        $stmt = $q->prepare();
                        $stmt->execute();

                    } elseif (isset($eventData['content']['type']) && $eventData['content']['type'] == 'rest_api') {
                        // Rest API in progress
                        // Ignore any customer request for a while
                        $keepEvent = true;
                    }
                }

                // Success message
                if (isset($eventData['content']['success_message']) && !empty($eventData['content']['success_message'])) {
                    self::sendAsBot($chat, self::translateMessage($eventData['content']['success_message'], array('chat' => $chat)));
                }

                // Initiate payload based callback if there is any
                if (isset($eventData['content']['success_callback']) && !empty($eventData['content']['success_callback'])) {
                    self::reprocessPayload($eventData['content']['success_callback'], $chat, 1);
                }

                // Initiate text based callback if there is any
                if (isset($eventData['content']['success_text_pattern']) && !empty($eventData['content']['success_text_pattern'])) {
                    self::reprocessPayload($eventData['content']['success_text_pattern'], $chat, 0);
                }

                // Execute next trigger if set
                if (isset($eventData['content']['attr_options']['collection_callback_pattern']) && is_numeric($eventData['content']['attr_options']['collection_callback_pattern'])) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($eventData['content']['attr_options']['collection_callback_pattern']);

                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                        $paramsTrigger = array();
                        erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $paramsTrigger);
                    }
                }
            }

            if ($keepEvent === false) {
                $chatEvent->removeThis();
            } else {
                $chatEvent->counter++;
                $chatEvent->saveThis();
            }

        } catch (Exception $e) {
             if ($e instanceof erLhcoreClassGenericBotException){

                 $message = $e->getMessage();
                 
                 $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);
                 if ($bot instanceof erLhcoreClassModelGenericBotBot) {
                     $configurationArray = $bot->configuration_array;
                     if (isset($configurationArray['exc_group_id']) && !empty($configurationArray['exc_group_id'])){
                         $exceptionMessage = erLhcoreClassModelGenericBotExceptionMessage::findOne(array('limit' => 1, 'sort' => 'priority ASC', 'filter' => array('active' => 1, 'code' => $e->getCode()), 'filterin' => array('exception_group_id' => $configurationArray['exc_group_id'])));
                         if ($exceptionMessage instanceof erLhcoreClassModelGenericBotExceptionMessage && $exceptionMessage->message != '') {
                             $message = erLhcoreClassGenericBotWorkflow::translateMessage($exceptionMessage->message, array('chat' => $chat));
                         }
                     }
                 }
                 
                 self::sendAsBot($chat, $message, $e->getContent());
             } else {
                 self::sendAsBot($chat, $e->getMessage());
             }
        }
    }

    public static function reprocessPayload($payload, $chat, $type = 1)
    {
        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_message', array(
            'chat' => & $chat,
            'payload' => $payload,
        ));

        if ($handler !== false) {
            $event = $handler['event'];
        } else {
            $event = self::findEvent($payload, $chat->gbot_id, $type, array(), array('dep_id' => $chat->dep_id));
        }

        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
            $message = self::processTrigger($chat, $event->trigger);
        }

        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
            self::setLastMessageId($chat, $message->id);
        } else {
            if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
            }
        }
    }

    public static function processWorkflow($workflow, & $chat, $params = array())
    {
        $reprocess = false;
        try {
            $currentStep = $workflow->collected_data_array['current_step'];
            $currentStepId = $workflow->collected_data_array['step'];

            if (isset($params['msg'])) {
                $payload = $params['msg']->msg;
            } else {
                $payload =  $params['payload'];
            }

            if ($payload == 'cancel_workflow') {
                $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_CANCELED;
            }

            if (isset($workflow->collected_data_array['collectable_options']['expires_in']) && is_numeric($workflow->collected_data_array['collectable_options']['expires_in']) && $workflow->collected_data_array['collectable_options']['expires_in'] > 0) {
                if ($workflow->time < (time() - ($workflow->collected_data_array['collectable_options']['expires_in'] * 60))) {
                    $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_EXPIRED;
                }
            }

            if (!in_array($workflow->status,array(erLhcoreClassModelGenericBotChatWorkflow::STATUS_CANCELED,erLhcoreClassModelGenericBotChatWorkflow::STATUS_EXPIRED)))
            {
                if ($currentStep['type'] == 'text') {

                    if (isset($currentStep['content']['validation_callback']) && !empty($currentStep['content']['validation_callback'])) {
                        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                            'render' => $currentStep['content']['validation_callback'],
                            'render_args' => (isset($currentStep['content']['validation_argument']) ? $currentStep['content']['validation_argument'] : null),
                            'chat' => & $chat,
                            'workflow' => & $workflow,
                            'payload' => & $payload,
                        ));

                        if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {

                            $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                            if ($dataProcess['valid'] == false) {
                                if (isset($dataProcess['message']) && !empty($dataProcess['message'])){
                                    throw new erLhcoreClassGenericBotException($dataProcess['message'], 0, null, (isset($dataProcess['params_exception']) ? $dataProcess['params_exception'] : array()));
                                } else if (isset($currentStep['content']['validation_error']) && !empty($currentStep['content']['validation_error'])){
                                    throw new erLhcoreClassGenericBotException($currentStep['content']['validation_error'], 0, null, (isset($dataProcess['params_exception']) ? $dataProcess['params_exception'] : array()));
                                } else {
                                    throw new erLhcoreClassGenericBotException('Your message does not match required format!', 0, null, (isset($dataProcess['params_exception']) ? $dataProcess['params_exception'] : array()));
                                }
                            }
                        }
                    }

                    if (isset($currentStep['content']['validation']) && !empty($currentStep['content']['validation'])) {
                        if (!preg_match('/' . $currentStep['content']['validation'] . '/',$payload)) {
                            if (isset($currentStep['content']['validation_error']) && !empty($currentStep['content']['validation_error'])){
                                throw new erLhcoreClassGenericBotException($currentStep['content']['validation_error']);
                            } else {
                                throw new erLhcoreClassGenericBotException('Your message does not match required format!');
                            }
                        }
                    }

                    $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                        'value' => $payload,
                        'name' => $currentStep['content']['name'],
                        'step' => $currentStepId
                    );

                    if (isset($workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary']) && $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] == true) {
                        $workflow->collected_data_array['step'] = $currentStepId = count($workflow->collected_data_array['steps']);
                    }

                } else if ($currentStep['type'] == 'email') {
                    if (filter_var($payload, FILTER_VALIDATE_EMAIL)) {
                        $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                            'value' => $payload,
                            'name' => $currentStep['content']['name'],
                            'step' => $currentStepId
                        );
                    } else {
                        throw new erLhcoreClassGenericBotException('Invalid e-mail address');
                    }

                    if (isset($workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary']) && $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] == true) {
                        $workflow->collected_data_array['step'] = $currentStepId = count($workflow->collected_data_array['steps']);
                    }

                } else if ($currentStep['type'] == 'phone') {

                    if ($payload == '' || mb_strlen($payload) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value) {
                        throw new erLhcoreClassGenericBotException(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone'));
                    }

                    if (mb_strlen($payload) > 100)
                    {
                        throw new erLhcoreClassGenericBotException(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters for phone'));
                    }

                    $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                        'value' => $payload,
                        'name' => $currentStep['content']['name'],
                        'step' => $currentStepId
                    );

                    if (isset($workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary']) && $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] == true) {
                        $workflow->collected_data_array['step'] = $currentStepId = count($workflow->collected_data_array['steps']);
                    }

                } else if ($currentStep['type'] == 'dropdown') {

                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                        'render' => $currentStep['content']['provider_dropdown'],
                        'render_args' => (isset($currentStep['content']['provider_argument']) ? $currentStep['content']['provider_argument'] : null),
                        'chat' => & $chat,
                        'workflow' => & $workflow,
                        'payload' => & $payload,
                    ));

                    if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {

                        $content = array(
                            'content' => array(
                                'dropdown' => array(
                                    'provider_dropdown' => $handler['render'],
                                    'provider_name' => $currentStep['content']['provider_name'],
                                    'provider_id' => $currentStep['content']['provider_id'],
                                )
                            )
                        );

                        $messageClick = self::getValueName($content, $payload);

                        if (empty($messageClick)) {
                            $reprocess = true;
                            throw new Exception('Please choose a value from dropdown!');
                        } else {
                            $message = self::sendAsUser($chat, $messageClick);
                            self::setLastMessageId($chat, $message->id);
                        }

                        $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                            'value' => $payload,
                            'value_literal' => $messageClick,
                            'name' => $currentStep['content']['name'],
                            'step' => $currentStepId
                        );

                        if (isset($workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary']) && $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] == true) {
                            $workflow->collected_data_array['step'] = $currentStepId = count($workflow->collected_data_array['steps']);
                        }

                    } else {
                        throw new erLhcoreClassGenericBotException('Validation function could not be found! Have you defined listener for ' . $currentStep['content']['provider_dropdown'] . ' identifier');
                    }

                } else if ($currentStep['type'] == 'buttons') {

                    $reprocess = true;

                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                        'render' => $currentStep['content']['render_validate'],
                        'render_args' => $currentStep['content']['render_args'],
                        'chat' => & $chat,
                        'workflow' => & $workflow,
                        'payload' => & $payload,
                    ));

                    if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                    {
                        $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                        $message = self::sendAsUser($chat, $dataProcess['chosen_value_literal']);
                        self::setLastMessageId($chat, $message->id);

                        $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                            'value' => $dataProcess['chosen_value'],
                            'value_literal' => $dataProcess['chosen_value_literal'],
                            'name' => $currentStep['content']['name'],
                            'step' => $currentStepId
                        );

                        if (isset($dataProcess['reset_step']) && is_array($dataProcess['reset_step']) && !empty($dataProcess['reset_step'])) {
                            foreach ($dataProcess['reset_step'] as $fieldName) {
                                unset($workflow->collected_data_array['collected'][$fieldName]);
                            }
                        }

                        if (isset($dataProcess['go_to_step'])) {
                            $workflow->collected_data_array['step'] = $currentStepId = $dataProcess['go_to_step'];
                        } else if (isset($dataProcess['go_to_next']) && $dataProcess['go_to_next'] == true) {
                            // Do nothing at the moment
                        } else if (isset($workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary']) && $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] == true) {
                            $workflow->collected_data_array['step'] = $currentStepId = count($workflow->collected_data_array['steps']);
                        }

                    } else {
                        throw new erLhcoreClassGenericBotException('Validation function could not be found! Have you defined listener for ' . $currentStep['content']['render_validate'] . ' identifier');
                    }

                } else if ($currentStep['type'] == 'custom') {
                    $reprocess = true;

                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                        'render' => $currentStep['content']['render_validate'],
                        'render_args' => $currentStep['content']['render_args'],
                        'chat' => & $chat,
                        'workflow' => & $workflow,
                        'payload' => & $payload,
                        'step_id' => $currentStepId
                    ));

                    if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                    {
                        $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                        if ($dataProcess['valid'] == false) {
                            if (isset($dataProcess['message']) && !empty($dataProcess['message'])) {
                                throw new erLhcoreClassGenericBotException($dataProcess['message']);
                            } else {
                                throw new erLhcoreClassGenericBotException('Your message does not match required format!');
                            }
                        }

                        $message = self::sendAsUser($chat, $dataProcess['chosen_value_literal']);
                        self::setLastMessageId($chat, $message->id);

                        $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                            'value' => $dataProcess['chosen_value'],
                            'value_literal' => $dataProcess['chosen_value_literal'],
                            'name' => $currentStep['content']['name'],
                            'step' => $currentStepId
                        );

                        if (isset($dataProcess['reset_step']) && is_array($dataProcess['reset_step']) && !empty($dataProcess['reset_step'])) {
                            foreach ($dataProcess['reset_step'] as $fieldName) {
                                unset($workflow->collected_data_array['collected'][$fieldName]);
                            }
                        }

                        if (isset($dataProcess['go_to_step'])) {
                            $workflow->collected_data_array['step'] = $currentStepId = $dataProcess['go_to_step'];
                        } else if (isset($dataProcess['go_to_next']) && $dataProcess['go_to_next'] == true) {
                            // Do nothing at the moment
                        } else if (isset($workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary']) && $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] == true) {
                            $workflow->collected_data_array['step'] = $currentStepId = count($workflow->collected_data_array['steps']);
                        }

                    } else {
                        throw new erLhcoreClassGenericBotException('Validation function could not be found!');
                    }
                }
            }
            if ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_PENDING_CONFIRM) {
                $reprocess = true;

                if ($payload == 'confirm') {

                    // Send message as user confirmed it
                    $message = self::sendAsUser($chat, 'Confirm');
                    self::setLastMessageId($chat, $message->id);

                    if (isset($workflow->collected_data_array['collectable_options']['collection_callback']) && $workflow->collected_data_array['collectable_options']['collection_callback'] !== '') {

                        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                            'render' => $workflow->collected_data_array['collectable_options']['collection_callback'],
                            'render_args' => (isset($workflow->collected_data_array['collectable_options']['collection_argument']) ? $workflow->collected_data_array['collectable_options']['collection_argument'] : null),
                            'chat' => & $chat,
                            'workflow' => & $workflow,
                            'payload' => & $payload,
                        ));

                        if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                        {
                            $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                            if (isset($dataProcess['chosen_value_literal']) && !empty($dataProcess['chosen_value_literal'])) {
                                $message = self::sendAsBot($chat, $dataProcess['chosen_value_literal']);
                            }
                            
                            $collectedInfo = array (
								'info' => $dataProcess['info'],
                            );
                            
                            if (isset($dataProcess['args_next'])) {
                            	$collectedInfo['args_next'] = $dataProcess['args_next'];
                            }
                            
                            $workflow->collected_data_array['collected_confirm'] = $collectedInfo;
                        }
                    }

                    $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_COMPLETED;
                    $workflow->collected_data_array['current_step'] = array();

                } else {
                    if (isset($workflow->collected_data_array['collectable_options']['collection_confirm_missing']) && !empty($workflow->collected_data_array['collectable_options']['collection_confirm_missing'])) {
                        throw new erLhcoreClassGenericBotException($workflow->collected_data_array['collectable_options']['collection_confirm_missing']);
                    } else {
                        throw new erLhcoreClassGenericBotException('Information was unconfirmed!');
                    }
                }
            }

            // There is more steps to proceed
            if (count($workflow->collected_data_array['steps']) >= $currentStepId+1 && isset($workflow->collected_data_array['steps'][$currentStepId+1]) && !in_array($workflow->status,array(erLhcoreClassModelGenericBotChatWorkflow::STATUS_CANCELED,erLhcoreClassModelGenericBotChatWorkflow::STATUS_EXPIRED))) {
                $workflow->collected_data_array['current_step'] = $workflow->collected_data_array['steps'][$currentStepId+1];
                $workflow->collected_data_array['step'] = $currentStepId+1;

                erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step']);
               
            } else {

                // Collected information should be confirmed by user
                if ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED && isset($workflow->collected_data_array['collectable_options']['show_summary']) && $workflow->collected_data_array['collectable_options']['show_summary'] == true) {

                    $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_PENDING_CONFIRM;

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_callback']) &&
                        $workflow->collected_data_array['collectable_options']['show_summary_callback'] !== ''
                    ) {
                        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                            'render' => $workflow->collected_data_array['collectable_options']['show_summary_callback'],
                            'render_args' => (isset($workflow->collected_data_array['collectable_options']['collection_argument']) ? $workflow->collected_data_array['collectable_options']['collection_argument'] : null),
                            'chat' => & $chat,
                            'workflow' => & $workflow,
                            'payload' => & $payload,
                        ));

                        if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {
                            $stepData = call_user_func_array($handler['render'], $handler['render_args']);
                        } else {
                            $stepData = erLhcoreClassGenericBotActionCollectable::sendSummary($chat, $workflow);
                        }

                    } else {
                       $stepData = erLhcoreClassGenericBotActionCollectable::sendSummary($chat, $workflow);
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_confirm_name']) && !empty($workflow->collected_data_array['collectable_options']['show_summary_confirm_name'])) {
                        $stepData['content']['collectable_options']['show_summary_confirm_name'] = $workflow->collected_data_array['collectable_options']['show_summary_confirm_name'];
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_checkbox_name']) && !empty($workflow->collected_data_array['collectable_options']['show_summary_checkbox_name'])) {
                        $stepData['content']['collectable_options']['show_summary_checkbox_name'] = $workflow->collected_data_array['collectable_options']['show_summary_checkbox_name'];
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_cancel_name']) && !empty($workflow->collected_data_array['collectable_options']['show_summary_cancel_name'])) {
                        $stepData['content']['collectable_options']['show_summary_cancel_name'] = $workflow->collected_data_array['collectable_options']['show_summary_cancel_name'];
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['edit_image_url']) && !empty($workflow->collected_data_array['collectable_options']['edit_image_url'])) {
                        $stepData['content']['collectable_options']['edit_image_url'] = $workflow->collected_data_array['collectable_options']['edit_image_url'];
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_checkbox']) && $workflow->collected_data_array['collectable_options']['show_summary_checkbox'] == true) {
                        $stepData['content']['collectable_options']['show_summary_checkbox'] = $workflow->collected_data_array['collectable_options']['show_summary_checkbox'];
                    }

                    $workflow->identifier = $workflow->collected_data_array['collectable_options']['identifier_collection'];
                    $workflow->collected_data_array['current_step'] = $stepData;
                    erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step']);

                } elseif ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_COMPLETED || $workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED) {

                    // Finish workflow if no more steps were found and no STATUS_PENDING_CONFIRM is pending
                    if ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED) {

                        if (isset($workflow->collected_data_array['collectable_options']['collection_callback']) && $workflow->collected_data_array['collectable_options']['collection_callback'] !== '') {

                            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                                'render' => $workflow->collected_data_array['collectable_options']['collection_callback'],
                                'render_args' => (isset($workflow->collected_data_array['collectable_options']['collection_argument']) ? $workflow->collected_data_array['collectable_options']['collection_argument'] : null),
                                'chat' => & $chat,
                                'workflow' => & $workflow,
                                'payload' => & $payload,
                            ));

                            if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                            {
                                $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                                if (isset($dataProcess['chosen_value_literal']) && !empty($dataProcess['chosen_value_literal'])) {
                                    $message = self::sendAsBot($chat, $dataProcess['chosen_value_literal']);
                                }
                                
                                $collectedInfo = array (
                                		'info' => $dataProcess['info'],
                                );
                                
                                if (isset($dataProcess['args_next'])) {
                                	$collectedInfo['args_next'] = $dataProcess['args_next'];
                                }
                                
                                $workflow->collected_data_array['collected_confirm'] = $collectedInfo;
                            }
                        }

                        $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_COMPLETED;
                        $workflow->collected_data_array['current_step'] = array();
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['confirmation_message']) && $workflow->collected_data_array['collectable_options']['confirmation_message'] != '') {
                        if (isset($workflow->collected_data_array['collected_confirm']['info']) && !empty($workflow->collected_data_array['collected_confirm']['info'])) {

                            $replaceArray = array();
                            foreach ($workflow->collected_data_array['collected_confirm']['info'] as $key => $value) {
                                $replaceArray['{'.$key.'}'] = $value;
                            }

                            self::sendAsBot($chat, str_replace(array_keys($replaceArray),$replaceArray,$workflow->collected_data_array['collectable_options']['confirmation_message']));
                        } else {
                            self::sendAsBot($chat, $workflow->collected_data_array['collectable_options']['confirmation_message']);
                        }
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['collection_callback_pattern']) && is_numeric($workflow->collected_data_array['collectable_options']['collection_callback_pattern'])) {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($workflow->collected_data_array['collectable_options']['collection_callback_pattern']);

                        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                        	                        	
                        	$paramsTrigger = array();
                        	if (isset($workflow->collected_data_array['collected_confirm']['args_next'])) {
                        		$paramsTrigger['args'] = $workflow->collected_data_array['collected_confirm']['args_next'];
                        	}

                        	erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $paramsTrigger);
                        }
                    }

                } elseif ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_CANCELED) {

                    if (isset($workflow->collected_data_array['collectable_options']['cancel_message']) && !empty($workflow->collected_data_array['collectable_options']['cancel_message'])) {
                       self::sendAsBot($chat, $workflow->collected_data_array['collectable_options']['cancel_message']);
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['collection_cancel_callback_pattern']) && is_numeric($workflow->collected_data_array['collectable_options']['collection_cancel_callback_pattern'])) {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($workflow->collected_data_array['collectable_options']['collection_cancel_callback_pattern']);

                        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                            erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                        }
                    }
                } elseif ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_EXPIRED) {

                    if (isset($workflow->collected_data_array['collectable_options']['expire_message']) && !empty($workflow->collected_data_array['collectable_options']['expire_message'])) {
                       self::sendAsBot($chat, $workflow->collected_data_array['collectable_options']['expire_message']);
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['collection_expire_callback_pattern']) && is_numeric($workflow->collected_data_array['collectable_options']['collection_expire_callback_pattern'])) {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($workflow->collected_data_array['collectable_options']['collection_expire_callback_pattern']);

                        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                            erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                        }
                    }
                }
            }

            $workflow->collected_data = json_encode($workflow->collected_data_array);
            $workflow->saveThis();

        } catch (Exception $e) {

            $metaError = array();

            if ($e instanceof erLhcoreClassGenericBotException) {

                $message = $e->getMessage();

                $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);
                if ($bot instanceof erLhcoreClassModelGenericBotBot) {
                    $configurationArray = $bot->configuration_array;
                    if (isset($configurationArray['exc_group_id']) && !empty($configurationArray['exc_group_id'])){
                        $exceptionMessage = erLhcoreClassModelGenericBotExceptionMessage::findOne(array('limit' => 1, 'sort' => 'priority ASC', 'filter' => array('active' => 1, 'code' => $e->getCode()), 'filterin' => array('exception_group_id' => $configurationArray['exc_group_id'])));
                        if ($exceptionMessage instanceof erLhcoreClassModelGenericBotExceptionMessage && $exceptionMessage->message != '') {
                            $message = erLhcoreClassGenericBotWorkflow::translateMessage($exceptionMessage->message, array('chat' => $chat));
                        }
                    }
                }
                
                if ($reprocess) {
                    $metaError['meta_error']['message'] = $message;
                    $metaError['meta_error']['content'] = $e->getContent();
                } else {
                    self::sendAsBot($chat, $message, $e->getContent());
                }
            } else {
                self::sendAsBot($chat, $e->getMessage());
            }

            if ($reprocess == true) {
                $message = erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step'], $metaError);

                if ($message instanceof erLhcoreClassModelmsg) {
                    self::setLastMessageId($chat, $message->id);
                }
            }

        }
    }

    public static function processTrigger($chat, $trigger, $setLastMessageId = false, $params = array())
    {
        static $recursion_counter = 0;

        $recursion_counter++;

        if ($recursion_counter > 50) {
            throw new Exception('To many calls to process trigger! [50]');
        }

        // Delete pending event if same even is executing already
        if ($chat->id > 0 && $trigger->id > 0){
            $db = ezcDbInstance::get();
            $stmt = $db->prepare("DELETE FROM lh_generic_bot_pending_event WHERE chat_id = :chat_id AND trigger_id = :trigger_id");
            $stmt->bindValue(':chat_id', $chat->id, PDO::PARAM_INT);
            $stmt->bindValue(':trigger_id', $trigger->id, PDO::PARAM_INT);
            $stmt->execute();
        }

        $message = null;
        foreach ($trigger->actions_front as $action) {
        	$messageNew = call_user_func_array("erLhcoreClassGenericBotAction" . ucfirst($action['type']).'::process',array($chat, $action, $trigger, (isset($params['args']) ? $params['args'] : array())));

            if ($messageNew instanceof erLhcoreClassModelmsg) {

                $message = $messageNew;

                if ($messageNew->id > 0 && $messageNew->id > $chat->last_msg_id) {
                    $chat->last_msg_id = $messageNew->id;
                }

            } elseif (is_array($messageNew) && isset($messageNew['status']) && ($messageNew['status'] == 'stop' || $messageNew['status'] == 'continue' || $messageNew['status'] == 'continue_all')) {

                $continue = false;
                if (isset($messageNew['trigger_id']) && is_numeric($messageNew['trigger_id'])) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($messageNew['trigger_id']);

                    // Pass custom arguments if any
                    if (isset($messageNew['validation_args']) && !empty($messageNew['validation_args'])) {
                        if (isset($params['args']['validation_args'])) {
                            $params['args']['validation_args'] = array_merge($params['args']['validation_args'],$messageNew['validation_args']);
                        } else {
                            $params['args']['validation_args'] = $messageNew['validation_args'];
                        }
                    }

                    if (isset($messageNew['replace_array'])) {
                        $params['args']['replace_array'] = $messageNew['replace_array'];
                    }
                    
                    if (isset($messageNew['meta_msg'])) {
                        $params['args']['meta_msg'] = $messageNew['meta_msg'];
                    }

                    $response = self::processTrigger($chat, $trigger, $setLastMessageId, $params);

                    if ($messageNew['status'] == 'continue_all' || (is_array($response) && isset($response['status']) && $response['status'] == 'stop' && $messageNew['status'] == 'continue')) {
                        $continue = true;
                    } else {
                        return array(
                            'status' => 'stop',
                            'response' => $response
                        );
                    }

                } elseif (isset($messageNew['response']) && $messageNew['response'] instanceof erLhcoreClassModelmsg) {
                    $message = $messageNew['response'];

                    if ($message->id > 0 && $message->id > $chat->last_msg_id) {
                        $chat->last_msg_id = $message->id;
                    }

                } elseif (isset($messageNew['ignore_trigger']) && $messageNew['ignore_trigger'] == true) {
                    return array(
                        'status' => 'stop',
                        'ignore_trigger' => true
                    );
                }

                if ($continue == false) {
                    return $messageNew;
                    break;
                }
            }
        }

        if ($setLastMessageId == true && isset($message) && $message instanceof erLhcoreClassModelmsg) {
            if ($message->id > 0) {
                self::setLastMessageId($chat, $message->id, true);
            }
        }

        return $message;
    }

    public static function processTriggerPreview($chat, $trigger, $params = array())
    {
        $messages = array();
        foreach ($trigger->actions_front as $action) {
            $messageNew = call_user_func_array("erLhcoreClassGenericBotAction" . ucfirst($action['type']).'::process',array($chat, $action, $trigger, (isset($params['args']) ? $params['args'] : array())));
            if ($messageNew instanceof erLhcoreClassModelmsg) {
                $messages[] = $messageNew;
            } else if (is_array($messageNew)) {
                $messages = array_merge($messages, $messageNew);
            }
        }

        return $messages;
    }

    public static function getClickName($metaData, $payload, $returnAll = false, $paramsExecution = array())
    {
        if (isset($metaData['content']['quick_replies'])) {
            foreach ($metaData['content']['quick_replies'] as $reply) {
                if ($reply['content']['payload'] == $payload && (!isset($paramsExecution['payload_hash']) || $paramsExecution['payload_hash'] == '' || md5($reply['content']['name']) == $paramsExecution['payload_hash'])) {
                    return $returnAll == false ? $reply['content']['name'] : $reply['content'];
                }
            }
        } elseif (isset($metaData['content']['buttons_generic'])) {
            foreach ($metaData['content']['buttons_generic'] as $reply) {
                if ($reply['content']['payload'] == $payload && (!isset($paramsExecution['payload_hash']) || $paramsExecution['payload_hash'] == '' || md5($reply['content']['name']) == $paramsExecution['payload_hash'])) {
                    return $returnAll == false ? $reply['content']['name'] : $reply['content'];
                }
            }
        }
    }

    public static function processStepEdit($chat, $messageContext, $payload, $params = array())
    {
        if (isset($chat->gbot_id)) {

            // Try to find current workflow first
            $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
            if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {

                $db = ezcDbInstance::get();

                try {

                    $db->beginTransaction();

                    $chat->syncAndLock();

                    $message = self::sendAsUser($chat, 'Edit - ' . '"'.$workflow->collected_data_array['steps'][$payload]['content']['name'] . '"');
                    self::setLastMessageId($chat, $message->id);

                    $workflow->collected_data_array['current_step'] = $workflow->collected_data_array['steps'][$payload];
                    $workflow->collected_data_array['step'] = $payload;
                    $workflow->collected_data_array['current_step']['content']['collectable_options']['go_to_summary'] = true;

                    erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step']);

                    $workflow->collected_data = json_encode($workflow->collected_data_array);
                    $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED;
                    $workflow->saveThis();

                    $db->commit();

                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
        }
    }

    public static function processTriggerClick($chat, $messageContext, $payload, $params = array()) {
        if (isset($chat->gbot_id)) {

            $db = ezcDbInstance::get();

            try {

                $db->beginTransaction();

                $chat->syncAndLock();

                $continueExecution = true;

                $payloadParams = explode('__',$payload);
                $payload = $payloadParams[0];
                $payloadHash = isset($payloadParams[1]) ? $payloadParams[1] : null;

                // Try to find current workflow first
                $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
                if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
                    self::processWorkflow($workflow, $chat, array('payload' => $payload));
                    $continueExecution = false;
                }

                if ($continueExecution == true)
                {
                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_trigger_click', array(
                        'chat' => & $chat,
                        'msg' => $messageContext,
                        'payload' => $payload,
                        'payload_hash' => $payloadHash,
                    ));

                    if ($handler !== false) {
                        $trigger = $handler['trigger'];
                    } else {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($payload);

                        if (!($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                            self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Trigger could not be found!'));
                            $continueExecution = false;
                        }
                    }

                    if ($continueExecution == true)
                    {
                        $messageClickData = self::getClickName($messageContext->meta_msg_array, $payload, true, array('payload_hash' => $payloadHash));

                        $messageClick = '';

                        if (isset($messageClickData['name'])) {

                            $messageClick = $messageClickData['name'];

                            $chatAdditionalData = $chat->additional_data_array;

                            $updateAdditionalData = false;
                            if (isset($messageClickData['store_name']) && $messageClickData['store_name'] != '') {
                                foreach ($chatAdditionalData as $dataItemIndex => $dataItem) {
                                    if ($dataItem['identifier'] == str_replace(' ','_',mb_strtolower($messageClickData['store_name']))) {
                                        $chatAdditionalData[$dataItemIndex]['value'] = (isset($messageClickData['store_value']) && $messageClickData['store_value'] != '') ?  $messageClickData['store_value'] : $messageClick;
                                        $updateAdditionalData = true;
                                        break;
                                    }
                                }

                                if ($updateAdditionalData == false) {
                                    $chatAdditionalData[] = array(
                                        'identifier' => str_replace(' ','_',mb_strtolower($messageClickData['store_name'])),
                                        'key' => $messageClickData['store_name'],
                                        'value' => ((isset($messageClickData['store_value']) && $messageClickData['store_value'] != '') ?  $messageClickData['store_value'] : $messageClick),
                                    );
                                }

                                $chat->additional_data_array = $chatAdditionalData;
                                $chat->additional_data = json_encode($chatAdditionalData);
                                $chat->updateThis(array('update' => array('additional_data')));
                            }
                        }

                        if (!empty($messageClick)) {
                            if ((isset($params['processed']) && $params['processed'] == true) || !isset($params['processed'])){
                                $messageContext->meta_msg_array['processed'] = true;
                            }
                            $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                            $messageContext->saveThis();
                            $message = self::sendAsUser($chat, $messageClick);
                        }

                        $messageTrigger = self::processTrigger($chat, $trigger);

                        if ($messageTrigger instanceof erLhcoreClassModelmsg)
                        {
                            $message = $messageTrigger;
                        }

                        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                            self::setLastMessageId($chat, $message->id);
                        } else {
                            if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
                                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
                            }
                        }
                    }
                }

                $db->commit();

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }

    public static function processButtonClick($chat, $messageContext, $payload, $params = array()) {

        if (isset($chat->gbot_id)) {

            $db = ezcDbInstance::get();

            try {

                $db->beginTransaction();

                $chat->syncAndLock();

                $payloadParams = explode('__',$payload);
                $payload = $payloadParams[0];
                $payloadHash = isset($payloadParams[1]) ? $payloadParams[1] : null;

                $continueExecution = true;
                
                // Try to find current callback handler just
                $chatEvent = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $chat->id)));
                if ($chatEvent instanceof erLhcoreClassModelGenericBotChatEvent) {
                    self::$currentEvent = $chatEvent;
                    self::processEvent($chatEvent, $chat, array('payload' => $payload));
                    $continueExecution = false;
                }

                if ($continueExecution == true) {
                    // Try to find current workflow first
                    $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0, 1)), 'filter' => array('chat_id' => $chat->id)));
                    if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
                        self::processWorkflow($workflow, $chat, array('payload' => $payload));
                        $continueExecution = false;
                    }

                    if ($continueExecution == true)
                    {
                        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_click', array(
                            'chat' => & $chat,
                            'msg' => $messageContext,
                            'payload' => $payload,
                            'payload_hash' => $payloadHash,
                        ));

                        if ($handler !== false) {
                            $event = $handler['event'];
                        } else {
                            $event = self::findEvent($payload, $chat->gbot_id, 1, array(), array('dep_id' => $chat->dep_id));
                        }

                        $messageClickData = self::getClickName($messageContext->meta_msg_array, $payload, true, array('payload_hash' => $payloadHash));

                        $messageClick = '';

                        if (isset($messageClickData['name'])) {

                            $messageClick = $messageClickData['name'];

                            $chatAdditionalData = $chat->additional_data_array;

                            $updateAdditionalData = false;
                            if (isset($messageClickData['store_name']) && $messageClickData['store_name'] != '') {
                                foreach ($chatAdditionalData as $dataItemIndex => $dataItem) {
                                    if ($dataItem['identifier'] == str_replace(' ','_',mb_strtolower($messageClickData['store_name']))) {
                                        $chatAdditionalData[$dataItemIndex]['value'] = (isset($messageClickData['store_value']) && $messageClickData['store_value'] != '') ?  $messageClickData['store_value'] : $messageClick;
                                        $updateAdditionalData = true;
                                        break;
                                    }
                                }

                                if ($updateAdditionalData == false) {
                                    $chatAdditionalData[] = array(
                                        'identifier' => str_replace(' ','_',mb_strtolower($messageClickData['store_name'])),
                                        'key' => $messageClickData['store_name'],
                                        'value' => ((isset($messageClickData['store_value']) && $messageClickData['store_value'] != '') ?  $messageClickData['store_value'] : $messageClick),
                                    );
                                }

                                $chat->additional_data_array = $chatAdditionalData;
                                $chat->additional_data = json_encode($chatAdditionalData);
                                $chat->updateThis(array('update' => array('additional_data')));
                            }
                        }

                        if (!empty($messageClick)) {
                            if ((isset($params['processed']) && $params['processed'] == true) || !isset($params['processed'])) {
                                $messageContext->meta_msg_array['processed'] = true;
                            }
                            $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                            $messageContext->saveThis();
                            $message = self::sendAsUser($chat, $messageClick);
                        }

                        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                            $message = self::processTrigger($chat, $event->trigger);
                        } else {

                            // Send default message for unknown button click
                            $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);

                            $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => array('default_unknown_btn' => 1)));

                            if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => array('msg_text' => $payload)));
                            }
                        }

                        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                            self::setLastMessageId($chat, $message->id);
                        } else {
                            if (erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output') == true) {
                                //self::sendAsBot($chat, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Button action could not be found!'));
                            }
                        }
                    }
                }

                $db->commit();

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_click_async', array(
                    'chat' => & $chat,
                    'msg' => $messageContext,
                    'payload' => $payload,
                ));

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }

    public static function processValueClick($chat, $messageContext, $payload, $params = array())
    {
        $db = ezcDbInstance::get();

        try {

            $db->beginTransaction();

            $chat->syncAndLock();

            // Try to find current workflow first
            $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
            
            if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {                
                self::processWorkflow($workflow, $chat, array('payload' => $payload));                
            } else {
                $messageClick = self::getValueName($messageContext->meta_msg_array, $payload);

                if (!empty($messageClick)) {
                    if ((isset($params['processed']) && $params['processed'] == true) || !isset($params['processed'])){
                        $messageContext->meta_msg_array['processed'] = true;
                    }
                    $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                    $messageContext->saveThis();
                    self::sendAsUser($chat, $messageClick);
                }
            }

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function getValueName($metaData, $payload)
    {
        if (isset($metaData['content']['dropdown'])) {
            $options = call_user_func_array($metaData['content']['dropdown']['provider_dropdown'], array());
            foreach ($options as $option) {
                if ($option->{$metaData['content']['dropdown']['provider_id']} == $payload) {
                    return $option->{$metaData['content']['dropdown']['provider_name']};
                }
            }
        }
    }
    /**
     * @desc generic update actions
     *
     * @param $chat
     * @param $messageContext
     * @param $payload
     */
    public static function processUpdateClick($chat, $messageContext, $payload)
    {
        if (isset($chat->gbot_id)) {

            if (is_callable('erLhcoreClassGenericBotUpdateActions::' . $payload . 'Action')){

                $messageClick = self::getClickName($messageContext->meta_msg_array, $payload, true);

                if (!empty($messageClick)) {
                    self::sendAsUser($chat, $messageClick['name']);
                }

                $message = call_user_func_array("erLhcoreClassGenericBotUpdateActions::" . $payload . 'Action', array($chat, $messageClick));

                $messageContext->meta_msg_array['processed'] = true;
                $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                $messageContext->saveThis();

                if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                    self::setLastMessageId($chat, $message->id);
                }

            } else {
                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Update actions could not be found!'));
            }
        }
    }

    public static function translateMessage($message, $params = array())
    {
        $depId = 0;
        $locale = null;
        if (isset($params['chat'])) {
            $depId = $params['chat']->dep_id;
            $locale = $params['chat']->chat_locale;
        }

        if (isset($params['chat'])) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.replace_before_message_bot', array('msg' => & $message, 'chat' => & $params['chat']));
        }

        $matches = array();
        preg_match_all('~\{((?:[^\{\}]++|(?R))*)\}~',$message,$matches);

        if (isset($matches[0]) && !empty($matches[0]))
        {
            $identifiers = array();
            foreach ($matches[0] as $key => $match) {
                if (strpos($matches[1][$key],'__') !== false) {
                    $parts = explode('__',$matches[1][$key]);
                    $identifiers[$parts[0]] = array('search' => $matches[0][$key], 'replace' => $parts[1], 'args' => array_slice($parts,2));
                }
            }

            if (!empty($identifiers)) {
                $department = erLhcoreClassModelDepartament::fetch($depId,true);

                if ($department instanceof erLhcoreClassModelDepartament) {
                    $configuration = $department->bot_configuration_array;
                    if (isset($configuration['bot_tr_id']) && $configuration['bot_tr_id'] > 0 && !empty($identifiers)) {
                        $items = erLhcoreClassModelGenericBotTrItem::getList(array('filterin' => array('identifier' => array_keys($identifiers)),'filter' => array('group_id' => $configuration['bot_tr_id'])));
                        foreach ($items as $item) {
                            $item->translateByChat($locale);
                            $identifiers[$item->identifier]['replace'] = $item->translation_front;
                        }
                    }
                }

                $replaceArray = array();
                foreach ($identifiers as $data) {
                    foreach ($data['args'] as $arg) {
                        $partArg = explode('[',$arg);

                        // Should we print this message
                        if ($partArg[0] == 't') {
                            $startEnd = explode(':',str_replace(']','',$partArg[1]));

                            if (isset($params['chat']) && $params['chat']->user_tz_identifier != '') {
                                $date = new DateTime(null, new DateTimeZone($params['chat']->user_tz_identifier));
                            } else {
                                $date = new DateTime();
                            }

                            if (!((int)$startEnd[0] <= $date->format('H') && (int)$startEnd[1] > $date->format('H'))) {
                                $data['replace'] = '';
                            }
                        }
                    }

                    $replaceArray[$data['search']] = $data['replace'];
                }

                $message = str_replace(array_keys($replaceArray), array_values($replaceArray), $message);
            }
        }

        if (isset($params['chat'])) {

            $replaceArray = array(
                '{lhc.nick}' => $params['chat']->nick,
                '{lhc.email}' => $params['chat']->email,
                '{lhc.department}' => (string)$params['chat']->department,
            );

            foreach ($params['chat']->additional_data_array as $keyItem => $addItem) {
                if (!is_string($addItem) || (is_string($addItem) && ($addItem != ''))) {
                    if (isset($addItem['identifier'])) {
                        if (is_string($addItem['value']) || is_numeric($addItem['value'])) {
                            $replaceArray['{lhc.add.' . $addItem['identifier'] . '}'] = $addItem['value'];
                        }
                    } else if (isset($addItem['key'])) {
                        if (is_string($addItem['value']) || is_numeric($addItem['value'])) {
                            $replaceArray['{lhc.add.' . $addItem['key'] . '}'] = $addItem['value'];
                        }
                    }
                }
            }

            foreach ($params['chat']->chat_variables_array as $keyItem => $addItem) {
                if (is_string($addItem) || is_numeric($addItem)) {
                    $replaceArray['{lhc.var.' . $keyItem . '}'] = $addItem;
                }
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.replace_message_bot', array('msg' => & $message, 'chat' => & $params['chat']));

            $message = str_replace(array_keys($replaceArray), array_values($replaceArray), $message);
        }

        return $message;

    }

    public static function sendAsBot($chat, $message, $metaMessage = array())
    {      
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = $message;
        $msg->chat_id = $chat->id;
        $msg->name_support = self::getDefaultNick($chat);
        $msg->user_id = -2;
        $msg->time = time() + 5;

        if (!empty($metaMessage)) {
            $msg->meta_msg = json_encode($metaMessage);
        }

        erLhcoreClassChat::getSession()->save($msg);

        self::setLastMessageId($chat, $msg->id, true);
    }

    public static function sendAsUser($chat, $messageText) {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($messageText);
        $msg->chat_id = $chat->id;
        $msg->user_id = 0;
        $msg->time = time();

        if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
            erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg, 'chat' => & $chat));

        erLhcoreClassChat::getSession()->save($msg);

        return $msg;
    }

    public static function setLastMessageId($chat, $messageId, $isBot = false) {

        $db = ezcDbInstance::get();

        $attrLastMessageTime = $isBot === false ? 'last_user_msg_time' : 'last_op_msg_time';

        $chat->{$attrLastMessageTime} = time();

        $stmt = $db->prepare("UPDATE lh_chat SET {$attrLastMessageTime} = :last_user_msg_time, lsync = :lsync, last_msg_id = :last_msg_id, has_unread_messages = :has_unread_messages, unanswered_chat = :unanswered_chat WHERE id = :id");
        $stmt->bindValue(':id', $chat->id, PDO::PARAM_INT);
        $stmt->bindValue(':lsync', time(), PDO::PARAM_INT);
        $stmt->bindValue(':has_unread_messages', ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT ? 0 : 1), PDO::PARAM_INT);
        $stmt->bindValue(':last_user_msg_time', time(), PDO::PARAM_INT);
        $stmt->bindValue(':unanswered_chat', 0, PDO::PARAM_INT);
        $stmt->bindValue(':last_msg_id',$messageId,PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>