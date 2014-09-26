function csrfSafeMethod(method) {
    // these HTTP methods do not require CSRF protection
    return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
};

$.ajaxSetup({
    crossDomain: false, // obviates need for sameOrigin test
    cache: false,
    beforeSend: function(xhr, settings) {
        if (!csrfSafeMethod(settings.type)) {
            xhr.setRequestHeader("X-CSRFToken", confLH.csrf_token);
        }
    }
});


$.postJSON = function(url, data, callback) {
	return $.post(url, data, callback, "json");
};



/*Port FN accordion*/
(function(e,t,n){"use strict";e.fn.foundationAccordion=function(t){var n=function(e){return e.hasClass("hover")&&!Modernizr.touch};e(document).on("mouseenter",".accordion-lhc li",function(){var t=e(this).parent();if(n(t)){var r=e(this).children(".content-lhc").first();e(".content-lhc",t).not(r).hide().parent("li").removeClass("active-lhc"),r.show(0,function(){r.parent("li").addClass("active-lhc")})}}),e(document).on("click.fndtn",".accordion-lhc li .title-lhc",function(){var t=e(this).closest("li"),r=t.parent();if(!n(r)){var i=t.children(".content-lhc").first();t.hasClass("active-lhc")?r.find("li").removeClass("active-lhc").end().find(".content-lhc").hide():(e(".content-lhc",r).not(i).hide().parent("li").removeClass("active-lhc"),i.show(0,function(){i.parent("li").addClass("active-lhc")}))}})}})(jQuery,this);

var LHCCallbacks = {};

