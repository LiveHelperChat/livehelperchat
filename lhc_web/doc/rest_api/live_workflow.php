<?php
include 'lhrestapi.php';
$settings = include 'settings.ini.php';

/**
 * General workflow
 * 1. Execute "chatcheckoperatormessage" request
 * 2. User should start chat with "startchat" also you can provide $vid so chat will be mapped to online visitor
 * 2.1 If chat should be started on proactive message just use "proactive" => true as argument for starchat function. See example.
 * 3. Then you should start executing two request
 * 3.1 "checkchatstatus" and wait untill chat becames activated and update assigned operator text. See examples below
 * 3.2 "fetchchatmessages" just fetch chat messages every few seconds
 * 4. Then chat is over execute "closechatasvisitor" request
 * 5. You can also update chat attributes "updatechatattributes" separately
 * */


$LHCRestAPI = new LHCRestAPI($settings['host'], $settings['user'], $settings['key']);

$id = '6702';
$hash = '4f52728f48b0c192d22f8fe7b7c4be4bab9ae430';
$vid = '8bcyn2z5nguhpc5orks4';
$addMessage = false;

// Just initialise variable
$urlAppend = '';

// Check's for messages from operator or just log new page view ir $vid is not set
/* if ($vid === false) {

    $response = $LHCRestAPI->execute('chatcheckoperatormessage', array(
        'ip' => '88.118.220.88',
        'dt' => 'APPLICATIN_NAME - Some Application Specific Window',
        'l' => '//google.com',
        'ua' => '', // User Agent
        'onattr' => json_encode(array('chat_id' => 'chat id here')) // Store additional chat attributes
     ), array(
        'vid' => '',        // It's new visitor it still has not vid assigned
        'count_page' => 1,  // Log it as real page view, not just message check from operator
        'tz' => 0,          // Time Zone Offset in seconds from GMT
        //'priority' => 0,    // Chat priority
        //'operator' => 0,    // Operator ID
        //'theme' => 0,       // Theme
        //'survey' => 0,      // Survey (optional)
        //'department' => 5,  // Department (optional)
        'uactiv' => 1       // Is user considered active or not
    ), 'POST');

    print_r($response);
    
    // If there is no error set $vid
    if ($response->error == false && isset($response->result->vid)) {
    	$vid = $response->result->vid;
    }
} */

;

// Make sure there is no errors, so we can use vid from response
if ($vid !== false) {

    /**
     * If you want just check is there any pending messages from operator you can execute this request
     * The only different count_page is equal 0
     */
    $response = $LHCRestAPI->execute('chatcheckoperatormessage', array(
        'ip' => '88.118.220.88',
        'dt' => 'APPLICATIN_NAME - Some Application Specific Window',
        'ua' => '', // User Agent
    	'onattr' => json_encode(array('chat_id' => 'new chat data')) // Store additional chat attributes
    ), array(
        'vid' => $vid,  // Use VID from previous request
        'count_page' => 0,  // Do not count as page view, just message check from operator
        'tz' => 0,          
        'priority' => 0,   
        'operator' => 0,    
        'theme' => 0,       
        'survey' => 0,      
        'department' => 5,  
        'uactiv' => 1       
    ), 'POST');
exit;

    // Count another page view
    $response = $LHCRestAPI->execute('chatcheckoperatormessage', array(
        'ip' => '88.118.220.88',
        'dt' => 'APPLICATIN_NAME - Some Application Specific Window',
        'l' => '#',
        'ua' => '', // User Agent
    ), array(
        'vid' => $vid,
        'count_page' => 1,  
        'tz' => 0,          
        'priority' => 0,    
        'operator' => 0,    
        'survey' => 0,      
        'uactiv' => 1       
    ), 'POST',true,'');

    if ($response->error == false && $response->result->action == 'read_message') {
        $urlAppend = $response->result->args->url_append;
        $message = $response->result->args->message;
        
        // Start chat based on proactive invitation
        $response = $LHCRestAPI->execute('startchat', array(
            'Email' => 'remdex@gmail.com',  // E-mail of visitor
            'DepartamentID' => '5',         // Department
            'Username' => 'Visitor name',   // From what page chat has started
            'Question' => 'my Question',    // Initial message from user
            'AcceptTOS' => true,            // Accept TOS
            'Phone' => '',                  // visitor phone if any
            'URLRefer' => '',               // From what page chat has started, it can be page or just some application window name
            'operator' => '',               // To what operator assign chat automatically
            'ip' => '88.118.220.88',        // IP of original user
            'data' => json_encode(array('chat_id' =>  array('val' => 'chat_id attribute'))), // Store additional chat information
            'proactive' => true             // Start chat based on proactive data
        ), array(
            'vid' => $vid // You can pass visitor id so chat will be associated with this visitor
        ), 'POST',
            true,
            $urlAppend);
        
        
        exit;
        // @todo add message read from operator
    } elseif ($response->error == false && $response->result->action == 'continue') {
        // Just repeat request
    }

    echo "Second page view\n";
} else {
    echo "Some error\n";    
}

