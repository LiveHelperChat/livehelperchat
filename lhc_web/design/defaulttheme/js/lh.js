
$.postJSON = function(url, data, callback) {
	$.post(url, data, callback, "json");
};

function lh(){

    this.wwwDir = WWW_DIR_JAVASCRIPT;
    this.addmsgurl = "chat/addmsgadmin/";
    this.addmsgurluser = "chat/addmsguser/";
    this.syncuser = "chat/syncuser/";
    this.syncadmin = "chat/syncadmin/";
    this.closechatadmin = "chat/closechatadmin/";    
    this.deletechatadmin = "chat/deletechatadmin/";    
    this.checkchatstatus = "chat/checkchatstatus/";
    this.syncadmininterfaceurl = "chat/syncadmininterface/";
    this.accepttransfer = "chat/accepttransfer/";
    this.trasnsferuser = "chat/transferuser/";
    this.userclosechaturl = "chat/userclosechat/";
    
    // On chat hash and chat_id is based web user chating. Hash make sure chat security.
    this.chat_id = null;
    this.hash = null;
    
    // Is synchronization under progress
    this.isSinchronizing = false;  
     
    
    this.syncroRequestSend = false;
    
    this.setSynchronizationRequestSend = function(status)
    {
        this.syncroRequestSend = status;
    }
    
    this.trackLastIDS = {};
    
    // Chats currently under synchronization
    this.chatsSynchronising = [];
    this.chatsSynchronisingMsg = [];
    
    // chat tabs window pointer
    this.chattabs = null;
    
    // Block synchronization till message add finished
    this.underMessageAdd = false;

    
    this.closeWindowOnChatCloseDelete = false;
      
    this.setChatID = function (chat_id){
        this.chat_id = chat_id;
    }
    
    this.setwwwDir = function (wwwdir){
        this.wwwDir = wwwdir;
    }
    
    this.setCloseWindowOnEvent = function (value)
    {
        this.closeWindowOnChatCloseDelete = value;
    }
    
    this.setSynchronizationStatus = function(status)
    {
        this.underMessageAdd = status;
    }
    
    this.startChat = function (chat_id,tabs,name)
    {
        if (this.chatUnderSynchronization(chat_id) == false)
        {
            //this.addSynchroChat(chat_id);
            tabs.tabs( 'add' , this.wwwDir +'chat/adminchat/'+chat_id , name );  
            this.syncadmininterfacestatic();
        }
    }
    
    this.setChatHash = function (hash)
    {
        this.hash = hash;
    }
    
    this.addSynchroChat = function (chat_id,message_id)
    {
        this.chatsSynchronising.push(chat_id);
        this.chatsSynchronisingMsg.push(chat_id + ',' +message_id);
    }
    
    this.removeSynchroChat = function (chat_id)
    {        
        var j = 0;
        
        while (j < this.chatsSynchronising.length) {   
                         
            if (this.chatsSynchronising[j] == chat_id) {
            
            this.chatsSynchronising.splice(j, 1);
            this.chatsSynchronisingMsg.splice(j, 1);
            
            } else { j++; }        
        }       
        
    }
    
    this.chatUnderSynchronization = function(chat_id)
    {
        var j = 0;
        
        while (j < this.chatsSynchronising.length) {   
                         
            if (this.chatsSynchronising[j] == chat_id) {
            
            return true;
            
            } else { j++; }        
        }
        
        return false;
    }
    
    this.getChatIndex = function(chat_id)
    {
        var j = 0;
        
        while (j < this.chatsSynchronising.length) {   
                         
            if (this.chatsSynchronising[j] == chat_id) {
            
            return j;
            
            } else { j++; }        
        }
        
        return false;
    }
    
    this.syncusercall = function()
	{
	    var inst = this;
	    
	    $.postJSON(this.wwwDir + this.syncuser + this.chat_id + '/' + this.hash ,{ }, function(data){ 
	        // If no error
	        if (data.error == 'false')
	        {	           
	           if (data.blocked != 'true')
	           {
    	            if (data.result != 'false' && data.status == 'true')
    	            {    	 
                			$('#messagesBlock').append(data.result);    			
                			$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 3000);  
                			
                			inst.playNewMessageSound();                            
    	                
    	            } else {
    	                if ( data.status != 'true') $('#status-chat').html(data.status);  
    	            }  			     	
        			setTimeout(chatsyncuser,3500);	
	           } else {
	               $('#status-chat').html(data.status);  
	           }
	        }		
    	});
	}, 
	
	this.closeActiveChatDialog = function(chat_id, tabs, hidetab)
	{    
	    $.postJSON(this.wwwDir + this.closechatadmin + chat_id ,{}, function(data){ 
	        	    	   
	    })	 
	    
	    if ($('#CSChatMessage-'+chat_id).length != 0){	    	    
	       $('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	    }
	        
	    if (hidetab == true) {	
	        
	        var selected_index = tabs.tabs('option', 'selected');
	        
	        if (selected_index != -1)	
	        {       	 	                   
    	        tabs.tabs('remove' , selected_index);  
	        }
	        else
	            tabs.tabs('remove' , 0);  
	            
	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close(); 
	        }
	            
	    }
	    
	    this.removeSynchroChat(chat_id); 
	    this.syncadmininterfacestatic();
	       
	}	
	
	this.removeDialogTab = function(chat_id, tabs, hidetab)
	{
	    
	    if ($('#CSChatMessage-'+chat_id).length != 0){	    	    
	       $('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	    }
	  	    
	    	    
	    if (hidetab == true) {	
	        
	        var selected_index = tabs.tabs('option', 'selected');
	        
	        if (selected_index != -1)	
	        {       	 	                   
    	        tabs.tabs('remove' , selected_index);  
	        }
	        else
	            tabs.tabs('remove' , 0);  
	            
	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close(); 
	        }
	            
	    }
	    
	    this.removeSynchroChat(chat_id); 
	    this.syncadmininterfacestatic();
	}
	
	this.deleteChat = function(chat_id, tabs, hidetab)
	{    
	    if ($('#CSChatMessage-'+chat_id).length != 0){	    	    
	       $('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	    }
	    
	    $.postJSON(this.wwwDir + this.deletechatadmin + chat_id ,{}, function(data){ 
	       if (data.error == 'true')
	       {
	           alert(data.result);
	       } 	    	   
	    })	

	     if (hidetab == true) {	
	        
	        var selected_index = tabs.tabs('option', 'selected');
	        
	        if (selected_index != -1)	
	        {       	 	                   
    	        tabs.tabs('remove' , selected_index);  
	        }
	        else
	            tabs.tabs('remove' , 0);  
	            
	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close();
	        }
	            
	    }

	        
	    this.syncadmininterfacestatic();
	    this.removeSynchroChat(chat_id);    
	}
	
	this.rejectPendingChat = function(chat_id, tabs)
	{    
	    $.postJSON(this.wwwDir + this.deletechatadmin + chat_id ,{}, function(data){ 
	        	    	   
	    })	   
	    this.syncadmininterfacestatic();   
	}
	
	this.startChatNewWindow = function(chat_id,name)
	{    	   
	    window.open(this.wwwDir + 'chat/single/'+chat_id,'chatwindow'+name+chat_id,"menubar=1,resizable=1,width=600,height=430"); 
	    this.syncadmininterfacestatic();    
        return false;          
	}
	
	this.startChatTransfer = function(chat_id,tabs,name,transfer_id){
	    
	     $.postJSON(this.wwwDir + this.accepttransfer + transfer_id ,{}, function(data){ 
	        	    	   
	    })	    
	    this.startChat(chat_id,tabs,name);
	}
	
	this.startChatNewWindowTransfer = function(chat_id,name,transfer_id)
	{
	    $.postJSON(this.wwwDir + this.accepttransfer + transfer_id ,{}, function(data){ 
	        	    	   
	    })
	    
	    return this.startChatNewWindow(chat_id,name);
	}
	
	this.transferChat = function(chat_id)
	{
	    var user_id = $('[name=TransferTo'+chat_id+']:checked').val();	  
	    	
	    $.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id ,{}, function(data){ 
	        
	        if (data.error == 'false')
	        {
	        	$('#transfer-block-'+data.chat_id).html(data.result);
	        
	        }
	        	    	   
	    })
	}
	
	this.chatTabsOpen = function ()
	{
	    window.open(this.wwwDir + 'chat/chattabs/','chatwindows',"menubar=1,resizable=1,width=580,height=420");	
	    return false;
	}
	
	this.deleteChatNewWindow = function()
	{
	    this.chattabs = null;
	}
	
	this.userclosedchat = function()
	{
	    $.postJSON(this.wwwDir + this.userclosechaturl + this.chat_id + '/' + this.hash ,{}, function(data){ 
	      	        	        		
    	});
	}
	
	this.chatsyncuserpending = function ()
	{
	    $.postJSON(this.wwwDir + this.checkchatstatus + this.chat_id + '/' + this.hash ,{}, function(data){ 
	        // If no error
	        if (data.error == 'false')
	        {
	            if (data.activated == 'false')
	            {
	               if (data.result != 'false')
	               {
	                   $('#status-chat').html(data.result); 
	               }
	               
	               setTimeout(chatsyncuserpending,3500);
	               
	            } else {
	               $('#status-chat').html(data.result); 	               
	            }
	        }	        		
    	});
	}	
	
	this.playNewMessageSound = function() {
	    	
	    if (Modernizr.audio) {    
    	    var audio = new Audio();            
            audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_message.wav';
            
            audio.load();
            audio.play();
	    }
	}
	
    this.syncadmincall = function()
	{	
	    if (this.chatsSynchronising.length > 0)  
	    {    
	        if (this.underMessageAdd == false && this.syncroRequestSend == false)
	        {
	            
	            this.syncroRequestSend = true;
                var inst = this;
                
        	    $.postJSON(this.wwwDir + this.syncadmin ,{ 'chats[]': this.chatsSynchronisingMsg }, function(data){ 
        	        // If no error
        	        if (data.error == 'false')
        	        {	           
        	            if (data.result != 'false')
        	            {  	                
        	                
        	                $.each(data.result,function(i,item) {    	                     
                                  $('#messagesBlock-'+item.chat_id).append(item.content);
        		                  $('#messagesBlock-'+item.chat_id).animate({ scrollTop: $("#messagesBlock-"+item.chat_id).prop("scrollHeight") }, 3000);
        		                  lhinst.updateChatLastMessageID(item.chat_id,item.message_id);       		              
                            });                      
                            
                            inst.playNewMessageSound();
                                                       
        	            }  			     	
            			setTimeout(chatsyncadmin,3500);	
        	        }
        	        
        	        //Allow another request to send check for messages
        	        lhinst.setSynchronizationRequestSend(false);
        	        		
            	});
	        } else {
	            setTimeout(chatsyncadmin,3500);	
	        }
        	
	    } else {
	        this.isSinchronizing = false;
	    }
	},
	
	this.updateChatLastMessageID = function(chat_id,message_id)
	{
	    this.chatsSynchronisingMsg[this.getChatIndex(chat_id)] = chat_id+','+message_id;
	}
	
	
	this.syncadmininterface = function()
	{
	    var inst = this;
	    
	    $.getJSON(this.wwwDir + this.syncadmininterfaceurl ,{ }, function(data){ 
	        // If no error
	        if (data.error == 'false')
	        {	 
                $.each(data.result,function(i,item) {	                    
                    if (item.content != '') { $(item.dom_id).html(item.content); }  
                    
                    if ( item.last_id_identifier ) {                                                
                        if (!inst.trackLastIDS[item.last_id_identifier] ) {
                            inst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);
                        } else if (inst.trackLastIDS[item.last_id_identifier] < item.last_id) {
                            inst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);
                            inst.playSoundNewAction(item.last_id_identifier);
                        }                    
                    }           
                });
	              			     	
    			setTimeout(chatsyncadmininterface,10000);	
	        }		
    	});
	}
	
	this.playSoundNewAction = function(identifier) {	    
	    if (identifier == 'pending_chat') {
	        if (Modernizr.audio) {    
        	    var audio = new Audio();            
                audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.ogg' :
                            Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_chat.wav';
                
                audio.load();
                audio.play();
    	    }
	    }
	}
	
	this.syncadmininterfacestatic = function()
	{
	    $.getJSON(this.wwwDir + this.syncadmininterfaceurl ,{ }, function(data){ 
	        // If no error
	        if (data.error == 'false')
	        {	 
                $.each(data.result,function(i,item) {
                    if (item.content != '') { $(item.dom_id).html(item.content); }
                });		     	
	        }		
    	});
	}	
	
	this.transferUserDialog = function(chat_id,title)
	{
	    if ($("#transfer-dialog-"+chat_id).hasClass("ui-dialog-content"))
	    {	    
	       $("#transfer-dialog-"+chat_id).dialog('open');
	       
	    } else {	
	     
        $("#transfer-dialog-"+chat_id).load(this.wwwDir + 'chat/transferchat/'+chat_id).dialog({
             title: title,
             modal: true,
             autoOpen: true,                 
             width: 500
          }); 
	    }
	   
	}
	
	this.abstractDialog = function(element_id,title,url)
	{
	    if ($("#"+element_id).hasClass("ui-dialog-content"))
	    {	    
	       $("#"+element_id).dialog('open');
	       
	    } else {	
	     
        $("#"+element_id).load(url).dialog({
             title: title,
             modal: true,
             autoOpen: true,                 
             width: 500
          }); 
	    }
	   
	}

	
    this.addmsgadmin = function (chat_id)
    {        
        this.underMessageAdd = true;
        
        var pdata = {
				msg	: $("#CSChatMessage-"+chat_id).val()
		}
		
	   $('#CSChatMessage-'+chat_id).val('');
	   
			
       $.postJSON(this.wwwDir + this.addmsgurl + chat_id, pdata , function(data){	
		   
           if (data.error == 'false')
           {            
		    $('#messagesBlock-'+data.chat_id).append(data.result);
		    $('#messagesBlock-'+data.chat_id).animate({ scrollTop: $("#messagesBlock-"+data.chat_id).prop("scrollHeight") }, 3000); 
		    lhinst.updateChatLastMessageID(data.chat_id,data.message_id);
           }
           
           $('#CSChatMessage-'+data.chat_id).val('');
           
           // Unblock messages synchronization
           lhinst.setSynchronizationStatus(false);
           return true;	          
		});
    }  
    
    this.addmsguser = function ()
    {        
        var pdata = {
				msg	: $("#CSChatMessage").val()
		}
		$('#CSChatMessage').attr('value','');
			
       $.postJSON(this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash, pdata , function(data){	
		    
		    $('#messagesBlock').append(data.result);
		    $('#messagesBlock').animate({ scrollTop: $("#messagesBlock").prop("scrollHeight") }, 3000); 		    
		    $('#CSChatMessage').val('');
		    		
           return true;	          
		});
    } 
      
    
    this.startSyncAdmin = function()
    {
        if (this.isSinchronizing == false)
        {
            this.isSinchronizing = true;
            this.syncadmincall();
        }
    }
    
}

var lhinst = new lh();

/*Helper functions*/
function chatsyncuser()
{
    lhinst.syncusercall();    
}

function chatsyncuserpending()
{
    lhinst.chatsyncuserpending();    
}

function chatsyncadmin()
{
    lhinst.syncadmincall();    
}

function chatsyncadmininterface()
{
    lhinst.syncadmininterface();    
}