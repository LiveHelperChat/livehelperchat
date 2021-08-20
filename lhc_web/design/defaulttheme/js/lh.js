var lhcError = {
    log : function(message, filename, lineNumber, stack, column) {
            var e;
            e = {};
            e.message = message || "";
            e.location = location && location.href ? location.href : "";
            e.message += "\n" + window.navigator.userAgent;
            e.file = filename || "";
            e.line = lineNumber || "";
            e.column = column || "";
            e.stack = stack ? JSON.stringify(stack) : "";
            e.stack = e.stack.replace(/(\r\n|\n|\r)/gm, "");
            var xhr = new XMLHttpRequest();
            xhr.open( "POST", WWW_DIR_JAVASCRIPT + '/audit/logjserror', true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send( "data=" + encodeURIComponent( JSON.stringify(e) ) );
    }
}

window.addEventListener('error', function(e) {
    if (lhcError && (e.filename.indexOf('js_static') !== -1 || e.filename.indexOf('compiledtemplates') !== -1 || e.filename.indexOf('defaulttheme') !== -1)) {
       lhcError.log(e.message, e.filename, e.lineNumber || e.lineno, e.error.stack, e.colno);
    }
})

try {

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
    this.forceBottomScroll = false;
    this.appendSyncArgument = '';
    this.nodeJsMode = false;
    this.previous_chat_id = 0;

    this.gmaps_loaded = false;

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
    
    this.audio = typeof window.Audio !== "undefined" ? new Audio() : null;
    if (this.audio !== null) {
        this.audio.autoplay = 'autoplay';
    }

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
            inst.addQuateHandler(chat_id);
            inst.loadMainData(chat_id);
            ee.emitEvent('chatTabLoaded', [chat_id]);
            ee.emitEvent('chatStartTab', [chat_id, {name: nick, focus: true}]);
        });
    }

    this.loadMainData = function(chat_id) {
        $.getJSON(this.wwwDir + 'chat/loadmaindata/' + chat_id, { }, function(data) {
            $.each(data.items, function( index, dataElement ) {
                var el = $(dataElement.selector);

                if (typeof dataElement.attr !== 'undefined') {
                    $.each(dataElement.attr, function( attr, data ) {
                        if (attr == 'text') {
                            el.text(data);
                        } else {
                            el.attr(attr,data);
                        }
                    });
                }

                if (typeof dataElement.action !== 'undefined') {
                    if (dataElement.action == 'hide') {
                        el.hide();
                    } else if(dataElement.action == 'show') {
                        el.show();
                    } else if(dataElement.action == 'remove') {
                        el.remove();
                    } else if(dataElement.action == 'event') {
                        ee.emitEvent(dataElement.event_name, dataElement.event_value);
                    } else if(dataElement.action == 'click') {
                        el.attr('auto-scroll',1);
                        el.click();
                    }
                }
            });

            ee.emitEvent('mainChatDataLoaded', [chat_id, data]);

        }).fail(function() {

        });
    }

    this.getSelectedText = function () {
        var text = '';
        var selection;

        if (window.getSelection) {
            selection = window.getSelection();
            text = selection.toString();
        } else if (document.selection && document.selection.type !== 'Control') {
            selection = document.selection.createRange();
            text = selection.text;
        }

        return {
            selection: selection,
            text: text
        };
    }

    this.popoverShown = false;
    this.popoverShownNow = false
    this.selection = null;

    this.mouseContextMenu = function(e) {

        if (e.which == 3 && typeof $(this).attr('id') !== 'undefined') {

            $('.popover-copy').popover('dispose');

            var selected = e.data.that.getSelectedText();
            var hasSelection = false;
            if (selected.text.length && (e.data.that.selection === null || e.data.that.selection.text !== selected.text)) {
                hasSelection = true;
                e.data.that.selection = selected;
            }

            var msgId = $(this).attr('id').replace('msg-','');
            var isOwner = $(this).attr('data-op-id') == confLH.user_id;

            var quoteParams = {
                placement:'right',
                trigger:'manual',
                animation:false,
                html:true,
                container:'#chat-id-'+e.data.chat_id,
                template : '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>',
                content:function(){
                    return '<a href="#" id="copy-popover-'+e.data.chat_id+'" ><i class="material-icons">&#xE244;</i>'+confLH.transLation.quote+'</a>'+ (isOwner ? '<br/><a href="#" id="edit-popover-'+e.data.chat_id+'" ><i class="material-icons">edit</i>'+confLH.transLation.edit+'</a>' : '') + '<br/><a href="#" id="ask-help-popover-'+e.data.chat_id+'" ><i class="material-icons">supervisor_account</i>'+confLH.transLation.ask_help+'</a>' + (hasSelection ? '<br/><a href="#" id="copy-text-popover-'+e.data.chat_id+'" ><i class="material-icons">content_copy</i>'+confLH.transLation.copy+' (Ctrl+C)</a>' : '') + (!hasSelection ? '<br/><a href="#" id="copy-all-text-popover-'+e.data.chat_id+'" ><i class="material-icons">content_copy</i>'+confLH.transLation.copy+' (Ctrl+C)</a><br/><a href="#" id="copy-group-text-popover-'+e.data.chat_id+'" ><i class="material-icons">content_copy</i>'+confLH.transLation.copy_group+'</a>' : '')+(!hasSelection ? '<br/><a href="#" id="translate-msg-'+e.data.chat_id+'" ><i class="material-icons">language</i>'+confLH.transLation.translate+'</a>' : '');
                }
            }
            
            var containerPopover = $('#messagesBlock-'+e.data.chat_id+' > #msg-'+msgId+' > .msg-body');

            if (containerPopover.length == 0) return ;

            ee.emitEvent('quoteActionRight', [quoteParams, e.data.chat_id, msgId]);

            containerPopover.popover(quoteParams).popover('show').addClass('popover-copy');

            $('#copy-popover-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                $.getJSON(e.data.that.wwwDir + 'chat/quotemessage/' + msgId, function(data){
                    data.msg && e.data.that.insertTextToMessageArea(e.data.chat_id, data.msg);
                    e.data.that.hidePopover();
                });
            });

            $('#ask-help-popover-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                $.getJSON(e.data.that.wwwDir + 'chat/quotemessage/' + msgId, function(data){
                    if (!$('#private-chat-tab-link-'+e.data.chat_id).attr('private-loaded')) {
                        $('#private-chat-tab-link-'+e.data.chat_id).attr('private-loaded',true).click();
                        ee.emitEvent('privateChatStart', [e.data.chat_id,{'default_message':data.msg}]);
                    } else {
                        $('#private-chat-tab-link-'+e.data.chat_id).attr('private-loaded',true).click();
                        ee.emitEvent('groupChatPrefillMessage', [e.data.chat_id,data.msg]);
                    }
                    e.data.that.hidePopover();
                });
            });

            !hasSelection && $('#translate-msg-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                lhc.methodCall('lhc.translation','translateMessageVisitor',{'msg_id':msgId,'chat_id':e.data.chat_id});
                e.data.that.hidePopover();
            });

            !hasSelection && $('#copy-all-text-popover-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                $.getJSON(e.data.that.wwwDir + 'chat/quotemessage/' + msgId, function(data){
                    var txtdom = $('#CSChatMessage-'+e.data.chat_id);
                    var originalAreaText = txtdom.val();
                    txtdom.val(data.msg);
                    txtdom.select();
                    document.execCommand("copy");
                    txtdom.val(originalAreaText);
                    e.data.that.hidePopover();
                });
            });

            !hasSelection && $('#copy-group-text-popover-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                $.getJSON(e.data.that.wwwDir + 'chat/quotemessage/' + msgId +'/(type)/group', function(data){
                    var txtdom = $('#CSChatMessage-'+e.data.chat_id);
                    var originalAreaText = txtdom.val();
                    txtdom.val(data.msg);
                    txtdom.select();
                    document.execCommand("copy");
                    txtdom.val(originalAreaText);
                    e.data.that.hidePopover();
                });
            });

            isOwner && $('#edit-popover-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                $.getJSON(e.data.that.wwwDir + 'chat/editprevious/' + e.data.chat_id + '/' + msgId, function(data){
                    if (data.error == 'f') {
                        var textArea = $('#CSChatMessage-'+e.data.chat_id);
                        textArea.val(data.msg).attr('data-msgid',data.id).addClass('edit-mode');
                        $('#msg-'+data.id).addClass('edit-mode');
                        textArea.focus();
                    } else {
                        alert(data.error);
                    }
                });
                e.data.that.hidePopover();
            });

            hasSelection && $('#copy-text-popover-'+e.data.chat_id).click(function(event){
                event.stopPropagation();
                event.preventDefault();
                var textToCopy = e.data.that.getSelectedTextPlain();
                var txtdom = $('#CSChatMessage-'+e.data.chat_id);
                var originalAreaText = txtdom.val();
                txtdom.val(textToCopy);
                txtdom.select();
                document.execCommand("copy");
                txtdom.val(originalAreaText);
                e.data.that.hidePopover();
            });

            e.data.that.popoverShown = true;
            e.data.that.popoverShownNow = false;

            return false;
        }
    }

    this.insertTextToMessageArea = function (chat_id, msg) {
        var textArea = $('#CSChatMessage-'+chat_id);
        var textAreaVal = textArea.val().replace(/^\s*\n/g, "");
        textArea.val((textAreaVal != '' ? textAreaVal + '[quote]' + msg + '[/quote]' : '[quote]'+msg+'[/quote]')+"\n").focus();

        var ta = textArea[0];
        var maxrows = 30;
        var lh = ta.clientHeight / ta.rows;
        while (ta.scrollHeight > ta.clientHeight && !window.opera && ta.rows < maxrows) {
            ta.style.overflow = 'hidden';
            ta.rows += 1;
        }

        if (ta.scrollHeight > ta.clientHeight) ta.style.overflow = 'auto';
    }

    this.mouseClicked = function (e) {
        selected = e.data.that.getSelectedText();

        $('.popover-copy').popover('dispose');

        if (selected.text.length && (e.data.that.selection === null || e.data.that.selection.text !== selected.text)) {

            e.data.that.selection = selected;

            var quoteParams = {
                placement:'right',
                trigger:'manual',
                animation:false,
                html:true,
                container:'#chat-id-'+e.data.chat_id,
                template : '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>',
                content:function(){return '<a href="#" id="copy-popover-'+e.data.chat_id+'" ><i class="material-icons">&#xE244;</i>'+confLH.transLation.quote+'</a>'; }
            }

            var placement = typeof $(this).attr('id') !== 'undefined' ? '#messagesBlock-'+e.data.chat_id+' > #msg-'+$(this).attr('id').replace('msg-','')+' > .msg-body' : this;

            var containerPopover = $(placement);

            if (containerPopover.length == 0) return ;

            ee.emitEvent('quoteAction', [quoteParams,e.data.chat_id]);

            containerPopover.popover(quoteParams).popover('show').addClass('popover-copy');

            $('#copy-popover-'+e.data.chat_id).click(function(){
                lhinst.quateSelection(e.data.chat_id);
            });


            e.data.that.popoverShown = true;
            e.data.that.popoverShownNow = true;
        } else {
            e.data.that.selection = null;
        }
    }

    this.addQuateHandler = function(chat_id)
    {
        this.popoverShown = false;
        $('#messagesBlock-'+chat_id+' > .message-row:not([qt])')
            .on('mouseup',{chat_id:chat_id, that : this}, lhinst.mouseClicked)
            .on('contextmenu', {chat_id:chat_id, that : this}, lhinst.mouseContextMenu).attr('qt',1);
    }

    this.getSelectedTextPlain = function() {
        var textToPaste = this.selection.text.replace(/[\uD7AF\uD7C7-\uD7CA\uD7FC-\uF8FF\uFA6E\uFA6F\uFADA]/g,'');

        textToPaste = textToPaste.replace(/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}(.*)/gm,'');
        textToPaste = textToPaste.replace(/^[0-9]{2}:[0-9]{2}:[0-9]{2}(.*)/gm,'');
        textToPaste = textToPaste.replace(/^\s*\n/gm, "");
        textToPaste = textToPaste.replace(/^ /gm, "");

        return textToPaste;
    }

    this.quateSelection = function (chat_id) {
        $('.popover-copy').popover('dispose');
        var textToPaste = this.getSelectedTextPlain();
        window.textreplace = textToPaste;
        this.insertTextToMessageArea(chat_id, textToPaste);
        this.popoverShown = false;
    };

    this.hidePopover = function () {

        if (this.popoverShownNow === true) {
            this.popoverShownNow = false;
        } else {
            if (this.popoverShown === true) {
                this.popoverShown = false;
                $('.popover-copy').popover('dispose');
            }
        }
    };

    this.addTab = function(tabs, url, name, chat_id, focusTab, position) {
    	// If tab already exits return
    	if (tabs.find('#chat-tab-link-'+chat_id).length > 0) {
    		return ;
    	}

    	var hideTabs = confLH.new_dashboard && confLH.hide_tabs ? ' d-none' : '';

    	var contentLi = '<li role="presentation" id="chat-tab-li-'+chat_id+'" class="nav-item'+hideTabs+'"><a class="nav-link" href="#chat-id-'+chat_id+'" id="chat-tab-link-'+chat_id+'" aria-controls="chat-id-'+chat_id+'" role="tab" data-toggle="tab"><i id="msg-send-status-'+chat_id+'" class="material-icons send-status-icon icon-user-online">send</i><i id="user-chat-status-'+chat_id+'" class="'+this.tabIconClass+'">'+this.tabIconContent+'</i><span class="ntab" id="ntab-chat-'+chat_id+'">' + name.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span><span onclick="return lhinst.removeDialogTab('+chat_id+',$(\'#tabs\'),true)" class="material-icons icon-close-chat">close</span></a></li>';

    	if (typeof position === 'undefined' || parseInt(position) == 0) {
    		tabs.find('> ul').append(contentLi);
    	} else {
    		tabs.find('> ul > li:eq('+ (position - 1)+')').after(contentLi);
    	};

    	$('#chat-tab-link-'+chat_id).click(function() {

    	    lhinst.previous_chat_id > 0 && $('#unread-separator-'+lhinst.previous_chat_id).remove();
            lhinst.previous_chat_id = chat_id;

            setTimeout(function() {
                $('#CSChatMessage-' + chat_id).focus();
            },2);

    		var inst = $(this);
    		setTimeout(function(){
    			inst.find('.msg-nm').remove();

    			var scrollNeeded = false;

                if (inst.hasClass('has-pm')) {
                    scrollNeeded = true;
                    inst.removeClass('has-pm');
                }

                if (scrollNeeded == true) {
                    $('#messagesBlock-'+chat_id).prop('scrollTop',$('#messagesBlock-'+chat_id).prop('scrollHeight'));
                }
    		},500);

            ee.emitEvent('chatTabClicked', [chat_id, inst]);
    	});
    	
    	var hash = window.location.hash.replace('#/','#');	

    	var inst = this;

    	$.get(url, function(data) {

    	    if (data == '') {
                inst.removeDialogTab(chat_id,tabs,true);
    	        return;
            }
    	    
    		if (typeof focusTab === 'undefined' || focusTab === true || hash == '#chat-id-'+chat_id){
	    		tabs.find('> ul > li > a.active').removeClass("active");
	    		tabs.find('> ul > #chat-tab-li-'+chat_id+' > a').addClass("active");
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
            inst.addQuateHandler(chat_id);
            inst.loadMainData(chat_id);
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
    
    this.forgetChat = function (chat_id,listId) {
        if (localStorage) {
            try {
                chat_id = parseInt(chat_id);

                var achat_id = localStorage.getItem(listId);
                var achat_id_array = new Array();

                if (achat_id !== null) {
                    achat_id_array = achat_id.split(',').map(Number);
                }

                if (achat_id_array.indexOf(chat_id) !== -1){
                    achat_id_array.splice(achat_id_array.indexOf(chat_id), 1);
                }

                localStorage.setItem(listId,achat_id_array.join(','));
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

        var textArea = $("#CSChatMessage-"+chat_id);

        if (textArea.is("[readonly]")) {
            return;
        }

    	var _this  = this;
        $.postJSON(this.wwwDir + 'chat/holdaction/' + chat_id, function(data) {
            if (data.error == false) {

                if (data.hold == true) {
                    inst.addClass('btn-outline-info');
				} else {
                    inst.removeClass('btn-outline-info');
				}

				if (data.msg != '') {
					$('#messagesBlock-'+chat_id).append(data.msg).scrollTop($("#messagesBlock-"+chat_id).prop("scrollHeight"));
				}

                _this.syncadmincall();
            } else {
                alert(data.msg);
            }
        });
	},

    this.copyContent = function(inst){

        var textArea = document.createElement("textarea");
        textArea.value = inst.attr('data-copy');

        // Avoid scrolling to bottom
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
        } catch (err) {
            alert('Oops, unable to copy');
        }

        document.body.removeChild(textArea);

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
            }, 1000);
        }

        setTooltip(inst.attr('data-success'));
        hideTooltip();

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

    this.removeDialogTabGroup = function(chat_id, tabs)
    {
        ee.emitEvent('unloadGroupChat', [chat_id]);
        var location = this.smartTabFocus(tabs, chat_id);
    };

    this.addGroupTab = function(tabs, name, chat_id, background) {
        // If tab already exits return
        if (tabs.find('#chat-tab-link-'+chat_id).length > 0) {
            tabs.find('> ul > li > a.active').removeClass("active");
            tabs.find('> ul > li#chat-tab-li-'+chat_id+' > a').addClass("active");
            tabs.find('> div.tab-content > div.active').removeClass('active');
            tabs.find('> div.tab-content > #chat-id-'+chat_id).addClass('active');
            ee.emitEvent('groupChatTabClicked', [chat_id]);
            return ;
        }

        var contentLi = '<li role="presentation" id="chat-tab-li-'+chat_id+'" class="nav-item"><a class="nav-link" href="#chat-id-'+chat_id+'" id="chat-tab-link-'+chat_id+'" aria-controls="chat-id-'+chat_id+'" role="tab" data-toggle="tab"><i id="msg-send-status-'+chat_id+'" class="material-icons send-status-icon icon-user-online">send</i><i class="whatshot blink-ani d-none text-warning material-icons">whatshot</i><i id="user-chat-status-'+chat_id+'" class="'+this.tabIconClass+'">group</i><span class="ntab" id="ntab-chat-'+chat_id+'">' + name.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span><span onclick="return lhinst.removeDialogTabGroup(\''+chat_id+'\',$(\'#tabs\'),true)" class="material-icons icon-close-chat">close</span></a></li>';

        tabs.find('> ul').append(contentLi);
        var hash = window.location.hash.replace('#/','#');

        var inst = this;

        if (background !== true) {
            tabs.find('> ul > li > a.active').removeClass("active");
            tabs.find('> ul > #chat-tab-li-'+chat_id+' > a').addClass("active");
            tabs.find('> div.tab-content > div.active').removeClass('active');
            tabs.find('> div.tab-content').append('<div role="tabpanel" class="tab-pane active" id="chat-id-'+chat_id+'"></div>');
        } else {
            tabs.find('> div.tab-content').append('<div role="tabpanel" class="tab-pane" id="chat-id-'+chat_id+'"></div>');
        }

        ee.emitEvent('groupChatTabLoaded', [chat_id]);
        
        $('#chat-tab-link-'+chat_id).click(function() {
            ee.emitEvent('groupChatTabClicked', [chat_id.replace('gc','')]);
        });
    };

    this.startGroupChat = function (chat_id,tabs,name, background) {
        this.addGroupTab(tabs, name, 'gc'+chat_id, background);
    }

    this.hideShowAction = function(options) {

        var messagesBlock = $('#messagesBlock-' + options['chat_id']);

        var needScroll = (messagesBlock.prop('scrollTop') + messagesBlock.height() + 30) > messagesBlock.prop('scrollHeight')

        var msg = $('#message-more-'+options['id']);
        if (msg.hasClass('hide')) {
            msg.removeClass('hide');
            options['hide_show'] == false ? $('#hide-show-action-'+options['id']).remove() : $('#hide-show-action-'+options['id']).text(options['hide_text']);
        } else {
            msg.addClass('hide');
            if (options['hide_show'] == true) {
                $('#hide-show-action-'+options['id']).text(options['show_text']);
            }
        }
        
        needScroll && messagesBlock.scrollTop(messagesBlock.prop('scrollHeight'));
    }

    this.buttonAction = function(inst,payload) {
        var row = inst.closest('.message-row');
        $.getJSON(this.wwwDir + 'chat/abstractclick/' + row.attr('id').replace('msg-','') + '/' + payload, function(data) {
            if (data.error) {
                alert(data.error);
            } else if (data.replace_id && data.html) {
                var messagesBlock = $('#messagesBlock-' + data.chat_id);
                var needScroll = (messagesBlock.prop('scrollTop') + messagesBlock.height() + 30) > messagesBlock.prop('scrollHeight');
                $(data.replace_id).replaceWith(data.html);
                lhinst.addQuateHandler(data.chat_id);
                needScroll && messagesBlock.scrollTop(messagesBlock.prop('scrollHeight'));
            } else if (data.modal) {
                lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+data.modal});
            }
        });
    }
    
    this.startChat = function (chat_id,tabs,name,focusTab,position) {
    	    	
    	this.removeBackgroundChat(chat_id);
    	this.hideNotification(chat_id);

    	$('#sub-tabs').length > 0 && $('#sub-tabs a[href=\'#sub-tabs-open\']').tab('show');

        if ( this.chatUnderSynchronization(chat_id) == false ) {        	
        	var focusTabAction = typeof focusTab !== 'undefined' ? focusTab : true;
        	var rememberAppend = this.disableremember == false ? '/(remember)/true' : '';
        	this.addTab(tabs, this.wwwDir +'chat/adminchat/'+chat_id+rememberAppend, name, chat_id, focusTabAction, position);
        	var inst = this;
        	 setTimeout(function(){
     	    	inst.syncadmininterfacestatic();
     	    },1000);
        } else {
        	tabs.find('> ul > li > a.active').removeClass("active");
    		tabs.find('> ul > li#chat-tab-li-'+chat_id+' > a').addClass("active");
    		tabs.find('> div.tab-content > div.active').removeClass('active');
    		tabs.find('> div.tab-content > #chat-id-'+chat_id).addClass('active');
    		window.location.hash = '#/chat-id-'+chat_id;
        }
        
        ee.emitEvent('chatStartTab', [chat_id, {name: name, focus: (typeof focusTab !== 'undefined' ? focusTab : true), position: position}]);
    };

    this.backgroundChats = [];
    
    this.startChatBackground = function (chat_id,tabs,name,backgroundType) {
    	if ( this.chatUnderSynchronization(chat_id) == false ) {  
    		this.backgroundChats.push(parseInt(chat_id));
	    	var rememberAppend = this.disableremember == false ? '/(remember)/true' : '';

	    	if (!backgroundType) {
                backgroundType = 'background';
            }

	    	this.addTab(tabs, this.wwwDir +'chat/adminchat/'+chat_id+rememberAppend+'/(arg)/'+backgroundType, name, chat_id, false);
	    	ee.emitEvent('chatStartBackground', [chat_id,{name:name}]);
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

        this.forgetChat(chat_id,'achat_id');

        ee.emitEvent('removeSynchroChat', [chat_id]);

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
            
            if (inst.nodeJsMode == true) {
            	inst.typing_timeout = setTimeout(function(){inst.typingStoppedOperator(chat_id);},3000);
            	ee.emitEvent('operatorTyping', [{'chat_id':chat_id,'status':true}]);
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
    	
    	$('#remarks-status-'+chat_id).addClass('text-warning');
    	$('#main-user-info-remarks-'+chat_id+' .alert').remove();
    	var inst = this;
    	this.remarksTimeout = setTimeout(function(){
    		$.postJSON(inst.wwwDir + 'chat/saveremarks/' + chat_id,{'data':$('#ChatRemarks-'+chat_id).val()}, function(data){
				if(data.error == 'false') {
					$('#remarks-status-'+chat_id).removeClass('text-warning');
				} else {
					$('#main-user-info-remarks-'+chat_id).prepend(data.result);
				}
    		});
    	},500);    	
    };
    
    this.saveNotes = function(chat_id) {
    	clearTimeout(this.remarksTimeout);    	    	
    	$('#remarks-status-online-'+chat_id).addClass('text-warning');
    	var inst = this;
    	this.remarksTimeout = setTimeout(function(){
    		$.postJSON(inst.wwwDir + 'chat/saveonlinenotes/' + chat_id,{'data':$('#OnlineRemarks-'+chat_id).val()}, function(data){
    			$('#remarks-status-online-'+chat_id).removeClass('text-warning');
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
            parent.postMessage("lhc_chat_closed_explicit", '*');
    	}
    };

    this.typingStoppedOperator = function(chat_id) {
        var inst = this;
        if (inst.is_typing == true){
        	
        	if (lhinst.nodeJsMode  == true) {
        		inst.is_typing = false;
           		ee.emitEvent('operatorTyping', [{'chat_id':chat_id,'status':false}]);
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

    this.initTypingMonitoringUser = function(chat_id) {

        var www_dir = this.wwwDir;
        var inst = this;        
        
        try {
	        if (sessionStorage && sessionStorage.getItem('lhc_ttxt') && sessionStorage.getItem('lhc_ttxt') != '') {
	        	jQuery('#CSChatMessage').val(sessionStorage.getItem('lhc_ttxt'));       
	    	}
        } catch(e) {}

        var hasMic = false;

        if (jQuery('#CSChatMessage').val() != '') {
            $('#lhc-send-icon').show();
            $('#lhc-mic-icon').hide();
        } else {
            if ($('#lhc-mic-icon').length > 0){
                $('#lhc-send-icon').hide();
                $('#lhc-mic-icon').show();
                hasMic = true;
            }
        }

        jQuery('#CSChatMessage').bind('keyup', function (evt){

            try {
                if (sessionStorage) {
        			 sessionStorage.setItem('lhc_ttxt',$(this).val());
                };
            } catch(e) {}

            var element = $(this)[0];
            element.style.height = "5px";

            if (hasMic == true) {
                if ($(this).val() != '') {
                    $('#lhc-send-icon').show();
                    $('#lhc-mic-icon').hide();
                    $('#voice-control-message').hide();
                } else {
                    $('#lhc-send-icon').hide();
                    $('#lhc-mic-icon').show();
                }
            }

            var heightScroll = ((element.scrollHeight)+3);

            if (heightScroll > 48) {
                heightScroll = heightScroll + 10;
                if (heightScroll > 90) {
                    element.style.overflowY = 'auto';
                } else {
                    element.style.overflowY = 'hidden';
                }
            }

            element.style.height = heightScroll+"px";

            if (inst.is_typing == false) {

                clearTimeout(inst.typing_timeout);

                if (LHCCallbacks.initTypingMonitoringUserInform) {

                	inst.typing_timeout = setTimeout(function(){
                        ee.emitEvent('visitorTypingStopped', [{'chat_id':chat_id,'hash':inst.hash}]);
                    },3000);

                    ee.emitEvent('visitorTyping', [{'chat_id':chat_id,'hash':inst.hash,'status':true,msg:$(this).val()}]);
                } else {

                    inst.is_typing = true;
	                $.postJSON(www_dir + 'chat/usertyping/' + chat_id+'/'+inst.hash+'/true',{msg:$(this).val()}, function(data){
	                   inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);},3000);
	                   
	                   if (LHCCallbacks.initTypingMonitoringUser) {
                           ee.emitEvent('initVisitorTyping', [chat_id,true]);
	                   };
	                   
	                }).fail(function(){
	                	inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);},3000);
	                });
                }
                                
            } else {
                 clearTimeout(inst.typing_timeout);
                 inst.typing_timeout = setTimeout(function(){inst.typingStoppedUser(chat_id);}, 3000);

                 var txtArea = $(this).val();
                 if (inst.currentMessageText != txtArea ) {
                	 if ( Math.abs(inst.currentMessageText.length - txtArea.length) > 6) {
                		 inst.currentMessageText = txtArea;                		 
                		 if (LHCCallbacks.initTypingMonitoringUserInform) {                         	
                                ee.emitEvent('visitorTyping', [{'chat_id':chat_id,'hash':inst.hash,'status':true,msg:$(this).val()}]);
                         } else {                		 
	                		 $.postJSON(www_dir + 'chat/usertyping/' + chat_id+'/'+inst.hash+'/true',{msg:txtArea}, function(data){
	                			 if (LHCCallbacks.initTypingMonitoringUser) {
                                        ee.emitEvent('initVisitorTyping', [chat_id,true]);
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
                ee.emitEvent('visitorTypingStopped', [{'chat_id':chat_id,'hash':this.hash,'status':false}]);
        	} else {
	            $.getJSON(this.wwwDir + 'chat/usertyping/' + chat_id+'/'+this.hash+'/false',{ }, function(data){
	                inst.is_typing = false;
	                if (LHCCallbacks.initTypingMonitoringUser) {
                        ee.emitEvent('initVisitorTyping', [chat_id,false]);
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


                            scrollHeight = messageBlock.prop("scrollHeight");

    	            		messageBlock.find('.pending-storage').remove();
                            messageBlock.append(data.result);

                            messageBlock.find('.meta-auto-hide').hide();
                            messageBlock.find('.message-row').last().find('.meta-auto-hide').show();

	  	                	if (isAtTheBottom < 20 || inst.forceBottomScroll == true) {
                                inst.forceBottomScroll = false;
	  	                		messageBlock.scrollTop(scrollHeight+2000);
	  	                	}

                			// If one the message owner is not current user play sound
                			if ( confLH.new_message_sound_user_enabled == 1 && data.uw == 'false') {
                			     inst.playNewMessageSound();
                			};

                			if (inst.last_message_id > 0) {
                				if ($('#msg-'+inst.last_message_id).attr('data-op-id') != data.msop || ($('#msg-'+inst.last_message_id+' > .usr-tit').text() !== $('#msg-'+data.message_id+' > .usr-tit').text())) {
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
		    document.location.replace(this.wwwDir + 'survey/'+fillType+'/(survey)/' + this.survey + '/(chatid)/' +this.chat_id + '/(hash)/'+ this.hash + modeWindow + operatorTyping + themeWindow + modeEmbed + explicitClose);
		    return true;
    	}

    	return false;
    };

    this.executeRemoteCommands = function(operations)
    {
    	 var inst = this;
    	 $.each(operations,function(i,item) {
			 	 if (item.indexOf('lhinst.') != -1) { // Internal operation
			 	     try {
                         eval(item);
                     } catch (e) {
			 	         console.log(e);
                     }
			 	 } else if (item.indexOf('lhc_ui_refresh') != -1) { // This can happen only if operator enables files upload. To support legacy widget.

                     var option = item.split(':')[1];

                     if (option == 1) {
                         lhinst.enableFileUpload();
                     } else {
                         lhinst.disableFileUpload();
                     }

			 	 } else if (inst.isWidgetMode) {
			 		 parent.postMessage(item, '*');
				 } else if (window.opener) {
					 window.opener.postMessage(item, '*');
				 };
		 });
    };
    
    this.disableFileUpload = function () {
         $('#fileupload').fileupload('destroy');
         $('#ChatMessageContainer .dropdown-menu .flex-row .file-uploader').remove();
    };
    
    this.syncusercall = function()
	{
	    var inst = this;
	    if (this.syncroRequestSend == false)
        {
		    clearTimeout(this.userTimeout);
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

	        	ee.emitEvent('syncUserCall', [inst,data]);

	    	}).fail(function(){
	    		inst.syncroRequestSend = false;
	    		inst.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
	    	});
	    }
	};

	this.scheduleSync = function() {
		this.syncroRequestSend = false;
        clearTimeout(this.userTimeout);
		this.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
	};

	this.closeActiveChatDialog = function(chat_id, tabs, hidetab)
	{
	    var that = this;

        ee.emitEvent('angularSyncDisabled', [true]);
	    $.postJSON(this.wwwDir + this.closechatadmin + chat_id, function (data) {
            ee.emitEvent('angularSyncDisabled', [false]);
	        if (data.error == false) {
                ee.emitEvent('angularLoadChatList');
            } else {
	            alert(data.result);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            ee.emitEvent('angularSyncDisabled', [false]);
            console.dir(jqXHR);
        });

        if ($('#CSChatMessage-'+chat_id).length != 0) {
            $('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
            $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
        };

        if (!!window.postMessage && window.opener) {
            window.opener.postMessage("lhc_ch:chatclosed:"+chat_id, '*');
        };

        that.removeSynchroChat(chat_id);

        if (hidetab == true) {

            var location = that.smartTabFocus(tabs, chat_id);

            setTimeout(function() {
                window.location.hash =  location;
            },500);

            if (that.closeWindowOnChatCloseDelete == true)
            {
                window.close();
            }
        };

        if (LHCCallbacks.chatClosedCallback) {
            LHCCallbacks.chatClosedCallback(chat_id);
        };

	};

	this.smartTabFocus = function(tabs, chat_id, params) {
		var index = tabs.find('> ul > #chat-tab-li-'+chat_id).index();

		if (!params) {params = {};}

		var navigationDirection = (params.up == true || typeof params.up == 'undefined') ? 1 : -1;

		if (!params['keep']) {
            tabs.find('> ul > #chat-tab-li-'+chat_id).remove();
            tabs.find('#chat-id-'+chat_id).remove();
        } else {
            tabs.find('> ul > li > a.active').removeClass('active');
        }

    	var linkTab = tabs.find('> ul > li:eq('+ (index - navigationDirection)+')');

    	if (linkTab.attr('id') !== undefined){
    		var link = linkTab.find('> a');
    	} else {
    		linkTabRight = tabs.find('> ul > li:eq('+ (index)+')');
    		if (linkTabRight.length > 0) {
    			var link = linkTabRight.find('> a');
    		} else {
    			var link = linkTab.find('> a');
    		}
    	}

    	if (!tabs.find('> ul > li > a.active').length) {
    		link.tab('show');

    		if (link.attr('id') !== undefined) {
        		var new_chat_id = link.attr('href').replace('#chat-id-','');
        		this.removeBackgroundChat(new_chat_id);
        		this.hideNotification(new_chat_id);
        		if (!params['keep']) {
                    ee.emitEvent('chatTabFocused', [new_chat_id]);
                }

        	}
    	}

    	if (link.attr('href') !== undefined) {
            return link.attr('href').replace('#','#/');
        } else {
    	    return '#';
        }
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

            var that = this;

            $.postJSON(this.wwwDir + this.deletechatadmin + chat_id, function(data){
                if (data.error == true) {
                    alert(data.result);
                } else {

                    if ($('#CSChatMessage-'+chat_id).length != 0){
                        $('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
                        $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
                    }

                    that.removeSynchroChat(chat_id);

                    if (hidetab == true) {

                        var location = that.smartTabFocus(tabs, chat_id);

                        setTimeout(function() {
                            window.location.hash = location;
                        },500);

                        if (that.closeWindowOnChatCloseDelete == true)
                        {
                            window.close();
                        }
                    };

                    if (LHCCallbacks.chatDeletedCallback) {
                        LHCCallbacks.chatDeletedCallback(chat_id);
                    };

                    that.syncadmininterfacestatic();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.dir(jqXHR);
                alert('getJSON request failed! ' + textStatus + ':' + errorThrown + ':' + jqXHR.responseText);
            });
        }
	};

	this.rejectPendingChat = function(chat_id, tabs)
	{
	    var that = this;
	    $.postJSON(this.wwwDir + this.deletechatadmin + chat_id ,{}, function(data){
            that.syncadmininterfacestatic();
	    }).fail(function(jqXHR, textStatus, errorThrown) {
            console.dir(jqXHR);
            alert('getJSON request failed! ' + textStatus + ':' + errorThrown + ':' + jqXHR.responseText);
        });
	};

	this.startChatNewWindow = function(chat_id,name)
	{
	    var popupWindow = window.open(this.wwwDir + 'chat/single/'+chat_id,'chatwindow-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650");

	    if (popupWindow !== null) {
            popupWindow.focus();
            var inst = this;
            setTimeout(function(){
                inst.syncadmininterfacestatic();
            },1000);

            ee.emitEvent('chatStartOpenWindow', [chat_id]);
        }

	};

    this.startChatNewWindowArchive = function(archive_id, chat_id,name)
    {
        var popupWindow = window.open(this.wwwDir + 'chatarchive/viewarchivedchat/' + archive_id + '/' + chat_id + '/(mode)/popup','chatwindow-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650");
        if (popupWindow !== null) {
            popupWindow.focus();
            ee.emitEvent('chatStartOpenWindowArchive', [archive_id, chat_id]);
        }
    };

	this.startCoBrowse = function(chat_id)
	{
        popupWindow = window.open(this.wwwDir + 'cobrowse/browse/'+chat_id,'chatwindow-cobrowse-chat-id-'+chat_id,"menubar=1,resizable=1,width=800,height=650");

		if (popupWindow !== null) {
            popupWindow.focus();
        }

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

	    	if ($('#tabs').length > 0) {
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

	this.switchLang = function(form,lang){
		var languageAppend = '<input type="hidden" value="'+lang+'" name="switchLang" />';
		form.append(languageAppend);
		form.submit();

		return false;
	};

	this.sendLinkToMail = function( embed_code,file_id) {
		var val = window.parent.$('#MailMessage').val();
		window.parent.$('#MailMessage').val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('btn-success');
	};

	this.sendLinkToEditor = function(chat_id, embed_code,file_id) {
		var val = window.parent.$('#CSChatMessage-'+chat_id).val();
		window.parent.$('#CSChatMessage-'+chat_id).val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('btn-success');
	};

	this.sendLinkToGeneralEditor = function(embed_code,file_id) {
	    var editor = window.parent.$('.embed-into');
		var val = editor.val();
        editor.val(((val != '') ? val+"\n" : val)+embed_code);
		$('#embed-button-'+file_id).addClass('btn-success');
	};

	this.hideTransferModal = function(chat_id)
	{
		var inst = this;

        setTimeout(function(){
            $('#myModal').modal('hide');
            if ($('#tabs').length > 0) {
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

	this.changeOwner = function(chat_id) {
        var inst = this;
        var user_id = $('#id_new_user_id').val();
        $.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id, {'type':'change_owner'}, function(data){
            if (data.error == 'false') {
                $('#transfer-block-'+data.chat_id).html(data.result);
                inst.hideTransferModal(chat_id);
            };
        });
    };

	this.changeDep = function(chat_id) {
        var inst = this;
        var user_id = $('#id_new_dep_id').val();
        $.postJSON(this.wwwDir + this.trasnsferuser + chat_id + '/' + user_id, {'type':'change_dep'}, function(data){
            if (data.error == 'false') {
                $('#transfer-block-'+data.chat_id).html(data.result);
                $('#myModal').modal('hide');
                inst.updateVoteStatus(chat_id);
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
	        cache: false
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

        ee.emitEvent('endedChat', []);

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

	this.checkChatStatusTimeout = null;

	this.chatStatus = null;

	this.chatsyncuserpending = function ()
	{
		var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';
		var themeWindow = this.theme !== null ? '/(theme)/'+this.theme : '';

		clearTimeout(this.checkChatStatusTimeout);

		var inst = this;
	    $.getJSON(this.wwwDir + this.checkchatstatus + this.chat_id + '/' + this.hash + modeWindow + themeWindow,{}, function(data) {

            ee.emitEvent('checkChatStatus', [inst.chat_id, data]);

            inst.chatStatus = data.status; 

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
	            	   document.location.replace(data.ru);
	               }

                    inst.checkChatStatusTimeout = setTimeout(chatsyncuserpending,confLH.chat_message_sinterval);

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

	    if (Modernizr.audio && this.audio !== null) {
    	    this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.ogg?v=3' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.mp3?v=3' : WWW_DIR_JAVASCRIPT_FILES + '/new_message.wav?v=3';
    	    this.audio.load();
	    };

	    if(!$("textarea[name=ChatMessage]").is(":focus")) {
	    	this.startBlinking();
    	};
	};

	this.playInvitationSound = function() {
		if (Modernizr.audio && this.audio !== null) {
    	    this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/invitation.ogg' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/invitation.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/invitation.wav';
    	    this.audio.load();
	    }
	};

	this.playPreloadSound = function() {
		if (Modernizr.audio && this.audio !== null) {
			this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/silence.ogg' :
				Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/silence.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/silence.wav';
            this.audio.load();
	    }
	};

	this.loadPreviousMessages = function (inst) {
        $.getJSON(this.wwwDir + 'chat/loadpreviousmessages/' + inst.attr('chat-id') + '/' + inst.attr('message-id') + '/(initial)/' + inst.attr('data-initial'), function(data) {
            if (data.error == false) {

                inst.attr('data-initial',0);

                var msg = $('#messagesBlock-'+inst.attr('chat-original-id'));
                msg.prepend(data.result);

                if (inst.attr('auto-scroll') == 1) {
                    inst.attr('auto-scroll',0);
                    msg.scrollTop(msg.prop('scrollHeight'));
                }

                if (data.has_messages == true) {
                    inst.attr('message-id', data.message_id);
                    inst.attr('chat-id',data.chat_id);
                } else {
                    inst.remove();
                }
            }
        });
    };

	this.hidenicknamesstatus = null;

	this.onScrollAdmin = function(chat_id)
    {
        var messageBlock = $('#messagesBlock-'+chat_id);
        var scrollHeight = messageBlock.prop("scrollHeight");
        var isAtTheBottom = Math.abs((scrollHeight - messageBlock.prop("scrollTop")) - messageBlock.prop("clientHeight"));

        if (isAtTheBottom > 20) {
            $('#scroll-button-admin-'+chat_id).removeClass('d-none');
        } else {
            $('#scroll-button-admin-'+chat_id).addClass('d-none').find('> button').text($('#scroll-button-admin-'+chat_id+' > button').attr('data-default'));
        }
    }

    this.scrollToTheBottomMessage = function(chat_id)
    {
        var unreadSeparator = $('#unread-separator-'+chat_id);
        if (unreadSeparator.length > 0) {
            unreadSeparator[0].scrollIntoView();
            setTimeout(function(){
                unreadSeparator.remove();
            },1000);
        } else {
            var messagesBlock = $('#messagesBlock-'+chat_id);
            messagesBlock.scrollTop(messagesBlock.prop('scrollHeight'));
        }
    }

    this.syncadmincall = function()
	{
	    if (this.chatsSynchronising.length > 0)
	    {
	        if (this.underMessageAdd == false && this.syncroRequestSend == false)
	        {
	            this.syncroRequestSend = true;

        	    $.postJSON(this.wwwDir + this.syncadmin ,{ 'chats[]': this.chatsSynchronisingMsg }, function(data){

                    if (typeof data.error_url !== 'undefined') {
                        document.location.replace(data.error_url);
                    }

        	    	try {
	        	        // If no error
	        	        if (data.error == 'false')
	        	        {
	        	            if (data.result != 'false')
	        	            {
	        	            	var playSound = false

	        	                $.each(data.result,function(i,item) {

	        	                	  var messageBlock = $('#messagesBlock-'+item.chat_id);
	        	                	  var scrollHeight = messageBlock.prop("scrollHeight");
	        	                	  var isAtTheBottom = Math.abs((scrollHeight - messageBlock.prop("scrollTop")) - messageBlock.prop("clientHeight"));

	        	                	  messageBlock.find('.pending-storage').slice(0, item.mn).remove();


                                    var mainElement = $('#chat-tab-link-'+item.chat_id);

                                    var needUnreadSeparator = !focused;

                                    if (!mainElement.hasClass('active')) {
                                        if (mainElement.find('span.msg-nm').length > 0) {
                                            var totalMsg = (parseInt(mainElement.find('span.msg-nm').attr('rel')) + item.mn);
                                            mainElement.find('span.msg-nm').html(' (' + totalMsg + ')' ).attr('rel',totalMsg);
                                        } else {
                                            needUnreadSeparator = true;
                                            mainElement.append('<span rel="'+item.mn+'" class="msg-nm"> ('+item.mn+')</span>');
                                            mainElement.addClass('has-pm');
                                        }
                                    }

                                    if (isAtTheBottom > 20) {
                                        needUnreadSeparator = true;
                                        $('#scroll-button-admin-'+item.chat_id+' > button').text($('#scroll-button-admin-'+item.chat_id+' > button').attr('data-new'));
                                    }

                                    if (needUnreadSeparator == true && document.getElementById('unread-separator-'+item.chat_id) === null) {
                                        item.content = item.content.replace('<span class="usr-tit','<div id="unread-separator-'+item.chat_id+'" class="new-msg-holder border-bottom border-danger text-center"><span class="new-msg bg-danger text-white d-inline-block fs12 rounded-top">'+confLH.transLation.new+'</span></div><span class="usr-tit');
                                    }

                                    messageBlock.append(item.content);
                                    messageBlock.find('.pending-storage').appendTo(messageBlock);

	        	                	  lhinst.addQuateHandler(item.chat_id);

	        	                	  if (isAtTheBottom < 20) {
	        	                		  messageBlock.scrollTop(scrollHeight);
	        	                	  }

	        		                  lhinst.updateChatLastMessageID(item.chat_id,item.message_id);



	        		                  if (playSound == false && data.uw == 'false' && (typeof item.ignore === 'undefined' || typeof item.ignore === false))
                                      {
                                          playSound = true;
                                      }

	        		                  if ( confLH.new_message_browser_notification == 1 && data.uw == 'false' && (typeof item.ignore === 'undefined' || typeof item.ignore === false)) {
	        		                	  lhinst.showNewMessageNotification(item.chat_id,item.msg,item.nck);
	  	                			  };

	  	                			  if (item.msfrom > 0) {
	  	                				if ($('#msg-'+item.msfrom).attr('data-op-id') != item.msop) {
	  	                					$('#msg-'+item.msfrom).next().addClass('operator-changes');
	  	                				}
	  	                			  }

	  	                			  ee.emitEvent('eventSyncAdmin', [item,i]);
	                            });

	                            if ( confLH.new_message_sound_admin_enabled == 1  && data.uw == 'false' && playSound == true) {
	                            	lhinst.playNewMessageSound();
	                            };

	        	            };

	        	            if (data.result_status != 'false')
	        	            {
	        	            	var groupTabs = $('#group-chats-status').hasClass('chat-active');

	        	                $.each(data.result_status,function(i,item) {

	        	                      var typingIndicator = $('#user-is-typing-'+item.chat_id);

	        	                      if (item.tp == 'true') {
                                          if (lhinst.nodeJsMode == false) {
                                                typingIndicator.html(item.tx);
                                          }
	        	                          if (typingIndicator.css('visibility') == 'hidden') {
                                              typingIndicator.css('visibility','visible');
                                          }
	        	                      } else {
                                          if (lhinst.nodeJsMode == false) {
                                              typingIndicator.css('visibility','hidden');
                                          }
	        	                      };

                                      $('#last-msg-chat-'+item.chat_id).text(item.lmsg);

	        	                      var userChatStatus = $('#user-chat-status-'+item.chat_id);

	        	                      var wasOnline = userChatStatus.hasClass('icon-user-online');

	        	                      $('#chat-duration-'+item.chat_id).text(item.cdur);

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

	        	                      if (statusel.attr('data-chat-status') != item.cs || statusel.attr('data-chat-user') != item.co)
                                      {
                                          lhinst.updateVoteStatus(item.chat_id);
                                      }

	        	                      if (item.um == 1) {
	        	                    	  statusel.addClass('chat-unread');
	        	                    	  $('#msg-send-status-'+item.chat_id).addClass('icon-user-offline');
	  	                			  } else {
	  	                				  $('#msg-send-status-'+item.chat_id).addClass('icon-user-online');
	  	                				  statusel.removeClass('chat-unread');
	  	                			  }

	        	                      if (item.lp !== false) {
	        	                    	  statusel.attr('title',item.lp+' s.');
	        	                      } else {
	        	                    	  statusel.attr('title','');
	        	                      }
	        	                      if (typeof item.oad != 'undefined' && item.oad == 1) {
                                          $('#lhc_sync_operation').remove();
                                          var th = document.getElementsByTagName('head')[0];
                                          var s = document.createElement('script');
                                          s.setAttribute('id','lhc_sync_operation');
                                          s.setAttribute('type','text/javascript');
                                          s.setAttribute('src',WWW_DIR_JAVASCRIPT + 'chat/loadoperatorjs/(type)/chat/(id)/'+item.chat_id);
                                          th.appendChild(s);
	        	                      };
	                            });
	        	            };

	        	            if (data.cg) {
                                $.each(data.cg,function(i,item) {
                                    return lhinst.removeDialogTab(item,$('#tabs'),true);
                                });
                            }

                            lhinst.hidenicknamesstatus = groupTabs;

                            clearTimeout(lhinst.userTimeout);
	        	            lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);

                            ee.emitEvent('chatAdminSync', [data]);

	        	        };
        	    	} catch (err) {
                        clearTimeout(lhinst.userTimeout);
        	    		lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
					};

        	        //Allow another request to send check for messages
        	        lhinst.setSynchronizationRequestSend(false);

        	        if (LHCCallbacks.syncadmincall) {
    	        		LHCCallbacks.syncadmincall(lhinst,data);
    	        	};


            	}).fail(function(){
                    clearTimeout(lhinst.userTimeout);
            		lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
            		lhinst.setSynchronizationRequestSend(false);
            	});
	        } else {
                clearTimeout(lhinst.userTimeout);
	        	lhinst.userTimeout = setTimeout(chatsyncadmin,confLH.chat_message_sinterval);
	        }

	    } else {
	        this.isSinchronizing = false;
	    }
	};

	this.updateVoteStatus = function(chat_id) {
		$.getJSON(this.wwwDir + 'chat/updatechatstatus/'+chat_id ,{ }, function(data){
			$('#main-user-info-tab-'+chat_id).html(data.result);

            $('#messagesBlock-'+chat_id+' span.vis-tit').each(function(i) {
                var cache = $(this).children();
                $(this).text(' '+data.nick).prepend(cache);
            });

            $('#ntab-chat-'+chat_id).text(data.nick);

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

	this.playNewChatAudio = function(sound) {
		clearTimeout(this.soundIsPlaying);
		this.soundPlayedTimes++;
		if (Modernizr.audio && this.audio !== null) {

			this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/'+sound+'.ogg?v=4' :
                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/'+sound+'.mp3?v=4' : WWW_DIR_JAVASCRIPT_FILES + '/'+sound+'.wav?v=4';
			this.audio.load();

            if (confLH.repeat_sound > this.soundPlayedTimes) {
            	var inst = this;
            	this.soundIsPlaying = setTimeout(function(){inst.playNewChatAudio(sound);},confLH.repeat_sound_delay*1000);
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

		// If it's customer chat make sure sync is running.
		if (parseInt(this.chat_id) > 0) {
            this.scheduleSync();
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

		if (confLH.new_chat_sound_enabled == 1 && (confLH.sn_off == 1 || $('#online-offline-user').text() == 'flash_on') && (identifier == 'active_chats' || identifier == 'bot_chats' || identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered')) {
	    	this.soundPlayedTimes = 0;
	        this.playNewChatAudio(identifier == 'active_chats' ? 'alert' : 'new_chat');
	    };



	    if(!$("textarea[name=ChatMessage]").is(":focus") && (confLH.sn_off == 1 || $('#online-offline-user').text() == 'flash_on') && (identifier == 'active_chats' || identifier == 'bot_chats' || identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered')) {
	    	this.startBlinking();
    	};

	    var inst = this;

	    if ( (identifier == 'active_chats' || identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'bot_chats' || identifier == 'pending_transfered') && (confLH.sn_off == 1 || $('#online-offline-user').text() == 'flash_on') && window.Notification && window.Notification.permission == 'granted') {

			var notification = new Notification(nick, { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: message, requireInteraction : true });

			notification.onclick = function () {

    	    	if (identifier == 'active_chats' || identifier == 'pending_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered' || identifier == 'bot_chats') {
    	    		if ($('#tabs').length > 0) {
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

	    if (identifier == 'transfer_chat' && confLH.accept_chats) {
            inst.startChatNewWindowTransferByTransfer(chat_id, nt);
        } else if (identifier == 'transfer_chat' && confLH.show_alert_transfer == 1) {
            if (confirm(confLH.transLation.transfered + "\n\n" + message)) {
                inst.startChatNewWindowTransferByTransfer(chat_id, nt);
			}
        }

	    if (confLH.show_alert == 1) {
    		if (confirm(confLH.transLation.new_chat+"\n\n"+message)) {
    			if (identifier == 'pending_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered' || identifier == 'bot_chats') {
    	    		if ($('#tabs').length > 0) {
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
            ee.emitEvent('angularLoadChatList');
		} catch(err) {
        	//
        };
	};

	this.addingUserMessage = false;
	this.addUserMessageQueue = [];
	this.addDelayedTimeout = null;

	this.addmsgadmin = function (chat_id, message)
	{
        $('#unread-separator-'+chat_id).remove();

		var textArea = $("#CSChatMessage-"+chat_id);

		if (textArea.is("[readonly]")) {
			return;
		}

		var pdata = {
				msg	: message || textArea.val()
		};

		if (textArea.attr('meta-msg')) {
            pdata.meta_msg = textArea.attr('meta-msg');
            textArea.removeAttr('meta-msg');
        }

		if (pdata.msg == '') {
		    return;
        }

		if (this.speechHandler !== false) {
			this.speechHandler.messageSend();
		};

        message || textArea.val('');

		var placeholerOriginal = textArea.attr('placeholder');

        textArea.attr('placeholder',confLH.transLation.sending || 'Sending...');

		if (textArea.hasClass('edit-mode')) {

			pdata.msgid = textArea.attr('data-msgid');

			$.postJSON(this.wwwDir + 'chat/updatemsg/' + chat_id, pdata , function(data){

			    textArea.attr('placeholder',placeholerOriginal);

				if (data.error == 'f') {
					textArea.removeClass('edit-mode');
					textArea.removeAttr('data-msgid');
					$('#msg-'+pdata.msgid).replaceWith(data.msg);

					if (LHCCallbacks.addmsgadmin) {
		        		LHCCallbacks.addmsgadmin(chat_id);
		        	};

		        	ee.emitEvent('chatAddMsgAdmin', [chat_id]);

                    lhinst.addQuateHandler(chat_id);

					return true;
				}
			});

		} else {

			var inst = this;

			var messagesBlock = $('#messagesBlock-'+chat_id);

            message || messagesBlock.append("<div class=\"message-row message-admin pending-storage\"><div class=\"msg-body\"><span class=\"material-icons lhc-spin\">autorenew</span>" + $("<div>").text(pdata.msg).html() + "</div></div>");

			messagesBlock.scrollTop(messagesBlock.prop('scrollHeight'));

			if (this.addingUserMessage == false)
			{
				this.addingUserMessage = true;


				var hasSubjects = false;
                if (textArea.attr('subjects_ids')) {
                    pdata.subjects_ids = textArea.attr('subjects_ids');
                    textArea.removeAttr('subjects_ids');
                    hasSubjects = true;
                }

				$.postJSON(this.wwwDir + this.addmsgurl + chat_id, pdata , function(data) {
                    textArea.removeAttr('readonly').attr('placeholder',placeholerOriginal);

                    if (data.error == 'false') {
                        if (LHCCallbacks.addmsgadmin) {
                            LHCCallbacks.addmsgadmin(chat_id);
                        };

                        ee.emitEvent('chatAddMsgAdmin', [chat_id]);

                        if (data.r != '') {
                            $('#messagesBlock-'+chat_id).append(data.r).scrollTop($("#messagesBlock-"+chat_id).prop("scrollHeight"));
                        };

                        if (data.hold_removed === true) {
                            $('#hold-action-'+chat_id).removeClass('btn-outline-info');
                        } else if (data.hold_added === true) {
                            $('#hold-action-'+chat_id).addClass('btn-outline-info');
                        }

                        if (hasSubjects == true){
                            inst.updateVoteStatus(chat_id);
                        }

                        lhinst.syncadmincall();
                    } else {
                        if (typeof data.token !== 'undefined') {
                            confLH.csrf_token = data.token;
                        }
                        textArea.attr('placeholder',placeholerOriginal).val((textArea.val() + ' ' + pdata.msg).trim());
                        $('.pending-storage').first().remove();
                        var escaped = '<div style="margin:10px 10px 30px 10px;" class="alert alert-warning" role="alert">' + $("<div>").text(data.r).html() + '</div>';
                        $('#messagesBlock-'+chat_id).append(escaped).scrollTop($("#messagesBlock-"+chat_id).prop("scrollHeight"));
                    }

					inst.addingUserMessage = false;

                    if (inst.addUserMessageQueue.length > 0) {
                        var elementAdd = inst.addUserMessageQueue.shift()
                        inst.addmsgadmin(elementAdd.chat_id,elementAdd.msg);
                    }

					return true;
				}).fail(function(respose) {
                    textArea.attr('placeholder',placeholerOriginal).val(textArea.val() + ' ' + pdata.msg);
                    var escaped = '<div style="margin:10px 10px 30px 10px;" class="alert alert-warning" role="alert">' + $("<div>").text('You have weak internet connection or the server has problems. Try to refresh the page or send the message again.' + (typeof respose.status !== 'undefined' ? ' Error code ['+respose.status+']' : '') + (typeof respose.responseText !== 'undefined' ? respose.responseText : '')).html() + '</div>';
                    $('#messagesBlock-'+chat_id).append(escaped).scrollTop($("#messagesBlock-"+chat_id).prop("scrollHeight"));
                    $('.pending-storage').first().remove();
                    inst.addingUserMessage = false;
                    if (inst.addUserMessageQueue.length > 0) {
                        var elementAdd = inst.addUserMessageQueue.shift()
                        inst.addmsgadmin(elementAdd.chat_id,elementAdd.msg);
                    }
		    	});

			} else {
                textArea.attr('placeholder', placeholerOriginal);
                this.addUserMessageQueue.push({'chat_id':chat_id,'msg':pdata.msg});
			}
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

    this.getInputSelection = function(elem) {
        if (typeof elem != "undefined") {
            s = elem[0].selectionStart;
            e = elem[0].selectionEnd;
            return elem.val().substring(s, e);
        } else {
            return '';
        }
    }

    this.handleBBCode = function(inst) {
        var str = $(inst.attr('data-selector')).val();
        var elem = $(inst.attr('data-selector'));

        if (typeof elem != "undefined") {
            var s = elem[0].selectionStart, e = elem[0].selectionEnd;
            var selection = str.substring(s, e);
        } else {
            var selection = '';
        }

        var bbcodeend = typeof inst.attr("data-bbcode-end") !== 'undefined' ?  inst.attr("data-bbcode-end") : inst.attr("data-bbcode");

        if (selection.length > 0) {
            $(inst.attr('data-selector')).val(str.substr(0,s) + "[" + inst.attr("data-bbcode") + "]" + selection + "[/" + bbcodeend + "]" + str.substring(e));
        } else {
            $(inst.attr('data-selector')).val(str + "[" + inst.attr("data-bbcode") + "]" + "[/" + bbcodeend + "]");
        }

        return false;
    }

	this.addAdminChatFinished = function(chat_id, last_message_id, arg) {

		var _that = this;

		var $textarea = jQuery('#CSChatMessage-'+chat_id);

		var cannedMessageSuggest = new LHCCannedMessageAutoSuggest({'chat_id': chat_id,'uppercase_enabled': confLH.auto_uppercase});

		var colorPickerDom = document.getElementById('color-picker-chat-' + chat_id);

		if (colorPickerDom !== null) {
            var colorP = new ColorPicker({
                dom: document.getElementById('color-picker-chat-' + chat_id),
                value: '#0F0'
            });

            colorP.addEventListener('change', function (colorItem) {
                $('#color-apply-'+chat_id).attr('data-bbcode','color='+colorP.getValue('hex'));
            });

            $('.downdown-menu-color-'+chat_id).on('click', function (e) {
                if ($(this).parent().is(".show")) {
                    var target = $(e.target);
                    if (target.hasClass("keepopen") || target.parents(".keepopen").length){
                        return false;
                    } else {
                        return true;
                    }
                }
            });

            $('.downdown-menu-color-'+chat_id+' .color-item').on('click',function () {
                colorP.setValue($(this).attr('data-color'));
            });
        }

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

            if ($textarea.val() == '') {
                $textarea.removeAttr('subjects_ids');
            }

            if ($textarea.val() == '' && evt.altKey && (evt.which == 38 || evt.which == 40)) {
                if (confLH.new_dashboard == true) {
                    ee.emitEvent('activateNextTab',[chat_id,(evt.which == 38 ? true : false)]);
                } else {
                    if (evt.which == 38) {
                        var tab = lhinst.smartTabFocus($('#tabs'),chat_id,{keep:true,up:true});
                    } else {
                        var tab = lhinst.smartTabFocus($('#tabs'),chat_id,{keep:true,up:false});
                    }
                    var parts = tab.split('chat-id-');
                    if (parts[1] && !isNaN(parts[1])) {
                        $('#chat-tab-link-'+parts[1]).click();
                    }
                }

                return ;
            }

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

		$messageBlock.css('height',this.getLocalValue('lhc_mheight',confLH.defaultm_hegiht));

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

        $messageBlock.prop('scrollTop',$messageBlock.prop('scrollHeight'));

		// Start synchronisation
		this.startSyncAdmin();

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

        $('#chat-write-button-'+chat_id).click(function() {
            $('#CSChatMessage-'+chat_id).show().focus();
            $(this).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
            $('#chat-preview-button-'+chat_id).removeClass('btn-outline-primary').addClass('btn-outline-secondary');
            $('#chat-preview-container-'+chat_id).hide();
        });

        $('#chat-preview-button-'+chat_id).click(function(){
            $('#chat-preview-container-'+chat_id).html('...').show();
            $('#CSChatMessage-'+chat_id).hide();
            $(this).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
            $('#chat-write-button-'+chat_id).removeClass('btn-outline-primary').addClass('btn-outline-secondary');
            jQuery.post(WWW_DIR_JAVASCRIPT +'chat/previewmessage', {msg_body: true, 'msg' : $('#CSChatMessage-'+chat_id).val()}, function(data){
                $('#chat-preview-container-'+chat_id).html(data);
            });
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

	this.executeExtension = function (extension, params) {
        if (document.getElementById('ext-' + extension) === null) {
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            var date = new Date();
            s.setAttribute('type','text/javascript');
            s.setAttribute('src', WWW_DIR_LHC_WEBPACK_ADMIN.replace('/design/defaulttheme/js/admin/dist/','') + '/extension/' + extension + '/design/' + extension + 'theme/js/'  + extension + '.legacy.js?v=' + ("" + date.getFullYear() + date.getMonth() + date.getDate()) );
            s.setAttribute('id','ext-' + extension);
            th.appendChild(s);
            s.onreadystatechange = s.onload = function() {
                ee.emitEvent(extension + '.init', [params]);
            };
        } else {
            ee.emitEvent(extension + '.init', [params]);
        }
    }

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
    			$('#msg-'+msgid).addClass('bg-success');
    			setTimeout(function(){
    				$('#msg-'+msgid).removeClass('bg-success');
    			},2000);
    		}
    	});
    };

    this.updateMessageRowAdmin = function(chat_id, msgid){
    	$.getJSON(this.wwwDir + 'chat/getmessageadmin/' + chat_id + '/' + msgid, function(data) {
    		if (data.error == 'f') {

                var messagesBlock = $('#messagesBlock-' + chat_id);
                var needScroll = (messagesBlock.prop('scrollTop') + messagesBlock.height() + 30) > messagesBlock.prop('scrollHeight');

    			$('#msg-'+msgid).replaceWith(data.msg);
                lhinst.addQuateHandler(chat_id);
    			$('#msg-'+msgid).addClass('bg-success');
    			setTimeout(function(){
    				$('#msg-'+msgid).removeClass('bg-success');
    			},2000);
                needScroll && messagesBlock.scrollTop(messagesBlock.prop('scrollHeight'));
    		}
		});
    };

    this.addmsguser = function (focusArea)
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

		if ($('#lhc-mic-icon').length > 0){
		    $('#lhc-send-icon').hide();
		    $('#lhc-mic-icon').show();
		    $('#voice-control-message').hide();
        }

        try {
            ee.emitEvent('messageSend', [{'chat_id':this.chat_id, 'hash': this.hash, msg: pdata.msg}]);
            if (sessionStorage) {
                sessionStorage.setItem('lhc_ttxt','');
            };
		} catch(e) {}

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

            messagesBlock.append("<div class=\"message-row response pending-storage\"><div class=\"msg-body\">" + $("<div>").text(pdata.msg).html() + "</div></div>");
			messagesBlock.scrollTop(messagesBlock.prop('scrollHeight'));

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
                        $('.pending-storage').remove();
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
				}).fail(function(respose) {
                    $('#CSChatMessage').val(textArea.val() + ' ' + pdata.msg);
                    var instStatus = $('#id-operator-typing');
                    instStatus.html('You have weak internet connection or the server has problems. Try to send the message again.');
                    instStatus.css('visibility','visible');
                    setTimeout(function(){
                        if (inst.operatorTyping == false) {
                            $('#id-operator-typing').html('').css('visibility','hidden');
                        }
                    },5000);
                    $('.pending-storage').remove();
                    inst.addingUserMessage = false;
                    if (inst.addUserMessageQueue.length > 0) {
                        inst.addDelayedMessage();
                    }
                });
	        } else {
	        	this.addUserMessageQueue.push({'retries':0, 'pdata':pdata,'url':this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow});
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
    	    this.addUserMessageQueue.push({'retries':0,'pdata':{msg : messages[i]},'url':this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow});
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

				}).fail(function () {
                    inst.addingUserMessage = false;
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
        if (inst.prop('tagName') != 'I') {
            inst = inst.find('> i.material-icons');
        }

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
        if (inst.prop('tagName') != 'I') {
            inst = inst.find('> i.material-icons');
        }

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
    	$.get(this.wwwDir+  'user/setsettingajax/'+attr+'/'+encodeURIComponent(value)+'/(indifferent)/true');
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
	   			if (data.is_owner === true) {
                    $('#CSChatMessage-'+chat_id).attr('placeholder','');
                    $('#CSChatMessage-'+chat_id).focus();
                }
	   		 } else {
	   			 alert(data.result);
	   		 }
	   	 });
    	return false;
    };

    this.submitModalForm = function(form){
    	var inst = this;
    	$.post(form.attr('action'),form.serialize(), function(data) {
    	    var styleOriginal = $('#myModal > .modal-dialog')[0].style.cssText;
	   		$('#myModal').html(data);
            $('#myModal > .modal-dialog')[0].style.cssText = styleOriginal;
	   	 });
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

    	if (inst.find('.form-protected').length == 0) {

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
    	            	$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
    	            	this.pendingMessagesToStore.push($('#id_Question').val());
        	  			$('#id_Question').val('');
    		  		}

    			} else {
    				// That means it's second submit, and that means user pressed enter
    				if ($('#messagesBlock').length > 0) {
    	            	jQuery('<div/>', {
    	    			    'class': 'message-row response',
    	    			    text: $('#id_Question').val()
    	    			}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+visitorTitle+'</span>');
    	            	$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
    				};
    	  			this.pendingMessagesToStore.push($('#id_Question').val());
    	  			$('#id_Question').val('');
    			}

		    	return false;
	  	} else {

	  		// Avoid users stupidity if they enable it but form has extra field
	  		if (inst.find('#hasFormExtraField').length == 1) {
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
                    try {
                        if (sessionStorage) {
		  					sessionStorage.setItem('lhc_ttxt',valueQuestion);
                        };
                    } catch(e) {}

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
	  			if ($('#messagesBlock').length > 0) {
	            	jQuery('<div/>', {
	    			    'class': 'message-row response',
	    			    text: $('#id_Question').val()
	    			}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+visitorTitle+'</span>');
	            	$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
				};
	  			this.pendingMessagesToStore.push($('#id_Question').val());
	  			$('#id_Question').val('');
	  		}
	  	}

	  	return false;
    };

    this.addCaptcha = function(timestamp,inst) {
    	if (inst.find('.form-protected').length == 0){
    			 inst.find('input[type="submit"]').attr('disabled','disabled');
		    	 $.getJSON(this.wwwDir + 'captcha/captchastring/form/'+timestamp, function(data) {
		    		 inst.append('<input type="hidden" value="'+timestamp+'" name="captcha_'+data.result+'" /><input type="hidden" value="'+timestamp+'" name="tscaptcha" /><input type="hidden" class="form-protected" value="1" />');
		    		 inst.submit();
		    	 });
		    	 return false;
	   	};

	   	return true;
    };

    this.setSubject = function(inst, chat_id) {
        $('#subject-message-'+chat_id).text('...');
        $.postJSON(this.wwwDir + 'chat/subject/'+chat_id + '/(subject)/' + inst.val() + '/(status)/' + inst.is(':checked'),{'update': true}, function(data) {
            lhinst.updateVoteStatus(chat_id);
            $('#subject-message-'+chat_id).text(data.message);
        });
    }

    this.addCaptchaSubmit = function(timestamp,inst) {
        if (inst.find('.form-protected').length == 0) {
            inst.find('input[type="submit"]').attr('disabled','disabled');
            inst.find('#ChatSendButtonContainer').remove();
            inst.find('#id_Question').attr('readonly','readonly');

            if (typeof formSubmitted !== 'undefined') {
                formSubmitted = true;
            }

            $.getJSON(this.wwwDir + 'captcha/captchastring/form/'+timestamp, function(data) {
                inst.append('<input type="hidden" value="'+timestamp+'" name="captcha_'+data.result+'" /><input type="hidden" value="'+timestamp+'" name="tscaptcha" /><input type="hidden" class="form-protected" value="1" />');

                if ( !! window.FormData) {
                    try {
                        var formData = new FormData(inst[0]);
                        var xhr = new XMLHttpRequest();
                        xhr.addEventListener('readystatechange', function (evt) {
                            var status, text, readyState;
                            try {
                                readyState = evt.target.readyState;
                                text = evt.target.responseText;
                                status = evt.target.status;
                            }
                            catch(e) {
                                 return;
                            }
                            if (readyState == 4 && status == '200' && evt.target.responseText) {
                                var headers = xhr.getResponseHeader("Content-Type");
                                if (headers.indexOf('application/json') == -1) {
                                    $('#widget-content-body').html(evt.target.responseText);
                                } else {
                                    location.replace(jQuery.parseJSON(evt.target.responseText)['location']);
                                }
                            }
                        }, false);
                        var action = inst.attr('action');
                        if (action != '') {
                            xhr.open('POST', action + '/(ajaxmode)/true', true);
                        } else {
                            xhr.open('POST', document.location + '&ajaxmode=true', true);
                        }
                        xhr.send(formData);
                    } catch(e) {
                        return false;
                    }

                } else {
                    inst.submit();
                }
            });
            return false;
        };

        return false;
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

    this.chooseFile = function () {
        if (document.getElementById('fileupload')) {
            document.getElementById('fileupload').click();
        }
    }

    this.enableFileUpload = function () {
        $.getJSON(this.wwwDir + 'file/fileoptions/' + this.chat_id + '/' + this.hash, function(data){
            $('#ChatMessageContainer .dropdown-menu .flex-row').prepend(data.html);
            data.options.ft_us = new RegExp('(\.|\/)(' +data.options.ft_us + ')$','i');
            lhinst.addFileUserUpload(data.options);
        });
    }

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
                    var txtArea = $('#CSChatMessage-'+data_config.chat_id);
                    var txtValue = jQuery.trim(txtArea.val());
                    txtArea.val(txtValue + (txtValue != '' ? "\n" : "") + response.result.msg + "\n");
				}

				if (LHCCallbacks.addFileUpload) {
    				LHCCallbacks.addFileUpload(data_config.chat_id);
    			}
    		},
    		dropZone: $('#CSChatMessage-'+data_config.chat_id),
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
    		$('#user-screenshot-container-'+chat_id).html(data);
    		$('#user-screenshot-container-'+chat_id).removeClass('screenshot-pending');
    	});
    };

    this.updateScreenshotOnline = function(online_id) {
    	$('#user-screenshot-container').html('').addClass('screenshot-pending');
    	$.get(this.wwwDir + 'chat/checkscreenshotonline/' + online_id,function(data){
    		$('#user-screenshot-container-'+online_id).html(data);
    		$('#user-screenshot-container-'+online_id).removeClass('screenshot-pending');
    	});
    };

    this.eNick = function() {
    	lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/editnick/'+this.chat_id+'/'+this.hash})
    }

    this.enableVisitorEditor = function()
    {
        $('#ChatMessageContainer').removeClass('hide');
        $('#CSChatMessage').focus();
    }

    this.disableVisitorEditor = function()
    {
        $('#ChatMessageContainer').addClass('hide');
    }

    this.buttonClicked = function(payload, id, btn, notHide) {

        if (btn.attr("data-no-change") == undefined) {
            btn.attr("disabled","disabled");
            btn.prepend("<i class=\"material-icons lhc-spin\">loop</i>");
        }

        var messageBlock = $('#messagesBlock');

        var scrollHeight = messageBlock.prop("scrollHeight");
        messageBlock.scrollTop(scrollHeight);

        this.syncroRequestSend = true;
        clearTimeout(this.userTimeout);

        $.get(this.wwwDir + 'genericbot/buttonclicked/'+this.chat_id+'/'+this.hash,{payload: payload, id : id, processed : (typeof notHide === 'undefined' || notHide == false)},function(data){
            if (typeof notHide === 'undefined' || notHide === false){
                $('.meta-message-'+id).remove();
            }

            var scrollHeight = messageBlock.prop("scrollHeight");
            messageBlock.scrollTop(scrollHeight);
            lhinst.forceBottomScroll = true;
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();
        }).fail(function() {
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();
        });

        lhinst.focusUserText();
    }

    this.editGenericStep = function(step, id) {

        var messageBlock = $('#messagesBlock');

        var scrollHeight = messageBlock.prop("scrollHeight");
        messageBlock.scrollTop(scrollHeight);

        this.syncroRequestSend = true;
        clearTimeout(this.userTimeout);

        $.get(this.wwwDir + 'genericbot/buttonclicked/'+this.chat_id+'/'+this.hash+'/(type)/editgenericstep',{payload : step,id : id},function(data){
            var scrollHeight = messageBlock.prop("scrollHeight");
            messageBlock.scrollTop(scrollHeight);
            lhinst.forceBottomScroll = true;
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();
        }).fail(function() {
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();
        });

        lhinst.focusUserText();
    }

    this.updateTriggerClicked = function(payload, id, btn, notHide) {

        if (btn.attr("data-no-change") == undefined) {
            btn.attr("disabled","disabled");
            btn.prepend("<i class=\"material-icons lhc-spin\">loop</i>");
        }

        var messageBlock = $('#messagesBlock');
        var scrollHeight = messageBlock.prop("scrollHeight");
        messageBlock.scrollTop(scrollHeight);

        this.syncroRequestSend = true;
        clearTimeout(this.userTimeout);

        $.get(this.wwwDir + 'genericbot/buttonclicked/'+this.chat_id+'/'+this.hash+'/(type)/triggerclicked',{payload: payload, id : id, processed : (typeof notHide === 'undefined' || notHide == false)},function(data) {
            if (typeof notHide === 'undefined' || notHide === false){
                $('.meta-message-'+id).remove();
            }

            var scrollHeight = messageBlock.prop("scrollHeight");
            messageBlock.scrollTop(scrollHeight);

            lhinst.forceBottomScroll = true;
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();

        }).fail(function() {
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();
        });

        lhinst.focusUserText();
    }

    this.updateChatClicked = function(payload, id, btn, notHide) {

        if (btn.attr("data-no-change") == undefined) {
            btn.attr("disabled","disabled");
            btn.prepend("<i class=\"material-icons lhc-spin\">loop</i>");
        }

        var messageBlock = $('#messagesBlock');

        var scrollHeight = messageBlock.prop("scrollHeight");
        messageBlock.scrollTop(scrollHeight);

        lhinst.syncroRequestSend = true;
        clearTimeout(this.userTimeout);

        $.get(this.wwwDir + 'genericbot/updatebuttonclicked/'+this.chat_id+'/'+this.hash,{payload: payload, id : id, processed : (typeof notHide === 'undefined' || notHide == false) },function(data){
            if (typeof notHide === 'undefined' || notHide === false){
                $('.meta-message-'+id).remove();
            }

            var scrollHeight = messageBlock.prop("scrollHeight");
            messageBlock.scrollTop(scrollHeight);

            lhinst.forceBottomScroll = true;
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();

        }).fail(function() {
            lhinst.syncroRequestSend = false;
            lhinst.enableVisitorEditor();
            lhinst.syncusercall();
        });
        lhinst.focusUserText();
    }

    this.dropdownClicked = function(id, btn) {

        if (btn.attr("data-no-change") == undefined) {
            btn.attr("disabled","disabled");
            btn.prepend("<i class=\"material-icons lhc-spin\">loop</i>");
        }
        var messageBlock = $('#messagesBlock');

        var scrollHeight = messageBlock.prop("scrollHeight");
        messageBlock.scrollTop(scrollHeight);

        if ($('#generic_list-'+id).val() != '') {
            this.syncroRequestSend = true;
            clearTimeout(this.userTimeout);
            $.get(this.wwwDir + 'genericbot/buttonclicked/'+this.chat_id+'/'+this.hash+'/(type)/valueclicked',{payload: $('#id_generic_list-'+id).val(), id : id},function(data){
                $('.meta-message-'+id).remove();
                var scrollHeight = messageBlock.prop("scrollHeight");
                messageBlock.scrollTop(scrollHeight);

                lhinst.forceBottomScroll = true;
                lhinst.syncroRequestSend = false;
                lhinst.enableVisitorEditor();
                lhinst.syncusercall();
            }).fail(function() {
                lhinst.syncroRequestSend = false;
                lhinst.enableVisitorEditor();
                lhinst.syncusercall();
            });

            lhinst.focusUserText();

        } else {
            alert('Please choose!');
        }
    }

    this.focusUserText = function() {
        $('#CSChatMessage').focus();
    }

    this.delayQueue = [];
    this.delayed = false;
    this.intervalPending = null;


    this.setDelay = function(params) {

        var id = params['id'];
        var duration = params['duration'];
        var delay = params['delay'];
        var untillMessage = params['untill_message'];

        if (delay > 0) {
            $('#msg-'+id).addClass('hide');
        }

        if (untillMessage == true && $('#msg-'+id).nextUntil('message-admin').length > 0) {
            return;
        }

        setTimeout(function () {

            if (lhinst.delayed == false) {

                if (untillMessage == true) {
                    clearInterval(lhinst.intervalPending);
                    lhinst.intervalPending = setInterval(function() {
                        if ($('#msg-'+id).nextUntil('message-admin').length > 0) {
                            lhinst.unhideDelayed(id);
                            $('#messagesBlock > #msg-'+id).remove();
                            clearInterval(lhinst.intervalPending);
                        } else {
                            if (!$('#msg-'+id).hasClass('meta-hider'))
                            {
                                $('#msg-'+id).addClass('meta-hider message-row-typing');
                                $('#msg-'+id).removeClass('hide');
                                $('#msg-'+id+' .msg-body').removeClass('hide');

                                var messageBlock = $('#messagesBlock');

                                var scrollHeight = messageBlock.prop("scrollHeight");
                                messageBlock.find('.meta-auto-hide').hide();
                                messageBlock.find('.message-row').last().find('.meta-auto-hide').show();
                                scrollHeight = messageBlock.prop("scrollHeight");

                                messageBlock.find('.pending-storage').remove();
                                messageBlock.scrollTop(scrollHeight + 2000);
                            }
                        }
                    },500);

                } else {
                    lhinst.delayed = true;

                    $('#msg-'+id).addClass('meta-hider message-row-typing').nextUntil('meta-hider').addClass('hide');
                    setTimeout(function () {
                        lhinst.unhideDelayed(id);
                    }, duration * 1000);
                    $('#msg-'+id).removeClass('hide');
                    $('#msg-'+id+' .msg-body').removeClass('hide');

                    if (delay > 0) {
                        var messageBlock = $('#messagesBlock');

                        var scrollHeight = messageBlock.prop("scrollHeight");
                        messageBlock.find('.meta-auto-hide').hide();
                        messageBlock.find('.message-row').last().find('.meta-auto-hide').show();
                        scrollHeight = messageBlock.prop("scrollHeight");

                        messageBlock.find('.pending-storage').remove();
                        messageBlock.scrollTop(scrollHeight + 2000);
                    }
                }

            } else {
                lhinst.delayQueue.push({'id' : id, 'delay' : duration});
            }
        },delay*1000);
    }

    this.sendHTML = function (id, options) {
        if (typeof(parent) !== 'undefined' && window.location !== window.parent.location) {
            parent.postMessage('lhc_html_snippet:' + id + ':' + options.type + '_' + options.id, '*');
        }
    }

    this.unhideDelayed = function (id) {

        var msg = $('#messagesBlock > #msg-'+id);
        msg.nextUntil('.meta-hider').removeClass('hide');
        msg.remove();

        var messageBlock = $('#messagesBlock');

        var scrollHeight = messageBlock.prop("scrollHeight");
        messageBlock.find('.meta-auto-hide').hide();
        messageBlock.find('.message-row').last().find('.meta-auto-hide').show();
        scrollHeight = messageBlock.prop("scrollHeight");

        messageBlock.find('.pending-storage').remove();
        messageBlock.scrollTop(scrollHeight+2000);

        if (this.delayQueue.length > 0) {
            var data = lhinst.delayQueue.pop();
            setTimeout(function () {
                lhinst.unhideDelayed(data.id);
            }, data.delay * 1000);
            $('#msg-'+data.id).removeClass('hide');
            $('#msg-' + data.id + ' .msg-body').removeClass('hide');
        } else {
            lhinst.delayed = false;
        }
    }

    this.gmaps_loading = false;
    this.queue_render = [];

    this.showMessageLocation = function(id,lat,lon) {
        var myLatLng = {lat: lat, lng: lon};

        if (this.gmaps_loaded == true) {

            var map = new google.maps.Map(document.getElementById('msg-location-' + id), {
                zoom: 13,
                center: myLatLng
            });

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: lat+","+lon
            });

        } else {
            if (this.gmaps_loading == false) {
                this.gmaps_loading = true;
                var po = document.createElement('script'); po.type = 'text/javascript';
                po.async = true;
                po.src = 'https://maps.googleapis.com/maps/api/js?key='+confLH.gmaps_api_key+"&callback=chatMapLoaded";
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(po, s);
                lhinst.queue_render.push({'id':id,'lat':lat,'lon':lon});
            } else {
                lhinst.queue_render.push({'id':id,'lat':lat,'lon':lon});
            }
        }
    }
}

function chatMapLoaded()
{
    if (lhinst.queue_render.length > 0){
        lhinst.gmaps_loaded = true;
        var i = lhinst.queue_render.pop();

        var myLatLng = {lat: i.lat, lng: i.lon};

        var map = new google.maps.Map(document.getElementById('msg-location-' + i.id), {
            zoom: 13,
            center: myLatLng
        });

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: i.lat+","+i.lon
        });

        if (lhinst.queue_render.length > 0) {
            chatMapLoaded();
        }
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
jQuery(document).on("click", function(){
    lhinst.hidePopover();
});
jQuery(document).on("touchstart", preloadSound);

function gMapsCallback(){

    lhinst.gmaps_loaded = true;

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
	
	var mapTabSection = $('#map-activator');
		
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

$.fn.makeDropdown = function() {
    var filterInput = this.find('.btn-block-department-filter > input');

    this.click(function(){
        setTimeout(function(){
            filterInput.focus();
        },50);
    })

    this.on("click", "[data-stopPropagation]", function(e) {
        e.stopPropagation();
    })

    var limitMax = this.attr('data-limit') ? parseInt(this.attr('data-limit')) : 0;

    var selectedItems = [];

    var _this = this;

    _this.each(function () {
        var selectedItems = $(this).find('.selected-items-filter');

        $(this).find('.btn-department-dropdown').attr('data-text',$(this).find('.btn-department-dropdown').text());

        var itemsSelectedCount = selectedItems.find('.delete-item').length;

        if (itemsSelectedCount > 0) {
            var defaultSelectedText = itemsSelectedCount == 1 ? selectedItems.find('.delete-item').first().parent().text().trim().replace('delete','') : '';
            $(this).find('.btn-department-dropdown').text((itemsSelectedCount == 1 ? defaultSelectedText : '['+itemsSelectedCount+'] ') + (itemsSelectedCount != 1 ? $(this).find('.btn-department-dropdown').attr('data-text') : ''));
        }

        var _thisItem = $(this);

        _thisItem.on("change","li input:checkbox",function() {
            var itemsSelectedCount = 0;
            var singleSelectedText = '';

            // We want to keep presently checked item always
            var presentId = 0;
            if ($(this).is(':checked')) {
                if (selectedItems.find('.delete-item[data-value="'+$(this).val()+'"]').length == 0) {
                    selectedItems.prepend('<div class="fs12"><a data-stoppropagation="true" class="delete-item" data-value="' + $(this).val() + '"><input type="hidden" value="' + $(this).val() + '" name="'+_thisItem.find('.btn-block-department-filter > input').attr('data-scope')+'[]" /><i class="material-icons chat-unread">delete</i>' + $(this).parent().text().trim() + "</a></div>");
                }
            } else {
                selectedItems.find('.delete-item[data-value="'+$(this).val()+'"]').remove();
            }

            itemsSelectedCount = selectedItems.find('.delete-item').length;
            singleSelectedText = itemsSelectedCount == 1 ? selectedItems.find('.delete-item').first().parent().text().trim().replace('delete','') : '';

            if (itemsSelectedCount > 0) {
                _thisItem.find('.btn-department-dropdown').text((itemsSelectedCount == 1 ? singleSelectedText : '['+itemsSelectedCount+'] ')+ (itemsSelectedCount != 1 ? _thisItem.find('.btn-department-dropdown').attr('data-text') : ''));
            } else {
                _thisItem.find('.btn-department-dropdown').text(_thisItem.find('.btn-department-dropdown').attr('data-text'));
            }
        });

        $(this).on('click','.delete-item',function () {
            _thisItem.find('input[value='+$(this).attr('data-value')+']').prop('checked',false);
            $(this).parent().remove();

            var itemsSelectedCount = _thisItem.find('li input:checked').length;

            if (itemsSelectedCount > 0) {
                _thisItem.find('.btn-department-dropdown').text('['+itemsSelectedCount+'] '+_thisItem.find('.btn-department-dropdown').attr('data-text'));
            } else {
                _thisItem.find('.btn-department-dropdown').text(_thisItem.find('.btn-department-dropdown').attr('data-text'));
            }
        });
    });

    // @todo add timout funtion
    var timeoutSearch = null;

    filterInput.keyup(function() {
        if ($(this).attr('ajax-provider')) {
            var parent = $(this).parent().parent();
            var parentHolder = $(this).parent();
            $.getJSON(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/' + $(this).attr('ajax-provider') + '/?q=' + encodeURIComponent($(this).val()), function(data) {
                var append = '';
                data.items.forEach(function(item) {
                    var isSelected = parentHolder.find('.delete-item[data-value="' + item.id + '"]').length == 1;
                    append += '<li class="search-option-item" data-stoppropagation="true"><label><input type="checkbox" '+(isSelected ? ' checked="checked" ' : '')+' name="selector-'+data.props.list_id+'[]" value="'+item.id+'"> ' + item.name +'</label></li>';
                });
                parent.find('.search-option-item').remove();
                parent.append(append);
            })
        } else {
            var filter = $(this).val();
            $(this).parent().parent().children('li').each(function(i) {
                if (i > 0) {
                    if (!$(this).text().toLowerCase().includes(filter) && filter != ''){
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        }
    });
};

var focused = true;
window.onfocus = window.onblur = function(e) {
    focused = (e || event).type === "focus";
    lhinst.focusChanged(focused);
};

window.lhcSelector = null;

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

} catch (e) {
    if (lhcError) lhcError.log(e.message, "lh.js", e.lineNumber || e.line, e.stack); else throw Error("lhc : " + e.message);
}