// Perhaps manually set hash and id
if ($hash === false && $id === false)
{
    // Start chat
    $response = $LHCRestAPI->execute('startchat', array(
        'Email' => 'remdex@gmail.com',  // E-mail of visitor
        'DepartamentID' => '5',         // Department
        'Username' => 'Visitor name',   // From what page chat has started
        'Question' => 'my Question',    // Initial message from user
        'AcceptTOS' => true,            // Accept TOS
        'Phone' => '',                  // visitor phone if any
        'URLRefer' => '',               // From what page chat has started, it can be page or just some application window name
        'operator' => '',               // To what operator assign chat automatically
        'ip' => '88.118.220.88',        // IP of original user
        'data' => json_encode(array('chat_id' =>  array('val' => 'chat_id attribute'))) // Store additional chat information
    ), array(
        'vid' => $vid // You can pass visitor id so chat will be associated with this visitor
    ), 'POST', 
        true, 
        $urlAppend);
    
    if ($response->error == false) {
        $hash = $response->result->chat->hash;
        $id = $response->result->chat->id;
    }
}

// Fetch chat data
$response = $LHCRestAPI->execute('fetchchat', array(
    'chat_id' => $id,
    'hash' => $hash
));

if ($response->error == false) {
    
    $status = array(
        0 => 'STATUS_PENDING_CHAT',
        1 => 'STATUS_ACTIVE_CHAT',
        2 => 'STATUS_CLOSED_CHAT',
        3 => 'STATUS_CHATBOX_CHAT',
        4 => 'STATUS_OPERATORS_CHAT',
    );
       
    // Means chat is accepted
    if ($response->chat->status == 1) {
        echo "Assigned operator - ",$response->chat->plain_user_name,"\n";
    }

    // This request can be executed every 3-5 seconds it checks is there any new messages
    $response = $LHCRestAPI->execute('fetchchatmessages', array(
        'chat_id' => $id,
        'last_message_id' => 0, // Optional, return messages from this <optional>
        'ignore_system_messages' => true,
        'workflow' => true
    ));

    // Check chat status
    $responseCheckStatus = $LHCRestAPI->execute('checkchatstatus', array(
    		'chat_id' => $id,
    		'hash' => $hash
    ));

    if ($responseCheckStatus->error == false) {
    	if ($responseCheckStatus->result->activated == false) {
    		$statusChat = $responseCheckStatus->result->status;  
    		
    		// Repeat sam request again because chat is still not activated    		
    	} else {
    		$statusChat = $responseCheckStatus->result->status;  
    	
    		// Assigned operator name
    		$nameSupport = $responseCheckStatus->result->name_support;
    		
    		if ($responseCheckStatus->result->closed == true) {
    			// Hide widget or do something you want
    		}
    	}
    }
    
    // Add message as a user
    if ($addMessage == true) {
	    // Now add message to chat
	    $response = $LHCRestAPI->execute('addmsguser', array(
	        'chat_id' => $id,
	        'hash' => $hash,
	        'msg' => 'User message here'
	    ), array(), 'POST');
    }
    
    // Close chat as visitor, let say visitor closes remote application etc
    // Operator will see a message, 
    $response = $LHCRestAPI->execute('closechatasvisitor', array(
    		'chat_id' => $id,
    		'hash' => $hash,
    		// eclose => true if user explicitly closed application
    ));  

    // Update chat attributes
    // We update chat attributes
    $response = $LHCRestAPI->execute('updatechatattributes', array(
		'chat_id' => $id,
		'hash' => $hash,
    	'data' => json_encode(array('chat_id' =>  array('val' => 'chat_id attribute')))
    ),
    array(), 'POST' );    
}
