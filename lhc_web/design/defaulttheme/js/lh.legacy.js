lhinst.addmsgurluser = "chat/addmsguser/";
lhinst.addmsgurluserchatbox = "chatbox/addmsguser/";
lhinst.syncuser = "chat/syncuser/";
lhinst.userclosechaturl = "chat/userclosechat/";
lhinst.checkchatstatus = "chat/checkchatstatus/";
lhinst.checkChatStatusTimeout = null;

lhinst.addmsguserchatbox = function (chat_id) {
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

lhinst.updateMessageRow = function(msgid){
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

lhinst.addmsguser = function (focusArea)
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

lhinst.addMessagesToStore = function(messages)
{
    var modeWindow = this.isWidgetMode == true ? '/(mode)/widget' : '';

    var arrayLength = messages.length;
    for (var i = 0; i < arrayLength; i++) {
        this.addUserMessageQueue.push({'retries':0,'pdata':{msg : messages[i]},'url':this.wwwDir + this.addmsgurluser + this.chat_id + '/' + this.hash + modeWindow});
    }

    this.addDelayedMessage();
};

lhinst.addDelayedMessage = function()
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

lhinst.switchToOfflineForm = function() {
    var form = $('#form-start-chat');
    form.attr('action',$('#form-start-chat').attr('action')+'/(switchform)/true/(offline)/true/(leaveamessage)/true/(department)/'+$('#id_DepartamentID').val());
    form.submit();
    return false;
};

lhinst.disableChatSoundUser = function(inst)
{
    if (inst.find('> i').text() == 'volume_off') {
        confLH.new_message_sound_user_enabled = 1;
        inst.find('> i').text('volume_up');
    } else {
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

lhinst.prestartChat = function(timestamp,inst) {

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


lhinst.addCaptcha = function(timestamp,inst) {
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

lhinst.addCaptchaSubmit = function(timestamp,inst) {
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

lhinst.addFileUserUploadOnline = function(data_config,callback) {
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

lhinst.addFileUploadOnlineUser = function(data_config, callbackUploaded) {
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

lhinst.eNick = function() {
    lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/editnick/'+this.chat_id+'/'+this.hash})
}

lhinst.enableVisitorEditor = function()
{
    $('#ChatMessageContainer').removeClass('hide');
    $('#CSChatMessage').focus();
}

lhinst.disableVisitorEditor = function()
{
    $('#ChatMessageContainer').addClass('hide');
}

lhinst.buttonClicked = function(payload, id, btn, notHide) {

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

lhinst.editGenericStep = function(step, id) {

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

lhinst.updateTriggerClicked = function(payload, id, btn, notHide) {

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

lhinst.updateChatClicked = function(payload, id, btn, notHide) {

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

lhinst.dropdownClicked = function(id, btn) {

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

lhinst.focusUserText = function() {
    $('#CSChatMessage').focus();
}

lhinst.unhideDelayed = function (id) {

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

lhinst.chatsyncuserpending = function ()
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

lhinst.setTheme = function(theme_id) {
    this.theme = theme_id;
};

lhinst.setSurvey = function(survey_id) {
    this.survey = survey_id;
};

lhinst.voteAction = function(inst) {

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

lhinst.sendemail = function(){
    $.postJSON(this.wwwDir + 'chat/sendchat/' + this.chat_id+'/'+this.hash,{csfr_token:confLH.csrf_token, email:$('input[name="UserEmail"]').val()}, function(data){
        if (data.error == 'false') {
            $('#myModal').modal('hide');
        } else {
            $('#user-action .alert-box').remove();
            $('#user-action').prepend(data.result);
        }
    });
};

lhinst.closeWindow  = function() {
    if (this.survey !== null && this.surveyShowed == false) {
        this.surveyShowed = true;
        this.chatClosed();
    } else {
        window.open('','_self','');
        window.close();
        parent.postMessage("lhc_chat_closed_explicit", '*');
    }
};

lhinst.setLastUserMessageID = function(message_id) {
    this.last_message_id = message_id;
};

lhinst.setChatID = function (chat_id){
    this.chat_id = chat_id;
};

lhinst.setCloseWindowOnEvent = function (value)
{
    this.closeWindowOnChatCloseDelete = value;
};

lhinst.setWidgetMode = function(status) {
    this.isWidgetMode = status;
};

lhinst.setEmbedMode = function(status) {
    this.isEmbedMode = status;
};

lhinst.setSyncUserURL = function(url) {
    this.syncuser = url;
};

lhinst.editPreviousUser = function() {
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

lhinst.addFileUserUpload = function(data_config) {
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

lhinst.setChatHash = function (hash)
{
    this.hash = hash;
};

lhinst.updateUserSyncInterface = function(inst,data)
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

lhinst.parseCloseArgs = function (args) {
    var tt = args.length/2;
    for (i = 0; i < tt; i++) {
        var argument = args[i*2];
        var value = args[(i*2)+1];
        if (argument == 'survey_id') {
            this.survey = value;
        }
    }
};

lhinst.chatClosed = function() {
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

lhinst.syncusercall = function()
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

lhinst.scheduleSync = function() {
    this.syncroRequestSend = false;
    clearTimeout(this.userTimeout);
    this.userTimeout = setTimeout(chatsyncuser,confLH.chat_message_sinterval);
};

lhinst.executeRemoteCommands = function(operations)
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

lhinst.disableFileUpload = function () {
    $('#fileupload').fileupload('destroy');
    $('#ChatMessageContainer .dropdown-menu .flex-row .file-uploader').remove();
};

lhinst.startChatNewWindow = function(chat_id,name)
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

lhinst.userclosedchat = function()
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

lhinst.userclosedchatembed = function()
{
    if (!!window.postMessage && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {
        parent.postMessage("lhc_chat_closed_explicit", '*');
    } else {
        if (this.chatClosed() == false) {
            window.close();
        }
    }
};

lhinst.continueChatFromSurvey = function(survey_id)
{
    if (this.isWidgetMode && typeof(parent) !== 'undefined' && window.location !== window.parent.location) {
        $.postJSON(this.wwwDir + "survey/backtochat/" + this.chat_id + '/' + this.hash + '/' + survey_id , function(data){
            if (data.closed) {
                lhinst.userclosedchatembed();
            } else {
                parent.postMessage('lhc_continue_chat', '*');
            }
        });
    } else {
        this.chatClosed();
    }

    return false;
}

lhinst.explicitChatCloseByUser = function()
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

lhinst.restoreWidget = function(hash){
    if (!!window.postMessage && window.opener) {
        window.opener.postMessage("lhc_ch:hash:"+hash, '*');
        window.opener.postMessage("lhc_ch:hash_resume:"+hash, '*');
        window.opener.postMessage("lhc_open_restore", '*');
        window.close();
    };
};

lhinst.userclosedchatandbrowser = function()
{
    if (LHCCallbacks.userleftchatNotification) {
        LHCCallbacks.userleftchatNotification(this.chat_id);
    };

    $.get(this.wwwDir + this.userclosechaturl + this.chat_id + '/' + this.hash + '/(eclose)/t',function(data){
        lhinst.closeWindow();
    });
};

lhinst.afterUserChatInit = function () {
    if (LHCCallbacks.afterUserChatInit) {
        LHCCallbacks.afterUserChatInit();
    }
};

lhinst.sendHTML = function (id, options) {
    if (typeof(parent) !== 'undefined' && window.location !== window.parent.location) {
        parent.postMessage('lhc_html_snippet:' + id + ':' + options.type + '_' + options.id, '*');
    }
}

lhinst.setDelay = function(params) {

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

lhinst.enableFileUpload = function () {
    $.getJSON(this.wwwDir + 'file/fileoptions/' + this.chat_id + '/' + this.hash, function(data){
        $('#ChatMessageContainer .dropdown-menu .flex-row').prepend(data.html);
        data.options.ft_us = new RegExp('(\.|\/)(' +data.options.ft_us + ')$','i');
        lhinst.addFileUserUpload(data.options);
    });
}

lhinst.chooseFile = function () {
    if (document.getElementById('fileupload')) {
        document.getElementById('fileupload').click();
    }
}

lhinst.executeExtension = function (extension, params) {
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

lhinst.initTypingMonitoringUser = function(chat_id) {

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

lhinst.typingStoppedUser = function(chat_id) {
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

function chatsyncuser()
{
    lhinst.syncusercall();
}

function chatsyncuserpending()
{
    lhinst.chatsyncuserpending();
}

this.afterChatWidgetInit = function () {
    if (LHCCallbacks.afterChatWidgetInit) {
        LHCCallbacks.afterChatWidgetInit();
    };
};