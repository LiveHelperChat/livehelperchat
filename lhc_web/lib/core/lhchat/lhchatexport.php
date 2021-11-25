<?php

class erLhcoreClassChatExport {

	public static function chatExportXML(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/xml.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}

	public static function chatExportJSON(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/json.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}

	public static function exportCannedMessages($messages) {
        $filename = "canned-messages-".date('Y-m-d').".csv";
        $fp = fopen('php://output', 'w');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);

        $counter = 0;
        foreach ($messages as $message) {
            $values = $message->getState();
            $values['subject'] = implode(',',$message->subject_name_front);
            $values['tags_plain'] = $message->tags_plain;
            $values['department_ids_front'] = implode(',',$message->department_ids_front);
            if ($counter == 0) {
                fputcsv($fp, array_keys($values));
            }
            fputcsv($fp, $values);
            $counter++;
        }
        exit;
    }

    public static function exportUsers($users) {
        $filename = "users-".date('Y-m-d').".csv";
        $fp = fopen('php://output', 'w');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);

        $counter = 0;
        foreach ($users as $user) {
            $values = $user->getState();
            unset($values['password']);

            $values['user_groups_id'] = implode(',',$user->user_groups_id);

            if (!empty($user->user_groups_id)) {
                $values['user_groups_name'] = implode(',',erLhcoreClassModelGroup::getList(array('filterin' => array('id' => $user->user_groups_id))));
            } else {
                $values['user_groups_name'] = '';
            }

            $values['dep_groups_id'] = '';
            $values['dep_groups_name'] = '';

            $depGroupIds = $depGroupNames = [];
            $userGroups = erLhcoreClassModelDepartamentGroupUser::getList(array('filter' => array('user_id' => $user->id)));
            foreach ($userGroups as $userGroup) {
                $depGroupIds[] = $userGroup->dep_group_id;
                $depGroupNames[] = (string)$userGroup->dep_group;
            }

            $values['dep_groups_id'] = implode(',',$depGroupIds);
            $values['dep_groups_name'] = implode(',',$depGroupNames);

            $lastLogin = erLhcoreClassModelUserLogin::findOne(array('sort' => 'ctime DESC','filter' => array('user_id' => $user->id)));
            
            $values['last_login'] = $lastLogin instanceof erLhcoreClassModelUserLogin ? date(erLhcoreClassModule::$dateDateHourFormat,$lastLogin->ctime) : '';

            if ($counter == 0) {
                fputcsv($fp, array_keys($values));
            }
            fputcsv($fp, $values);
            $counter++;
        }
        exit;
    }

    public static function exportDepartments($items) {
        $filename = "departments-".date('Y-m-d').".csv";
        $fp = fopen('php://output', 'w');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);

        $counter = 0;
        foreach ($items as $item) {
            $values = $item->getState();

            $botConfiguration = $item->bot_configuration_array;

            $values['bot_id'] = isset($botConfiguration['bot_id']) ? $botConfiguration['bot_id'] : 0;
            $values['bot_name'] = $values['bot_id'] > 0 ? (string)erLhcoreClassModelGenericBotBot::fetch($values['bot_id']) : '';

            $values['bot_translation_group_id'] = isset($botConfiguration['bot_tr_id']) ? $botConfiguration['bot_tr_id'] : 0;
            $values['bot_translation_group_name'] = $values['bot_translation_group_id']  > 0 ? (string)erLhcoreClassModelGenericBotTrGroup::fetch($values['bot_translation_group_id']) : '';

            $memberOfGroups = erLhcoreClassModelDepartamentGroupMember::getList(['filter' => ['dep_id' => $item->id]]);

            $ids = [];
            $names = [];

            foreach ($memberOfGroups as $member) {
                $departmentGroup = erLhcoreClassModelDepartamentGroup::fetch($member->dep_group_id);
                $ids[] = $member->dep_group_id;
                $names[] = (string)$departmentGroup;
            }

            $values['department_group_ids'] = implode(',',$ids);
            $values['department_group_names'] = implode(',',$names);

            unset($values['department_transfer_id']);
            unset($values['transfer_timeout']);

            $values['department_transfer_id'] = $item->department_transfer_id;
            $values['transfer_timeout'] = $item->transfer_timeout;
            $values['department_transfer_name'] = $item->department_transfer_id > 0 ? (string)erLhcoreClassModelDepartament::fetch($item->department_transfer_id) : '';

            if ($counter == 0) {
                fputcsv($fp, array_keys($values));
            }
            fputcsv($fp, $values);
            $counter++;
        }
        exit;
    }

	public static function exportDepartmentStats($departments) {
	    include 'lib/core/lhform/PHPExcel.php';
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			$cacheSettings = array( 'memoryCacheSize ' => '64MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->setActiveSheetIndex(0);
	    $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
	    $objPHPExcel->getActiveSheet()->setTitle('Report');
	    
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "ID");
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department name'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Pending chats number'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Active chats number'));
	    
	    $attributes = array(
	        'id',
	        'name',
	        'pending_chats_counter',
	        'active_chats_counter',
	    );
	    
	    $i = 2;
	    foreach ($departments as $item) {
	        foreach ($attributes as $key => $attr) {
	            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item->{$attr});
	        }
	        $i++;
	    }
	    
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    
	    // We'll be outputting an excel file
	    header('Content-type: application/vnd.ms-excel');
	    
	    // It will be called file.xls
	    header('Content-Disposition: attachment; filename="report.xlsx"');
	    
	    // Write file to the browser
	    $objWriter->save('php://output');
	}

    public static function messagesStatistic(& $itemData, $item)
    {
        $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('chat_id' => $item->id))); // Total messages
        $visitorMessagesCount = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => 0, 'chat_id' => $item->id)));
        $itemData[] =  $visitorMessagesCount; // Visitor messages
        $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => -2, 'chat_id' => $item->id))); // Bot messages
        $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filtergt' => array('user_id' => 0),'filter' => array('chat_id' => $item->id))); // Operator messages
        $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => -1,'chat_id' => $item->id))); // System messages
        // We have a bot assigned
        // Chat does not have an operator OR it has operator and message time is less than chat become pending
        $visitorMessagesBotCount = 0;
        $botMessages = [];
        $agentMessages = [];

        if ($item->gbot_id > 0) {
            // All visitor messages were interactions with bot
            if ($item->user_id == 0) {
                $visitorMessagesBotCount = $visitorMessagesCount;
                $itemData[] = $visitorMessagesBotCount;
                // All interactions were with a bot
                $botMessages = erLhcoreClassModelmsg::getList(array('limit' => false, 'filter' => array('chat_id' => $item->id)));
            } else {
                $botMessages = erLhcoreClassModelmsg::getList(array('limit' => false, 'filterlte' => array('time' => $item->pnd_time),'filter' => array('chat_id' => $item->id)));
                $agentMessages =  erLhcoreClassModelmsg::getList(array('limit' => false, 'filtergt' => array('time' => $item->pnd_time),'filter' => array('chat_id' => $item->id)));
                $visitorMessagesBotCount = erLhcoreClassModelmsg::getCount(array('limit' => false, 'filterlte' => array('time' => $item->pnd_time),'filter' => array('user_id' => 0, 'chat_id' => $item->id)));
                $itemData[] = $visitorMessagesBotCount;
            }
        } else { // There was no bot assigned
            $itemData[] = 0;
            $agentMessages = erLhcoreClassModelmsg::getList(array('limit' => false, 'filter' => array('chat_id' => $item->id)));
        }

        $itemData[] = $visitorMessagesCount - $visitorMessagesBotCount;

        $timesResponse = [];
        $startTime = 0;
        $firstBotResponseTime = 'None';

        foreach ($botMessages as $messageWithABot) {
            if ($messageWithABot->user_id == 0) {
                if ($startTime == 0) {
                    $startTime = $messageWithABot->time;
                }
            } elseif ($messageWithABot->user_id == -2) {
                if ($startTime > 0) {
                    if (empty($timesResponse)){
                        $firstBotResponseTime = $messageWithABot->time - $startTime;
                        $timesResponse[] = $firstBotResponseTime;
                    } else {
                        $timesResponse[] = $messageWithABot->time - $startTime;
                    }

                    $startTime = 0;
                }
            }
        }

        $tillFirstOperatorMessage = 'None';
        $firstAgentResponseTime = 'None';
        $timesResponseAgent = [];
        $startTime = $item->pnd_time;

        foreach ($agentMessages as $agentMessage) {
            if ($agentMessage->user_id == 0) {
                if ($startTime == 0) {
                    $startTime = $agentMessage->time;
                }
            } elseif ($agentMessage->user_id > 0) {
                if ($tillFirstOperatorMessage == 'None') {
                    $tillFirstOperatorMessage = $agentMessage->time - $item->pnd_time;
                    if ($tillFirstOperatorMessage < 0) { // It was operator who first send a message
                        $tillFirstOperatorMessage = 0;
                    }
                }

                if ($startTime > 0) {
                    // It's first agent response
                    if (empty($timesResponseAgent)) {
                        $responseTime = $agentMessage->time - ($item->wait_time + $item->pnd_time);
                        if ($responseTime > 0) {
                            $firstAgentResponseTime = $responseTime;
                            $timesResponseAgent[] = $firstAgentResponseTime;
                        } else {
                            $responseTime = $agentMessage->time - $item->pnd_time;
                            if ($responseTime > 0) {
                                $firstAgentResponseTime = $responseTime;
                                $timesResponseAgent[] = $firstAgentResponseTime;
                            } else {

                                $firstAgentResponseTime = $agentMessage->time - $item->time;

                                // Happens for old proactive chat invitations
                                if ($firstAgentResponseTime < 0) {
                                    $firstAgentResponseTime = 0;
                                }

                                $timesResponseAgent[] = $firstAgentResponseTime;
                            }
                        }
                    } else {
                        $timesResponseAgent[] = $agentMessage->time - $startTime;
                    }
                    $startTime = 0;
                }
            }
        }

        $itemData[] = !empty($timesResponseAgent) ? max($timesResponseAgent) : 'None';
        $itemData[] = !empty($timesResponse) ? max($timesResponse) : 'None';
        $itemData[] = !empty($timesResponseAgent) ? (array_sum($timesResponseAgent)/count($timesResponseAgent)) : 'None';
        $itemData[] = !empty($timesResponse) ? array_sum($timesResponse)/count($timesResponse) : 'None';
        $itemData[] = $firstAgentResponseTime;
        $itemData[] = $firstBotResponseTime;
        $itemData[] = $tillFirstOperatorMessage;
    }

	public static function chatListExportXLS($chats, $params = array()) {

		include 'lib/core/lhform/PHPExcel.php';
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize ' => '64MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$chatArray = array();
		
		$id = "ID";
		$name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor Name');
		$email = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','E-mail');
		$phone = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Phone');
		$wait = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Wait time');
		$waitAbandoned = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Wait time abandoned');
		$country = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Country');
		$countryCode = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Country Code');
		$city = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','City');
		$ip = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','IP');
		$operator = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator');
		$operatorName = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator Name');
		$dept = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department');
		$date = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Date');
		$minutes = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Minutes');
		$vote = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Vote status');
		$subjects = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Subjects');
		$mail = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Mail send');
		$page = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Page');
		$from = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Came from');
		$link = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Link');
		$remarks = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Remarks');
		$visitorRemarks = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor remarks');
		$device = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Device');
		$visitorID = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor ID');
		$duration = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Duration');
		$chat_initiator = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Started by');
		$browser = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','User agent');
		$browserBrand = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Browser');
		$platform = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Platform');
        $user_id_op = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','User ID');
        $referrer = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat start page');      // Page visitor started a chat
        $session_referrer = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Referer page'); // Page from which visitor come to website
        $chat_start_time = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat start time');
        $chat_end_time = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat end time');
        $is_unread = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Is unread by operator');
        $is_unread_visitor = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Is unread by visitor');
        $is_abandoned = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Is abandoned');
        $bot = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Bot');
        $chat_actions = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat actions');

		$additionalDataPlain = array();
		for ($i = 1; $i <= 20; $i++) {
            $additionalDataPlain[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Additional data').' - '.$i;
        }

		$additionalData = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Additional data');

        $survey = array();
        for ($i = 1; $i <= 20; $i++) {
            $survey[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Survey data').' - '.$i;
        }

		$mainColumns = array($id, $name, $email, $phone, $wait, $waitAbandoned, $country, $countryCode, $city, $ip, $operator, $operatorName, $user_id_op, $dept, $date, $minutes, $vote, $mail, $page, $from, $link, $remarks, $visitorRemarks, $subjects, $is_unread, $is_unread_visitor, $is_abandoned, $bot, $chat_actions, $device, $visitorID, $duration, $chat_initiator, $browser, $browserBrand, $platform, $referrer, $session_referrer, $chat_start_time, $chat_end_time);

		if (isset($params['type']) && in_array(2,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat content');
        }

        if (isset($params['type']) && in_array(6, $params['type'])) {
            $chatVariables = erLhAbstractModelChatVariable::getList();
            foreach ($chatVariables as $chatVariable){
                $mainColumns[] = $chatVariable->var_name;
            }
        }

        if (isset($params['type']) && in_array(4,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Bot messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','System messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages to bot');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages to operator');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Maximum agent response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Maximum bot response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average agent response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average bot response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','First agent response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','First bot response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Wait time till first operator message');
        }

        if (isset($params['type']) && in_array(5,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Subject');
        }

		if (isset($params['type']) && in_array(3,$params['type'])) {
            $mainColumns = array_merge($mainColumns,$survey);
        }

        $chatArray[] = array_merge($mainColumns, $additionalDataPlain, array($additionalData));

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.list_export_columns',array('items' => & $chatArray));

		if ($params['csv'] && $params['csv'] == true) {
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // force download
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename=report.csv");
            header("Content-Transfer-Encoding: binary");

            $df = fopen("php://output", 'w');

            // First row
            fputcsv($df, $chatArray[0]);
        }

        foreach ($chats as $item) {
                $item = erLhcoreClassModelChat::fetch($item->id, false);
                $id = (string)$item->{'id'};
                $nick = (string)$item->{'nick'};
                $email = (string)$item->{'email'};
                $phone = (string)$item->{'phone'};
                $wait = (string)$item->{'wait_time'};
                $country = (string)$item->{'country_name'};
                $countryCode = (string)$item->{'country_code'};
                $city = (string)$item->{'city'};
                $ip = (string)$item->{'ip'};
                $user = (string)$item->{'user'};
                $operatorName = (string)$item->{'n_off_full'};
                $user_id_op = (string)$item->{'user_id'};
                $dept = (string)$item->{'department'};
                $remarks = (string)$item->{'remarks'};
                $device = (string)$item->{'device_type'} == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ((string)$item->{'device_type'} == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mobile') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'));
                $visitorID = (string)$item->online_user_id;
                $duration = (string)$item->chat_duration;
                $chat_initiator = $item->chat_initiator == erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT ? 'visitor' : 'proactive';
                $browser = (string)$item->uagent;
                $visitorRemarks = $item->online_user_id > 0 && ($onlineUser = erLhcoreClassModelChatOnlineUser::fetch($item->online_user_id, false)) && $onlineUser instanceof erLhcoreClassModelChatOnlineUser ? $onlineUser->notes : '';

                $detect = new BrowserDetection;
                $OSDetails = $detect->getOS($item->uagent);
                $browserDetails = $detect->getBrowser($item->uagent);

                $osFamily = isset($OSDetails['os_family']) ? $OSDetails['os_family'] : 'Unknown';
                $browserBrand = isset($browserDetails['browser_name']) ? $browserDetails['browser_name'] : 'Unknown';

                $referrer = (string)$item->referrer;
                $session_referrer = (string)$item->session_referrer;
                $chat_start_time = date('Y-m-d H:i:s',$item->time);
                $chat_end_time = $item->cls_time > 0 ? date('Y-m-d H:i:s',$item->cls_time) : '';

                $subjects = implode(',',erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $item->id))));
                $is_unread = (int)$item->has_unread_messages;
                $is_unread_visitor = (int)$item->has_unread_op_messages;
                $is_abandoned = ($item->lsync < ($item->pnd_time + $item->wait_time) && $item->wait_time > 1) || ($item->lsync > ($item->pnd_time + $item->wait_time) && $item->wait_time > 1 && $item->user_id == 0) ? 1 : 0;
                $waitAbandoned = 'None';

                if ($is_abandoned == true) {
                    $waitAbandoned = ($item->cls_time > 0 ? $item->cls_time : time()) - $item->pnd_time;
                }

                $bot = (string)$item->bot;

                $date = date(erLhcoreClassModule::$dateFormat,$item->time);
                $minutes = date('H:i:s',$item->time);
                $vote = ($item->fbst == 1 ? 'UP' : ($item->fbst == 2 ? 'DOWN' : 'NONE'));
                $mail = $item->mail_send == 1 ? 'Yes' : 'No';
                $page = $item->referrer;
                $additionalDataContent = $item->additional_data;

                // Create empty array of 20 to make sure all are filled
                $urlData = array();
                $pairsRegular = array();
                if (!empty($additionalDataContent)) {
                    foreach (json_decode($additionalDataContent,true) as $index => $additionalItem) {
                        if (isset($additionalItem['url']) && $additionalItem['url'] == true) {
                            $urlData[] = $additionalItem['key'] . ' - ' . $additionalItem['value'];
                        } else {
                            $pairsRegular[] = (isset($additionalItem['key']) ? $additionalItem['key'] : '') . ' - ' . (isset($additionalItem['value']) ? $additionalItem['value'] : '');
                        }

                    }
                }
                       
                // Put URL arguments always first
                $additionalPairs = array_merge($urlData,$pairsRegular);
                $additionalPairs = array_merge($additionalPairs,array_fill(count($additionalPairs),20-count($additionalPairs),''));

                if ($item->session_referrer != '') {
                        $referer = parse_url($item->session_referrer);                    
                        if (isset($referer['host'])) {
                            $from = $referer['host'];
                        } else {
                        	$from = null;
                        }
                } else {
                	$from = null;
                }

                $url = erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('user/login').'/(r)/'.rawurlencode(base64_encode('chat/single/'.$item->id));

                $chat_actions = '';

                foreach (erLhcoreClassModelChatAction::getList(['sort' => 'id ASC', 'limit' => false, 'filter' => ['chat_id' => $item->id]]) as $chatActionItem) {
                    $chat_actions .= $chatActionItem->body."\n";
                }

                $itemData = array($id, $nick, $email, $phone, $wait, $waitAbandoned, $country, $countryCode, $city, $ip, $user, $operatorName, $user_id_op, $dept, $date, $minutes, $vote, $mail, $page, $from, $url, $remarks, $visitorRemarks, $subjects, $is_unread, $is_unread_visitor, $is_abandoned, $bot, trim($chat_actions), $device, $visitorID, $duration, $chat_initiator, $browser, $browserBrand, $osFamily, $referrer, $session_referrer, $chat_start_time, $chat_end_time);

                // Print chat content to last column
                if (isset($params['type']) && in_array(2,$params['type'])) {

                    $messages = erLhcoreClassModelmsg::getList(array('limit' => 10000,'sort' => 'id ASC','filter' => array('chat_id' => $item->id)));                       
                    $messagesContent = '';

                    foreach ($messages as $msg ) {
                        if ($msg->user_id == -1) {
                                $messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
                        } else {
                                $messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($item->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
                        }
                    }
                    $itemData[] = trim($messagesContent);
                }

                if (isset($chatVariables)) {
                    foreach ($chatVariables as $chatVariable) {
                        if ($chatVariable->inv == true) {
                            $chatVariablesPassed = $item->chat_variables_array;
                        } else {
                            foreach ($item->additional_data_array as $chatVariablePassed) {
                                if (isset($chatVariablePassed['identifier'])){
                                    $chatVariablesPassed[$chatVariablePassed['identifier']] = $chatVariablePassed['value'];
                                }

                            }
                        }

                        $valueVariable = '';

                        if (isset($chatVariablesPassed[$chatVariable->var_identifier])){
                            $valueVariable = $chatVariablesPassed[$chatVariable->var_identifier];
                        }

                        $itemData[] = $valueVariable;
                    }
                }

                if (isset($params['type']) && in_array(4,$params['type'])) {
                    self::messagesStatistic($itemData, $item);
                }

                if (isset($params['type']) && in_array(5,$params['type'])) {
                    $subjects = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $item->id)));
                    $subjectValue = [];
                    foreach ($subjects as $subject) {
                        $subjectValue[] = (string)$subject->subject;
                    }
                    $itemData[] = implode("\n",$subjectValue);
                }

                if (isset($params['type']) && in_array(3,$params['type'])) {
                    $surveyItem = erLhAbstractModelSurveyItem::findOne(array('filter' => array('chat_id' =>$item->{'id'})));
                    if ($surveyItem instanceof erLhAbstractModelSurveyItem){
                        $survey = erLhAbstractModelSurvey::fetch($surveyItem->survey_id);
                        $exported = erLhcoreClassSurveyExporter::exportRAW(array($surveyItem),$survey);
                        $pairs = array_fill(0,20,'');
                        $i = 0;
                        foreach ($exported['value'] as $valueItems) {
                            foreach ($exported['title'] as $indexColumn => $columnName) {
                                $pairs[$i] = $columnName . ' - ' . $valueItems[$indexColumn];
                                $i++;
                            }
                        }
                        $itemData = array_merge($itemData,$pairs);
                    } else {
                        $itemData = array_merge($itemData,array_fill(0,20,''));
                    }
                }

                $itemData = array_merge($itemData, $additionalPairs, array($additionalDataContent));

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.list_export_item_data',array('item' => & $itemData, 'chat' => $item));

                if ($params['csv'] && $params['csv'] == true) {
                    fputcsv($df, $itemData);
                } else {
                    $chatArray[] = $itemData;
                }
        }

        if ($params['csv'] && $params['csv'] == true) {
            fclose($df);
        } else {
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            // Set the starting point and array of data
            $objPHPExcel->getActiveSheet()->fromArray($chatArray, null, 'A1');

            // Set style for top row
            $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);

            // Set file type and name of file
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="report.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $writer->save('php://output');
        }
	}
}

?>
