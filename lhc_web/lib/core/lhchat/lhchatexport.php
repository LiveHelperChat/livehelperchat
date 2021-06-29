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
		$country = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Country');
		$city = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','City');
		$ip = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','IP');
		$operator = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator');
		$dept = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department');
		$date = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Date');
		$minutes = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Minutes');
		$vote = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Vote status');
		$mail = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Mail send');
		$page = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Page');
		$from = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Came from');
		$link = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Link');
		$remarks = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Remarks');
		$device = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Device');

		$additionalDataPlain = array();
		for ($i = 1; $i <= 20; $i++) {
            $additionalDataPlain[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Additional data').' - '.$i;
        }

		$additionalData = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Additional data');

        $survey = array();
        for ($i = 1; $i <= 20; $i++) {
            $survey[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Survey data').' - '.$i;
        }

        $surveyData = array();

		$mainColumns = array($id, $name, $email, $phone, $wait, $country, $city, $ip, $operator, $dept, $date, $minutes, $vote, $mail, $page, $from, $link, $remarks, $device);

		if (isset($params['type']) && in_array(2,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat content');
        }

        if (isset($params['type']) && in_array(4,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Bot messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','System messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages to bot');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages to operator');
        }

        if (isset($params['type']) && in_array(5,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Subject');
        }

		if (isset($params['type']) && in_array(3,$params['type'])) {
            $mainColumns = array_merge($mainColumns,$survey);
            $surveyData = erLhAbstractModelSurveyItem::getList(array_merge(array('filterin' => array('chat_id' => array_keys($chats)), 'offset' => 0, 'limit' => 100000)));
        }

        $chatArray[] = array_merge($mainColumns, $additionalDataPlain, array($additionalData));

        $exportChatData = array();
        foreach ($surveyData as $surveyItem)
        {
            $survey = erLhAbstractModelSurvey::fetch($surveyItem->survey_id);
            $exported = erLhcoreClassSurveyExporter::exportRAW(array($surveyItem),$survey);

            $pairs = array_fill(0,20,'');

            $i = 0;
            foreach ($exported['value'] as $chatId => $valueItems) {
                foreach ($exported['title'] as $indexColumn => $columnName) {
                    $pairs[$i] = $columnName . ' - ' . $valueItems[$indexColumn];
                    $i++;
                }
            }

            $exportChatData[$surveyItem->chat_id] = $pairs;
        }

        foreach ($chats as $item) {
                $id = (string)$item->{'id'};
                $nick = (string)$item->{'nick'};
                $email = (string)$item->{'email'};
                $phone = (string)$item->{'phone'};
                $wait = (string)$item->{'wait_time'};
                $country = (string)$item->{'country_name'};
                $city = (string)$item->{'city'};
                $ip = (string)$item->{'ip'};
                $user = (string)$item->{'user'};
                $dept = (string)$item->{'department'};
                $remarks = (string)$item->{'remarks'};
                $device = (string)$item->{'device_type'} == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ((string)$item->{'device_type'} == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mobile') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'));

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
                            $pairsRegular[] = $additionalItem['key'] . ' - ' . $additionalItem['value'];
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

                $itemData = array($id, $nick, $email, $phone, $wait, $country, $city, $ip, $user, $dept, $date, $minutes, $vote, $mail, $page, $from, $url, $remarks, $device);

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

                if (isset($params['type']) && in_array(4,$params['type'])) {
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('chat_id' => $item->id))); // Total messages
                    $visitorMessagesCount = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => 0, 'chat_id' => $item->id)));
                    $itemData[] =  $visitorMessagesCount; // Visitor messages
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => -2, 'chat_id' => $item->id))); // Bot messages
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filtergt' => array('user_id' => 0),'filter' => array('chat_id' => $item->id))); // Operator messages
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => -1,'chat_id' => $item->id))); // System messages
                    // We have a bot assigned
                    // Chat does not have an operator OR it has operator and message time is less than chat become pending
                    $visitorMessagesBotCount = 0;
                    if ($item->gbot_id > 0) {
                        // All visitor messages were interactions with bot
                        if ($item->user_id == 0) {
                            $visitorMessagesBotCount = $visitorMessagesCount;
                            $itemData[] = $visitorMessagesBotCount;
                        } else {
                            $visitorMessagesBotCount = erLhcoreClassModelmsg::getCount(array('limit' => false, 'filterlte' => array('time' => $item->pnd_time),'filter' => array('user_id' => 0, 'chat_id' => $item->id)));
                            $itemData[] = $visitorMessagesBotCount;
                        }
                    } else { // There was no bot assigned
                        $itemData[] = 0;
                    }

                    $itemData[] = $visitorMessagesCount - $visitorMessagesBotCount;
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
                    $itemData = array_merge($itemData, isset($exportChatData[$item->id]) ? $exportChatData[$item->id] : array_fill(0,20,''));
                }

                $itemData = array_merge($itemData, $additionalPairs, array($additionalDataContent));

                $chatArray[] = $itemData;
        }

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
            /*fputcsv($df, array_keys(reset($array)));*/
            foreach ($chatArray as $row) {
                fputcsv($df, $row);
            }
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
