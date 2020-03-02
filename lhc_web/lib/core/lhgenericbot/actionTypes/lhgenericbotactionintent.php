<?php

class erLhcoreClassGenericBotActionIntent {

    public static function process($chat, $action, $trigger, $params)
    {

        $payload = '';
        if (isset($params['msg']) && $params['msg'] instanceof erLhcoreClassModelmsg) {
            $payload = $params['msg']->msg;
        } elseif (isset($params['msg_text']) && $params['msg_text'] != '') {
            $payload = $params['msg_text'];
        }

        // We have nothing to do if no message was provided
        if ($payload != '')
        {
            $messageText = mb_strtolower($payload);

            if (isset($action['content']['intents']) && is_array($action['content']['intents'])) {
                foreach ($action['content']['intents'] as $intent) {

                    $wordsFound = true;

                    $wordsTypo = isset($intent['content']['words_typo']) && is_numeric($intent['content']['words_typo']) ? (int)$intent['content']['words_typo'] : 0;
                    $wordsTypoExc = isset($intent['content']['exc_words_typo']) && is_numeric($intent['content']['exc_words_typo']) ? (int)$intent['content']['exc_words_typo'] : 0;

                    // // We should include atleast one word from group
                    if (isset($intent['content']['only_these']) && $intent['content']['only_these'] == true) {
                        if (isset($intent['content']['words']) && $intent['content']['words'] != '') {
                            $words = explode(' ',$messageText);
                            $mustCombinations = explode('&&',$intent['content']['words']);
                            foreach ($words as $messageWord) {
                                foreach ($mustCombinations as $mustCombination) {
                                    if (!erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$messageWord,$wordsTypo)) {
                                        $wordsFound = false;
                                        break;
                                    }
                                }
                            }
                        }
                    } else if (isset($intent['content']['words']) && $intent['content']['words'] != '') {
                        $mustCombinations = explode('&&',$intent['content']['words']);
                        foreach ($mustCombinations as $mustCombination) {
                            if (!erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$messageText,$wordsTypo)) {
                                $wordsFound = false;
                                break;
                            }
                        }
                    }

                    // We should NOT include any of these words
                    if (isset($intent['content']['exc_words']) && $intent['content']['exc_words'] != '') {
                        $mustCombinations = explode('&&',$intent['content']['exc_words']);
                        foreach ($mustCombinations as $mustCombination) {
                            if (erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$messageText,$wordsTypoExc) == true) {
                                $wordsFound = false;
                                break;
                            }
                        }
                    }

                    $validationArgs = array();
                    if (isset($intent['content']['words_match']) && $intent['content']['words_match'] != '') {
                        $rule = str_replace("\r","\n",$intent['content']['words_match']);
                        $rules = array_filter(explode("\n",$rule));
                        foreach ($rules as $ruleItem) {
                            $ruleItemData = explode('==>',$ruleItem);
                            $matches = array();
                            preg_match($ruleItemData[0], $messageText,$matches);
                            if (!empty($matches) && isset($matches[$ruleItemData[1]]) && trim($matches[$ruleItemData[1]]) != '') {
                                $validationArgs[$ruleItemData[2]] = trim($matches[$ruleItemData[1]]);
                                $wordsFound = true;
                            }
                        }
                    }

                    //intention username some
                    if ($wordsFound == true) {
                        if (isset($intent['content']['exec_insta']) && $intent['content']['exec_insta'] == true) {

                            $return = array(
                                'status' => ((isset($intent['content']['exec_cont']) && $intent['content']['exec_cont'] == 1) ? 'continue' : 'stop'),
                                'trigger_id' => $intent['content']['trigger_id'],
                                'validation_args' => $validationArgs
                            );

                            return $return;

                        } else {
                            for ($i = 0; $i < 3; $i++) {
                                try {
                                    $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                                    $pendingAction->chat_id = $chat->id;
                                    $pendingAction->trigger_id = $intent['content']['trigger_id'];
                                    $pendingAction->saveThis();
                                    break;
                                } catch (Exception $e) {
                                    usleep(500);
                                }
                            }
                        }
                    }
                }
            }

            return null;
        }
    }
}

?>