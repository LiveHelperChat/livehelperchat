<?php

$Module = array( "name" => "Chat",
				 'variable_params' => true );

$ViewList = array();
    
$ViewList['adminchat'] = array( 
    'script' => 'adminchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );    
      
$ViewList['closechatadmin'] = array( 
    'script' => 'closechatadmin.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );  
          
$ViewList['closechat'] = array( 
    'script' => 'closechat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );   
        
$ViewList['transferchat'] = array( 
    'script' => 'transferchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'allowtransfer' )
    );        
    
$ViewList['accepttransfer'] = array( 
    'script' => 'accepttransfer.php',
    'params' => array('transfer_id'),
    'functions' => array( 'use' )
    );   
        
$ViewList['deletechatadmin'] = array( 
    'script' => 'deletechatadmin.php',
    'params' => array('chat_id'),
    'functions' => array( 'deletechat' )
    );  
           
$ViewList['delete'] = array( 
    'script' => 'delete.php',
    'params' => array('chat_id'),
    'functions' => array( 'deletechat' )
    ); 
    
$ViewList['syncadmininterface'] = array( 
    'script' => 'syncadmininterface.php',
    'params' => array(),
    'functions' => array( 'use' )
    );    
     
$ViewList['lists'] = array( 
    'script' => 'lists.php',
    'params' => array(),
    'functions' => array( 'use' )
    );    
     
$ViewList['chattabs'] = array( 
    'script' => 'chattabs.php',
    'params' => array('chat_id'),
    'functions' => array( 'allowchattabs' )
    );   
       
$ViewList['single'] = array( 
    'script' => 'single.php',
    'params' => array('chat_id'),
    'functions' => array( 'singlechatwindow' )
);  
       
$ViewList['syncadmin'] = array( 
    'script' => 'syncadmin.php',
    'params' => array(),
    'functions' => array( 'use' )
    ); 
          
$ViewList['activechats'] = array( 
    'script' => 'activechats.php',
    'params' => array(),
    'functions' => array( 'use' )
    );  
            
$ViewList['closedchats'] = array( 
    'script' => 'closedchats.php',
    'params' => array(),
    'functions' => array( 'use' )
    ); 
               
$ViewList['pendingchats'] = array( 
    'script' => 'pendingchats.php',
    'params' => array(),
    'functions' => array( 'use' )
    );
         
$ViewList['addmsgadmin'] = array( 
    'script' => 'addmsgadmin.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );      
        
/* Anonymous functions */    
$ViewList['addmsguser'] = array( 
    'script' => 'addmsguser.php',
    'params' => array('chat_id','hash')
    );    
     
$ViewList['syncuser'] = array( 
    'script' => 'syncuser.php',
    'params' => array('chat_id','hash')
    );         
    
$ViewList['checkchatstatus'] = array( 
    'script' => 'checkchatstatus.php',
    'params' => array('chat_id','hash')
    );    
    
$ViewList['transferuser'] = array( 
    'script' => 'transferuser.php',
    'params' => array('chat_id','user_id'),
    'functions' => array( 'allowtransfer' )
    );
        
$ViewList['blockuser'] = array( 
    'script' => 'blockuser.php',
    'params' => array('chat_id'),
    'functions' => array( 'allowblockusers' )
);        

$ViewList['blockedusers'] = array( 
    'script' => 'blockedusers.php',
    'params' => array(),
    'uparams' => array('remove_block'),
    'functions' => array( 'allowblockusers' )
);
    
$ViewList['getstatus'] = array( 
    'script' => 'getstatus.php',
    'params' => array(),
    'uparams' => array('click'),
    );   
    
$ViewList['startchat'] = array( 
    'script' => 'startchat.php',
    'params' => array()
    );
         
$ViewList['chatwidget'] = array( 
    'script' => 'chatwidget.php',
    'params' => array()
);  
      
$ViewList['chat'] = array( 
    'script' => 'chat.php',
    'params' => array('chat_id','hash')
);   

$ViewList['userclosechat'] = array( 
    'script' => 'userclosechat.php',
    'params' => array('chat_id','hash')
);

$ViewList['onlineusers'] = array( 
    'script' => 'onlineusers.php',
    'params' => array(),
    'uparams' => array('clear_list','method'),
    'functions' => array( 'use' )
);

$ViewList['geoconfiguration'] = array( 
    'script' => 'geoconfiguration.php',
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['listchatconfig'] = array( 
    'script' => 'listchatconfig.php',
    'params' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['editchatconfig'] = array( 
    'script' => 'editchatconfig.php',
    'params' => array('config_id'),
    'functions' => array( 'administrateconfig' )
); 

$FunctionList['use'] = array('explain' => 'General chat usage permission');  
$FunctionList['singlechatwindow'] = array('explain' =>'Allow user to use single chat window functionality');  
$FunctionList['allowchattabs'] = array('explain' =>'Allow user to user chat rooms functionality');  
$FunctionList['deletechat'] = array('explain' =>'Allow user to delete his own chats');  
$FunctionList['deleteglobalchat'] = array('explain' =>'Allow to delete all chats');  
$FunctionList['allowtransfer'] = array('explain' =>'Allow user to transfer chat to another user');  
$FunctionList['allowcloseremote'] = array('explain' =>'Allow user to close another user chat');  
$FunctionList['allowblockusers'] = array('explain' =>'Allow user to block users');  
$FunctionList['administrateconfig'] = array('explain' =>'Allow to change chat config');  

?>