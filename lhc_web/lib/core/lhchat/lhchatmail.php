<?php

class erLhcoreClassChatMail {

	public static function setupSMTP(PHPMailer & $phpMailer)
	{
        // Allow extension override mail settings
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatmail.setup_smtp', array(
            'phpmailer' => & $phpMailer
        ));
        
        if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
            return;
        }
	    
	        
		$smtpData = erLhcoreClassModelChatConfig::fetch('smtp_data');
		$data = (array)$smtpData->data;

		if ( isset($data['sender']) && $data['sender'] != '' ) {
		    $phpMailer->Sender = $data['sender'];
		}
		
		if ($phpMailer->From == 'root@localhost') {
		    $phpMailer->From = $data['default_from'];
		}
		
		if ($phpMailer->FromName == 'Root User') {
		    $phpMailer->FromName = $data['default_from_name'];
		}
		
		if ( isset($data['use_smtp']) && $data['use_smtp'] == 1 ) {
			$phpMailer->IsSMTP();
			$phpMailer->Host = $data['host'];
			$phpMailer->Port = $data['port'];
			
			if ($data['username'] != '' && $data['password'] != '') {			
				$phpMailer->Username = $data['username'];
				$phpMailer->Password = $data['password'];
				$phpMailer->SMTPAuth = true;
				$phpMailer->From = isset($data['default_from']) ? $data['default_from'] : $data['username'];
			} else {
			    $phpMailer->From = '';
			}			
		}
	}

	public static function sendTestMail($userData) {

		$mail = new PHPMailer(true);
		$mail->CharSet = "UTF-8";
		$mail->Subject = 'LHC Test mail';
		$mail->AddReplyTo($userData->email,(string)$userData);
		$mail->Body = 'This is test mail. If you received this mail. That means that your mail settings is correct.';
		$mail->AddAddress( $userData->email );

		self::setupSMTP($mail);
		
		try {
			return $mail->Send();
		} catch (Exception $e) {
			throw $e;
		}
		$mail->ClearAddresses();
	}

	// Prepare template variables
    public static function prepareSendMail(erLhAbstractModelEmailTemplate & $sendMail, $chat)
    {
    	$currentUser = erLhcoreClassUser::instance();
    	
    	if ($currentUser->isLogged() == true){    	
	    	$userData = $currentUser->getUserData();  	
	    		    	    	
	    	$sendMail->subject = str_replace(array('{name_surname}','{department}'),array($userData->name.' '.$userData->surname, (string)$chat->department), $sendMail->subject);
	    	$sendMail->from_name = str_replace(array('{name_surname}','{department}'),array($userData->name.' '.$userData->surname, (string)$chat->department), $sendMail->from_name);
	
	    	if (empty($sendMail->from_email)) {
	    		$sendMail->from_email = $userData->email;
	    	}
	
	    	if (empty($sendMail->reply_to)) {
	    		$sendMail->reply_to = $userData->email;
	    	}
    	}
    }

    // Validate send mail
    public static function validateSendMail(erLhAbstractModelEmailTemplate & $sendMail, & $chat, $params = array())
    {
    	$Errors = array();

    	$validationFields = array();
    	$validationFields['Message'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
    	$validationFields['Subject'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
    	$validationFields['FromName'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
    	$validationFields['FromEmail'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'validate_email');
    	$validationFields['ReplyEmail'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'validate_email');
    	$validationFields['RecipientEmail'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'validate_email');

    	$form = new ezcInputForm( INPUT_POST, $validationFields );
    	$Errors = array();

    	if (isset($params['archive_mode']) && $params['archive_mode'] == true){
    		$messages = array_reverse(erLhcoreClassChat::getList(array('limit' => 100, 'sort' => 'id DESC','customfilter' => array('user_id != -1'), 'filter' => array('chat_id' => $chat->id)),'erLhcoreClassModelChatArchiveMsg',erLhcoreClassModelChatArchiveRange::$archiveMsgTable));
    	} else {
    		$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 100,'sort' => 'id DESC','customfilter' => array('user_id != -1'), 'filter' => array('chat_id' => $chat->id))));
    	}
    	
    	// Fetch chat messages
    	$tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
    	$tpl->set('chat', $chat);
    	$tpl->set('messages', $messages);
    	
    	$surveyContent = self::getSurveyContent($chat);
    	
    	$sendMail->content = str_replace(array('{user_chat_nick}','{messages_content}','{chat_id}','{survey}'), array($chat->nick,$tpl->fetch(),$chat->id,$surveyContent), $sendMail->content);
    	
    	if ($form->hasValidData( 'Message' ) )
    	{
    		$sendMail->content = str_replace('{additional_message}', $form->Message, $sendMail->content);
    	}

    	$sendMail->content = erLhcoreClassBBCode::parseForMail($sendMail->content);
    	
    	if ( $form->hasValidData( 'FromEmail' ) ) {
    		$sendMail->from_email = $form->FromEmail;
    	}

    	if ( $form->hasValidData( 'ReplyEmail' ) ) {
    		$sendMail->reply_to = $form->ReplyEmail;
    	}

    	if ( $form->hasValidData( 'FromName' ) ) {
    		$sendMail->from_name = $form->FromName;
    	}

    	if ( $form->hasValidData( 'Subject' ) ) {
    		$sendMail->subject = $form->Subject;
    	}

    	if ( $form->hasValidData( 'RecipientEmail' ) ) {
    		$sendMail->recipient = $form->RecipientEmail;
    	} else {
    		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Please enter recipient e-mail!');
    	}

    	if (empty($sendMail->from_email)) {
    		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From e-mail is missing!');
    	}

    	if (empty($sendMail->reply_to)) {
    		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Reply e-mail is missing!');
    	}

    	if (empty($sendMail->subject)) {
    		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject is missing!');
    	}

    	return $Errors;
    }

    // Send mail
    public static function sendMail(erLhAbstractModelEmailTemplate & $sendMail, & $chat) {

    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";
    	
    	if ($sendMail->from_email != '') {
    		$mail->Sender = $mail->From = $sendMail->from_email;
    	}
    	
    	$mail->FromName = $sendMail->from_name;
    	$mail->Subject = str_replace(array('{chat_id}'), array($chat->id), $sendMail->subject);
    	
    	if ($sendMail->reply_to != '') {
    		$mail->AddReplyTo($sendMail->reply_to,$sendMail->from_name);
    	}
    	    	
    	$mail->Body = $sendMail->content;
    	$mail->AddAddress( $sendMail->recipient );

    	self::setupSMTP($mail);
    	
    	if ($sendMail->bcc_recipients != '') {
    		$recipientsBCC = explode(',',$sendMail->bcc_recipients);
    		foreach ($recipientsBCC as $recipientBCC) {
    			$mail->AddBCC(trim($recipientBCC));
    		}
    	}
    	
    	$mail->Send();
    	$mail->ClearAddresses();
    }

    public static function sendMailFAQ($faq) {

    	$sendMail = erLhAbstractModelEmailTemplate::fetch(6);

    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";

    	$mail->FromName = $sendMail->from_name;
    	
    	if ($sendMail->from_email != '') {
    		$mail->From = $mail->Sender = $sendMail->from_email;
    	}

    	if ($faq->email != ''){    
    		$mail->AddReplyTo($faq->email);
    	}

    	$mail->Subject = $sendMail->subject;
    	
    	$mail->Body = str_replace(array('{email}','{question}','{url_request}','{url_question}'), array($faq->email,$faq->question,erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('user/login').'/(r)/'.rawurlencode(base64_encode('faq/view/'.$faq->id)),$faq->url), $sendMail->content);
    	
    	if ($sendMail->recipient != '') { // Perhaps template has default recipient
    		$emailRecipient = explode(',',$sendMail->recipient);
    	} else { // Lets find first user and send him an e-mail
    		$list = erLhcoreClassModelUser::getUserList(array('limit' => 1,'sort' => 'id ASC'));
    		$user = array_pop($list);
    		$emailRecipient = array($user->email);
    	}
    	
    	foreach ($emailRecipient as $receiver) {
    		$mail->AddAddress( $receiver );
    	}
    	
    	self::setupSMTP($mail);
    	
    	$mail->Send();    	
    }
    
    public static function sendMailRequestPermission(erLhcoreClassModelUser $recipient, erLhcoreClassModelUser $sender, $requestedPermissions) {
        $sendMail = erLhAbstractModelEmailTemplate::fetch(10);
        
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        
        if ($sendMail->from_email != '') {
            $mail->From = $mail->Sender = $sendMail->from_email;
        }
                     
        $mail->Subject = str_replace(array('{user}'),array((string)$sender),$sendMail->subject);
        $mail->AddReplyTo($sender->email,(string)$sender);
                 
        $mail->Body = str_replace(array('{permissions}','{user}'), array($requestedPermissions,(string)$sender), $sendMail->content);
        
        $mail->AddAddress( $recipient->email );
        
        self::setupSMTP($mail);
                     
        $mail->Send();
        $mail->ClearAddresses();
    }
    
    public static function sendMailRequest($inputData, erLhcoreClassModelChat $chat, $params = array()) {

    	$sendMail = erLhAbstractModelEmailTemplate::fetch(2);

    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";

    	if ($sendMail->from_email != '') {
    		$mail->From = $mail->Sender = $sendMail->from_email;
    	}    	
        	
    	$mail->FromName = $sendMail->from_name;
    	    	
    	$mail->Subject = str_replace(array('{name}','{department}','{country}','{city}','{chat_id}'),array($chat->nick,(string)$chat->department,$chat->country_name,$chat->city,$chat->id),$sendMail->subject);
    	$mail->AddReplyTo($chat->email,$chat->nick);
    	
    	$prefillchat = '-'; 
    	if (isset($params['chatprefill']) && $params['chatprefill'] instanceof erLhcoreClassModelChat){
    		$prefillchat = erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('user/login').'/(r)/'.rawurlencode(base64_encode('chat/single/'.$params['chatprefill']->id));
    	}
    	
    	// Format user friendly additional data
    	if ($chat->additional_data != '') {
    	    $paramsAdditional = json_decode($chat->additional_data,true);
    	    $elementsAdditional = array();
    	    if (is_array($paramsAdditional) && !empty($paramsAdditional)) {
    	        foreach ($paramsAdditional as $param) {
    	            $elementsAdditional[] = $param['key'].' - '.$param['value'];
    	        }
    	        
    	        $additional_data = implode("\n", $elementsAdditional);
    	    } else {
    	        $additional_data = $chat->additional_data;
    	    }
    	    
    	} else {
    	    $additional_data = '';
    	}
    	
    	
    	$mail->Body = str_replace(array('{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{country}','{city}','{prefillchat}'), array($chat->phone,$chat->nick,$chat->email,$inputData->question,$additional_data,(isset($_POST['URLRefer']) ? $_POST['URLRefer'] : ''),erLhcoreClassIPDetect::getIP(),(string)$chat->department,$chat->country_name,$chat->city,$prefillchat), $sendMail->content);

    	/*
    	 * Attatch file
    	 * */
    	if ($inputData->has_file == true) { 
    		$mail->AddAttachment($inputData->file_location,'file.'.$inputData->file_extension);
    	}
    	
    	$emailRecipient = array();
    	if ($chat->department !== false && $chat->department->email != '') { // Perhaps department has assigned email
    		$emailRecipient = explode(',',$chat->department->email);
    	} elseif ($sendMail->recipient != '') { // Perhaps template has default recipient
    		$emailRecipient = explode(',',$sendMail->recipient);
    	} else { // Lets find first user and send him an e-mail
    		$list = erLhcoreClassModelUser::getUserList(array('limit' => 1,'sort' => 'id ASC'));
    		$user = array_pop($list);
    		$emailRecipient = array($user->email);
    	}

    	foreach ($emailRecipient as $receiver) {
    		$mail->AddAddress( $receiver );
    	}

    	self::setupSMTP($mail);

    	if ($sendMail->bcc_recipients != '') {
    		$recipientsBCC = explode(',',$sendMail->bcc_recipients);
    		foreach ($recipientsBCC as $recipientBCC) {
    			$mail->AddBCC(trim($recipientBCC));    			
    		}
    	}
    	
    	if ($sendMail->user_mail_as_sender == 1 && $chat->email != '') {    	       	 
    	   $mail->From = $chat->email;
    	   $mail->FromName = $chat->nick;    	  
    	}
    	    	
    	$mail->Send();
    	$mail->ClearAddresses();
    }
    
    public static function sendMailUnacceptedChat(erLhcoreClassModelChat $chat, $templateID = 4) {
    	$sendMail = erLhAbstractModelEmailTemplate::fetch($templateID);
    	
    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";
    	
    	if ($sendMail->from_email != '') { 	
    		$mail->Sender = $mail->From = $sendMail->from_email;
    	}
    	
    	$mail->FromName = $sendMail->from_name;
    	
    	if ($sendMail->reply_to != '') {
    	    $mail->AddReplyTo($sendMail->reply_to,$sendMail->from_name);
    	} elseif ($chat->email != '') {    		
    		$mail->AddReplyTo($chat->email,$chat->nick);
    	}

    	
    	$mail->Subject = str_replace(array('{chat_id}','{department}'), array($chat->id,(string)$chat->department), $sendMail->subject);
    	   	    	
    	$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 10,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
    	$messagesContent = '';
    	
    	foreach ($messages as $msg ) {
	    	 if ($msg->user_id == -1) {
	    		$messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
	    	 } else {
	    		$messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
	    	 }
    	}
    	
    	$emailRecipient = array();
    	if ($chat->department !== false && $chat->department->email != '') { // Perhaps department has assigned email
    		$emailRecipient = explode(',',$chat->department->email);
    	} elseif ($sendMail->recipient != '') { // Perhaps template has default recipient
    		$emailRecipient = explode(',',$sendMail->recipient);
    	} else { // Lets find first user and send him an e-mail
    		$list = erLhcoreClassModelUser::getUserList(array('limit' => 1,'sort' => 'id ASC'));
    		$user = array_pop($list);
    		$emailRecipient = array($user->email);
    	}
    	
    	self::setupSMTP($mail);
    	
    	$cfgSite = erConfigClassLhConfig::getInstance();
    	$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
    	    	
    	// Format user friendly additional data
    	if ($chat->additional_data != '') {
    	    $paramsAdditional = json_decode($chat->additional_data,true);
    	    $elementsAdditional = array();
    	    if (is_array($paramsAdditional) && !empty($paramsAdditional)) {
    	        foreach ($paramsAdditional as $param) {
    	            $elementsAdditional[] = $param['key'].' - '.$param['value'];
    	        }
    	        
    	        $additional_data = implode("\n", $elementsAdditional);
    	    } else {
    	        $additional_data = $chat->additional_data;
    	    }
    	    
    	} else {
    	    $additional_data = '';
    	}
    	
    	$surveyContent = self::getSurveyContent($chat);
    	    	
    	foreach ($emailRecipient as $receiver) {   
    		$veryfyEmail = 	sha1(sha1($receiver.$secretHash).$secretHash);
    		$mail->Body = str_replace(array('{survey}','{chat_duration}','{waited}','{created}','{user_left}','{user_name}','{chat_id}','{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{url_accept}','{country}','{city}'), array($surveyContent, ($chat->chat_duration > 0 ? $chat->chat_duration_front : '-'), ($chat->wait_time > 0 ? $chat->wait_time_front : '-'), $chat->time_created_front, ($chat->user_closed_ts > 0 && $chat->user_status == 1 ? $chat->user_closed_ts_front : '-'),$chat->user_name,$chat->id,$chat->phone,$chat->nick,$chat->email,$messagesContent,$additional_data,$chat->referrer,erLhcoreClassIPDetect::getIP(),(string)$chat->department,'http://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$receiver,$chat->country_name,$chat->city), $sendMail->content);
    		$mail->AddAddress( $receiver );
    		$mail->Send();
    		$mail->ClearAddresses();
    	}
    	
    	if ($sendMail->bcc_recipients != '') {
    		$recipientsBCC = explode(',',$sendMail->bcc_recipients);
    		foreach ($recipientsBCC as $receiver) {
    			$receiver = trim($receiver);
    			$veryfyEmail = 	sha1(sha1($receiver.$secretHash).$secretHash);
    			$mail->Body = str_replace(array('{survey}','{chat_duration}','{waited}','{created}','{user_left}','{user_name}','{chat_id}','{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{url_accept}','{country}','{city}'), array($surveyContent, ($chat->chat_duration > 0 ? $chat->chat_duration_front : '-'), ($chat->wait_time > 0 ? $chat->wait_time_front : '-'), $chat->time_created_front, ($chat->user_closed_ts > 0 && $chat->user_status == 1 ? $chat->user_closed_ts_front : '-'),$chat->user_name,$chat->id,$chat->phone,$chat->nick,$chat->email,$messagesContent,$additional_data,$chat->referrer,erLhcoreClassIPDetect::getIP(),(string)$chat->department,'http://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$receiver,$chat->country_name,$chat->city), $sendMail->content);
    			$mail->AddAddress( $receiver );
    			$mail->Send();
    			$mail->ClearAddresses();    			
    		}
    	}
    }

    public static function getSurveyContent($chat)
    {
        $surveyContent = '';
        include (erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));
        
        foreach (erLhAbstractModelSurveyItem::getList(array(
            'filter' => array(
                'chat_id' => $chat->id
            )
        )) as $survey_item) {
            $survey = $survey_item->survey;
            $surveyContent .= "[[" . (string) $survey_item->survey . "]]\n";
            for ($i = 0; $i < 16; $i ++) {
                foreach ($sortOptions as $keyOption => $sortOption) {
                    if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'} == 1) {
                        if ($sortOption['type'] == 'stars') {
                            $surveyContent .= "\n==" . $survey->{$sortOption['field'] . '_title'} . "==\n";
                            $surveyContent .= $survey_item->{$sortOption['field']} . "\n";
                        } elseif ($sortOption['type'] == 'question') {
                            $surveyContent .= "\n==" . $survey->{$sortOption['field']} . "==\n";
                            $surveyContent .= $survey_item->{$sortOption['field']} . "\n";
                        } elseif ($sortOption['type'] == 'question_options') {
                            $surveyContent .= "\n==" . $survey->{$sortOption['field']} . "==\n";
                            $options = $survey->{$sortOption['field'] . '_items_front'};
                            if (isset($options[$survey_item->{$sortOption['field']} - 1])) {
                                $surveyContent .= $options[$survey_item->{$sortOption['field']} - 1]['option'] . "\n";
                            } else {
                                $surveyContent .= $survey_item->{$sortOption['field']} . "\n";
                            }
                        }
                    }
                }
            }
            $surveyContent .= "===========================\n";
        }
        
        return $surveyContent == '' ? '-' : $surveyContent;
    }
    
    public static function informFormFilled($formCollected, $params = array()) {
    	$sendMail = erLhAbstractModelEmailTemplate::fetch(8);
    	
    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";
    	
    	if ($sendMail->from_email != '') {
    		$mail->From = $mail->Sender = $sendMail->from_email;
    	}
    	
    	if (isset($params['email']) && $params['email'] !== false && $params['email'] != '')
    	{
    	    $mail->AddReplyTo($params['email']);    	    
    	}
    	
    	$mail->FromName = $sendMail->from_name;    	
    	$mail->Subject = str_replace(array('{form_name}'),array($formCollected->form),$sendMail->subject);   	     	
    	$mail->Body = str_replace(array('{identifier}','{form_name}','{content}','{ip}','{url_download}','{url_view}'), array($formCollected->identifier,(string)$formCollected->form, $formCollected->form_content, $formCollected->ip, erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('user/login').'/(r)/'.rawurlencode(base64_encode('form/downloaditem/'.$formCollected->id)), erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('user/login').'/(r)/'.rawurlencode(base64_encode('form/viewcollected/'.$formCollected->id))), $sendMail->content);

    	$emailRecipient = array();
    	if ($formCollected->form->recipient != '') {
    		$emailRecipient = array($formCollected->form->recipient);
    	} elseif ($sendMail->recipient != '') {
    		$emailRecipient = array($sendMail->recipient);
    	}

    	if (!empty($emailRecipient)) {    	
	    	foreach ($emailRecipient as $receiver) {
	    		$mail->AddAddress( $receiver );
	    	}
	    	
	    	self::setupSMTP($mail);
	    	
	    	if ($sendMail->bcc_recipients != '') {
	    		$recipientsBCC = explode(',',$sendMail->bcc_recipients);
	    		foreach ($recipientsBCC as $recipientBCC) {
	    			$mail->AddBCC(trim($recipientBCC));
	    		}
	    	}
	    	
	    	$mail->Send();
	    	$mail->ClearAddresses();
    	}
    }
    
    public static function informVisitorUnreadMessage(erLhcoreClassModelChat $chat)
    {
    	if ($chat->email == '') {
    		return ;
    	}
    	
    	$sendMail = erLhAbstractModelEmailTemplate::fetch(11);

    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";

    	$fromSet = false;
    	if ($sendMail->from_email != '') {
    		$mail->Sender = $mail->From = $sendMail->from_email;
    		$fromSet = true;
    	}

    	$mail->FromName = $sendMail->from_name;

    	if ($sendMail->reply_to != '') {
    		$mail->AddReplyTo($sendMail->reply_to);
    		if ($fromSet == false) {
    			$mail->From = $sendMail->reply_to;
    		}
    	} elseif ($chat->user !== false) {
    		$mail->AddReplyTo($chat->user->email, $chat->user->name_support);
    		if ($fromSet == false) {
    			$mail->From = $chat->user->email;
    		}
    	}

    	$mail->Subject = $sendMail->subject;

    	$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 10, 'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
    	$messagesContent = '';

    	foreach ($messages as $msg ) {
    		if ($msg->user_id != -1) {    			
    			$messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
    		}
    	}

    	$nameSupport = $chat->user !== false ? $chat->user->name_support : '';

    	$mail->Body = str_replace(array('{messages}','{operator}'), array($messagesContent, $nameSupport), $sendMail->content);

    	self::setupSMTP($mail);

    	$mail->AddAddress( $chat->email );
    	$mail->Send();
    }
    
    public static function informChatClosed(erLhcoreClassModelChat $chat, $operator = false) {
    	$sendMail = erLhAbstractModelEmailTemplate::fetch(5);
    	
    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";

        if ($sendMail->from_email != '') {
            $mail->Sender = $mail->From = $sendMail->from_email;
        }

        if($sendMail->from_email == '{chat_email}' && $chat->email != '') {
            $mail->From = $chat->email;
        }

        $mail->FromName = $sendMail->from_name;
        $mail->Subject = str_replace(array('{chat_id}','{department}'), array($chat->id,(string)$chat->department), $sendMail->subject);;

        if($sendMail->from_name == '{chat_nick}' && $chat->nick != '') {
            $mail->FromName = $chat->nick;
        }
    	   	    	
    	$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 10,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
    	$messagesContent = '';
    	
    	foreach ($messages as $msg ) {
	    	 if ($msg->user_id == -1) {
	    		$messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
	    	 } else {
	    		$messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
	    	 }
    	}
    	
    	$emailRecipient = array();
    	$emailRecipientAll = array();
    	    	    	    
    	if ($chat->department !== false && $chat->department->inform_close_all == 1 && $chat->department->inform_close_all_email != '') {
    	    $emailRecipientAll = explode(',', $chat->department->inform_close_all_email);
    	}
    	    	
    	if ($sendMail->recipient != '') { // This time we give priority to template recipients
    		$emailRecipient = explode(',',$sendMail->recipient);    		
    	} elseif ($chat->department !== false && $chat->department->email != '' && $chat->department->inform_close == 1) {    			
    		$emailRecipient = explode(',',$chat->department->email);    		
    	} elseif (empty($emailRecipientAll)) { // Lets find first user and send him an e-mail
    		$list = erLhcoreClassModelUser::getUserList(array('limit' => 1,'sort' => 'id ASC'));
    		$user = array_pop($list);
    		$emailRecipient = array($user->email);
    	}
    	
    	$emailRecipient = array_unique(array_merge($emailRecipient,$emailRecipientAll));

    	self::setupSMTP($mail);
    	
    	$cfgSite = erConfigClassLhConfig::getInstance();
    	$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
    	
    	if ($sendMail->reply_to != '') {
    	    $mail->AddReplyTo($sendMail->reply_to,$sendMail->from_name);
    	} elseif ($chat->email != '') {    	
    		$mail->AddReplyTo($chat->email, $chat->nick);
    	}
    	
    	// Format user friendly additional data
    	if ($chat->additional_data != '') {
    	    $paramsAdditional = json_decode($chat->additional_data,true);
    	    $elementsAdditional = array();
    	    if (is_array($paramsAdditional) && !empty($paramsAdditional)) {
    	        foreach ($paramsAdditional as $param) {
    	            $elementsAdditional[] = $param['key'].' - '.$param['value'];
    	        }
    	        
    	        $additional_data = implode("\n", $elementsAdditional);
    	    } else {
    	        $additional_data = $chat->additional_data;
    	    }
    	    
    	} else {
    	    $additional_data = '';
    	}
    	
    	$surveyContent = self::getSurveyContent($chat);

    	foreach ($emailRecipient as $receiver) {   
    		$veryfyEmail = 	sha1(sha1($receiver.$secretHash).$secretHash);
    		$mail->Body = str_replace(array('{survey}','{chat_duration}','{waited}','{created}','{user_left}','{chat_id}','{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{url_accept}','{operator}','{country}','{city}'), array($surveyContent,($chat->chat_duration > 0 ? $chat->chat_duration_front : '-'), ($chat->wait_time > 0 ? $chat->wait_time_front : '-'), $chat->time_created_front, ($chat->user_closed_ts > 0 && $chat->user_status == 1 ? $chat->user_closed_ts_front : '-'),$chat->id,$chat->phone,$chat->nick,$chat->email,$messagesContent,$additional_data,$chat->referrer,$chat->ip,(string)$chat->department,(isset($_SERVER['HTTP_HOST'])) ? 'http://' . $_SERVER['HTTP_HOST'] : erLhcoreClassModelChatConfig::fetch('customer_site_url')->current_value . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$receiver,$operator,$chat->country_name,$chat->city), $sendMail->content);
    		$mail->AddAddress( $receiver );    		    		
    		$mail->Send();
    		$mail->ClearAddresses();
    	}
    	    	
    	if ($sendMail->bcc_recipients != '') {
    		$recipientsBCC = explode(',',$sendMail->bcc_recipients);
    		foreach ($recipientsBCC as $receiver) {    			
    			$receiver = trim($receiver);
    			$veryfyEmail = 	sha1(sha1($receiver.$secretHash).$secretHash);
	    		$mail->Body = str_replace(array('{survey}','{chat_duration}','{waited}','{created}','{user_left}','{chat_id}','{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{url_accept}','{operator}','{country}','{city}'), array($surveyContent,($chat->chat_duration > 0 ? $chat->chat_duration_front : '-'), ($chat->wait_time > 0 ? $chat->wait_time_front : '-'), $chat->time_created_front, ($chat->user_closed_ts > 0 && $chat->user_status == 1 ? $chat->user_closed_ts_front : '-'),$chat->id,$chat->phone,$chat->nick,$chat->email,$messagesContent,$additional_data,$chat->referrer,$chat->ip,(string)$chat->department,(isset($_SERVER['HTTP_HOST'])) ? 'http://' . $_SERVER['HTTP_HOST'] : erLhcoreClassModelChatConfig::fetch('customer_site_url')->current_value . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$receiver,$operator,$chat->country_name,$chat->city), $sendMail->content);
	    		$mail->AddAddress( $receiver );    		    		
	    		$mail->Send();
	    		$mail->ClearAddresses();    			 
    		}
    	}    	
    	
    	
    }
    
    

}

?>