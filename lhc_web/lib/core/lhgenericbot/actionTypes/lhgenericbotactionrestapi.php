<?php

class erLhcoreClassGenericBotActionRestapi
{

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['rest_api']) && is_numeric($action['content']['rest_api']) && isset($action['content']['rest_api_method']) && !empty($action['content']['rest_api_method'])) {

            $restAPI = erLhcoreClassModelGenericBotRestAPI::fetch($action['content']['rest_api']);

            if ($restAPI instanceof erLhcoreClassModelGenericBotRestAPI) {
                $method = false;
                foreach ($restAPI->configuration_array['parameters'] as $parameter) {
                    if ($action['content']['rest_api_method'] == $parameter['id']) {
                        $method = $parameter;
                    }
                }

                // Within next user message we will validate his username or anything else
                if ((isset($action['content']['attr_options']['background_process']) && $action['content']['attr_options']['background_process'] == true)) {

                    $event = new erLhcoreClassModelGenericBotChatEvent();
                    $event->chat_id = $chat->id;
                    $event->ctime = time();
                    $event->content = json_encode(array('callback_list' => array(
                        array(
                            'content' => array(
                                'type' => 'rest_api',
                                'replace_array' => (isset($params['replace_array']) ? $params['replace_array'] : array()),
                                'action' => $action,
                                'msg_id' => (isset($params['msg']) && is_object($params['msg']) ? $params['msg']->id : 0),
                                'msg_text' => (isset($params['msg_text']) ? $params['msg_text'] : ''),
                                'event' => (isset($action['content']['event']) ? $action['content']['event'] : null),
                            )
                        )
                    )));

                    // Save only if user has resque extension
                    if ((!isset($params['do_not_save']) || $params['do_not_save'] == false) && class_exists('erLhcoreClassExtensionLhcphpresque')) {
                        $event->saveThis();
                        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_rest_api_queue', 'erLhcoreClassLHCBotWorker', array('action' => 'rest_api', 'event_id' => $event->id));
                        return ;
                    }
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_before_request', array(
                    'restapi' => & $restAPI,
                    'chat' => $chat
                ));

                $response = self::makeRequest($restAPI->configuration_array['host'], $method, array('action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));

                // We have found exact matching response type
                // Let's check has user checked any trigger to execute.
                if (isset($response['id'])) {
                    if (isset($action['content']['rest_api_method_output'][$response['id']]) && is_numeric($action['content']['rest_api_method_output'][$response['id']])) {
                        return array(
                            'status' => 'continue_all',
                            'replace_array' => array(
                                '{content_1}' => $response['content'],
                                '{content_2}' => $response['content_2'],
                                '{content_3}' => $response['content_3'],
                                '{content_4}' => $response['content_4'],
                                '{content_5}' => $response['content_5'],
                                '{content_6}' => $response['content_6'],
                                '{http_code}' => $response['http_code']
                            ),
                            'meta_msg' => $response['meta'],
                            'trigger_id' => $action['content']['rest_api_method_output'][$response['id']]
                        );
                    } else {
                        // Do nothing as user did not chose any trigger to execute
                    }
                } elseif (isset($action['content']['rest_api_method_output']['default_trigger']) && is_numeric($action['content']['rest_api_method_output']['default_trigger'])) {
                    return array(
                        'status' => 'continue_all',
                        'replace_array' => array(
                            '{content_1}' => $response['content'],
                            '{content_2}' => $response['content_2'],
                            '{content_3}' => $response['content_3'],
                            '{content_4}' => $response['content_4'],
                            '{content_5}' => $response['content_5'],
                            '{content_6}' => $response['content_6'],
                            '{http_code}' => $response['http_code']
                        ),
                        'meta_msg' => $response['meta'],
                        'trigger_id' => $action['content']['rest_api_method_output']['default_trigger']
                    );
                }

                if ($response['content'] != '' || (isset($response['meta']) && !empty($response['meta']))){

                    $msg = new erLhcoreClassModelmsg();
                    $msg->chat_id = $chat->id;
                    $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
                    $msg->user_id = -2;
                    $msg->time = time() + 5;
                    $msg->meta_msg = (isset($response['meta']) && !empty($response['meta'])) ? json_encode($response['meta']) : '';
                    $msg->msg = $response['content'];

                    if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                        $msg->saveThis();
                    }

                    return  $msg;
                }
            }
        }
    }

    public static function makeRequest($host, $methodSettings, $paramsCustomer)
    {

        $msg_text = '';
        $msg_text_cleaned = '';

        if (isset($paramsCustomer['params']['msg'])) {
            $msg_text_cleaned = $msg_text = $paramsCustomer['params']['msg']->msg;
        } elseif (isset($paramsCustomer['params']['msg_text'])) {
            $msg_text_cleaned = $msg_text = $paramsCustomer['params']['msg_text'];
        }

        // We have to extract attached files and send them separately
        $matches = array();
        preg_match_all('/\[file="?(.*?)"?\]/', $msg_text, $matches);

        $media = array();

        foreach ($matches[1] as $index => $body) {
            $parts = explode('_', $body);
            $fileID = $parts[0];
            $hash = $parts[1];
            try {
                $file = erLhcoreClassModelChatFile::fetch($fileID);
                if (is_object($file) && $hash == $file->security_hash) {

                    if (isset($_SERVER['HTTP_HOST'])) {
                        $url = (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";
                    } else {
                        $url = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->settings['site_address'] . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";
                    }

                    $media[] = array(
                        'id' => $file->id,
                        'size' => $file->size,
                        'upload_name' => $file->upload_name,
                        'type' => $file->type,
                        'extension' => $file->extension,
                        'hash' => $hash,
                        'url' => $url,
                    );

                    $msg_text_cleaned = str_replace($matches[0][$index],'',$msg_text_cleaned);
                }

            } catch (Exception $e) {

            }
        }

        $replaceVariables = array(
            '{{msg}}' => $msg_text,
            '{{msg_clean}}' => trim($msg_text_cleaned),
            '{{msg_url}}' => erLhcoreClassBBCodePlain::make_clickable($msg_text, array('sender' => 0)),
            '{{chat_id}}' => $paramsCustomer['chat']->id,
            '{{lhc.nick}}' =>$paramsCustomer['chat']->nick,
            '{{lhc.email}}' => $paramsCustomer['chat']->email,
            '{{lhc.department}}' => (string)$paramsCustomer['chat']->department,
            '{{lhc.dep_id}}' => (string)$paramsCustomer['chat']->dep_id,
            '{{ip}}' => (string)erLhcoreClassIPDetect::getIP(),
            '{{media}}' => json_encode($media)
        );

        $replaceVariablesJSON = array(
            '{{msg}}' => json_encode($msg_text),
            '{{msg_clean}}' => json_encode(trim($msg_text_cleaned)),
            '{{msg_url}}' => json_encode(erLhcoreClassBBCodePlain::make_clickable($msg_text, array('sender' => 0))),
            '{{chat_id}}' => json_encode($paramsCustomer['chat']->id),
            '{{lhc.nick}}' => json_encode($paramsCustomer['chat']->nick),
            '{{lhc.email}}' => json_encode($paramsCustomer['chat']->email),
            '{{lhc.department}}' => json_encode((string)$paramsCustomer['chat']->department),
            '{{lhc.dep_id}}' => json_encode((string)$paramsCustomer['chat']->dep_id),
            '{{ip}}' => json_encode(erLhcoreClassIPDetect::getIP()),
            '{{media}}' => json_encode($media),
        );

        foreach ($paramsCustomer['chat']->additional_data_array as $keyItem => $addItem) {
            if (!is_string($addItem) || (is_string($addItem) && ($addItem != ''))) {
                if (isset($addItem['identifier'])) {
                    if (is_string($addItem['value']) || is_numeric($addItem['value'])) {
                        $replaceVariables['{{lhc.add.' . $addItem['identifier'] . '}}'] = $addItem['value'];
                        $replaceVariablesJSON['{{lhc.add.' . $addItem['identifier'] . '}}'] = json_encode($addItem['value']);
                    }
                } else if (isset($addItem['key'])) {
                    if (is_string($addItem['value']) || is_numeric($addItem['value'])) {
                        $replaceVariables['{{lhc.add.' . $addItem['key'] . '}}'] = $addItem['value'];
                        $replaceVariablesJSON['{{lhc.add.' . $addItem['key'] . '}}'] = json_encode($addItem['value']);
                    }
                }
            }
        }

        foreach ($paramsCustomer['chat']->chat_variables_array as $keyItem => $addItem) {
            if (is_string($addItem) || is_numeric($addItem)) {
                $replaceVariables['{{lhc.var.' . $keyItem . '}}'] = $addItem;
                $replaceVariablesJSON['{{lhc.var.' . $keyItem . '}}'] = json_encode($addItem);
            }
        }

        $queryArgs = array();

        if (isset($methodSettings['query']) && !empty($methodSettings['query'])) {
            foreach ($methodSettings['query'] as $dataQuery) {
                $queryArgs[$dataQuery['key']] = str_replace(array_keys($replaceVariables), array_values($replaceVariables), $dataQuery['value']);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if (isset($methodSettings['method']) && ($methodSettings['method'] == 'PUT' || $methodSettings['method'] == 'DELETE')) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methodSettings['method']);
        }

        $headers = array();

        if (isset($methodSettings['header']) && !empty($methodSettings['header'])) {
            foreach ($methodSettings['header'] as $header) {
                $headers[] = $header['key'] . ': ' . $header['value'];
            }
        }

        if (isset($methodSettings['method']) && $methodSettings['method'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'basicauth') {
            curl_setopt($ch, CURLOPT_USERPWD, $methodSettings['auth_username'] . ":" . $methodSettings['auth_password']);
        } elseif (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'bearer' && isset($methodSettings['auth_bearer']) && $methodSettings['auth_bearer'] != '') {
            $headers[] = 'Authorization: Bearer ' . $methodSettings['auth_bearer'];
        } else if (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'apikey') {
            if ($methodSettings['api_key_location'] == 'header' && isset($methodSettings['auth_api_key_key']) && isset($methodSettings['auth_api_key_name'])) {
                $headers[] = $methodSettings['auth_api_key_key'] . ': ' . $methodSettings['auth_api_key_name'];
            } else if ($methodSettings['api_key_location'] == 'queryparams') {
                $queryArgs[$methodSettings['auth_api_key_key']] = $methodSettings['auth_api_key_name'];
            }
        }

        if (isset($methodSettings['userparams']) && !empty($methodSettings['userparams'])) {
            foreach ($methodSettings['userparams'] as $userParam) {

                $valueParam = '';

                if (isset($paramsCustomer['action']['content']['rest_api_method_params'][$userParam['id']]) && !empty($paramsCustomer['action']['content']['rest_api_method_params'][$userParam['id']])) {
                    $valueParam = $paramsCustomer['action']['content']['rest_api_method_params'][$userParam['id']];
                }

                if (!isset($userParam['location']) || $userParam['location'] == '') {
                    $queryArgs[$userParam['key']] = $valueParam;
                } elseif (isset($userParam['location']) && $userParam['location'] == 'post_param') {
                    $postParams[$userParam['key']] = $valueParam;
                } elseif (isset($userParam['location']) && $userParam['location'] == 'body_param') {
                    $methodSettings['body_raw'] = str_replace('{{' . $userParam['key'] . '}}', json_encode($valueParam), $methodSettings['body_raw']);
                }
            }
        }


        if (isset($methodSettings['body_request_type']) && $methodSettings['body_request_type'] == 'form-data') {
            if (isset($methodSettings['postparams']) && !empty($methodSettings['postparams'])) {
                $postParams = array();
                foreach ($methodSettings['postparams'] as $postParam) {
                    $postParams[$postParam['key']] = str_replace(array_keys($replaceVariables), array_values($replaceVariables), $postParam['value']);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
            }
        } elseif (isset($methodSettings['body_request_type']) && $methodSettings['body_request_type'] == 'raw') {
            $bodyPOST = str_replace(array_keys($replaceVariablesJSON), array_values($replaceVariablesJSON), $methodSettings['body_raw']);
            $bodyPOST = preg_replace('/{{lhc\.(var|add)\.(.*?)}}/','""',$bodyPOST);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyPOST);
        }

        $url = rtrim($host) . str_replace(array_keys($replaceVariables), array_values($replaceVariables), (isset($methodSettings['suburl']) ? $methodSettings['suburl'] : '')) . '?' . http_build_query($queryArgs);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $additionalError = ' [ERR: ' . curl_error($ch) . '] ';
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (isset($methodSettings['output']) && !empty($methodSettings['output'])) {

            $allOptional = true;

            // First check is there any required to check combinations and disable others if so.
            foreach ($methodSettings['output'] as $index => $outputCombination)
            {
                if (isset($paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk']) && $paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk'] == true) {
                    $allOptional = false;
                    break;
                }
            }

            foreach ($methodSettings['output'] as $outputCombination)
            {
                if ($allOptional == false && (!isset($paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk']) || $paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk'] == false)) {
                    // One of the conditions is checked, but not this one.
                    continue;
                }

                // Verify HTTP Status code
                if (!isset($outputCombination['success_header']) || $outputCombination['success_header'] == '' || in_array((string)$httpcode,explode(',',$outputCombination['success_header']))){

                    if (isset($outputCombination['success_location']) && $outputCombination['success_location'] != '') {

                        if (isset($outputCombination['format']) && $outputCombination['format'] == 'xml') {
                            $contentJSON = json_decode(json_encode(simplexml_load_string($content)),true);
                        } else {
                            $contentJSON = json_decode($content, true);
                        }

                        $successLocation = self::extractAttribute($contentJSON, $outputCombination['success_location']);

                        if ($successLocation['found'] === true) {

                            $responseValueSub = array();
                            for ($i = 2; $i <= 6; $i++){
                                if (isset($outputCombination['success_location_' . $i]) && $outputCombination['success_location_' . $i] != '') {
                                    $successLocationNumbered = self::extractAttribute($contentJSON,$outputCombination['success_location_' . $i]);
                                    if ($successLocationNumbered['found'] === true) {
                                        $responseValueSub[$i] = $successLocationNumbered['value'];
                                    }
                                }
                            }

                            $responseValueCompare = $responseValue = $successLocation['value'];
                            if (isset($outputCombination['success_condition_val']) && !empty($outputCombination['success_condition_val'])) {
                                $responseValueCompareLocation = self::extractAttribute($contentJSON, $outputCombination['success_condition_val']);
                                if ($responseValueCompareLocation['found'] === true) {
                                    $responseValueCompare = $responseValueCompareLocation['value'];
                                } else {
                                    // Attribute was not found
                                    continue;
                                }
                            }
                        } else {
                            continue; // Required attribute was not found
                        }
                    } else {
                        $responseValueCompare = $responseValue = $content;
                    }

                    if (isset($outputCombination['success_condition']) && $outputCombination['success_condition'] != '' && isset($outputCombination['success_compare_value']) && $outputCombination['success_compare_value'] != '') {
                        if ( $outputCombination['success_condition'] == 'eq' && !($responseValueCompare == $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'lt' && !($responseValueCompare < $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'lte' && !($responseValueCompare <= $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'neq' && !($responseValueCompare != $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'gte' && !($responseValueCompare >= $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'gt' && !($responseValueCompare > $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$outputCombination['success_compare_value']),$responseValueCompare,0) == false) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$outputCombination['success_compare_value']),$responseValueCompare,0) == true) {
                            continue;
                        }
                    }

                    $meta = array();

                    if (isset($outputCombination['success_location_meta']) && $outputCombination['success_location_meta'] != '') {
                        $contentJSON = json_decode($content,true);
                        $metaResponse = self::extractAttribute($contentJSON, $outputCombination['success_location_meta']);

                        if ($metaResponse['found'] == true) {
                            $meta = $metaResponse['value'];
                        }
                    }

                    return array(
                        'content' => $responseValue,
                        'http_code' => $httpcode,
                        'content_2' => (isset($responseValueSub[2]) ? $responseValueSub[2] : ''),
                        'content_3' => (isset($responseValueSub[3]) ? $responseValueSub[3] : ''),
                        'content_4' => (isset($responseValueSub[4]) ? $responseValueSub[4] : ''),
                        'content_5' => (isset($responseValueSub[5]) ? $responseValueSub[5] : ''),
                        'content_6' => (isset($responseValueSub[6]) ? $responseValueSub[6] : ''),
                        'meta' => $meta,
                        'id' => $outputCombination['id']);
                }
            }

            // We did not found matching response. Return everything.
            return array(
                'content' => $content,
                'http_code' => $httpcode,
                'content_2' => '',
                'content_3' => '',
                'content_4' => '',
                'content_5' => '',
                'content_6' => '',
                'meta' => array()
            );
        }

        return array(
            'content' => $content,
            'http_code' => $httpcode,
            'content_2' => '',
            'content_3' => '',
            'content_4' => '',
            'meta' => array()
        );
    }

    public static function extractAttribute($partData, $string)
    {
        $parts = explode(':', $string);

        $partFound = true;
        foreach ($parts as $part) {

            if (strpos($part,'[') === 0) {

                $conditions = explode('=', str_replace(['[',']'],'',$part));

                $foundConditions = false;
                foreach ($partData as $partItem) {
                    if ($partItem[$conditions[0]] == $conditions[1]) {
                        $partData = $partItem;
                        $foundConditions = true;
                        continue;
                    }
                }

                if ($foundConditions == false) {
                    $partFound = false;
                    break;
                }

            } else {
                if (isset($partData[$part]) ) {
                    $partData = $partData[$part];
                } else {
                    $partFound = false;
                    break;
                }
            }
        }

        return array('found' => $partFound, 'value' => $partData);
    }

}

?>