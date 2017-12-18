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
    
    // Disable sync, is used in angular controllers before migration to new JS structure
    this.disableSync = false;
    
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

    // is Embed mode
    this.isEmbedMode = false;

    this.syncroRequestSend = false;

    this.currentMessageText = '';
    
    this.setWidgetMode = function(status) {
    	this.isWidgetMode = status;
    };

    this.setEmbedMode = function(status) {
    	this.isEmbedMode = status;
    };

    this.setSynchronizationRequestSend = function(status)
    {
        this.syncroRequestSend = status;
    };

    this.setSyncUserURL = function(url) {
    	this.syncuser = url;
    };

    // Chats currently under synchronization
    this.chatsSynchronising = [];
    this.chatsSynchronisingMsg = [];
    
    // Notifications array
    this.notificationsArray = [];

    this.speechHandler = false;
    
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

    this.tabIconContent = 'face';
    this.tabIconClass = 'icon-user-status material-icons icon-user-online';
    
    this.audio = new Audio();
    this.audio.autoplay = 'autoplay';

    this.reloadTab = function(chat_id, tabs, nick)
    {
        $('#ntab-chat-'+chat_id).text(nick);

        if ($('#CSChatMessage-'+chat_id).length != 0){
            $('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
            $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
        }

        this.removeSynchroChat(chat_id);
        this.removeBackgroundChat(chat_id);
        this.hideNotification(chat_id);
        var inst = this;
        $.get(this.wwwDir +'chat/adminchat/'+chat_id+'/(remember)/true', function(data) {
            $('#chat-id-'+chat_id).html(data);
            $('#CSChatMessage-'+chat_id).focus();
            inst.rememberTab(chat_id);
            ee.emitEvent('chatTabLoaded', [chat_id]);
        });
    }

    this.addTab = function(tabs, url, name, chat_id, focusTab, position) {    
    	// If tab already exits return
    	if (tabs.find('#chat-tab-link-'+chat_id).size() > 0) {
    		return ;
    	}
    	
    	var contentLi = '<li role="presentation" id="chat-tab-li-'+chat_id+'" ><a href="#chat-id-'+chat_id+'" id="chat-tab-link-'+chat_id+'" aria-controls="chat-id-'+chat_id+'" role="tab" data-toggle="tab"><i id="msg-send-status-'+chat_id+'" class="material-icons send-status-icon icon-user-online">send</i><i id="user-chat-status-'+chat_id+'" class="'+this.tabIconClass+'">'+this.tabIconContent+'</i><span class="ntab" id="ntab-chat-'+chat_id+'">' + name.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span><span onclick="return lhinst.removeDialogTab('+chat_id+',$(\'#tabs\'),true)" class="material-icons icon-close-chat">close</span></a></li>';
    	
    	if (typeof position === 'undefined' || parseInt(position) == 0) {
    		tabs.find('> ul').append(contentLi);
    	} else {
    		tabs.find('> ul > li:eq('+ (position - 1)+')').after(contentLi);
    	};
    	
    	$('#chat-tab-link-'+chat_id).click(function() {
    		var inst = $(this);
    		setTimeout(function(){
    			inst.find('.msg-nm').remove();
    			inst.removeClass('has-pm');    			
    			$('#messagesBlock-'+chat_id).stop(true,false).animate({ scrollTop: $('#messagesBlock-'+chat_id).prop('scrollHeight') }, 500);
    			$('#CSChatMessage-'+chat_id).focus();
    			ee.emitEvent('chatTabClicked', [chat_id, inst]);
    		},500);
    	});
    	
    	var hash = window.location.hash.replace('#/','#');	

    	var inst = this;

    	$.get(url, function(data) {
    		if (typeof focusTab === 'undefined' || focusTab === true || hash == '#chat-id-'+chat_id){
	    		tabs.find('> ul > li.active').removeClass("active");
	    		tabs.find('> ul > #chat-tab-li-'+chat_id).addClass("active");
	    		tabs.find('> div.tab-content > div.active').removeClass('active');
	    		tabs.find('> div.tab-content').append('<div role="tabpanel" class="tab-pane active" id="chat-id-'+chat_id+'"></div>'); 
	    		window.location.hash = '#/chat-id-'+chat_id;	
	    	} else {
	    		tabs.find('> div.tab-content').append('<div role="tabpanel" class="tab-pane" id="chat-id-'+chat_id+'"></div>');  
	    	}
    		 		
    		$('#chat-id-'+chat_id).html(data);  
    		$('#CSChatMessage-'+chat_id).focus();

            if (inst.disableremember == false) {
                inst.rememberTab(chat_id);
            }

            ee.emitEvent('chatTabLoaded', [chat_id]);
    	});
    };

    this.rememberTab = function(chat_id) {
        if (localStorage) {
            try{
                chat_id = parseInt(chat_id);

                var achat_id = localStorage.getItem('achat_id');
                var achat_id_array = new Array();

                if (achat_id !== null) {
                    var achat_id_array = achat_id.split(',').map(Number);
                }

                if (achat_id_array.indexOf(chat_id) === -1) {
                    achat_id_array.push(chat_id);
                }

                localStorage.setItem('achat_id',achat_id_array.join(','));
            } catch (e) {
                console.log(e);
            }
        }
    };
    
    this.forgetChat = function (chat_id) {
        if (localStorage) {
            try {
                chat_id = parseInt(chat_id);

                var achat_id = localStorage.getItem('achat_id');
                var achat_id_array = new Array();

                if (achat_id !== null) {
                    achat_id_array = achat_id.split(',').map(Number);
                }

                if (achat_id_array.indexOf(chat_id) !== -1){
                    achat_id_array.splice(achat_id_array.indexOf(chat_id), 1);
                }

                localStorage.setItem('achat_id',achat_id_array.join(','));
            } catch (e) {
                console.log(e);
            }

        }
    };
    
    this.attachTabNavigator = function() {
    	$('#tabs > ul.nav > li > a').click(function(){
    		$(this).find('.msg-nm').remove();
    		$(this).removeClass('has-pm');
    	});
    };

    this.holdAction = function(chat_id, inst) {

    	var _this  = this;
        $.postJSON(this.wwwDir + 'chat/holdaction/' + chat_id, function(data) {
            if (data.error == false) {

                if (data.hold == true) {
                    inst.addClass('btn-info');
				} else {
                    inst.removeClass('btn-info');
				}

				if (data.msg != '') {
					$('#messagesBlock-'+chat_id).append(data.msg);
					$('#messagesBlock-'+chat_id).stop(true,false).animate({ scrollTop: $("#messagesBlock-"+chat_id).prop("scrollHeight") }, 500);
				}

                _this.syncadmincall();
            } else {
                alert(data.msg);
            }
        });
	},

	this.copyMessages = function(inst) {

        $('#chat-copy-messages').select();
        document.execCommand("copy");

        inst.tooltip({
            trigger: 'click',
            placement: 'top'
        });

        function setTooltip(message) {
            inst.tooltip('hide')
                .attr('data-original-title', message)
                .tooltip('show');
        }

        function hideTooltip() {
            setTimeout(function() {
                inst.tooltip('hide');
            }, 3000);
        }

        setTooltip(inst.attr('data-success'));
        hideTooltip();


        return false;
	},

    this.startChat = function (chat_id,tabs,name,focusTab,position) {
    	    	
    	this.removeBackgroundChat(chat_id);
    	this.hideNotification(chat_id);
    	
        if ( this.chatUnderSynchronization(chat_id) == false ) {        	
        	var focusTabAction = typeof focusTab !== 'undefined' ? focusTab : true;
        	var rememberAppend = this.disableremember == false ? '/(remember)/true' : '';
        	this.addTab(tabs, this.wwwDir +'chat/adminchat/'+chat_id+rememberAppend, name, chat_id, focusTabAction, position);
        	var inst = this;
        	 setTimeout(function(){
     	    	inst.syncadmininterfacestatic();
     	    },1000);
        } else {
        	tabs.find('> ul > li.active').removeClass("active");
    		tabs.find('> ul > #chat-tab-li-'+chat_id).addClass("active");
    		tabs.find('> div.tab-content > div.active').removeClass('active');
    		tabs.find('> div.tab-content > #chat-id-'+chat_id).addClass('active');  
    		window.location.hash = '#/chat-id-'+chat_id;
        }
        
        ee.emitEvent('chatStartTab', [chat_id]);	
    };

    this.backgroundChats = [];
    
    this.startChatBackground = function (chat_id,tabs,name) {
    	if ( this.chatUnderSynchronization(chat_id) == false ) {  
    		this.backgroundChats.push(parseInt(chat_id));
	    	var rememberAppend = this.disableremember == false ? '/(remember)/true' : '';
	    	this.addTab(tabs, this.wwwDir +'chat/adminchat/'+chat_id+rememberAppend+'/(arg)/background', name, chat_id, false); 
	    	ee.emitEvent('chatStartBackground', [chat_id]);	
	    	return true;
    	}
    	
    	return false;
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

        this.forgetChat(chat_id);
        
        if (LHCCallbacks.removeSynchroChat) {
        	LHCCallbacks.removeSynchroChat(chat_id);
        }

    };

    this.is_typing = false;
    this.typing_timeout = null;
   
    this.operatorTypingCallback = function(chat_id)
    {
    	var www_dir = this.wwwDir;
        var inst = this;
        
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
    };
    
    this.initTypingMonitoringAdmin = function(chat_id) {
    	var inst = this;
        jQuery('#CSChatMessage-'+chat_id).bind('keyup', function (evt){
        	inst.operatorTypingCallback(chat_id);
        });
    };

    this.remarksTimeout = null;
    
    this.saveRemarks = function(chat_id) {
    	clearTimeout(this.remarksTimeout);
    	
    	$('#remarks-status-'+chat_id).addClass('warning-color');
    	$('#main-user-info-remarks-'+chat_id+' .alert').remove();
    	var inst = this;
    	this.remarksTimeout = setTimeout(function(){
    		$.postJSON(inst.wwwDir + 'chat/saveremarks/' + chat_id,{'data':$('#ChatRemarks-'+chat_id).val()}, function(data){
				if(data.error == 'false') {
					$('#remarks-status-'+chat_id).removeClass('warning-color');
				} else {
					$('#main-user-info-remarks-'+chat_id).prepend(data.result);
				}
    		});
    	},500);    	
    };
    
    this.saveNotes = function(chat_id) {
    	clearTimeout(this.remarksTimeout);    	    	
    	$('#remarks-status-online-'+chat_id).addClass('warning-color');
    	var inst = this;
    	this.remarksTimeout = setTimeout(function(){
    		$.postJSON(inst.wwwDir + 'chat/saveonlinenotes/' + chat_id,{'data':$('#OnlineRemarks-'+chat_id).val()}, function(data){
    			$('#remarks-status-online-'+chat_id).removeClass('warning-color');
            });
    	},500);    	
    };
    
    this.surveyShowed = false;
    
    this.closeWindow  = function() {
    	if (this.survey !== null && this.surveyShowed == false) {
    		this.surveyShowed = true;
    		this.chatClosed();
    	} else {
	    	window.open('','_self','');
	    	window.close();
    	}
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

    this.sendemail = function(){    
    	$.postJSON(this.wwwDir + 'chat/sendchat/' + this.chat_id+'/'+this.hash,{csfr_token:confLH.csrf_token, email:$('input[name="UserEmail"]').val()}, function(data){
    		if (data.error == 'false') {
    			$('#myModal').modal('hide');   			
    		} else {
    			$('#user-action .alert-box').remove();
    			$('#user-action').prepend(data.result);    		
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
        
        try {
	        if (sessionStorage && sessionStorage.getItem('lhc_ttxt') && sessionStorage.getItem('lhc_ttxt') != '') {
	        	jQuery('#CSChatMessage').val(sessionStorage.getItem('lhc_ttxt'));       
	    	}
        } catch(e) {}
        
        jQuery('#CSChatMessage').bind('keyup', function (evt){
        	
        	 if (sessionStorage) {
        		 try {
        			 sessionStorage.setItem('lhc_ttxt',$(this).val());
        		 } catch(e) {}
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

    this.makeAbstractRequest = function(chat_id, inst) { 
    	$.get(inst.attr('href'), function(data) {
    		lhinst.syncadmininterfacestatic();	
    		
			if (LHCCallbacks.userRedirectedSurvey) {
	       		LHCCallbacks.userRedirectedSurvey(chat_id);
			};
			
    	});
    	return false;
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
    	if ($('#chat-main-column-'+chat_id+' .collapse-right').text() == 'chevron_right'){
	    	$('#chat-right-column-'+chat_id).hide();
	    	$('#chat-main-column-'+chat_id).removeClass('col-sm-7').addClass('col-sm-12');
	    	$('#chat-main-column-'+chat_id+' .collapse-right').text('chevron_left');
	    	try {
		    	if (localStorage) {
					localStorage.setItem('lhc_rch',1);				
				}
	    	} catch(e) {}
    	} else {
    		$('#chat-right-column-'+chat_id).show();
	    	$('#chat-main-column-'+chat_id).removeClass('col-sm-12').addClass('col-sm-7');
	    	$('#chat-main-column-'+chat_id+' .collapse-right').text('chevron_right');
	    	
	    	try {
		    	if (localStorage) {
					localStorage.removeItem('lhc_rch');				
				}
	    	} catch(e) {}
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
    	            		var messageBlock = $('#messagesBlock');
    	            		var scrollHeight = messageBlock.prop("scrollHeight");
    	            		var isAtTheBottom = Math.abs((scrollHeight - messageBlock.prop("scrollTop")) - messageBlock.prop("clientHeight"));
    	            		
    	            		messageBlock.find('.pending-storage').remove();
    	            		messageBlock.append(data.result);
    	            		
	  	                	if (isAtTheBottom < 20) {
	  	                		messageBlock.stop(true,false).animate({ scrollTop: scrollHeight }, 500);
	  	                	}  	                	  
    	            		
                			// If one the message owner is not current user play sound
                			if ( confLH.new_message_sound_user_enabled == 1 && data.uw == 'false') {
                			     inst.playNewMessageSound();
                			};
                			
                			if (inst.last_message_id > 0) {
                				if ($('#msg-'+inst.last_message_id).attr('data-op-id') != data.msop) {
                					$('#msg-'+inst.last_message_id).next().addClass('operator-changes');
                				}
                			}
                			
                			// Set last message ID                			
                			inst.last_message_id = data.message_id;

							if (data.uw == 'false' && inst.isWidgetMode && typeof(parent) !== 'undefined') {
								parent.postMessage('lhc_newopmsg', '*');
							}

                        } else {
    	                if ( data.status != 'true') $('#status-chat').html(data.status);
    	            }

    	            inst.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
    	            
    	            if (data.cs == 't') {
        				inst.chatsyncuserpending();
        			}
    	            
        			if ( data.ott != '' && data.ott != 'f') {
        				var instStatus = $('#id-operator-typing');
        				instStatus.text(data.ott);
        				instStatus.css('visibility','visible');
        				inst.operatorTyping = true;
        			} else if (data.ott == 'f') {
        				inst.operatorTyping = false;
        			    $('#id-operator-typing').css('visibility','hidden');
        			}
        			
        			// Execute pending operations
        			if (data.op != '') {
        				 inst.executeRemoteCommands(data.op);	   	    			 	    			
        			};	
        			
        			
	           } else {
	               $('#status-chat').html(data.status);
	               $('#ChatMessageContainer').remove();
	               $('#ChatSendButtonContainer').remove();
	               $('#id-operator-typing').css('visibility','hidden');
	               inst.operatorTyping = false;
	               
	               // Execute pending operations
	       		   if (typeof data.op !== 'undefined' && data.op != '') {
	       			   inst.executeRemoteCommands(data.op);	   	    			 	    			
	       		   };
	       		   	       		   
	               if (data.closed && data.closed == true) {

	                    ee.emitEvent('chatClosedSyncUser', [inst.chat_id]);
		   			 	if (inst.isWidgetMode && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {	
		   			 		 parent.postMessage('lhc_chat_closed' + (typeof data.closed_arg !== 'undefined' ? ':'+data.closed_arg : ''), '*');
		   				} else {
                            if (typeof data.closed_arg !== 'undefined'){
                                inst.parseCloseArgs(data.closed_arg.split(':'));
                            };
		   					inst.chatClosed();
		   				}
	               }
	           }
	        };
        } catch(err) {		     
        	inst.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
        };

        inst.syncroRequestSend = false;
    };

    this.parseCloseArgs = function (args) {
        var tt = args.length/2;
        for (i = 0; i < tt; i++) {
            var argument = args[i*2];
            var value = args[(i*2)+1];
            if (argument == 'survey_id') {
                this.survey = value;
            }
        }
    };

    this.chatClosed = function() {
    	if (this.survey !== null) {
    		var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
		    var operatorTyping = this.operatorTyping == true ? '/(ot)/t' : '';
		    var themeWindow = this.theme !== null ? '/(theme)/'+this.theme : '';
		    var modeEmbed = this.isEmbedMode == true ? '/(modeembed)/embed' : '';
		    var fillType = this.isWidgetMode == true ? 'fillwidget' : 'fill';
		    var explicitClose =  this.explicitClose == true ? '/(eclose)/t' : '';
		    document.location = this.wwwDir + 'survey/'+fillType+'/(survey)/' + this.survey + '/(chatid)/' +this.chat_id + '/(hash)/'+ this.hash + modeWindow + operatorTyping + themeWindow + modeEmbed + explicitClose;
		    return true;
    	}
    	
    	return false;
    };
    
    this.executeRemoteCommands = function(operations)
    {
    	 var inst = this;
    	 $.each(operations,function(i,item) {	   	    				 	   	    				 		
			 	 if (item.indexOf('lhinst.') != -1) { // Internal operation
			 		eval(item);	
			 	 } else if (inst.isWidgetMode) {
			 		 parent.postMessage(item, '*');
				 } else if (window.opener) {
					 window.opener.postMessage(item, '*');	  
				 };
		 });
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
		    var themeWindow = this.theme !== null ? '/(theme)/'+this.theme : '';
		    var modeEmbed = this.isEmbedMode == true ? '/(modeembed)/embed' : '';
		    
		    $.getJSON(this.wwwDir + this.syncuser + this.chat_id + '/'+ this.last_message_id + '/' + this.hash + modeWindow + operatorTyping + themeWindow + modeEmbed ,{ }, function(data){
		    			    	
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
	    	$('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
	       $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
	    };

	    if (hidetab == true) {

	    	var location = this.smartTabFocus(tabs, chat_id);

	    	setTimeout(function() {
	    		window.location.hash =  location;
	    	},500);

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
	
	this.smartTabFocus = function(tabs, chat_id) {			
		var index = tabs.find('> ul > #chat-tab-li-'+chat_id).index();
    	tabs.find('> ul > #chat-tab-li-'+chat_id).remove();
    	tabs.find('#chat-id-'+chat_id).remove();	    	
    	var linkTab = tabs.find('> ul > li:eq('+ (index - 1)+')');
    	  
    	if (linkTab.attr('id') !== undefined){
    		var link = linkTab.find('> a');
    	} else {
    		linkTabRight = tabs.find('> ul > li:eq('+ (index)+')');
    		if (linkTabRight.size() > 0) {
    			var link = linkTabRight.find('> a');
    		} else {
    			var link = linkTab.find('> a');
    		}
    	}
    	
    	if (!tabs.find('> ul > li.active').length) {
    		link.tab('show');
    		
    		if (link.attr('id') !== undefined) {
        		var new_chat_id = link.attr('href').replace('#chat-id-','');
        		this.removeBackgroundChat(new_chat_id);
        		this.hideNotification(new_chat_id);
        		ee.emitEvent('chatTabFocused', [new_chat_id]);
        	}
    	}
    	
    	return link.attr('href').replace('#','#/');
	};
	
	this.startChatCloseTabNewWindow = function(chat_id, tabs, name)
	{
		window.open(this.wwwDir + 'chat/single/'+chat_id,'chatwindow-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650");

    	this.smartTabFocus(tabs, chat_id);
    	
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
	    	$('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
	       $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
	    }
	   
	    this.removeSynchroChat(chat_id);
	    
	    if (hidetab == true) {

	    	var location = this.smartTabFocus(tabs, chat_id);
	    	
	    	setTimeout(function() {
	    		window.location.hash = location;
	    	},500);
	    	
	        if (this.closeWindowOnChatCloseDelete == true)
	        {
	            window.close();
	        };
	    };
	    
	   
	    this.syncadmininterfacestatic();
	};

	this.removeActiveDialogTag = function(tabs) {

		/* @todo add removement of current active tab */
		
        if (this.closeWindowOnChatCloseDelete == true)
        {
            window.close();
        };
	};

	this.deleteChat = function(chat_id, tabs, hidetab)
	{
        if (confirm(confLH.transLation.delete_confirm)) {
			if ($('#CSChatMessage-'+chat_id).length != 0){
				$('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
			   $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
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

				var location = this.smartTabFocus(tabs, chat_id);

				setTimeout(function() {
					window.location.hash = location;
				},500);

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
        }
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

        ee.emitEvent('chatStartOpenWindow', [chat_id]);	
	};

    this.startChatNewWindowArchive = function(archive_id, chat_id,name)
    {
        window.open(this.wwwDir + 'chatarchive/viewarchivedchat/' + archive_id + '/' + chat_id + '/(mode)/popup','chatwindow-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650").focus();
        ee.emitEvent('chatStartOpenWindowArchive', [archive_id, chat_id]);
    };

	this.startCoBrowse = function(chat_id)
	{
		window.open(this.wwwDir + 'cobrowse/browse/'+chat_id,'chatwindow-cobrowse-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650").focus();
		return false;
	};
	
	this.speechToText = function(chat_id)
	{
		if (this.speechHandler == false)
		{
			this.speechHandler = new LHCSpeechToText();
		}
		
		this.speechHandler.listen({'chat_id':chat_id});
		
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

	this.startChatNewWindowTransferByTransfer = function(chat_id, nt)
	{
		var inst = this;
		$.ajax({
	        type: "GET",
	        url: this.wwwDir + this.accepttransfer + chat_id+'/(mode)/chat',
	        cache: false,
	        dataType: 'json'
	    }).done(function(data){	  
	    		    	
	    	if ($('#tabs').size() > 0) {
    			window.focus();
    			inst.startChat(data.chat_id, $('#tabs'), nt);
    		} else {
    			inst.startChatNewWindow(data.chat_id,'');
    		}

	    	if (LHCCallbacks.operatorAcceptedTransfer) {
	       		LHCCallbacks.operatorAcceptedTransfer(data.chat_id);
	    	};
	    });
			   
	    this.syncadmininterfacestatic();
        return false;
	};

	this.blockUser = function(chat_id,msg) {
		if (typeof msg === 'undefined' || confirm(msg)) {
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
	};
	
	this.sendLinkToMail = function( embed_code,file_id) {
		var val = window.parent.$('#MailMessage').val();		
		window.parent.$('#MailMessage').val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('success');	
	};
	
	this.sendLinkToEditor = function(chat_id, embed_code,file_id) {
		var val = window.parent.$('#CSChatMessage-'+chat_id).val();		
		window.parent.$('#CSChatMessage-'+chat_id).val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('success');	
	};

	this.hideTransferModal = function(chat_id)
	{
		var inst = this;

        setTimeout(function(){
            $('#myModal').modal('hide');
            if ($('#tabs').size() > 0) {
                inst.removeDialogTab(chat_id,$('#tabs'),true)
            }
        },1000);
	};

	this.transferChat = function(chat_id)
	{
        var inst = this;

		var user_id = $('[name=TransferTo'+chat_id+']:checked').val();

		$.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id ,{'type':'user'}, function(data){
			if (data.error == 'false') {
				$('#transfer-block-'+data.chat_id).html(data.result);
                inst.hideTransferModal(chat_id);
			};
		});
	};
		
	this.chooseSurvey = function(chat_id)
	{
		var survey_id = $('[name=SurveyItem'+chat_id+']:checked').val();
		
		$.postJSON(this.wwwDir + "survey/choosesurvey/" + chat_id + '/' + survey_id, function(data){
			if (data.error == 'false') {
				$('#survey-block-'+data.chat_id).html(data.result);
			};
		});
	};
		
	this.redirectContact = function(chat_id,message){		
		if (typeof message === 'undefined' || confirm(message)){	
			$.postJSON(this.wwwDir + 'chat/redirectcontact/' + chat_id, function(data){				
				lhinst.syncadmininterfacestatic();				
				if (LHCCallbacks.userRedirectedContact) {
		       		LHCCallbacks.userRedirectedContact(chat_id);
				};
			});
		}		
	};
	
	this.redirectToURL = function(chat_id,trans) {
		var url = prompt(trans, "");
		if (url != null) {
			lhinst.addRemoteCommand(chat_id,'lhc_chat_redirect:'+url.replace(new RegExp(':','g'),'__SPLIT__'));	
		}
	};
	
	this.redirectToURLOnline = function(online_user_id,trans) {
		var url = prompt(trans, "");
		if (url != null) {
			lhinst.addRemoteOnlineCommand(online_user_id,'lhc_chat_redirect:'+url.replace(new RegExp(':','g'),'__SPLIT__'));	
			lhinst.addExecutionCommand(online_user_id,'lhc_cobrowse_multi_command__lhc_chat_redirect:'+url.replace(new RegExp(':','g'),'__SPLIT__'));		
		}
	};
	
	this.transferChatDep = function(chat_id)
	{
		var inst = this;
	    var user_id = $('[name=DepartamentID'+chat_id+']:checked').val();
	    $.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id ,{'type':'dep'}, function(data){
	        if (data.error == 'false') {
	        	$('#transfer-block-'+data.chat_id).html(data.result);
                inst.hideTransferModal(chat_id);
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
	    if (!!window.postMessage && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {
	    	parent.postMessage("lhc_chat_closed_explicit", '*');
	    } else {
	    	if (this.chatClosed() == false) {
				window.close();
			}
	    }
	};
	
	this.continueChatFromSurvey = function(survey_id)
	{
		if (this.isWidgetMode && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {	
			$.postJSON(this.wwwDir + "survey/backtochat/" + this.chat_id + '/' + this.hash + '/' + survey_id , function(data){				
				 parent.postMessage('lhc_continue_chat', '*');
		    });
		} else {
			this.chatClosed();
		}
		
		return false;
	}
	
	this.explicitClose = false;
	
	this.explicitChatCloseByUser = function()
	{
		this.explicitClose = true;
		
		if (this.isWidgetMode && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {		   			 
	 		 parent.postMessage('lhc_chat_closed_explicit', '*');
		} else {			
			if (this.chatClosed() == false) {
				window.close();
			}
		}
		return false;
	};
	
	this.restoreWidget = function(hash){
		 if (!!window.postMessage && window.opener) { 	    	          	    
 	    	window.opener.postMessage("lhc_ch:hash:"+hash, '*');
 	    	window.opener.postMessage("lhc_ch:hash_resume:"+hash, '*');            	    	
 	    	window.opener.postMessage("lhc_open_restore", '*'); 
 	    	window.close();
 	    };
	};
	
	this.userclosedchatandbrowser = function()
	{
		if (LHCCallbacks.userleftchatNotification) {
	   		LHCCallbacks.userleftchatNotification(this.chat_id);
	    };
    
		$.get(this.wwwDir + this.userclosechaturl + this.chat_id + '/' + this.hash + '/(eclose)/t',function(data){
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
		    				msg	: $('#id_CannedMessage-'+chat_id).find(':selected').attr('data-msg')
		    		};
		    		$('#CSChatMessage-'+chat_id).val('');
		    		$.postJSON(www_dir + inst.addmsgurl + chat_id, pdata , function(data){
		    			if (LHCCallbacks.addmsgadmin) {
		            		LHCCallbacks.addmsgadmin(chat_id);
		            	};		            	
		            	ee.emitEvent('chatAddMsgAdmin', [chat_id]);		            	
		    			lhinst.syncadmincall();
		    			return true;
		    		});
	        	},delayMiliseconds);
	        } else {
	        	var pdata = {
	    				msg	: $('#id_CannedMessage-'+chat_id).find(':selected').attr('data-msg')
	    		};
	    		$('#CSChatMessage-'+chat_id).val('');
	    		$.postJSON(this.wwwDir + this.addmsgurl + chat_id, pdata , function(data){
	    			if (LHCCallbacks.addmsgadmin) {
	            		LHCCallbacks.addmsgadmin(chat_id);	            		
	            	};
	            	ee.emitEvent('chatAddMsgAdmin', [chat_id]);	
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
					$('.up-vote-action').removeClass('up-voted');
					$('.down-vote-action').removeClass('down-voted');
				} else if (data.status == 1) {
					$('.up-vote-action').addClass('up-voted');
					$('.down-vote-action').removeClass('down-voted');
				} else if (data.status == 2) {
					$('.up-vote-action').removeClass('up-voted');
					$('.down-vote-action').addClass('down-voted');
				}
	        }
    	});
	};

	this.theme = null;
	
	this.chatsyncuserpending = function ()
	{	
		var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';	
		var themeWindow = this.theme !== null ? '/(theme)/'+this.theme : '';	
		
		var inst = this;
	    $.getJSON(this.wwwDir + this.checkchatstatus + this.chat_id + '/' + this.hash + modeWindow + themeWindow,{}, function(data){
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
	            	
	            	 if (data.closed && data.closed == true) {
                        ee.emitEvent('chatClosedCheckStatus', [inst.chat_id]);
		   			 	if (inst.isWidgetMode && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {		   			 
		   			 		 parent.postMessage('lhc_chat_closed', '*');
		   				} else {		   				
		   					inst.chatClosed();
		   				}
	            	 }
	            }
	        }
    	}).fail(function(){
    		setTimeout(chatsyncuserpending,confLH.chat_message_sinterval);
    	});
	};
	
	this.setTheme = function(theme_id) {
		this.theme = theme_id;
	};
	
	this.survey = null;
	
	this.setSurvey = function(survey_id) {
		this.survey = survey_id;
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
    	    this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_message.wav';
    	    this.audio.load();
	    };

	    if(!$("textarea[name=ChatMessage]").is(":focus")) {
	    	this.startBlinking();
    	};
	};

	this.playInvitationSound = function() {
		if (Modernizr.audio) {
    	    this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/invitation.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/invitation.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/invitation.wav';
    	    this.audio.load();
	    }
	};

	this.playPreloadSound = function() {
		if (Modernizr.audio) { 		
			this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/silence.ogg' :
				Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/silence.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/silence.wav';		
            this.audio.load();
	    }
	};

	this.hidenicknamesstatus = null;

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

	        	                	  var messageBlock = $('#messagesBlock-'+item.chat_id);   
	        	                	  var scrollHeight = messageBlock.prop("scrollHeight");
	        	                	  var isAtTheBottom = Math.abs((scrollHeight - messageBlock.prop("scrollTop")) - messageBlock.prop("clientHeight"));

	        	                	  messageBlock.find('.pending-storage').remove();
	        	                	  messageBlock.append(item.content);	

	        	                	  if (isAtTheBottom < 20) {
	        	                		  messageBlock.stop(true,false).animate({ scrollTop: scrollHeight }, 500);
	        	                	  }

	        		                  lhinst.updateChatLastMessageID(item.chat_id,item.message_id);
	
	        		                  var mainElement = $('#chat-tab-link-'+item.chat_id);
	
	        		                  if (!mainElement.parent().hasClass('active')) {
	        		                	  if (mainElement.find('span.msg-nm').length > 0) {
	        		                		  var totalMsg = (parseInt(mainElement.find('span.msg-nm').attr('rel')) + item.mn);
	        		                		  mainElement.find('span.msg-nm').html(' (' + totalMsg + ')' ).attr('rel',totalMsg);
	        		                	  } else {
	        		                		  mainElement.append('<span rel="'+item.mn+'" class="msg-nm"> ('+item.mn+')</span>');
	        		                		  mainElement.addClass('has-pm');	        		                		
	        		                	  }
	        		                  }
	        		                  
	        		                  if ( confLH.new_message_browser_notification == 1 && data.uw == 'false') {	        		                	 
	        		                	  lhinst.showNewMessageNotification(item.chat_id,item.msg,item.nck);
	  	                			  }; 
	  	                			  
	  	                			  if (item.msfrom > 0) {	  	                				
	  	                				if ($('#msg-'+item.msfrom).attr('data-op-id') != item.msop) {
	  	                					$('#msg-'+item.msfrom).next().addClass('operator-changes');
	  	                				}
	  	                			  }
	  	                			  
	  	                			  ee.emitEvent('eventSyncAdmin', [item,i]);	  	                			  
	                            });

	                            if ( confLH.new_message_sound_admin_enabled == 1  && data.uw == 'false') {
	                            	lhinst.playNewMessageSound();
	                            };


	        	            };
	
	        	            if (data.result_status != 'false')
	        	            {
	        	            	var groupTabs = $('#group-chats-status').hasClass('chat-active');

	        	                $.each(data.result_status,function(i,item) {
	        	                      if (item.tp == 'true') {
	                                      $('#user-is-typing-'+item.chat_id).html(item.tx).css('visibility','visible');
	        	                      } else {
	        	                          $('#user-is-typing-'+item.chat_id).css('visibility','hidden');
	        	                      };

	        	                      var userChatStatus = $('#user-chat-status-'+item.chat_id);

	        	                      var wasOnline = userChatStatus.hasClass('icon-user-online');

									  userChatStatus.removeClass('icon-user-online icon-user-away icon-user-pageview');
	        	                      $('#msg-send-status-'+item.chat_id).removeClass('icon-user-online icon-user-offline');
	        	                      
	        	                      if (item.us == 0) {
                                          userChatStatus.addClass('icon-user-online');
	        	                      } else if (item.us == 2) {
                                          userChatStatus.addClass('icon-user-away');
	        	                      } else if (item.us == 3) {
                                          userChatStatus.addClass('icon-user-pageview');
	        	                      }

                                    if (groupTabs == true) {
                                        if (wasOnline == true && item.us != 0 || (lhinst.hidenicknamesstatus != groupTabs && item.us != 0)) {
                                            $('#ntab-chat-' + item.chat_id).hide();
                                        } else if (wasOnline == false && item.us == 0 || (lhinst.hidenicknamesstatus != groupTabs && item.us == 0)) {
                                            $('#ntab-chat-' + item.chat_id).show();
                                        }
                                    } else if (lhinst.hidenicknamesstatus != groupTabs) {
                                        $('#ntab-chat-' + item.chat_id).show();
									}

	        	                      var statusel = $('#chat-id-'+item.chat_id +'-mds');
	        	                      
	        	                      if (item.um == 1) {
	        	                    	  statusel.removeClass('chat-active');
	        	                    	  statusel.addClass('chat-unread');	        	                    	  
	        	                    	  $('#msg-send-status-'+item.chat_id).addClass('icon-user-offline');
	  	                			  } else {
	  	                				  $('#msg-send-status-'+item.chat_id).addClass('icon-user-online');
	  	                				  statusel.removeClass('chat-unread');
	  	                				  statusel.addClass('chat-active');
	  	                			  }
	        	                      	        	                    		  
	        	                      if (item.lp !== false) {	        	                    	 
	        	                    	  statusel.attr('title',item.lp+' s.');
	        	                      } else {
	        	                    	  statusel.attr('title','');
	        	                      }
	        	                    		  
	        	                      if (typeof item.oad != 'undefined') {	        	                    
	        	                    	  eval(item.oad);
	        	                      };
	
	                            });
	        	            };

                            lhinst.hidenicknamesstatus = groupTabs;

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
            ee.emitEvent('chatTabInfoReload', [chat_id]);
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
					
			this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_chat.wav';
			this.audio.load();
			
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
						
						delete inst.notificationsArrayMessages[chat_id];
					}
				});
			}
		}
	};
	
	this.notificationsArrayMessages = [];
	
	this.showNewMessageNotification = function(chat_id,message,nick) {		
		try {
			
		if (window.Notification && focused == false && window.Notification.permission == 'granted') {			
				if (typeof this.notificationsArrayMessages[chat_id] !== 'undefined') {					
					this.notificationsArrayMessages[chat_id].close();
					delete this.notificationsArrayMessages[chat_id];
				};	
				
  				var notification = new Notification(nick, { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: message });
  				var _that = this;
  				
  				notification.onclick = function () {
  					window.focus();
	    	        notification.close();
	    	        delete _that.notificationsArrayMessages[chat_id];
	    	    };
	    	    
	    	    notification.onclose = function() {
	    	    	if (typeof _that.notificationsArrayMessages[chat_id] !== 'undefined') {
	    				delete _that.notificationsArrayMessages[chat_id];
	    			};
	    	    };
	    	    
	    	    this.notificationsArrayMessages[chat_id] = notification;
	    	    this.scheduleNewMessageClose(notification,chat_id);					
		  }
		} catch(err) {		     
        	console.log(err);
        };		
	};	
	
	this.scheduleNewMessageClose = function(notification, chat_id) {		
		var _that = this;		
		setTimeout(function() {				
			if (window.webkitNotifications) {
				notification.cancel();
			} else {
				notification.close();
			};
			
			if (typeof _that.notificationsArrayMessages[chat_id] !== 'undefined') {
				delete _that.notificationsArrayMessages[chat_id];
			};
			
		},10*1000);		
	};
	
	this.playSoundNewAction = function(identifier,chat_id,nick,message,nt) {
		
		if (this.backgroundChats.indexOf(parseInt(chat_id)) != -1) {
			return ;
		}
		
		if (confLH.new_chat_sound_enabled == 1 && (confLH.sn_off == 1 || $('#online-offline-user').text() == 'flash_on') && (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered')) {
	    	this.soundPlayedTimes = 0;
	        this.playNewChatAudio();
	    };
    
	    if(!$("textarea[name=ChatMessage]").is(":focus") && (confLH.sn_off == 1 || $('#online-offline-user').text() == 'flash_on') && (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered')) {
	    	this.startBlinking();
    	};

	    var inst = this;
	    
	    if ( (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered') && (confLH.sn_off == 1 || $('#online-offline-user').text() == 'flash_on') && window.Notification && window.Notification.permission == 'granted') {
    		
			var notification = new Notification(nick, { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: message, requireInteraction : true });
				  				
			notification.onclick = function () {
    	    	if (identifier == 'pending_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered') {
    	    		if ($('#tabs').size() > 0) {
    	    			window.focus();
    	    			inst.startChat(chat_id, $('#tabs'), nt);
    	    		} else {
    	    			inst.startChatNewWindow(chat_id,'ChatRequest');
    	    		}
    	    	} else {    	    		
    	    		inst.startChatNewWindowTransferByTransfer(chat_id, nt);
    	    	};
    	        notification.close();
    	    };
    	   	    	        	    
    	    if (identifier != 'pending_transfered') {
    	    	if (this.notificationsArray[chat_id] !== 'undefined') {
    	    		 notification.close();
    	    	}
    	    	
    	    	this.notificationsArray[chat_id] = notification;	
			};
	    };

        if (identifier == 'transfer_chat' && confLH.show_alert_transfer == 1) {
            if (confirm(confLH.transLation.transfered + "\n\n" + message)) {
                inst.startChatNewWindowTransferByTransfer(chat_id, nt);
			}
        }


	    if (confLH.show_alert == 1) {	    	
    		if (confirm(confLH.transLation.new_chat+"\n\n"+message)) {
    			if (identifier == 'pending_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered') {
    	    		if ($('#tabs').size() > 0) {
    	    			window.focus();
    	    			inst.startChat(chat_id, $('#tabs'), nt);
    	    		} else {
    	    			inst.startChatNewWindow(chat_id,'ChatRequest');
    	    		}
    	    	} else {
    	    		inst.startChatNewWindowTransferByTransfer(chat_id, nt);
    	    	};
    		};
	    };
	};
		
	this.syncadmininterfacestatic = function()
	{
		try {
			var lhcController = angular.element('body').scope(); 
			lhcController.loadChatList();
		} catch(err) {		     
        	//
        };
	};
	
	this.addingUserMessage = false;
	this.addUserMessageQueue = [];
	this.addDelayedTimeout = null;
	    
	this.addmsgadmin = function (chat_id)
	{
		var textArea = $("#CSChatMessage-"+chat_id);

		if (textArea.is("[readonly]")) {
			return;
		}

		var pdata = {
				msg	: textArea.val()
		};
		
		if (this.speechHandler !== false) {
			this.speechHandler.messageSend();
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
		        	
		        	ee.emitEvent('chatAddMsgAdmin', [chat_id]);	
		        	
					return true;
				}
			});
			
		} else {
			
			var inst = this;
						
			var messagesBlock = $('#messagesBlock-'+chat_id);
			jQuery('<div/>', {
			    'class': 'message-row pending-storage',					   
			    text: pdata.msg
			}).appendTo(messagesBlock);
			
			messagesBlock.stop(true,false).animate({ scrollTop: messagesBlock.prop('scrollHeight') }, 500);
			
			if (this.addingUserMessage == false && this.addUserMessageQueue.length == 0)
			{
				this.addingUserMessage = true;
				
				$.postJSON(this.wwwDir + this.addmsgurl + chat_id, pdata , function(data){
					
					if (LHCCallbacks.addmsgadmin) {
		        		LHCCallbacks.addmsgadmin(chat_id);
		        	};
		        	
		        	ee.emitEvent('chatAddMsgAdmin', [chat_id]);	
		        	
		        	if (data.r != '') {
	            		$('#messagesBlock-'+chat_id).append(data.r);
		                $('#messagesBlock-'+chat_id).stop(true,false).animate({ scrollTop: $("#messagesBlock-"+chat_id).prop("scrollHeight") }, 500);
	            	};

		        	if (data.hold_removed === true) {
                        $('#hold-action-'+chat_id).removeClass('btn-info');
					} else if (data.hold_added === true) {
                        $('#hold-action-'+chat_id).addClass('btn-info');
					}

					lhinst.syncadmincall();		
					
					inst.addingUserMessage = false;
					
					return true;
				}).fail(function() {	
					inst.addUserMessageQueue.push({'pdata':pdata,'url':inst.wwwDir + inst.addmsgurl + chat_id,'chat_id':chat_id});
		        	clearTimeout(inst.addDelayedTimeout);
		        	inst.addDelayedTimeout = setTimeout(function(){		        		
		        		inst.addDelayedMessageAdmin();
		        	},50);
		        	inst.addingUserMessage = false;
		    	});
				
			} else {
				this.addUserMessageQueue.push({'pdata':pdata,'url':this.wwwDir + this.addmsgurl + chat_id,'chat_id':chat_id});
	        	clearTimeout(this.addDelayedTimeout);
	        	this.addDelayedTimeout = setTimeout(function(){
	        		inst.addDelayedMessageAdmin();
	        	},50);
			}
		}
	};
	
	this.addDelayedMessageAdmin = function()
    {
    	var inst = this;
    	
    	if (this.addingUserMessage == false) {
    		
    		if (this.addUserMessageQueue.length > 0)
    		{
	    		var elementAdd = this.addUserMessageQueue.shift();
	    		this.addingUserMessage = true;
	    		
		        $.postJSON(elementAdd.url, elementAdd.pdata , function(data) {
		        		        	
		        	if (LHCCallbacks.addmsgadmin) {
		        		LHCCallbacks.addmsgadmin(elementAdd.chat_id);
		        	};
		        	
		        	ee.emitEvent('chatAddMsgAdmin', [elementAdd.chat_id]);	
		        	
		        	if (data.r != '') {
	            		$('#messagesBlock-'+elementAdd.chat_id).append(data.r);
		                $('#messagesBlock-'+elementAdd.chat_id).animate({ scrollTop: $("#messagesBlock-"+elementAdd.chat_id).prop("scrollHeight") }, 500);
	            	};
	            	
	            	lhinst.syncadmincall();	
	            	
		        	inst.addingUserMessage = false;
		        	
		        	// There is still pending messages, add them
		        	if (inst.addUserMessageQueue.length > 0) {
		        		clearTimeout(inst.addDelayedTimeout);	        		
		            	inst.addDelayedMessageAdmin();	            	
		        	}
		        	
				}).fail(function() {					
					inst.addUserMessageQueue.unshift(elementAdd);
					inst.addingUserMessage = false;
					clearTimeout(inst.addDelayedTimeout);
					inst.addDelayedTimeout = setTimeout(function(){
		        		inst.addDelayedMessageAdmin();
					}, 500);
				});
    		}
    		
    	} else {
    		clearTimeout(this.addDelayedTimeout);
        	this.addDelayedTimeout = setTimeout(function(){
        		inst.addDelayedMessageAdmin();
        	},50);	        		        	
    	}
    }
	
	this.editPrevious = function(chat_id) {	
		var textArea = $('#CSChatMessage-'+chat_id);
		if (textArea.val() == '') {				
			$.getJSON(this.wwwDir + 'chat/editprevious/'+chat_id, function(data){
				if (data.error == 'f') {
					textArea.val(data.msg);
					textArea.attr('data-msgid',data.id);
					textArea.addClass('edit-mode');
					$('#msg-'+data.id).addClass('edit-mode');
					if (LHCCallbacks.editPrevious) {
						LHCCallbacks.editPrevious(chat_id, data);
					}
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
					if (LHCCallbacks.editPreviousUser) {
						LHCCallbacks.editPreviousUser(data);
					}
				}
			});			
		}
	};

	this.afterAdminChatInit = function (chat_id) {
		if (LHCCallbacks.afterAdminChatInit) {
			LHCCallbacks.afterAdminChatInit(chat_id);
		}
	};

	this.afterUserChatInit = function () {
		if (LHCCallbacks.afterUserChatInit) {
			LHCCallbacks.afterUserChatInit();
		}
	};

	this.afterChatWidgetInit = function () {
		if (LHCCallbacks.afterChatWidgetInit) {
			LHCCallbacks.afterChatWidgetInit();
		};
	};
		
	this.addAdminChatFinished = function(chat_id, last_message_id, arg) {
		
		var _that = this;
		
		var $textarea = jQuery('#CSChatMessage-'+chat_id);
		
		var cannedMessageSuggest = new LHCCannedMessageAutoSuggest({'chat_id':chat_id});
		
		$textarea.bind('keydown', 'return', function (evt){			
				_that.addmsgadmin(chat_id);
				ee.emitEvent('afterAdminMessageSent',[chat_id]);
				$textarea[0].rows = 2;
				return false;				
		});
		
		$textarea.bind('keyup', 'up', function (evt){
			_that.editPrevious(chat_id);	
		});
		
		$textarea.bind('keyup', function (evt){
			var ta = $textarea[0];
			var maxrows = 30;
			var lh = ta.clientHeight / ta.rows;
			while (ta.scrollHeight > ta.clientHeight && !window.opera && ta.rows < maxrows) {
				ta.style.overflow = 'hidden';
				ta.rows += 1;
			}
			if (ta.scrollHeight > ta.clientHeight) ta.style.overflow = 'auto';							
		});
		
		// Resize by user 
		$messageBlock = $('#messagesBlock-'+chat_id);
		
		$messageBlock.css('height',this.getLocalValue('lhc_mheight','200'));
		
		$messageBlock.data('resized',false);
		$messageBlock.data('y', $messageBlock.outerHeight()); 
	
		$messageBlock.bind('mouseup mousemove',function(event) {
			  var $this = jQuery(this);
 			  
		      if ($this.outerHeight() != $this.data('y')) {
		    	   if ($this.data('resized') == false) {
		    		   $this.css('height','1px');
		    		   $this.data('resized',true)
		    	   }
		    	   
		    	   if (this.resize_timeout) {
		    		   clearTimeout(this.resize_timeout);
		    	   }
		    	   
		    	   this.resize_timeout = setTimeout(function(){
		    		   _that.setLocalValue('lhc_mheight', $this.outerHeight());
		    		   $this.data('y', $this.outerHeight());
		    	   },100);
		      }
		});
		
		this.initTypingMonitoringAdmin(chat_id);
		
		this.afterAdminChatInit(chat_id);
		
		this.addSynchroChat(chat_id,last_message_id);

		$('#messagesBlock-'+chat_id).animate({ scrollTop: $('#messagesBlock-'+chat_id).prop('scrollHeight') }, 1000);

		// Start synchronisation
		this.startSyncAdmin();	
		
		jQuery('#id_CannedMessageSearch-'+chat_id).keyup(function(evt) {
			
			if ($(this).val() != '') {
				jQuery('#id_CannedMessage-'+chat_id).attr('size',10);	
			} else {
				jQuery('#id_CannedMessage-'+chat_id).removeAttr('size');
			}
			
			var q = $(this).val();
			$.getJSON(_that.wwwDir + 'chat/getcannedfiltered/' + chat_id, {q: q}, function(data) {
                 if (data.error == false) {
                	 $('#id_CannedMessage-'+chat_id).html(data.result);
                	 
                	 if (q != '') {
                		 var options = $('#id_CannedMessage-'+chat_id).find('option');
                		 if (options.size() > 1) {
                			 $(options[1]).attr('selected','selected');
                		 }
                	 }
                 }                 
            }); 
		});

		// Hide notification only if chat was not started in background
		if (arg === null || typeof arg !== 'object' || arg.indexOf('background') === -1) {
			this.hideNotification(chat_id);
		} else {
			$('#chat-tab-link-'+chat_id).click(function() {
				_that.removeBackgroundChat(parseInt(chat_id));
				_that.hideNotification(parseInt(chat_id));
			});
		}
	
		try {
			if (localStorage) {			
				if (localStorage.getItem('lhc_rch') == 1) {
					this.processCollapse(chat_id);
				}
			}
		} catch(e) {};

        $('#chat-tab-items-' + chat_id+' > li > a').click(function(){
            ee.emitEvent('adminChatTabSubtabClicked', [chat_id,$(this)]);
        });

		ee.emitEvent('adminChatLoaded', [chat_id,last_message_id,arg]);
	};
	
	this.removeBackgroundChat = function(chat_id) {
		var index = this.backgroundChats.indexOf(parseInt(chat_id));
		if (index !== -1) {
			delete this.backgroundChats[index];
		};
	};
	
	this.getLocalValue = function(variable,defaultValue) {
		try {
			if (localStorage) {
				var value = localStorage.getItem(variable);
				if (value !== null) {					
						return value;					
				} else {
					return defaultValue;
				}
			}
		} catch(e) {}
		return defaultValue;
	};
	
	this.setLocalValue = function(key,val){
		try {
	    	if (localStorage) {
				localStorage.setItem(key,val);				
			}
    	} catch(e) {}	
	};
	
	this.hideNotification = function(chat_id)
	{
		chat_id = parseInt(chat_id);
		if (typeof this.notificationsArray[chat_id] !== 'undefined' && this.backgroundChats.indexOf(chat_id) == -1) {
			this.notificationsArray[chat_id].close();
			delete this.notificationsArray[chat_id];
		};
		
		clearTimeout(this.soundIsPlaying);
	}
	
	this.showMyPermissions = function(user_id) {
		$.get(this.wwwDir + 'permission/getpermissionsummary/'+user_id, function(data){
			$('#permissions-summary').html(data);
		});
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
			try {
				sessionStorage.setItem('lhc_ttxt','');
			} catch(e) {}
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
					
			var messagesBlock = $('#messagesBlock');
			
						
			jQuery('<div/>', {
			    'class': 'message-row pending-storage',					   
			    text: pdata.msg
			}).appendTo(messagesBlock);
			
			messagesBlock.stop(true,false).animate({ scrollTop: messagesBlock.prop('scrollHeight') }, 500);	
						
			if (this.addingUserMessage == false && this.addUserMessageQueue.length == 0)
			{
				this.addingUserMessage = true;
		        $.postJSON(this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow, pdata , function(data) {
		        	
		        	if (data.error == 'f') {
			        	if (LHCCallbacks.addmsguser) {
			        		LHCCallbacks.addmsguser(inst,data);
			        	};
			        	
			        	inst.syncusercall();
		        	} else {
		        		$('#CSChatMessage').val(pdata.msg);
		        		var instStatus = $('#id-operator-typing');
						instStatus.html(data.r);
						instStatus.css('visibility','visible');	
						setTimeout(function(){
							if (inst.operatorTyping == false){
								$('#id-operator-typing').css('visibility','hidden');
	        			    }
						},3000);
		        	}
		        	
		        	inst.addingUserMessage = false;
				});
	        } else {
	        	this.addUserMessageQueue.push({'pdata':pdata,'url':this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow});
	        	clearTimeout(this.addDelayedTimeout);
	        	this.addDelayedTimeout = setTimeout(function(){
	        		inst.addDelayedMessage();
	        	},50);	        		        	
	        }
        }
    };

    this.addMessagesToStore = function(messages) 
    {
    	var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
    	
    	var arrayLength = messages.length;
    	for (var i = 0; i < arrayLength; i++) {    	    
    	    this.addUserMessageQueue.push({'pdata':{msg : messages[i]},'url':this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow});
    	}
    	
    	this.addDelayedMessage();
    };
    
    this.addDelayedMessage = function()
    {
    	var inst = this;
    	
    	if (this.addingUserMessage == false) {
    		
    		if (this.addUserMessageQueue.length > 0)
    		{
	    		var elementAdd = this.addUserMessageQueue.shift();
	    		
	    		this.addingUserMessage = true;
	    		
	    		// Format message body from pending messages
	    		var messagesData = [];
	    		messagesData.push(elementAdd.pdata.msg);
	    		
	    		var arrayLength = this.addUserMessageQueue.length;
	        	for (var i = 0; i < arrayLength; i++) { 
	        		messagesData.push(this.addUserMessageQueue[i].pdata.msg);
	        	};
	        	
	        	this.addUserMessageQueue = [];
	        	
		        $.postJSON(elementAdd.url, {msg:messagesData.join("[[msgitm]]")} , function(data) {
		        		        	
		        	if (data.error == 'f') {
			        	if (LHCCallbacks.addmsguser) {
			        		LHCCallbacks.addmsguser(inst,data);
			        	};
			        	
			        	inst.syncusercall();
		        	}
		        	
		        	inst.addingUserMessage = false;
		        	
		        	// There is still pending messages, add them
		        	if (inst.addUserMessageQueue.length > 0) {
		        		clearTimeout(inst.addDelayedTimeout);
		        		inst.addDelayedMessage();	            	
		        	}
		        	
				});
    		}
    		
    	} else {
    		clearTimeout(this.addDelayedTimeout);
        	this.addDelayedTimeout = setTimeout(function(){
        		inst.addDelayedMessage();
        	},50);	        		        	
    	}
    }
    
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
    	if (inst.text() == 'volume_off'){
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/1');
    		confLH.new_message_sound_admin_enabled = 1;
    		inst.text('volume_up');
    	} else {
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/0');
    		confLH.new_message_sound_admin_enabled = 0;
    		inst.text('volume_off');
    	}
    	return false;
    };

    this.disableNewChatSoundAdmin = function(inst)
    {
    	if (inst.text() == 'volume_off'){
    		$.get(this.wwwDir+  'user/setsettingajax/new_chat_sound/1');
    		confLH.new_chat_sound_enabled = 1;
    		inst.text('volume_up');
    	} else {
    		$.get(this.wwwDir+  'user/setsettingajax/new_chat_sound/0');
    		confLH.new_chat_sound_enabled = 0;
    		inst.text('volume_off');
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
        if (inst.text() == 'flash_off'){
            $.getJSON(this.wwwDir+  'user/setoffline/false',function (data) {
                if (data.error == false) {
                    inst.text('flash_on');
                } else {
                    alert(data.msg);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.dir(jqXHR);
                alert('getJSON request failed! ' + textStatus + ':' + errorThrown + ':' + jqXHR.responseText);
            });
        } else {
            $.getJSON(this.wwwDir+  'user/setoffline/true',function () {
                inst.text('flash_off');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.dir(jqXHR);
                alert('getJSON request failed! ' + textStatus + ':' + errorThrown + ':' + jqXHR.responseText);
            });
        }
        return false;
    };
   	
	this.switchToOfflineForm = function(){
		var form = $('#form-start-chat');
		form.attr('action',$('#form-start-chat').attr('action')+'/(switchform)/true/(offline)/true/(leaveamessage)/true/(department)/'+$('#id_DepartamentID').val());
		form.submit();
		return false;
	};
	    
    this.changeStatusAction = function(form,chat_id){
    	var inst = this;
    	$.postJSON(form.attr('action'),form.serialize(), function(data) {
	   		 if (data.error == 'false') {
	   			$('#myModal').modal('hide');
	   			inst.updateVoteStatus(chat_id);
	   		 } else {
	   			 alert(data.result);
	   		 }
	   	 });
    	return false;
    };
    
    this.submitModalForm = function(form){
    	var inst = this;
    	$.post(form.attr('action'),form.serialize(), function(data) {	   		
	   		$('#myModal').html(data);	   		
	   	 });
    	return false;
    };
    
    this.changeVisibility = function(inst)
    {
    	if (inst.text() == 'visibility_off'){
    		$.get(this.wwwDir+  'user/setinvisible/false');
    		inst.text('visibility');
    	} else {
    		$.get(this.wwwDir+  'user/setinvisible/true');
    		inst.text('visibility_off');
    	}
    	return false;
    };

    
    this.disableChatSoundUser = function(inst)
    {
    	if (inst.find('> i').text() == 'volume_off') {
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/1');
    		confLH.new_message_sound_user_enabled = 1;
    		inst.find('> i').text('volume_up');
    	} else {
    		$.get(this.wwwDir+  'user/setsettingajax/chat_message/0');
    		confLH.new_message_sound_user_enabled = 0;
    		inst.find('> i').text('volume_off');
    	};

    	if (!!window.postMessage && parent) {
    		if (inst.find('> i').text() == 'volume_off') {
    			parent.postMessage("lhc_ch:s:0", '*');
    		} else {
    			parent.postMessage("lhc_ch:s:1", '*');
    		}
    	};

    	return false;
    };
    
    this.pendingMessagesToStore = [];
    
    this.prestartChat = function(timestamp,inst) {
    	    	
    	if (inst.find('.form-protected').size() == 0) {
    		
    			if (inst.attr('lhc-captcha-submitted') != 1) {
    				inst.attr('lhc-captcha-submitted',1);    				
    				inst.find('input[type="submit"]').attr('disabled','disabled');    				
    		    	$.getJSON(this.wwwDir + 'captcha/captchastring/form/'+timestamp, function(data) {
    		    		 inst.append('<input type="hidden" value="'+timestamp+'" name="captcha_'+data.result+'" /><input type="hidden" value="'+timestamp+'" name="tscaptcha" /><input type="hidden" class="form-protected" value="1" />');
    		    		 inst.submit();
    		    	});
    		    	
    		    	var keyUpStarted = inst.attr('key-up-started') == 1;
    		    	
    		  		if (keyUpStarted == true) {
    		  			jQuery('<div/>', {
    	    			    'class': 'message-row response',					   
    	    			    text: $('#id_Question').val()
    	    			}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+visitorTitle+'</span>');
    	            	$('#messagesBlock').stop(true,false).animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 500);
    	            	this.pendingMessagesToStore.push($('#id_Question').val());    	  			
        	  			$('#id_Question').val('');   
    		  		}
    		  		
    			} else {
    				// That means it's second submit, and that means user pressed enter
    				if ($('#messagesBlock').size() > 0) {
    	            	jQuery('<div/>', {
    	    			    'class': 'message-row response',					   
    	    			    text: $('#id_Question').val()
    	    			}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+visitorTitle+'</span>');
    	            	$('#messagesBlock').stop(true,false).animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 500);
    				};
    	  			this.pendingMessagesToStore.push($('#id_Question').val());    	  			
    	  			$('#id_Question').val('');    	  			   	  			
    			}
		    	
		    	return false;
	  	} else {
	  		
	  		// Avoid users stupidity if they enable it but form has extra field
	  		if (inst.find('#hasFormExtraField').size() == 1) {
	  			return true;
	  		}
	  		
	  		if (inst.attr('lhc-form-submitted') != 1) { // Form is not submitted
		  		inst.attr('lhc-form-submitted',1);
		  		var instSelf = this;	
		  				  				  		
		  		var keyUpStarted = inst.attr('key-up-started') == 1;
		  		
		  		if (keyUpStarted == true) {
		  			inst.append('<input type="hidden" value="1" name="keyUpStarted" />');
		  		}
		  				  		
		  		$.post(inst.attr('action'),inst.serialize(), function (response) {		  		
		  			var valueQuestion = $('#id_Question').val();
		  			if (sessionStorage) {
		  				try {
		  					sessionStorage.setItem('lhc_ttxt',valueQuestion);
		  				} catch(e) {}
		         	};
		         		         	
		         	var scripts = $('head > script');
		         	var headCurrent =  $('head');
		  			var currentSripts = [];
		         	
		         	$('head > script').each( function() {
		         		var script = $(this);
		         		if (script.attr('src') !== undefined) {
		         			currentSripts.push(script.attr('src'));
		         		}
		         	});
		         			  		
		  			$('<div>').html(response).find('> script').each(function(){
		  				var script = $(this);		  				
		  				if (script.attr('src') === undefined) {		  					
		  					headCurrent.append(script);		  					
		  				} else {
		  					if (currentSripts.indexOf(script.attr('src')) == -1) { // Add JS only if it's new
		  						headCurrent.append("<script src=\""+script.attr('src')+"\"></script>");		  						
		  					}
		  				}
		  			});
		  					  			
		  			paramsDocument = "<script>lhinst.addMessagesToStore("+JSON.stringify(instSelf.pendingMessagesToStore)+")</script>";		
		         	$('#widget-layout').html($('<div>').html(response).find('#widget-layout').html());		  			
		         	$('#widget-layout-js').html($('<div>').html(response).find('#widget-layout-js').html()+paramsDocument);		         	
		         	
	            });
		  		
		  		if (keyUpStarted == false) {
		  			$('#id_Question').val('');
		  		}
		  		
	  		} else {	  			
	  			if ($('#messagesBlock').size() > 0) {
	            	jQuery('<div/>', {
	    			    'class': 'message-row response',					   
	    			    text: $('#id_Question').val()
	    			}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+visitorTitle+'</span>');
	            	$('#messagesBlock').stop(true,false).animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 500);
				};	  			
	  			this.pendingMessagesToStore.push($('#id_Question').val());
	  			$('#id_Question').val('');
	  		}
	  	}

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
				var response = data.response();
				if (response != undefined && response.result != undefined && response.result.error == 'true' && response.result.error_msg != undefined) {
					alert(response.result.error_msg);
				}
    			if (LHCCallbacks.addFileUserUpload) {
    				LHCCallbacks.addFileUserUpload(data_config.chat_id);
    			}
    		},
    		progressall: function (e, data) {
    			var progress = parseInt(data.loaded / data.total * 100, 10);
    			$('#id-operator-typing').css('visibility','visible');
    			$('#id-operator-typing').html(progress+'%');
    		}}).prop('disabled', !$.support.fileInput)
    		.parent().addClass($.support.fileInput ? undefined : 'disabled');
    };

    
    this.addFileUserUploadOnline = function(data_config,callback) {
    	var _this = this;
    	$('#fileuploadonline').fileupload({
            url: this.wwwDir + 'file/uploadfileonline/'+data_config.online_user_vid,
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
       			_this.updateOnlineFilesUser(data_config.online_user_vid);
       			if (callback) {
       				callback(data_config.online_user_vid);
       	        };			
       		},       		
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);               
                $('#upload-status-user-online').html(progress+'%');
            }}).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    };
    
    this.updateChatFiles = function(chat_id) {
    	$.postJSON(this.wwwDir + 'file/chatfileslist/' + chat_id, function(data){
    		$('#chat-files-list-'+chat_id).html(data.result);
    	});
    };
    
    this.updateOnlineFiles = function(online_user_id) {
    	$.postJSON(this.wwwDir + 'file/onlinefileslist/' + online_user_id, function(data){
    		$('#online-user-files-list-'+online_user_id).html(data.result);
    	});
    };    

    this.updateOnlineFilesUser = function(online_user_vid) {
    	$.postJSON(this.wwwDir + 'file/useronlinefileslist/' + online_user_vid, function(data){
    		$('#user-online-files-list').html(data.result);
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
				var response = data.response();
				if (response != undefined && response.result != undefined && response.result.error == 'true' && response.result.error_msg != undefined) {
					alert(response.result.error_msg);
				} else {
					lhinst.updateChatFiles(data_config.chat_id);
				}

				if (LHCCallbacks.addFileUpload) {
    				LHCCallbacks.addFileUpload(data_config.chat_id);
    			}
    		},
    		dropZone: $('#drop-zone-'+data_config.chat_id),
    		pasteZone: $('#CSChatMessage-'+data_config.chat_id),
    		progressall: function (e, data) {
    			var progress = parseInt(data.loaded / data.total * 100, 10);
    			$('#user-is-typing-'+data_config.chat_id).css('visibility','visible');
    			$('#user-is-typing-'+data_config.chat_id).html(progress+'%');
    		}}).prop('disabled', !$.support.fileInput)
    		.parent().addClass($.support.fileInput ? undefined : 'disabled');
    };

    this.addFileUploadOnlineUser = function(data_config, callbackUploaded) {
    	var _this = this;
    	$('#fileupload-online-user-'+data_config.online_user_id).fileupload({
            url: this.wwwDir + 'file/uploadfileadminonlineuser/'+data_config.online_user_id,
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
       			if (callbackUploaded) {
       				callbackUploaded(data_config.online_user_id);
       	        };
       	        _this.updateOnlineFiles(data_config.online_user_id);
       		},
            dropZone: $('#drop-zone-online-user-'+data_config.online_user_id),        
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#upload-status-admin-'+data_config.online_user_id).html(progress+'%');
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
			if (data.error == 'true' && data.errors != null) {
				alert(data.errors.join("\n"));
			}
    	});    	
    	if (operation == 'lhc_screenshot') {
    		$('#user-screenshot-container').html('').addClass('screenshot-pending');
    		var inst = this;
    		setTimeout(function(){
    			inst.updateScreenshot(chat_id);
    		},5000);    		
    	};
    };
    
    this.addRemoteOnlineCommand = function(online_user_id,operation) {
    	$.postJSON(this.wwwDir + 'chat/addonlineoperationiframe/' + online_user_id,{'operation':operation}, function(data){
    		if (LHCCallbacks.addRemoteOnlineCommand) {
   	        	LHCCallbacks.addRemoteOnlineCommand(online_user_id);
   	        };
    	});    	
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
    
    this.eNick = function() {
    	lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/editnick/'+this.chat_id+'/'+this.hash})   
    }

}

var lhinst = new lh();
lhinst.playPreloadSound();

function preloadSound() {
	lhinst.playPreloadSound();
	jQuery(document).off("click", preloadSound);
	jQuery(document).off("touchstart", preloadSound);
}

jQuery(document).on("click", preloadSound);
jQuery(document).on("touchstart", preloadSound);

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
	
	var mapTabSection = $('#map-activator').parent();
		
	function showMarkers() {
	    if ( processing == false) {	    		
	    	if (mapTabSection.hasClass('active')) {
		        processing = true;
	    		$.ajax({
	    			url : WWW_DIR_JAVASCRIPT + 'chat/jsononlineusers'+(parseInt($('#id_department_map_id').val()) > 0 ? '/(department)/'+parseInt($('#id_department_map_id').val()) : '' )+(parseInt($('#maxRows').val()) > 0 ? '/(maxrows)/'+parseInt($('#maxRows').val()) : '' )+(parseInt($('#userTimeout').val()) > 0 ? '/(timeout)/'+parseInt($('#userTimeout').val()) : '' ),
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
    				lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+e.Id})    				
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