function lh(){

    this.wwwDir = WWW_DIR_JAVASCRIPT;
    this.addmsgurl = "chat/addmsgadmin/";
    this.addmsgurluser = "chat/addmsguser/";
    this.addmsgurluserchatbox = "chatbox/addmsguser/";
    this.syncuser = "chat/syncuser/";
    this.syncadmin = "chat/syncadmin/";
    this.closechatadmin = "chat/closechatadmin/";
    this.deletechatadmin = "chat/deletechatadmin/";
    this.checkchatstatus = "chat/checkchatstatus/";
    this.syncadmininterfaceurl = "chat/syncadmininterface/";
    this.accepttransfer = "chat/accepttransfer/";
    this.trasnsferuser = "chat/transferuser/";
    this.userclosechaturl = "chat/userclosechat/";
    this.disableremember = false;
    this.operatorTyping = false;
    this.appendSyncArgument = '';
    
    // On chat hash and chat_id is based web user chating. Hash make sure chat security.
    this.chat_id = null;
    this.hash = null;

    this.soundIsPlaying = false;
    this.soundPlayedTimes = 0;
    
    // Used for synchronization for user chat
    this.last_message_id = 0;

    // Is synchronization under progress
    this.isSinchronizing = false;

    // is Widget mode
    this.isWidgetMode = false;

    this.syncroRequestSend = false;

    this.currentMessageText = '';

    this.setWidgetMode = function(status) {
    	this.isWidgetMode = status;
    };

    this.setSynchronizationRequestSend = function(status)
    {
        this.syncroRequestSend = status;
    };

    this.setSyncUserURL = function(url) {
    	this.syncuser = url;
    };

    this.trackLastIDS = {};

    // Chats currently under synchronization
    this.chatsSynchronising = [];
    this.chatsSynchronisingMsg = [];
    
    // Notifications array
    this.notificationsArray = [];

    // Block synchronization till message add finished
    this.underMessageAdd = false;


    this.closeWindowOnChatCloseDelete = false;

    this.userTimeout = false;

    this.lastOnlineSyncTimeout = false;

    this.setLastUserMessageID = function(message_id) {
    	this.last_message_id = message_id;
    };

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

    this.setDisableRemember = function (value)
    {
        this.disableremember = value;
    };

    this.setSynchronizationStatus = function(status)
    {
        this.underMessageAdd = status;
    };

    this.addTab = function(tabs, url, name, chat_id) {
    	tabs.find('> section.active').removeClass("active").attr('style','');    
    	tabs.append('<section class="active" id="chat-tab-'+chat_id+'"><p class="title"><a class="chat-tab-item" id="chat-id-'+chat_id+'" href="#chat'+chat_id+'">'+ '<i id="user-chat-status-'+chat_id+'" class="icon-user-status icon-user icon-user-online"></i>' + name.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</a><a href="#" onclick="return lhinst.removeDialogTab('+chat_id+',$(\'#tabs\'),true)" class="icon-cancel icon-close-chat"></a></p><div class="content" data-slug="chat'+chat_id+'" id="simple'+chat_id+'Tab">...</div></section>');

    	$('#chat-id-'+chat_id).click(function() {
    		var inst = $(this);
    		setTimeout(function(){
    			inst.find('.msg-nm').remove();
    			inst.removeClass('has-pm');
    			$(document).foundation('section', 'resize');
    			$('#messagesBlock-'+chat_id).animate({ scrollTop: $('#messagesBlock-'+chat_id).prop('scrollHeight') }, 1000);
    		},500);
    	});

    	$.get(url, function(data) {
    		  $('#simple'+chat_id+'Tab').html(data);    		
    		  $(document).foundation('section', 'resize');    		
    	});
    };

    this.attachTabNavigator = function(){
    	$('#tabs > section > p.title > a.chat-tab-item').click(function(){
    		$(this).find('.msg-nm').remove();
    		$(this).removeClass('has-pm');
    	});
    };

    this.startChat = function (chat_id,tabs,name) {
        if ( this.chatUnderSynchronization(chat_id) == false ) {
        	var rememberAppend = this.disableremember == false ? '/(remember)/true' : '';
        	this.addTab(tabs, this.wwwDir +'chat/adminchat/'+chat_id+rememberAppend, name, chat_id);
        	var inst = this;
        	 setTimeout(function(){
     	    	inst.syncadmininterfacestatic();
     	    },1000);
        }
    };

    this.protectCSFR = function()
    {
    	$('a.csfr-required').click(function(){
    		var inst = $(this);
    		if (!inst.attr('data-secured')){
        		inst.attr('href',inst.attr('href')+'/(csfr)/'+confLH.csrf_token);
        		inst.attr('data-secured',1);
        	}
    	});
    };

    this.setChatHash = function (hash)
    {
        this.hash = hash;
    };

    this.addSynchroChat = function (chat_id,message_id)
    {
        this.chatsSynchronising.push(chat_id);
        this.chatsSynchronisingMsg.push(chat_id + ',' +message_id);
        
        if (LHCCallbacks.addSynchroChat) {
        	LHCCallbacks.addSynchroChat(chat_id,message_id);
        }
    };

    this.removeSynchroChat = function (chat_id)
    {
        var j = 0;

        while (j < this.chatsSynchronising.length) {

            if (this.chatsSynchronising[j] == chat_id) {

            this.chatsSynchronising.splice(j, 1);
            this.chatsSynchronisingMsg.splice(j, 1);

            } else { j++; }
        };
        
        if (LHCCallbacks.removeSynchroChat) {
        	LHCCallbacks.removeSynchroChat(chat_id);
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
                
                if (LHCCallbacks.initTypingMonitoringAdminInform) {
                	inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);   
               		LHCCallbacks.initTypingMonitoringAdminInform({'chat_id':chat_id,'status':true});
                } else {                
	                $.getJSON(www_dir + 'chat/operatortyping/' + chat_id+'/true',{ }, function(data){
	                   inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);                   
	                   if (LHCCallbacks.initTypingMonitoringAdmin) {
	                   		LHCCallbacks.initTypingMonitoringAdmin(chat_id,true);
	                   }                   
	                }).fail(function(){
	                	inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);
	                });
                }
                
            } else {
                 clearTimeout(inst.typing_timeout);
                 inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);
            }
        });
    };

    this.remarksTimeout = null;
    
    this.saveRemarks = function(chat_id) {
    	clearTimeout(this.remarksTimeout);
    	    	
    	$('#remarks-status-'+chat_id).addClass('warning-color').html('...');
    	var inst = this;
    	this.remarksTimeout = setTimeout(function(){
    		$.postJSON(inst.wwwDir + 'chat/saveremarks/' + chat_id,{'data':$('#ChatRemarks-'+chat_id).val()}, function(data){
    			$('#remarks-status-'+chat_id).removeClass('warning-color').html('');
            });
    	},500);    	
    };
    
    this.closeWindow  = function() {
    	window.open('','_self','');
    	window.close();
    };

    this.typingStoppedOperator = function(chat_id) {
        var inst = this;
        if (inst.is_typing == true){
        	
        	if (LHCCallbacks.typingStoppedOperatorInform) {
        		inst.is_typing = false;
           		LHCCallbacks.typingStoppedOperatorInform({'chat_id':chat_id,'status':false});
            } else {        	
	            $.getJSON(this.wwwDir + 'chat/operatortyping/' + chat_id+'/false',{ }, function(data){
	                inst.is_typing = false;                
	                if (LHCCallbacks.initTypingMonitoringAdmin) {
	               		LHCCallbacks.initTypingMonitoringAdmin(chat_id,false);
	                };
	            }).fail(function(){
	            	inst.is_typing = false;
	            });
            }
        }
    };

    this.cancelcolorbox = function(){
    	$.colorbox.remove();
    };

    this.sendemail = function(){
    	$.postJSON(this.wwwDir + 'chat/sendchat/' + this.chat_id+'/'+this.hash,{csfr_token:confLH.csrf_token, email:$('input[name="UserEmail"]').val()}, function(data){
    		if (data.error == 'false') {
    			$.colorbox.remove();
    		} else {
    			$('#user-action .alert-box').remove();
    			$('#user-action').prepend(data.result);
    			$.colorbox.resize();
    		}
    	});
    };

    this.reopenchat = function(inst){
    	 $.postJSON(this.wwwDir + 'chat/reopenchat/' + inst.attr('data-id'), function(data){
             if (data.error == 'true') {
            	 alert(data.result);
             } else {
            	 $('#action-block-row-'+ inst.attr('data-id')+' .send-row').removeClass('hide');
            	 $('#CSChatMessage-'+inst.attr('data-id')).removeAttr('readonly').focus();
            	 $('#chat-status-text-'+inst.attr('data-id')).text(data.status);
            	 inst.remove();
             }
         });
    };

    this.initTypingMonitoringUser = function(chat_id) {

        var www_dir = this.wwwDir;
        var inst = this;        
        
        if (sessionStorage && sessionStorage.getItem('lhc_ttxt') && sessionStorage.getItem('lhc_ttxt') != '') {
        	jQuery('#CSChatMessage').val(sessionStorage.getItem('lhc_ttxt'));
    	}
                
        jQuery('#CSChatMessage').bind('keyup', function (evt){
        	
        	 if (sessionStorage) {
        		 sessionStorage.setItem('lhc_ttxt',$(this).val());
         	 };
        	
            if (inst.is_typing == false) {
                inst.is_typing = true;
                clearTimeout(inst.typing_timeout);
                                
                if (LHCCallbacks.initTypingMonitoringUserInform) {
                	inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);},3000);
               		LHCCallbacks.initTypingMonitoringUserInform({'chat_id':chat_id,'hash':inst.hash,'status':true,msg:$(this).val()});               		
                } else {               
	                $.postJSON(www_dir + 'chat/usertyping/' + chat_id+'/'+inst.hash+'/true',{msg:$(this).val()}, function(data){
	                   inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);},3000);
	                   
	                   if (LHCCallbacks.initTypingMonitoringUser) {
	                   		LHCCallbacks.initTypingMonitoringUser(chat_id,true);
	                   };
	                   
	                }).fail(function(){
	                	inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);},3000);
	                });
                }
                                
            } else {
                 clearTimeout(inst.typing_timeout);
                 inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);},3000);
                 var txtArea = $(this).val();
                 if (inst.currentMessageText != txtArea ) {
                	 if ( Math.abs(inst.currentMessageText.length - txtArea.length) > 6) {
                		 inst.currentMessageText = txtArea;                		 
                		 if (LHCCallbacks.initTypingMonitoringUserInform) {                         	
                        		LHCCallbacks.initTypingMonitoringUserInform({'chat_id':chat_id,'hash':inst.hash,'status':true,msg:txtArea});               		
                         } else {                		 
	                		 $.postJSON(www_dir + 'chat/usertyping/' + chat_id+'/'+inst.hash+'/true',{msg:txtArea}, function(data){
	                			 if (LHCCallbacks.initTypingMonitoringUser) {
	                            		LHCCallbacks.initTypingMonitoringUser(chat_id,true);
	                             };
	                		 });
                		 }
                	 }
                 }
            }
        });
    };

    this.typingStoppedUser = function(chat_id) {
        var inst = this;
        if (inst.is_typing == true){        	
        	if (LHCCallbacks.typingStoppedUserInform) {   
        		inst.is_typing = false;
        		LHCCallbacks.typingStoppedUserInform({'chat_id':chat_id,'hash':this.hash,'status':false});               		
        	} else {        	        	
	            $.getJSON(this.wwwDir + 'chat/usertyping/' + chat_id+'/'+this.hash+'/false',{ }, function(data){
	                inst.is_typing = false;
	                if (LHCCallbacks.initTypingMonitoringUser) {
	            		LHCCallbacks.initTypingMonitoringUser(chat_id,false);
	                };
	            }).fail(function(){
	            	inst.is_typing = false;
	            });
            }
        }
    };

    this.refreshFootPrint = function(inst) {
    	inst.addClass('disabled');
    	$.get(this.wwwDir + 'chat/chatfootprint/' + inst.attr('rel'),{ }, function(data){
    		$('#footprint-'+inst.attr('rel')).html(data);
    		inst.removeClass('disabled');
    	});
    };

    this.refreshOnlineUserInfo = function(inst) {
    	 inst.addClass('disabled');
    	 $.get(this.wwwDir + 'chat/refreshonlineinfo/' + inst.attr('rel'),{ }, function(data){
    		 $('#online-user-info-'+inst.attr('rel')).html(data);
    		 inst.removeClass('disabled');
         });
    };

    this.processCollapse = function(chat_id)
    {
    	if (!$('#chat-main-column-'+chat_id+' .collapse-right').hasClass('icon-left-circled')){
	    	$('#chat-right-column-'+chat_id).hide();
	    	$('#chat-main-column-'+chat_id).addClass('large-12');
	    	$('#chat-main-column-'+chat_id+' .collapse-right').addClass('icon-left-circled').removeClass('icon-right-circled');
    	} else {
    		$('#chat-right-column-'+chat_id).show();
	    	$('#chat-main-column-'+chat_id).removeClass('large-12');
	    	$('#chat-main-column-'+chat_id+' .collapse-right').removeClass('icon-left-circled').addClass('icon-right-circled');
	    	$(document).foundation('section', 'reflow');
    	};
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

    this.updateUserSyncInterface = function(inst,data)
    {
    	try {
	        // If no error
	        if (data.error == 'false')
	        {
	           if (data.blocked != 'true')
	           {
    	            if (data.result != 'false' && data.status == 'true')
    	            {
                			$('#messagesBlock').append(data.result);
                			$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 1000);

                			// If one the message owner is not current user play sound
                			if ( confLH.new_message_sound_user_enabled == 1 && data.uw == 'false') {
                			     inst.playNewMessageSound();
                			};
                			
                			// Set last message ID                			
                			inst.last_message_id = data.message_id;
                			
                			

    	            } else {
    	                if ( data.status != 'true') $('#status-chat').html(data.status);
    	            }

    	            inst.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
    	            
    	            if (data.cs == 't') {
        				inst.chatsyncuserpending();
        			}
    	            
        			if ( data.ott != '' ) {
        				var instStatus = $('#id-operator-typing');
        				instStatus.find('i').html(data.ott);
        				instStatus.fadeIn();
        				inst.operatorTyping = true;
        			} else {
        				inst.operatorTyping = false;
        			    $('#id-operator-typing').fadeOut();
        			}
        			
        			// Execute pending operations
        			if (data.op != '') {
	   	    			 $.each(data.op,function(i,item) {	   	    				 	   	    				 		
	   	    				 	 if (item.indexOf('lhinst.') != -1) { // Internal operation
	   	    				 		eval(item);	
	   	    				 	 } else if (inst.isWidgetMode) {
	   	    				 		 parent.postMessage(item, '*');
	   	    					 } else if (window.opener) {
	   	    						 window.opener.postMessage(item, '*');	  
	   	    					 };
	   	    			 });	    			
        			};	
        			
        			
	           } else {
	               $('#status-chat').html(data.status);
	               $('#ChatMessageContainer').remove();
	               $('#ChatSendButtonContainer').remove();
	           }
	        };
        } catch(err) {		     
        	inst.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
        };

        inst.syncroRequestSend = false;
    };
    
    this.syncusercall = function()
	{
	    var inst = this;
	    if (this.syncroRequestSend == false)
        {
		    clearTimeout(inst.userTimeout);
		    this.syncroRequestSend = true;
		    var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
		    var operatorTyping = this.operatorTyping == true ? '/(ot)/t' : '';
		    $.getJSON(this.wwwDir + this.syncuser + this.chat_id + '/'+ this.last_message_id + '/' + this.hash + modeWindow + operatorTyping ,{ }, function(data){
		    			    	
		    	inst.updateUserSyncInterface(inst,data);
		        
		        if (LHCCallbacks.syncusercall) {
	        		LHCCallbacks.syncusercall(inst,data);
	        	};
		        
	    	}).fail(function(){
	    		inst.syncroRequestSend = false;
	    		inst.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
	    	});
	    }
	};
	
	this.scheduleSync = function() {
		this.syncroRequestSend = false;
		this.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
	};

	this.closeActiveChatDialog = function(chat_id, tabs, hidetab)
	{
	    $.ajax({
	        type: "POST",
	        url: this.wwwDir + this.closechatadmin + chat_id,
	        async: false
	    });

	    if ($('#CSChatMessage-'+chat_id).length != 0){
	    	$('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	       $('#CSChatMessage-'+chat_id).unbind('keyup', 'up', function(){});
	    };

	    if (hidetab == true) {

	    	var index = tabs.find(' > section.active').index();
	    	tabs.find(' > section.active').remove();
			tabs.find(' > section:eq(' + (index - 1) + ')').addClass("active");

			$(document).foundation('section', 'resize');

	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close();
	        }

	    };
	    
	    if (LHCCallbacks.chatClosedCallback) {
        	LHCCallbacks.chatClosedCallback(chat_id);
        };
	    
	    this.removeSynchroChat(chat_id);
	    this.syncadmininterfacestatic();

	};

	this.startChatCloseTabNewWindow = function(chat_id, tabs, name)
	{
		window.open(this.wwwDir + 'chat/single/'+chat_id,'chatwindow-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650");

	    $.ajax({
	        type: "GET",
	        url: this.wwwDir + 'chat/adminleftchat/' + chat_id,
	        async: true
	    });

    	var index = tabs.find(' > section.active').index();
    	tabs.find(' > section.active').remove();
		tabs.find(' > section:eq(' + (index - 1) + ')').addClass("active");

		$(document).foundation('section', 'resize');

        if (this.closeWindowOnChatCloseDelete == true)
        {
            window.close();
        };

        this.removeSynchroChat(chat_id);
	    this.syncadmininterfacestatic();

	    return false;
	};

	this.removeDialogTab = function(chat_id, tabs, hidetab)
	{
	    if ($('#CSChatMessage-'+chat_id).length != 0){
	    	$('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	       $('#CSChatMessage-'+chat_id).unbind('keyup', 'up', function(){});
	    }
	    
	    this.removeSynchroChat(chat_id);
	    
	    if (hidetab == true) {

	    	$.ajax({
		        type: "GET",
		        url: this.wwwDir + 'chat/adminleftchat/' + chat_id,
		        async: true
		    });

	    	var index = tabs.find(' > #chat-tab-'+chat_id).index();
	    	tabs.find(' > #chat-tab-'+chat_id).remove();
			tabs.find(' > section:eq(' + (index - 1) + ')').addClass("active");


			$(document).foundation('section', 'resize');

	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close();
	        };
	    };
	    
	   
	    this.syncadmininterfacestatic();
	};

	this.removeActiveDialogTag = function(tabs) {

		var index = tabs.find(' > section.active').index();
    	tabs.find(' > section.active').remove();
		tabs.find(' > section:eq(' + (index - 1) + ')').addClass("active");

		$(document).foundation('section', 'resize');

        if (this.closeWindowOnChatCloseDelete == true)
        {
            window.close();
        };
	};

	this.deleteChat = function(chat_id, tabs, hidetab)
	{
	    if ($('#CSChatMessage-'+chat_id).length != 0){
	    	$('#CSChatMessage-'+chat_id).unbind('keydown', 'enter', function(){});
	       $('#CSChatMessage-'+chat_id).unbind('keyup', 'up', function(){});
	    }
	    	    
	    $.ajax({
	        type: "POST",
	        url: this.wwwDir + this.deletechatadmin + chat_id,
	        cache: false,
	        dataType: 'json',
	        async: false
	    }).done(function(data){	    
	    	if (data.error == 'true')
		    {
		       alert(data.result);
		    }
	    });
	    
	     if (hidetab == true) {

	        // Remove active tab
	    	var index = tabs.find(' > section.active').index();
		    tabs.find(' > section.active').remove();
			tabs.find(' > section:eq(' + (index - 1) + ')').addClass("active");

			$(document).foundation('section', 'resize');


	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close();
	        }
	    };

	    if (LHCCallbacks.chatDeletedCallback) {
        	LHCCallbacks.chatDeletedCallback(chat_id);
        };
	    
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
	    window.open(this.wwwDir + 'chat/single/'+chat_id,'chatwindow-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650").focus();
	    var inst = this;
	    setTimeout(function(){
	    	inst.syncadmininterfacestatic();
	    },1000);	   
        return false;
	};

	this.startChatTransfer = function(chat_id,tabs,name,transfer_id){
		var inst = this;
	    $.getJSON(this.wwwDir + this.accepttransfer + transfer_id ,{}, function(data){
	    	inst.startChat(chat_id,tabs,name);
	    	
	    	if (LHCCallbacks.operatorAcceptedTransfer) {
	       		LHCCallbacks.operatorAcceptedTransfer(chat_id);
	    	};
	    	
	    }).fail(function(){
	    	inst.startChat(chat_id,tabs,name);
	    });
	};

	this.startChatNewWindowTransfer = function(chat_id,name,transfer_id)
	{
		$.getJSON(this.wwwDir + this.accepttransfer + transfer_id ,{}, function(data){
			if (LHCCallbacks.operatorAcceptedTransfer) {
	       		LHCCallbacks.operatorAcceptedTransfer(chat_id);
	    	};
		});
		return this.startChatNewWindow(chat_id,name);
	};

	this.startChatNewWindowTransferByTransfer = function(transfer_id)
	{
		var inst = this;
		$.ajax({
	        type: "GET",
	        url: this.wwwDir + this.accepttransfer + transfer_id,
	        cache: false,
	        dataType: 'json',
	        async: false
	    }).done(function(data){	    
	    	inst.startChatNewWindow(data.chat_id,'');
	    	if (LHCCallbacks.operatorAcceptedTransfer) {
	       		LHCCallbacks.operatorAcceptedTransfer(data.chat_id);
	    	};
	    });
			   
	    this.syncadmininterfacestatic();
        return false;
	};

	this.blockUser = function(chat_id,msg) {
		if (confirm(msg)) {
			$.postJSON(this.wwwDir + 'chat/blockuser/' + chat_id,{}, function(data){
				alert(data.msg);
			});
		}
	};
	
	this.switchLang = function(form,lang){
		var languageAppend = '<input type="hidden" value="'+lang+'" name="switchLang" />';				
		form.append(languageAppend);
		form.submit();
		
		return false;
	},
	
	this.sendMail = function(chat_id) {
		$.colorbox({iframe:true, width:'90%',height:'90%', href:this.wwwDir + 'chat/sendmail/'+chat_id});
	};
	
	this.attatchLinkToFile = function(chat_id) {
		$.colorbox({iframe:true, width:'90%',height:'90%', href:this.wwwDir + 'file/attatchfile/'+chat_id});
	};
	
	this.embedFileSendMail = function(chat_id) {
		$.colorbox({iframe:true, width:'90%',height:'90%', href:this.wwwDir + 'file/attatchfilemail'});
	};	
	
	this.sendLinkToMail = function( embed_code,file_id) {
		var val = window.parent.$('#MailMessage').val();		
		window.parent.$('#MailMessage').val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('success');	
	},
	
	this.sendLinkToEditor = function(chat_id, embed_code,file_id) {
		var val = window.parent.$('#CSChatMessage-'+chat_id).val();		
		window.parent.$('#CSChatMessage-'+chat_id).val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('success');	
	},
	
	this.sendMailArchive = function(archive_id,chat_id) {
		$.colorbox({iframe:true, width:'550px',height:'500px', href:this.wwwDir + 'chatarchive/sendmail/'+archive_id+'/'+chat_id});
	};

	this.transferChat = function(chat_id)
	{
		var user_id = $('[name=TransferTo'+chat_id+']:checked').val();

		$.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id ,{'type':'user'}, function(data){
			if (data.error == 'false') {
				$('#transfer-block-'+data.chat_id).html(data.result);
			};
		});
	};

	this.previewChat = function(chat_id)
	{
		$.colorbox({'iframe':true,height:'500px',width:'500px', href:this.wwwDir+'chat/previewchat/'+chat_id});
	};
	
	this.redirectContact = function(chat_id,message){		
		if (confirm(message)){	
			$.postJSON(this.wwwDir + 'chat/redirectcontact/' + chat_id, function(data){				
				lhinst.syncadmininterfacestatic();
			});
		}		
	};
	
	this.transferChatDep = function(chat_id)
	{
	    var user_id = $('[name=DepartamentID'+chat_id+']:checked').val();
	    $.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id ,{'type':'dep'}, function(data){
	        if (data.error == 'false') {
	        	$('#transfer-block-'+data.chat_id).html(data.result);
	        };
	    });
	};

	this.chatTabsOpen = function ()
	{
	    window.open(this.wwwDir + 'chat/chattabs/','chatwindows',"menubar=1,resizable=1,width=800,height=650");
	    return false;
	};

	this.userclosedchat = function()
	{
		if (LHCCallbacks.userleftchatNotification) {
	       		LHCCallbacks.userleftchatNotification(this.chat_id);
	    };
		 
	    $.ajax({
	        type: "GET",
	        url: this.wwwDir + this.userclosechaturl + this.chat_id + '/' + this.hash,
	        cache: false,
	        async: false
	    });
	    
	   
	};

	this.userclosedchatembed = function()
	{
	    if (!!window.postMessage) {
	    	parent.postMessage("lhc_close", '*');
	    };
	};

	this.userclosedchatandbrowser = function()
	{
		if (LHCCallbacks.userleftchatNotification) {
	   		LHCCallbacks.userleftchatNotification(this.chat_id);
	    };
    
		$.get(this.wwwDir + this.userclosechaturl + this.chat_id + '/' + this.hash,function(data){
			lhinst.closeWindow();
	    });
	};

	this.sendCannedMessage = function(chat_id,link_inst)
	{
		if ($('#id_CannedMessage-'+chat_id).val() > 0) {
			link_inst.addClass('secondary');
			var delayMiliseconds = parseInt($('#id_CannedMessage-'+chat_id).find(':selected').attr('data-delay'))*1000;
			var www_dir = this.wwwDir;
			var inst  = this;
			if (inst.is_typing == false) {
	            inst.is_typing = true;
	            clearTimeout(inst.typing_timeout);
	            
	            if (LHCCallbacks.initTypingMonitoringAdminInform) {                
               		LHCCallbacks.initTypingMonitoringAdminInform({'chat_id':chat_id,'status':true});
                };
	            
	            $.getJSON(www_dir + 'chat/operatortyping/' + chat_id+'/true',{ }, function(data){
	               if (LHCCallbacks.initTypingMonitoringAdmin) {
                   		LHCCallbacks.initTypingMonitoringAdmin(chat_id,true);
                   };
                   
	               inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);link_inst.removeClass('secondary');},(delayMiliseconds > 3000 ? delayMiliseconds : 3000));
	            }).fail(function(){
	            	inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);
	            });
	        } else {
	             clearTimeout(inst.typing_timeout);
	             inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);
	             link_inst.removeClass('secondary');
	        };
	        if (delayMiliseconds > 0) {
	        	setTimeout(function(){
	        		var pdata = {
		    				msg	: $('#id_CannedMessage-'+chat_id).find(':selected').text()
		    		};
		    		$('#CSChatMessage-'+chat_id).val('');
		    		$.postJSON(www_dir + inst.addmsgurl + chat_id, pdata , function(data){
		    			if (LHCCallbacks.addmsgadmin) {
		            		LHCCallbacks.addmsgadmin(chat_id);
		            	};
		    			lhinst.syncadmincall();
		    			return true;
		    		});
	        	},delayMiliseconds);
	        } else {
	        	var pdata = {
	    				msg	: $('#id_CannedMessage-'+chat_id).find(':selected').text()
	    		};
	    		$('#CSChatMessage-'+chat_id).val('');
	    		$.postJSON(this.wwwDir + this.addmsgurl + chat_id, pdata , function(data){
	    			if (LHCCallbacks.addmsgadmin) {
	            		LHCCallbacks.addmsgadmin(chat_id);
	            	};
	    			lhinst.syncadmincall();
	    			return true;
	    		});
	        }
		};
		return false;
	};

	this.voteAction = function(inst) {
		
		var chat_id = this.chat_id;
		
		$.postJSON(this.wwwDir + 'chat/voteaction/' + this.chat_id + '/' + this.hash + '/' + inst.attr('data-id') ,{}, function(data){
			if (data.error == 'false')
	        {
				if (LHCCallbacks.uservoted) {
            		LHCCallbacks.uservoted(chat_id);
            	};
				
				if (data.status == 0) {
					$('.icon-thumbs-up').removeClass('up-voted');
					$('.icon-thumbs-down').removeClass('down-voted');
				} else if (data.status == 1) {
					$('.icon-thumbs-up').addClass('up-voted');
					$('.icon-thumbs-down').removeClass('down-voted');
				} else if (data.status == 2) {
					$('.icon-thumbs-up').removeClass('up-voted');
					$('.icon-thumbs-down').addClass('down-voted');
				}
	        }
    	});
	};

	this.chatsyncuserpending = function ()
	{	
		var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';	
		var inst = this;
	    $.getJSON(this.wwwDir + this.checkchatstatus + this.chat_id + '/' + this.hash + modeWindow ,{}, function(data){
	        // If no error
	        if (data.error == 'false')
	        {
	            if (data.activated == 'false')
	            {
	               if (data.result != 'false')
	               {
	                   $('#status-chat').html(data.result);
	               }

	               if (data.ru != '') {	            	   
	            	   document.location = data.ru;
	               }
	               
	               setTimeout(chatsyncuserpending,confLH.chat_message_sinterval);

	            } else {
	            	$('#status-chat').html(data.result);	                
	            }
	        }
    	}).fail(function(){
    		setTimeout(chatsyncuserpending,confLH.chat_message_sinterval);
    	});
	};

	this.isBlinking = false;

	this.startBlinking = function(){
		if (this.isBlinking == false) {
        	var inst = this;
            var newExcitingAlerts = (function () {
            	  var oldTitle = document.title;
            	  var msg = "!!! "+document.title;
            	  var timeoutId;
            	  var blink = function() { document.title = document.title == msg ? ' ' : msg; };
            	  var clear = function() {
            	    clearInterval(timeoutId);
            	    document.title = oldTitle;
            	    window.onmousemove = null;
            	    timeoutId = null;
            	    inst.isBlinking = false;
            	  };
            	  return function () {
            	    if (!timeoutId) {
            	      timeoutId = setInterval(blink, 1000);
            	      window.onmousemove = clear;
            	    }
            	  };
            }());
            newExcitingAlerts();
            this.isBlinking = true;
        };
	};

	this.playNewMessageSound = function() {

	    if (Modernizr.audio) {
    	    var audio = new Audio();
    	    audio.autoplay = 'autoplay';
            audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_message.wav';

            audio.load();
	    };

	    if(!$("textarea[name=ChatMessage]").is(":focus")) {
	    	this.startBlinking();
    	};
	};

	this.playInvitationSound = function() {

		if (Modernizr.audio) {
    	    var audio = new Audio();

            audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/invitation.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/invitation.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/invitation.wav';
            audio.load();

            setTimeout(function(){
            	audio.play();
            },500);


	    }
	};

    this.syncadmincall = function()
	{
	    if (this.chatsSynchronising.length > 0)
	    {
	        if (this.underMessageAdd == false && this.syncroRequestSend == false)
	        {
	            this.syncroRequestSend = true;

                clearTimeout(this.userTimeout);
        	    $.postJSON(this.wwwDir + this.syncadmin ,{ 'chats[]': this.chatsSynchronisingMsg }, function(data){
        	    	
        	    	try {
	        	        // If no error
	        	        if (data.error == 'false')
	        	        {
	        	            if (data.result != 'false')
	        	            {
	        	                $.each(data.result,function(i,item) {
	                                  $('#messagesBlock-'+item.chat_id).append(item.content);
	        		                  $('#messagesBlock-'+item.chat_id).animate({ scrollTop: $("#messagesBlock-"+item.chat_id).prop("scrollHeight") }, 1000);
	        		                  lhinst.updateChatLastMessageID(item.chat_id,item.message_id);
	
	        		                  var mainElement = $('#chat-id-'+item.chat_id);
	
	        		                  if (!mainElement.parent().parent().hasClass('active')) {
	        		                	  if (mainElement.find('span.msg-nm').length > 0) {
	        		                		  mainElement.find('span.msg-nm').html(' (' + (parseInt(mainElement.find('span.msg-nm').attr('rel')) + item.mn) + ')' );
	        		                	  } else {
	        		                		  mainElement.append('<span rel="'+item.mn+'" class="msg-nm"> ('+item.mn+')</span>');
	        		                		  mainElement.addClass('has-pm');
	        		                		  $(document).foundation('section', 'resize');
	        		                	  }
	        		                  }
	        		                  
	        		                  if ( confLH.new_message_browser_notification == 1 && data.uw == 'false') {	        		                	 
	        		                	  lhinst.showNewMessageNotification(item.chat_id,item.msg,item.nck);
	  	                			  };
	  	                			
	                            });
	
	                            if ( confLH.new_message_sound_admin_enabled == 1  && data.uw == 'false') {
	                            	lhinst.playNewMessageSound();
	                            };
	                            
	                            
	        	            };
	
	        	            if (data.result_status != 'false')
	        	            {
	        	                $.each(data.result_status,function(i,item) {
	        	                      if (item.tp == 'true') {
	                                      $('#user-is-typing-'+item.chat_id).html(item.tx).fadeIn();
	        	                      } else {
	        	                          $('#user-is-typing-'+item.chat_id).fadeOut();
	        	                      };
	
	        	                      if (item.us == 0){
	        	                    	  $('#user-chat-status-'+item.chat_id).addClass('icon-user-online');
	        	                      } else {
	        	                    	  $('#user-chat-status-'+item.chat_id).removeClass('icon-user-online');
	        	                      };
	        	                      
	        	                      if (typeof item.oad != 'undefined') {	        	                    
	        	                    	  eval(item.oad);
	        	                      };
	
	                            });
	        	            };
	
	        	            lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
	        	        };
        	    	} catch (err) {        	    	
        	    		lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
					};
        	        									
        	        //Allow another request to send check for messages
        	        lhinst.setSynchronizationRequestSend(false);

        	        if (LHCCallbacks.syncadmincall) {
    	        		LHCCallbacks.syncadmincall(lhinst,data);
    	        	};
        	        
        	        
            	}).fail(function(){
            		lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
            		lhinst.setSynchronizationRequestSend(false);
            	});
	        } else {
	        	lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
	        }

	    } else {
	        this.isSinchronizing = false;
	    }
	};

	this.updateVoteStatus = function(chat_id) {
		$.getJSON(this.wwwDir + 'chat/updatechatstatus/'+chat_id ,{ }, function(data){
			$('#main-user-info-tab-'+chat_id).html(data.result);
		});
	};
	
	this.updateChatLastMessageID = function(chat_id,message_id)
	{
	    this.chatsSynchronisingMsg[this.getChatIndex(chat_id)] = chat_id+','+message_id;
	};

	this.requestNotificationPermission = function() {
		if (window.webkitNotifications) {
			window.webkitNotifications.requestPermission();
		} else if(window.Notification){
			Notification.requestPermission(function(permission){});
		} else {
			alert('Notification API in your browser is not supported.');
		}
	};
	
	this.playNewChatAudio = function() {	
		clearTimeout(this.soundIsPlaying);
		this.soundPlayedTimes++;
		if (Modernizr.audio) {
			
    	    var audio = new Audio();
            audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_chat.wav';
            audio.load();
            setTimeout(function(){
            	audio.play();
            },500);

            if (confLH.repeat_sound > this.soundPlayedTimes) {
            	var inst = this;
            	this.soundIsPlaying = setTimeout(function(){inst.playNewChatAudio();},confLH.repeat_sound_delay*1000);
            }            
	    };
	};
	
	this.focusChanged = function(status){		
		if (confLH.new_message_browser_notification == 1 && status == true){
			if (window.webkitNotifications || window.Notification) {
				var inst = this;
				$.each(this.chatsSynchronising, function( index, chat_id ) {				
					if (typeof inst.notificationsArrayMessages[chat_id] !== 'undefined') {					
						if (window.webkitNotifications) {
							inst.notificationsArrayMessages[chat_id].cancel();
						} else {
							inst.notificationsArrayMessages[chat_id].close();
						}
					}
				});
			}
		}
	};
	
	this.notificationsArrayMessages = [];
	
	this.showNewMessageNotification = function(chat_id,message,nick) {		
		try {
		if (window.webkitNotifications || window.Notification) {
			if (focused == false) {
				if (typeof this.notificationsArrayMessages[chat_id] !== 'undefined') {
					if (window.webkitNotifications) {
						this.notificationsArrayMessages[chat_id].cancel();
					} else {
						this.notificationsArrayMessages[chat_id].close();
					}
				};
				if (window.webkitNotifications) {
			    	  var havePermission = window.webkitNotifications.checkPermission();
			    	  if (havePermission == 0) {
			    	    // 0 is PERMISSION_ALLOWED
			    	    var notification = window.webkitNotifications.createNotification(
			    	      WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png',
			    	      nick,
			    	      message
			    	    );
			    	    notification.onclick = function () {
			    	    	window.focus();
			    	    	notification.cancel();
			    	    };
			    	    notification.show();
			    	    this.notificationsArrayMessages[chat_id] = notification;
			    	  }
		    	  } else if(window.Notification) {
		    		  if (window.Notification.permission == 'granted') {
			  				var notification = new Notification(nick, { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: message });
			  				notification.onclick = function () {			  				
			  					window.focus();
				    	        notification.close();				    	        
				    	    };				    	
				    	    this.notificationsArrayMessages[chat_id] = notification;
			    	  }
		    	  }
			}			
		  }
		} catch(err) {		     
        	console.log(err);
        };		
	};	
	
	this.playSoundNewAction = function(identifier,chat_id,nick,message) {
	    if (confLH.new_chat_sound_enabled == 1 && (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat')) {
	    	this.soundPlayedTimes = 0;
	        this.playNewChatAudio();
	    };
    
	    if(!$("textarea[name=ChatMessage]").is(":focus")) {
	    	this.startBlinking();
    	};

	    var inst = this;
	    if ( (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat') && (window.webkitNotifications || window.Notification)) {

	    	 if (window.webkitNotifications) {
		    	  var havePermission = window.webkitNotifications.checkPermission();
		    	  if (havePermission == 0) {
		    	    // 0 is PERMISSION_ALLOWED
		    	    var notification = window.webkitNotifications.createNotification(
		    	      WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png',
		    	      nick,
		    	      message
		    	    );
		    	    notification.onclick = function () {
		    	    	if (identifier == 'pending_chat' || identifier == 'unread_chat'){
		    	    		inst.startChatNewWindow(chat_id,'ChatRequest');
		    	    	} else {
		    	    		inst.startChatNewWindowTransferByTransfer(chat_id);
		    	    	};
		    	        notification.cancel();
		    	    };
		    	    notification.show();
		    	    this.notificationsArray.push(notification);
		    	  }
	    	  } else if(window.Notification) {
	    		  if (window.Notification.permission == 'granted') {
		  				var notification = new Notification(nick, { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: message });
		  				notification.onclick = function () {
			    	    	if (identifier == 'pending_chat' || identifier == 'unread_chat'){
			    	    		inst.startChatNewWindow(chat_id,'ChatRequest');
			    	    	} else {
			    	    		inst.startChatNewWindowTransferByTransfer(chat_id);
			    	    	};
			    	        notification.close();
			    	    };
			    	    this.notificationsArray.push(notification);
		    	   }
	    	  }

	    };
	    
	    if (confLH.show_alert == 1) {
	    	setTimeout(function() {
	    		if ($('#right-pending-chats ul').size() > 0) {
		    		if (confirm(confLH.transLation.new_chat)){		    		
		    			if (identifier == 'pending_chat'){
		    	    		inst.startChatNewWindow(chat_id,'ChatRequest');
		    	    	} else {
		    	    		inst.startChatNewWindowTransferByTransfer(chat_id);
		    	    	};
		    		};
	    		};	    		
	    	},5000);	    	
	    };
	};

	this.hideNotifications = function(){
		
		clearTimeout(this.soundIsPlaying);
		
		$.each(this.notificationsArray,function(i,item) {
			try {
				 if (window.webkitNotifications) {
					 item.cancel();
				 } else {
					item.close();
				}
			} catch(err) {		     
	        	console.log(err);
	        };
		});
		
		// Reset array
		this.notificationsArray = [];
	};
	
	this.syncadmininterfacestatic = function()
	{
		try {
			var lhcController = angular.element('body').scope(); 
			lhcController.loadChatList();
		} catch(err) {		     
        	//
        };
        
		/*var inst = this;
	    $.getJSON(this.wwwDir + this.syncadmininterfaceurl ,{ }, function(data){
	        // If no error
	        if (data.error == 'false')
	        {
                $.each(data.result,function(i,item) {
                    if (item.content != '') {
                    	$(item.dom_id).html(item.content);
                    };

                    if (item.dom_id_status != undefined) {
                    	if (parseInt(item.dom_item_count) > 0) {
                    		$(item.dom_id_status).html(' ('+item.dom_item_count+')');
                    	} else {
                    		$(item.dom_id_status).html('');
                    	};
                    };
                });
                
                if ($('#right-pending-chats ul').size() == 0) {
                	clearTimeout(inst.soundIsPlaying);
                }
	        }
    	});*/
	};

	this.transferUserDialog = function(chat_id,title)
	{
		$.colorbox({width:'550px',height:'400px', href:this.wwwDir + 'chat/transferchat/'+chat_id});
	};

	this.addmsgadmin = function (chat_id)
	{
		var textArea = $("#CSChatMessage-"+chat_id);
		
		var pdata = {
				msg	: textArea.val()
		};
		
		textArea.val('');
		
		if (textArea.hasClass('edit-mode')) {
			
			pdata.msgid = textArea.attr('data-msgid');
			
			$.postJSON(this.wwwDir + 'chat/updatemsg/' + chat_id, pdata , function(data){
				if (data.error == 'f') {
					textArea.removeClass('edit-mode');	
					textArea.removeAttr('data-msgid');
					$('#msg-'+pdata.msgid).replaceWith(data.msg);
					
					if (LHCCallbacks.addmsgadmin) {
		        		LHCCallbacks.addmsgadmin(chat_id);
		        	};
		        	
					return true;
				}
			});
			
		} else {
			$.postJSON(this.wwwDir + this.addmsgurl + chat_id, pdata , function(data){
				
				if (LHCCallbacks.addmsgadmin) {
	        		LHCCallbacks.addmsgadmin(chat_id);
	        	};
	        	
				lhinst.syncadmincall();				
				return true;
			});
		}
	};
	
	this.editPrevious = function(chat_id) {	
		var textArea = $('#CSChatMessage-'+chat_id);
		if (textArea.val() == '') {				
			$.getJSON(this.wwwDir + 'chat/editprevious/'+chat_id, function(data){
				if (data.error == 'f') {
					textArea.val(data.msg);
					textArea.attr('data-msgid',data.id);
					textArea.addClass('edit-mode');
					$('#msg-'+data.id).addClass('edit-mode');
				}
			});			
		}
	};
	
	this.editPreviousUser = function() {	
		var textArea = $('#CSChatMessage');
		if (textArea.val() == '') {				
			$.getJSON(this.wwwDir + 'chat/editprevioususer/'+this.chat_id + '/' + this.hash, function(data){
				if (data.error == 'f'){
					textArea.val(data.msg);
					textArea.attr('data-msgid',data.id);
					textArea.addClass('edit-mode');
					$('#msg-'+data.id).addClass('edit-mode');
				}
			});			
    	}
	};
	
    this.addmsguserchatbox = function (chat_id)
    {
    	var nickCurrent = false;

    	var pdata = {
    			msg	: $("#CSChatMessage").val(),
				nick: $("#CSChatNick").val()
		};

        var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
		$('#CSChatMessage').val('');
		var inst = this;
		
        $.postJSON(this.wwwDir + this.addmsgurluserchatbox + this.chat_id + '/' + this.hash + modeWindow + this.appendSyncArgument, pdata , function(data) {
	        	    
        		if (data.error == 'f') {        	
	        		if (LHCCallbacks.addmsguserchatbox) {
	        			LHCCallbacks.addmsguserchatbox(inst,{chat_id:inst.chat_id,data:data});
		        	};
		        		        	
		        	inst.syncusercall();  
	        	} else {
	        		alert(data.or);
	        	}        	
		});

        if (nickCurrent != $("#CSChatNick").val() && !!window.postMessage && parent) {
			parent.postMessage('lhc_chb:nick:'+ $("#CSChatNick").val(), '*');
			nickCurrent = $("#CSChatNick").val();
        }
    };
    
    this.updateMessageRow = function(msgid){    	
    	var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';    	
    	$.getJSON(this.wwwDir + 'chat/getmessage/' + this.chat_id + '/' + this.hash + '/'+ msgid + modeWindow, function(data) {  
    		if (data.error == 'f') {
    			$('#msg-'+msgid).replaceWith(data.msg);
    			$('#msg-'+msgid).addClass('edit-mode-done');
    			setTimeout(function(){
    				$('#msg-'+msgid).removeClass('edit-mode-done');
    			},2000);
    		}
    	});
    };
    
    this.updateMessageRowAdmin = function(chat_id, msgid){ 
    	$.getJSON(this.wwwDir + 'chat/getmessageadmin/' + chat_id + '/' + msgid, function(data) {    	
    		if (data.error == 'f') {
    			$('#msg-'+msgid).replaceWith(data.msg);
    			$('#msg-'+msgid).addClass('edit-mode-done');
    			setTimeout(function(){
    				$('#msg-'+msgid).removeClass('edit-mode-done');
    			},2000);
    		}
		});
    };
    
    this.addmsguser = function ()
    {
    	if (LHCCallbacks.addmsguserbefore) {
    		LHCCallbacks.addmsguserbefore(this);
    	};
    	
    	var textArea = $("#CSChatMessage");
    	
        var pdata = {
				msg	: textArea.val()
		};

        var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
        textArea.val('');
		var inst = this;
		
		if (sessionStorage) {
   		   sessionStorage.setItem('lhc_ttxt','');
    	};
    	    			
		if (textArea.hasClass('edit-mode')) {
			
			pdata.msgid = textArea.attr('data-msgid');
			
			$.postJSON(this.wwwDir + 'chat/updatemsguser/' + this.chat_id + '/' + this.hash + modeWindow, pdata , function(data){
				if (data.error == 'f') {
					textArea.removeClass('edit-mode');	
					textArea.removeAttr('data-msgid');
					$('#msg-'+pdata.msgid).replaceWith(data.msg);
					return true;
				}
			});			
						
		} else { 
	        $.postJSON(this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow, pdata , function(data) {
	        	
	        	if (data.error == 'f') {
		        	if (LHCCallbacks.addmsguser) {
		        		LHCCallbacks.addmsguser(inst,data);
		        	};
		        	
		        	inst.syncusercall();
	        	} else {
	        		$('#CSChatMessage').val(pdata.msg);
	        		var instStatus = $('#id-operator-typing');
					instStatus.find('i').html(data.r);
					instStatus.fadeIn();				
	        	}
			});
        }
    };

    this.startSyncAdmin = function()
    {
        if (this.isSinchronizing == false)
        {
            this.isSinchronizing = true;
            this.syncadmincall();
        }
    };
    
    this.disableChatSoundAdmin = function(inst)
    {
    	if (inst.hasClass('icon-mute')){
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/1');
    		confLH.new_message_sound_admin_enabled = 1;
    		inst.removeClass('icon-mute');
    	} else {
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/0');
    		confLH.new_message_sound_admin_enabled = 0;
    		inst.addClass('icon-mute');
    	}
    	return false;
    };

    this.disableNewChatSoundAdmin = function(inst)
    {
    	if (inst.hasClass('icon-mute')){
    		$.get(this.wwwDir+  'user/setsettingajax/new_chat_sound/1');
    		confLH.new_chat_sound_enabled = 1;
    		inst.removeClass('icon-mute');
    	} else {
    		$.get(this.wwwDir+  'user/setsettingajax/new_chat_sound/0');
    		confLH.new_chat_sound_enabled = 0;
    		inst.addClass('icon-mute');
    	}
    	return false;
    };
    
    this.changeUserSettings = function(attr,value){
    	$.get(this.wwwDir+  'user/setsettingajax/'+attr+'/'+value);
    }; 
    
    this.changeUserSettingsIndifferent = function(attr,value){
    	$.get(this.wwwDir+  'user/setsettingajax/'+attr+'/'+value+'/(indifferent)/true');
    };
    
    this.disableUserAsOnline = function(inst)
    {
    	if (inst.hasClass('user-online-disabled')){
    		$.get(this.wwwDir+  'user/setoffline/false');
    		inst.removeClass('user-online-disabled');
    	} else {
    		$.get(this.wwwDir+  'user/setoffline/true');
    		inst.addClass('user-online-disabled');
    	}
    	return false;
    };
    
    this.closeReveal = function(id){
		$(id).foundation('reveal', 'close');
	};
	
	this.switchToOfflineForm = function(){
		var form = $('#form-start-chat');
		form.attr('action',$('#form-start-chat').attr('action')+'/(switchform)/true/(offline)/true/(leaveamessage)/true/(department)/'+$('#id_DepartamentID').val());
		form.submit();
		return false;
	};
	
    this.changeChatStatus = function(chat_id){    	
    	if ($('#myModal').size() == 0){
    		$('body').append('<div id="myModal" class="reveal-modal"></div>');    	
    	}    	
    	$('#myModal').foundation('reveal','open',{url: this.wwwDir+  'chat/changestatus/'+chat_id});
    };
    
    this.changeStatusAction = function(form,chat_id){
    	var inst = this;
    	$.postJSON(form.attr('action'),form.serialize(), function(data) {
	   		 if (data.error == 'false') {
	   			$('#myModal').foundation('reveal', 'close');
	   			inst.updateVoteStatus(chat_id);
	   		 } else {
	   			 alert(data.result);
	   		 }
	   	 });
    	return false;
    };
    
    this.changeVisibility = function(inst)
    {
    	if (inst.hasClass('user-online-disabled')){
    		$.get(this.wwwDir+  'user/setinvisible/false');
    		inst.removeClass('user-online-disabled');
    	} else {
    		$.get(this.wwwDir+  'user/setinvisible/true');
    		inst.addClass('user-online-disabled');
    	}
    	return false;
    };

    
    this.disableChatSoundUser = function(inst)
    {
    	if (inst.hasClass('icon-mute')){
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/1');
    		confLH.new_message_sound_user_enabled = 1;
    		inst.removeClass('icon-mute');
    	} else {
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/0');
    		confLH.new_message_sound_user_enabled = 0;
    		inst.addClass('icon-mute');
    	};

    	if (!!window.postMessage && parent) {
    		if (inst.hasClass('icon-mute')){
    			parent.postMessage("lhc_ch:s:0", '*');
    		} else {
    			parent.postMessage("lhc_ch:s:1", '*');
    		}
    	};

    	return false;
    };

    this.addCaptcha = function(timestamp,inst) {
    	if (inst.find('.form-protected').size() == 0){
    			 inst.find('input[type="submit"]').attr('disabled','disabled');
		    	 $.getJSON(this.wwwDir + 'captcha/captchastring/form/'+timestamp, function(data) {
		    		 inst.append('<input type="hidden" value="'+timestamp+'" name="captcha_'+data.result+'" /><input type="hidden" value="'+timestamp+'" name="tscaptcha" /><input type="hidden" class="form-protected" value="1" />');
		    		 inst.submit();
		    	 });
		    	 return false;
	   	};

	   	return true;
    };

    this.deleteChatfile = function(file_id){
    	$.postJSON(this.wwwDir + 'file/deletechatfile/' + file_id, function(data){
    		if (data.error == 'false') {
    			$('#file-id-'+file_id).remove();
    		} else {
    			alert(data.result);
    		}
    	});
    };

    this.addFileUserUpload = function(data_config) {
    	$('#fileupload').fileupload({
            url: this.wwwDir + 'file/uploadfile/'+data_config.chat_id+'/'+data_config.hash,
            dataType: 'json',
            add: function(e, data) {
                var uploadErrors = [];
                var acceptFileTypes = data_config.ft_us;                
                if (!(acceptFileTypes.test(data.originalFiles[0]['type']) || acceptFileTypes.test(data.originalFiles[0]['name']))) {
                    uploadErrors.push(data_config.ft_msg);
                };
                if(data.originalFiles[0]['size'] > data_config.fs) {
                    uploadErrors.push(data_config.fs_msg);
                };
                if(uploadErrors.length > 0) {
                    alert(uploadErrors.join("\n"));
                } else {
                    data.submit();
                };
       		},
       		done: function(e,data) {       			
       			if (LHCCallbacks.addFileUserUpload) {
       	        	LHCCallbacks.addFileUserUpload(data_config.chat_id);
       	        };			
       		},
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#id-operator-typing').show();
                $('#id-operator-typing').html(progress+'%');
            }}).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    };

    this.updateChatFiles = function(chat_id) {
    	$.postJSON(this.wwwDir + 'file/chatfileslist/' + chat_id, function(data){
    		$('#chat-files-list-'+chat_id).html(data.result);
    	});
    };

    this.addFileUpload = function(data_config) {
    	$('#fileupload-'+data_config.chat_id).fileupload({
            url: this.wwwDir + 'file/uploadfileadmin/'+data_config.chat_id,
            dataType: 'json',
            add: function(e, data) {
                var uploadErrors = [];
                var acceptFileTypes = data_config.ft_op;
                if(!(acceptFileTypes.test(data.originalFiles[0]['type']) || acceptFileTypes.test(data.originalFiles[0]['name']))) {
                    uploadErrors.push(data_config.ft_msg);
                };
                if(data.originalFiles[0]['size'] > data_config.fs) {
                    uploadErrors.push(data_config.fs_msg);
                };
                if(uploadErrors.length > 0) {
                    alert(uploadErrors.join("\n"));
                } else {
                    data.submit();
                };
       		},
       		done: function(e,data) {       			
       			lhinst.updateChatFiles(data_config.chat_id); 
       			if (LHCCallbacks.addFileUpload) {
       	        	LHCCallbacks.addFileUpload(data_config.chat_id);
       	        };
       		},
            dropZone: $('#drop-zone-'+data_config.chat_id),
            pasteZone: $('#CSChatMessage-'+data_config.chat_id),
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#user-is-typing-'+data_config.chat_id).show();
                $('#user-is-typing-'+data_config.chat_id).html(progress+'%');
            }}).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    };
    
    this.addExecutionCommand = function(online_user_id,operation) {
    	$.postJSON(this.wwwDir + 'chat/addonlineoperation/' + online_user_id,{'operation':operation}, function(data){
    		if (LHCCallbacks.addExecutionCommand) {
   	        	LHCCallbacks.addExecutionCommand(online_user_id);
   	        };
    	});
    	if (operation == 'lhc_screenshot') {
    		$('#user-screenshot-container').html('').addClass('screenshot-pending');
    		var inst = this;
    		setTimeout(function(){
    			inst.updateScreenshotOnline(online_user_id);
    		},15000);    		
    	};
    };
    
    this.addRemoteCommand = function(chat_id,operation) {
    	$.postJSON(this.wwwDir + 'chat/addoperation/' + chat_id,{'operation':operation}, function(data){
    		if (LHCCallbacks.addRemoteCommand) {
   	        	LHCCallbacks.addRemoteCommand(chat_id);
   	        };
    	});    	
    	if (operation == 'lhc_screenshot') {
    		$('#user-screenshot-container').html('').addClass('screenshot-pending');
    		var inst = this;
    		setTimeout(function(){
    			inst.updateScreenshot(chat_id);
    		},5000);    		
    	};
    };
    
    this.updateScreenshot = function(chat_id) {
    	$('#user-screenshot-container').html('').addClass('screenshot-pending');
    	$.get(this.wwwDir + 'chat/checkscreenshot/' + chat_id,function(data){
    		$('#user-screenshot-container').html(data);
    		$('#user-screenshot-container').removeClass('screenshot-pending');
    	}); 
    };

    this.updateScreenshotOnline = function(online_id) {
    	$('#user-screenshot-container').html('').addClass('screenshot-pending');
    	$.get(this.wwwDir + 'chat/checkscreenshotonline/' + online_id,function(data){
    		$('#user-screenshot-container').html(data);
    		$('#user-screenshot-container').removeClass('screenshot-pending');
    	}); 
    };

}

