<?php

class erLhcoreClassChatMail {

	public static function setupSMTP(PHPMailer & $phpMailer)
	{
		$smtpData = erLhcoreClassModelChatConfig::fetch('smtp_data');
		$data = (array)$smtpData->data;

		if ( isset($data['use_smtp']) && $data['use_smtp'] == 1 ) {
			$phpMailer->IsSMTP();
			$phpMailer->Host = $data['host'];
			$phpMailer->Port = $data['port'];
			
			if ($data['username'] != '' && $data['password'] != '') {			
				$phpMailer->Username = $data['username'];
				$phpMailer->Password = $data['password'];
				$phpMailer->SMTPAuth = true;
			}
		}
	}

	public static function sendTestMail($userData) {

		$mail = new PHPMailer(true);
		$mail->CharSet = "UTF-8";
		$mail->Sender = $userData->email;
		$mail->From = $userData->email;
		$mail->FromName = $userData->email;
		$mail->Subject = 'LHC Test mail';
		$mail->AddReplyTo($userData->email,(string)$userData);
		$mail->Body = 'This is test mail. If you received this mail. That means that your SMTP settings is correct.';
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
    public static function prepareSendMail(erLhAbstractModelEmailTemplate & $sendMail)
    {
    	$currentUser = erLhcoreClassUser::instance();
    	$userData = $currentUser->getUserData();
    	$sendMail->subject = str_replace(array('{name_surname}'),array($userData->name.' '.$userData->surname),$sendMail->subject);
    	$sendMail->from_name = str_replace(array('{name_surname}'),array($userData->name.' '.$userData->surname),$sendMail->from_name);

    	if (empty($sendMail->from_email)) {
    		$sendMail->from_email = $userData->email;
    	}

    	if (empty($sendMail->reply_to)) {
    		$sendMail->reply_to = $userData->email;
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
    		$messages = array_reverse(erLhcoreClassChat::getList(array('limit' => 100, 'sort' => 'id DESC', 'filter' => array('chat_id' => $chat->id)),'erLhcoreClassModelChatArchiveMsg',erLhcoreClassModelChatArchiveRange::$archiveMsgTable));
    	} else {
    		$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
    	}
    	

    	
    	// Fetch chat messages
    	$tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
    	$tpl->set('chat', $chat);
    	$tpl->set('messages', $messages);

    	$sendMail->content = str_replace(array('{user_chat_nick}','{messages_content}'), array($chat->nick,$tpl->fetch()), $sendMail->content);

    	if ($form->hasValidData( 'Message' ) )
    	{
    		$sendMail->content = str_replace('{additional_message}', $form->Message, $sendMail->content);
    	}

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
    	$mail->Sender = $mail->From = $sendMail->from_email;
    	$mail->FromName = $sendMail->from_name;
    	$mail->Subject = $sendMail->subject;
    	$mail->AddReplyTo($sendMail->reply_to,$sendMail->from_name);

    	$mail->Body = $sendMail->content;
    	$mail->AddAddress( $sendMail->recipient );

    	self::setupSMTP($mail);

    	$mail->Send();
    	$mail->ClearAddresses();
    }

    public static function sendMailRequest($inputData, erLhcoreClassModelChat $chat) {

    	$sendMail = erLhAbstractModelEmailTemplate::fetch(2);

    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";

    	if ($sendMail->from_email != '') {
    		$mail->Sender = $sendMail->from_email;
    	}

    	$mail->From = $chat->email;
    	$mail->FromName = $chat->nick;
    	$mail->Subject = $sendMail->subject;
    	$mail->AddReplyTo($chat->email,$chat->nick);
    	$mail->Body = str_replace(array('{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}'), array($chat->phone,$chat->nick,$chat->email,$inputData->question,$chat->additional_data,(isset($_POST['URLRefer']) ? $_POST['URLRefer'] : ''),erLhcoreClassIPDetect::getIP(),(string)$chat->department), $sendMail->content);

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

    	$mail->Send();
    	$mail->ClearAddresses();
    }
    
    public static function sendMailUnacceptedChat(erLhcoreClassModelChat $chat) {
    	$sendMail = erLhAbstractModelEmailTemplate::fetch(4);
    	
    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";
    	
    	if ($sendMail->from_email != '') { 	
    		$mail->Sender = $mail->From = $sendMail->from_email;
    	}
    	
    	$mail->FromName = $sendMail->from_name;
    	
    	if ($chat->email != '') {
    		$mail->From = $chat->email;
    		$mail->AddReplyTo($chat->email,$chat->nick);
    	}
    	  	
    	$mail->Subject = $sendMail->subject;
    	   	    	
    	$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 10,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
    	$messagesContent = '';
    	
    	foreach ($messages as $msg ) {
	    	 if ($msg->user_id == -1) {
	    		$messagesContent .= date('Y-m-d H:i:s',$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
	    	 } else {
	    		$messagesContent .= date('Y-m-d H:i:s',$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
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
    	
    	foreach ($emailRecipient as $receiver) {   
    		$veryfyEmail = 	sha1(sha1($receiver.$secretHash).$secretHash);
    		$mail->Body = str_replace(array('{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{url_accept}'), array($chat->phone,$chat->nick,$chat->email,$messagesContent,$chat->additional_data,$chat->referrer,erLhcoreClassIPDetect::getIP(),(string)$chat->department,'http://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$receiver), $sendMail->content);
    		$mail->AddAddress( $receiver );    		    		
    		$mail->Send();
    		$mail->ClearAddresses();
    	}  
    }
    
    
    public static function informChatClosed(erLhcoreClassModelChat $chat, $operator = false) {
    	$sendMail = erLhAbstractModelEmailTemplate::fetch(5);
    	
    	$mail = new PHPMailer();
    	$mail->CharSet = "UTF-8";

    	if ($sendMail->from_email != '') { 	
    		$mail->Sender = $mail->From = $sendMail->from_email;
    	}

    	$mail->FromName = $chat->nick != '' ? $chat->nick : $sendMail->from_name;    	
    	$mail->Subject = $sendMail->subject;
    	   	    	
    	$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 10,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
    	$messagesContent = '';
    	
    	foreach ($messages as $msg ) {
	    	 if ($msg->user_id == -1) {
	    		$messagesContent .= date('Y-m-d H:i:s',$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
	    	 } else {
	    		$messagesContent .= date('Y-m-d H:i:s',$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
	    	 }
    	}
    	
    	$emailRecipient = array();
    	if ($sendMail->recipient != '') { // This time we give priority to template recipients
    		$emailRecipient = explode(',',$sendMail->recipient);    		
    	}elseif ($chat->department !== false && $chat->department->email != '') {    			
    		$emailRecipient = explode(',',$chat->department->email);    		
    	} else { // Lets find first user and send him an e-mail
    		$list = erLhcoreClassModelUser::getUserList(array('limit' => 1,'sort' => 'id ASC'));
    		$user = array_pop($list);
    		$emailRecipient = array($user->email);
    	}
    	
    	self::setupSMTP($mail);
    	
    	$cfgSite = erConfigClassLhConfig::getInstance();
    	$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
    	
    	if ($chat->email != '') {
    		$mail->From = $chat->email;
    		$mail->AddReplyTo($chat->email, $chat->nick);
    	}

    	foreach ($emailRecipient as $receiver) {   
    		$veryfyEmail = 	sha1(sha1($receiver.$secretHash).$secretHash);
    		$mail->Body = str_replace(array('{phone}','{name}','{email}','{message}','{additional_data}','{url_request}','{ip}','{department}','{url_accept}','{operator}'), array($chat->phone,$chat->nick,$chat->email,$messagesContent,$chat->additional_data,$chat->referrer,erLhcoreClassIPDetect::getIP(),(string)$chat->department,'http://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$receiver,$operator), $sendMail->content);
    		$mail->AddAddress( $receiver );    		    		
    		$mail->Send();
    		$mail->ClearAddresses();
    	}
    }
    
    

}

?>