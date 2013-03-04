
$.postJSON = function(url, data, callback) {
	$.post(url, data, callback, "json");
};


$.fn.foundationTabs.addTab = function(tabs, url, name){
	
	tabs.find('> dd.active').removeClass("active");
	$('#tabs-content > li').hide();
	
	var nextElement = tabs.find('> dd').size() + 1;		
	tabs.append('<dd class="active"><a href="#simple'+nextElement+'">'+name+'</a></dd>');	
	$('#tabs-content').append('<li id="simple'+nextElement+'Tab" style="display:block;"></li>');

	$.get(url, function(data) {
		  $('#simple'+nextElement+'Tab').html(data);		 
	});	
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
     
    // is Widget mode
    this.isWidgetMode = false;
    
    this.syncroRequestSend = false;
    
    this.setWidgetMode = function(status) {
    	this.isWidgetMode = status;
    };
    
    this.setSynchronizationRequestSend = function(status)
    {
        this.syncroRequestSend = status;
    };
    
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
    };
    
    this.setwwwDir = function (wwwdir){
        this.wwwDir = wwwdir;
    };
    
    this.setCloseWindowOnEvent = function (value)
    {
        this.closeWindowOnChatCloseDelete = value;
    };
    
    this.setSynchronizationStatus = function(status)
    {
        this.underMessageAdd = status;
    };

    this.startChat = function (chat_id,tabs,name)
    {
        if ( this.chatUnderSynchronization(chat_id) == false ) {            
            $.fn.foundationTabs.addTab(tabs, this.wwwDir +'chat/adminchat/'+chat_id, name);
        }
    };
    
    this.setChatHash = function (hash)
    {
        this.hash = hash;
    };
    
    this.addSynchroChat = function (chat_id,message_id)
    {
        this.chatsSynchronising.push(chat_id);
        this.chatsSynchronisingMsg.push(chat_id + ',' +message_id);
    };
    
    this.removeSynchroChat = function (chat_id)
    {        
        var j = 0;
        
        while (j < this.chatsSynchronising.length) {   
                         
            if (this.chatsSynchronising[j] == chat_id) {
            
            this.chatsSynchronising.splice(j, 1);
            this.chatsSynchronisingMsg.splice(j, 1);
            
            } else { j++; }        
        }       
        
    };
    
    this.is_typing = false;
    this.typing_timeout = null;
    
    this.initTypingMonitoringAdmin = function(chat_id) {
       
        var www_dir = this.wwwDir;
        var inst = this;
            
        jQuery('#CSChatMessage-'+chat_id).bind('keyup', function (evt){            
            if (inst.is_typing == false) {
                inst.is_typing = true;
                clearTimeout(inst.typing_timeout);
                $.getJSON(www_dir + 'chat/operatortyping/' + chat_id+'/true',{ }, function(data){ 
                   inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id)},2000);
                });
            } else {
                 clearTimeout(inst.typing_timeout);
                 inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id)},2000);
            }
        });
    };
    
    this.typingStoppedOperator = function(chat_id) {        
        var inst = this;
        if (inst.is_typing == true){
            $.getJSON(this.wwwDir + 'chat/operatortyping/' + chat_id+'/false',{ }, function(data){ 
                inst.is_typing = false;
            });
        }
    };
    
    this.initTypingMonitoringUser = function(chat_id) {
       
        var www_dir = this.wwwDir;
        var inst = this;
            
        jQuery('#CSChatMessage').bind('keyup', function (evt){            
            if (inst.is_typing == false) {
                inst.is_typing = true;
                clearTimeout(inst.typing_timeout);
                $.getJSON(www_dir + 'chat/usertyping/' + chat_id+'/'+inst.hash+'/true',{ }, function(data){ 
                   inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id)},2000);
                });
            } else {
                 clearTimeout(inst.typing_timeout);
                 inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id)},2000);
            }
        });
    };
      
    this.typingStoppedUser = function(chat_id) {        
        var inst = this;
        if (inst.is_typing == true){
            $.getJSON(this.wwwDir + 'chat/usertyping/' + chat_id+'/'+this.hash+'/false',{ }, function(data){ 
                inst.is_typing = false;
            });
        }
    };      
    
    this.chatUnderSynchronization = function(chat_id)
    {
        var j = 0;
        
        while (j < this.chatsSynchronising.length) {   
                         
            if (this.chatsSynchronising[j] == chat_id) {
            
            return true;
            
            } else { j++; }        
        }
        
        return false;
    };
    
    this.getChatIndex = function(chat_id)
    {
        var j = 0;
        
        while (j < this.chatsSynchronising.length) {   
                         
            if (this.chatsSynchronising[j] == chat_id) {
            
            return j;
            
            } else { j++; }        
        }
        
        return false;
    };
    
    this.syncusercall = function()
	{
	    var inst = this;
	    
	    var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
	    
	    $.postJSON(this.wwwDir + this.syncuser + this.chat_id + '/' + this.hash + modeWindow ,{ }, function(data){ 
	        // If no error
	        if (data.error == 'false')
	        {	           
	           if (data.blocked != 'true')
	           {
    	            if (data.result != 'false' && data.status == 'true')
    	            {    	 
                			$('#messagesBlock').append(data.result);    			
                			$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 3000);  
                			
                			if ( confLH.new_message_sound_user_enabled == 1 ) {
                			     inst.playNewMessageSound(); 
                			};
    	                
    	            } else {
    	                if ( data.status != 'true') $('#status-chat').html(data.status);  
    	            }  			     	
        			setTimeout(chatsyncuser,confLH.chat_message_sinterval);	
        			
        			if ( data.is_typing == 'true' ) {
        			    $('#id-operator-typing').fadeIn();
        			} else {
        			    $('#id-operator-typing').fadeOut();
        			}	           

	           } else {
	               $('#status-chat').html(data.status);  
	           }
	        }		
    	});
	}, 
	
	this.closeActiveChatDialog = function(chat_id, tabs, hidetab)
	{    
	    $.postJSON(this.wwwDir + this.closechatadmin + chat_id ,{}, function(data){ 
	        	    	   
	    });	 
	    
	    if ($('#CSChatMessage-'+chat_id).length != 0){	    	    
	       $('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	    }
	        
	    if (hidetab == true) {	
	        	        
	        var selected = tabs.find('dd.active');
	    	var hrefid = selected.find('a').attr('href');
	    	$(hrefid+'Tab').remove();
	    	selected.remove();
	    	tabs.find('dd:eq(0)').addClass("active");
	    	$('#tabs-content').find('li:eq(0)').show();

	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close(); 
	        }
	            
	    }
	    
	    this.removeSynchroChat(chat_id); 
	    this.syncadmininterfacestatic();
	       
	};	
	
	this.removeDialogTab = function(chat_id, tabs, hidetab)
	{
	    if ($('#CSChatMessage-'+chat_id).length != 0){	    	    
	       $('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	    }
	  	 	    
	    if (hidetab == true) {	
	        	        	        
	    	// Remove active tab
	    	var selected = tabs.find('dd.active');
	    	var hrefid = selected.find('a').attr('href');	        
	    	$(hrefid+'Tab').remove();
	    	selected.remove();	            
	    	tabs.find('dd:eq(0)').addClass("active");
	    	$('#tabs-content').find('li:eq(0)').show();
	    	
	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close(); 
	        }
	            
	    }
	    
	    this.removeSynchroChat(chat_id); 
	    this.syncadmininterfacestatic();
	};
	
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
	    });	

	     if (hidetab == true) {	
	        	        	            
	        // Remove active tab
	    	var selected = tabs.find('dd.active');
	    	var hrefid = selected.find('a').attr('href');	        
	    	$(hrefid+'Tab').remove();
	    	selected.remove();	            
	    	tabs.find('dd:eq(0)').addClass("active");
	    	$('#tabs-content').find('li:eq(0)').show();
	        	        
	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close();
	        }	            
	    }
	        
	    this.syncadmininterfacestatic();
	    this.removeSynchroChat(chat_id);    
	};
	
	this.rejectPendingChat = function(chat_id, tabs)
	{    
	    $.postJSON(this.wwwDir + this.deletechatadmin + chat_id ,{}, function(data){ 
	        	    	   
	    });	   
	    this.syncadmininterfacestatic();   
	};
	
	this.startChatNewWindow = function(chat_id,name)
	{    	   
	    window.open(this.wwwDir + 'chat/single/'+chat_id,'chatwindow'+name+chat_id,"menubar=1,resizable=1,width=600,height=450"); 
	    this.syncadmininterfacestatic();    
        return false;          
	};
	
	this.startChatTransfer = function(chat_id,tabs,name,transfer_id){
	    
	     $.postJSON(this.wwwDir + this.accepttransfer + transfer_id ,{}, function(data){ 
	        	    	   
	    });	    
	    this.startChat(chat_id,tabs,name);
	};
	
	this.startChatNewWindowTransfer = function(chat_id,name,transfer_id)
	{
	    $.postJSON(this.wwwDir + this.accepttransfer + transfer_id ,{}, function(data){ 
	        	    	   
	    });	    
	    return this.startChatNewWindow(chat_id,name);
	};
	
	this.blockUser = function(chat_id,msg) {
	    if (confirm(msg)) {
	        $.postJSON(this.wwwDir + 'chat/blockuser/' + chat_id,{}, function(data){ 
	      	     alert(data.msg);	      	      	        
    	    });
	    }
	};
	
	this.transferChat = function(chat_id)
	{
	    var user_id = $('[name=TransferTo'+chat_id+']:checked').val();	  
	    	
	    $.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id ,{}, function(data){ 
	        
	        if (data.error == 'false')
	        {
	        	$('#transfer-block-'+data.chat_id).html(data.result);
	        
	        }
	        	    	   
	    });
	};
	
	this.chatTabsOpen = function ()
	{
	    window.open(this.wwwDir + 'chat/chattabs/','chatwindows',"menubar=1,resizable=1,width=580,height=460");	
	    return false;
	};
	
	this.deleteChatNewWindow = function()
	{
	    this.chattabs = null;
	};
	
	this.userclosedchat = function()
	{
	    $.postJSON(this.wwwDir + this.userclosechaturl + this.chat_id + '/' + this.hash ,{}, function(data){ 
	      	        	        		
    	});
	};
	
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
	               
	               setTimeout(chatsyncuserpending,confLH.chat_message_sinterval);
	               
	            } else {
	               $('#status-chat').html(data.result); 	               
	            }
	        }	        		
    	});
	};	
	
	this.playNewMessageSound = function() {
	    	
	    if (Modernizr.audio) {    
    	    var audio = new Audio();            
            audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_message.wav';
            
            audio.load();
            audio.play();
	    }
	};
	
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
                            
                            if ( confLH.new_message_sound_admin_enabled == 1 ) {
                                inst.playNewMessageSound();
                            };
                                                       
        	            }; 
        	            
        	            if (data.result_status != 'false')
        	            {
        	                $.each(data.result_status,function(i,item) {    
        	                      if (item.tp == 'true') {                     
                                      $('#user-is-typing-'+item.chat_id).fadeIn();  
        	                      } else {
        	                          $('#user-is-typing-'+item.chat_id).fadeOut(); 
        	                      }     		              
                            });
        	            };        	            
        	             			     	
            			setTimeout(chatsyncadmin,confLH.chat_message_sinterval);	
        	        };
        	        
        	        //Allow another request to send check for messages
        	        lhinst.setSynchronizationRequestSend(false);
        	        		
            	});
	        } else {
	            setTimeout(chatsyncadmin,confLH.chat_message_sinterval);	
	        }
        	
	    } else {
	        this.isSinchronizing = false;
	    }
	};
	
	this.updateChatLastMessageID = function(chat_id,message_id)
	{
	    this.chatsSynchronisingMsg[this.getChatIndex(chat_id)] = chat_id+','+message_id;
	};
	
	
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
                        if (inst.trackLastIDS[item.last_id_identifier] == undefined ) {
                            inst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);                    
                        } else if (inst.trackLastIDS[item.last_id_identifier] < parseInt(item.last_id)) {                            
                            inst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);
                            inst.playSoundNewAction(item.last_id_identifier);                           
                        }                    
                    };           
                });
	              			     	
    			setTimeout(chatsyncadmininterface,confLH.back_office_sinterval);	
	        }		
    	});
	};
	
	this.playSoundNewAction = function(identifier) {	    
	    if (confLH.new_chat_sound_enabled == 1 && identifier == 'pending_chat') {
	        if (Modernizr.audio) {    
        	    var audio = new Audio();            
                audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.ogg' :
                            Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_chat.wav';
                
                audio.load();
                audio.play();
    	    }
	    }
	};
	
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
	};	
	
	this.transferUserDialog = function(chat_id,title)
	{		
		$.colorbox({width:'400px',height:'200px', href:this.wwwDir + 'chat/transferchat/'+chat_id});
	};
			
    this.addmsgadmin = function (chat_id)
    {        
        this.underMessageAdd = true;
        
        var pdata = {
				msg	: $("#CSChatMessage-"+chat_id).val()
		};
		
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
    };  
    
    this.addmsguser = function ()
    {        
        var pdata = {
				msg	: $("#CSChatMessage").val()
		};
        
        var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
        
		$('#CSChatMessage').attr('value','');
			
       $.postJSON(this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow, pdata , function(data){	
		    
		    $('#messagesBlock').append(data.result);
		    $('#messagesBlock').animate({ scrollTop: $("#messagesBlock").prop("scrollHeight") }, 3000); 		    
		    $('#CSChatMessage').val('');
		    		
           return true;	          
		});
    }; 
          
    this.startSyncAdmin = function()
    {
        if (this.isSinchronizing == false)
        {
            this.isSinchronizing = true;
            this.syncadmincall();
        }
    };

    this.syncOnlineUsers = function()
    {
        $.getJSON(this.wwwDir + 'chat/onlineusers/(method)/ajax', {} , function(data) {		                      
           $('#online-users').html(data.result); 
           setTimeout(function(){
               lhinst.syncOnlineUsers();
           },10000); // Check online users for every 10 seconds
		});
    };
}

var lhinst = new lh();



/*Helper functions*/
function chatsyncuser()
{
    lhinst.syncusercall();    
}

function startOnlineSync()
{
    lhinst.syncOnlineUsers();
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