var lhinst = new lh();

function gMapsCallback(){

	var $mapCanvas = $('#map_canvas');

	var map = new google.maps.Map($mapCanvas[0], {
        zoom: GeoLocationData.zoom,
        center: new google.maps.LatLng(GeoLocationData.lat, GeoLocationData.lng),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        options: {
            zoomControl: true,
            scrollwheel: true,
            streetViewControl: true
        }
    });

	var locationSet = false;
	
	var processing = false;
	var pendingProcess = false;
	var pendingProcessTimeout = false;
		

	google.maps.event.addListener(map, 'idle', showMarkers);
	
	var mapTabSection = $('#map-activator').parent().parent();
	
	
	function showMarkers() {
	    if ( processing == false) {	    		
	    	if (mapTabSection.hasClass('active')) {
		        processing = true;
	    		$.ajax({
	    			url : WWW_DIR_JAVASCRIPT + 'chat/jsononlineusers'+(parseInt($('#id_department_map_id').val()) > 0 ? '/(department)/'+parseInt($('#id_department_map_id').val()) : '' )+(parseInt($('#maxRows').val()) > 0 ? '/(maxrows)/'+parseInt($('#maxRows').val()) : '' ),
	    			dataType: "json",
	    			error:function(){
	    				clearTimeout(pendingProcessTimeout);
	    				pendingProcessTimeout = setTimeout(function(){
							showMarkers();
						},10000);
	    			},
	    			success : function(response) {
	    				bindMarkers(response);
	    				processing = false;
	    				clearTimeout(pendingProcessTimeout);
	    				if (pendingProcess == true) {
	    				    pendingProcess = false;
	    				    showMarkers();
	    				} else {
	    					pendingProcessTimeout = setTimeout(function(){
	    						showMarkers();
	    					},10000);
	    				}
	    			}
	    		});
    		} else {
    			pendingProcessTimeout = setTimeout(function(){
					showMarkers();
				},10000);
    		}    		
	    } else {
	       pendingProcess = true;
	    }
 	};

 	var markers = [];
 	var markersObjects = [];

 	var infoWindow = new google.maps.InfoWindow({ content: 'Loading...' });

 	function bindMarkers(mapData) {
		$(mapData.result).each(function(i, e) {

		    if ($.inArray(e.Id,markers) == -1) {
    			var latLng = new google.maps.LatLng(e.Latitude, e.Longitude);
    			var marker = new google.maps.Marker({ position: latLng, icon : e.icon, map : map });

    			google.maps.event.addListener(marker, 'click', function() {
    				var content = '<div class="map-preview">...</div>';
    				infoWindow.setContent(content);
    				infoWindow.open(map, this);
    				$.get(WWW_DIR_JAVASCRIPT + 'chat/getonlineuserinfo/'+e.Id,function(result){
    					infoWindow.setContent(result);

    					setTimeout(function(){
    						$(document).foundation('section', 'reflow');
    					},250);


    				});
    			});

    			marker.setVisible(true);
    			marker.setAnimation(google.maps.Animation.DROP);
    			markersObjects[e.Id] = marker;
    			markers.push(e.Id);
    			clearTimeout(markersObjects[e.Id].timeOutMarker);

    			markersObjects[e.Id].timeOutMarker = setTimeout(function(){
            		markers.splice($.inArray(e.Id,markers), 1);
            		google.maps.event.clearInstanceListeners(markersObjects[e.Id]);
            		markersObjects[e.Id].setMap(null);
            		markersObjects[e.Id] = null;
            	},parseInt($('#markerTimeout option:selected').val())*1000);

            } else {
            	markersObjects[e.Id].setIcon(e.icon);
            	clearTimeout(markersObjects[e.Id].timeOutMarker);
            	markersObjects[e.Id].timeOutMarker = setTimeout(function(){
            		markers.splice($.inArray(e.Id,markers), 1);
            		google.maps.event.clearInstanceListeners(markersObjects[e.Id]);
            		markersObjects[e.Id].setMap(null);
            		markersObjects[e.Id] = null;
            	},parseInt($('#markerTimeout option:selected').val())*1000);
            }
		});
	};
	
	$('#id_department_map_id').change(function(){
		showMarkers();
		lhinst.changeUserSettingsIndifferent('omap_depid',$(this).val());
	});
	
	$('#markerTimeout').change(function(){
		showMarkers();
		lhinst.changeUserSettingsIndifferent('omap_mtimeout',$(this).val());
	});
	
	$('#map-activator').click(function(){
		setTimeout(function(){
			google.maps.event.trigger(map, 'resize');
			if (locationSet == false) {
				locationSet = true;
				map.setCenter(new google.maps.LatLng(GeoLocationData.lat, GeoLocationData.lng));
			}
		},500);
		showMarkers();
	});
	
	

};

var focused = true;
window.onfocus = window.onblur = function(e) {
    focused = (e || event).type === "focus";
    lhinst.focusChanged(focused);
};

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