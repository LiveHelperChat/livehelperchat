export default {
    setInactive : async function (status) {
        const response = await fetch(WWW_DIR_JAVASCRIPT + 'user/setinactive/'+status, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });
        return response.json();
    },
    setLocalSettings : async function (attr,val) {
        const response = await fetch(WWW_DIR_JAVASCRIPT + 'front/settings/', {
            method: "POST",
            body: JSON.stringify({"attr":attr,"val":val}),
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });
        return response.json();
    },

    searchProvider : async function (scope, keyword){
        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/searchprovider/'+scope+'/?exclude_disabled=1&q=' +keyword, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });

        return responseTrack.json();
    },

    restoreLocalSetting : function(variable,defaultValue,split) {
        try {
            if (localStorage) {
                var value = localStorage.getItem(variable);
                if (value !== null){
                    if (split == true){

                        var values = value.split('/');
                        var valuesInt = new Array();

                        values.forEach(function(val) {
                            valuesInt.push(parseInt(val));
                        });

                        return valuesInt;
                    } else {
                        return value;
                    }
                } else {
                    return defaultValue;
                }
            }
        } catch(e) {}
        return defaultValue;
    },
    getChatData : async function(id) {
        const response = await fetch(WWW_DIR_JAVASCRIPT + 'chat/getchatdata/' + id, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });

        return response.json();
    },
    getToggleWidget : function(lhcList, variable, defaultValue) {
        lhcList.update((list) => {
            list.toggleWidgetData[variable] = this.restoreLocalSetting(variable,(typeof defaultValue === 'undefined' ? 'false' : defaultValue), false) == 'false' ? false : true;
            return list;
        });
    },

    suspendSync : function(lhcList, type) {
        lhcList.update((list) => {

            // Check if type is already in the array
            const index = list.suspend_widgets.indexOf(type);

            if (index === -1) {
                // Add type if it's not in the array
                list.suspend_widgets.push(type);
            } else {
                // Remove type if it's already in the array
                list.suspend_widgets.splice(index, 1);
            }

            return list;
        });
    },

    getToggleWidgetSort : function(lhcList, variable, defaultValue) {
        lhcList.update((list) => {
            list.toggleWidgetData[variable] = this.restoreLocalSetting(variable,(typeof defaultValue === 'undefined' ? '' : defaultValue),false);
            return list;
        });
    },

    toggleWidget : function(lhcList, variable, forceReload) {

        lhcList.update((list) => {
            list.toggleWidgetData[variable] = typeof list.toggleWidgetData[variable] !== 'undefined' ? !list.toggleWidgetData[variable] : true;

            if (localStorage) {
                try {
                    localStorage.setItem(variable,list.toggleWidgetData[variable]);
                } catch(err) {
                };
            };

            return list;
        });

        if (typeof forceReload !== 'undefined' && forceReload == true) {
            ee.emitEvent('angularLoadChatList');
        }
    },
    toggleWidgetSort : function(lhcList, variable, val, val_desc, forceReload) {

        lhcList.update((list) => {
            list.toggleWidgetData[variable] = typeof list.toggleWidgetData[variable] === 'undefined' ? val : (list.toggleWidgetData[variable] == val ? val_desc : val);

            if (localStorage) {
                try {
                    localStorage.setItem(variable, list.toggleWidgetData[variable]);
                } catch(err) {
                };
            };

            return list;
        });

        if (typeof forceReload !== 'undefined' && forceReload == true) {
            ee.emitEvent('angularLoadChatList');
        }
    },
    previewMail : function(chat_id, event){
        if (event) {
            event.stopPropagation();
        }
        lhc.previewMail(chat_id);
    },
    startMailChat : function (chat_id, name, background) {
        if (jQuery('#tabs').length > 0) {
            return lhinst.startMailChat(chat_id,jQuery('#tabs'),this.truncate(name || 'Mail',10), background);
        }
    },
    previewChat : function(chat_id,event){
        if (event) {
            event.stopPropagation();
        }
        lhc.previewChat(chat_id);
    },
    deleteChat : function(chat_id, tabs, hidetab) {
        return lhinst.deleteChat(chat_id, tabs, hidetab);
    },
    redirectContact : function(chat_id,message,event) {
        if (event) {
            event.stopPropagation();
        }
        return lhinst.redirectContact(chat_id,message);
    },
    openModal : function(url, event) {
        if (event) {
            event.stopPropagation();
        }
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+url, hidecallback: function() { ee.emitEvent('angularLoadChatList'); }});
    },
    changeWidgetHeight : function(lhcList, widget, expand) {
        let elm = document.getElementById(widget+'-panel-list');
        if (elm) {
            lhcList.update((list) => {
                list[widget + '_m_h'] = expand === true ? parseInt(elm.offsetHeight + 28) + 'px' : (parseInt(elm.offsetHeight - 28) > 56 ? parseInt(elm.offsetHeight - 28) : 56) + 'px';
                localStorage.setItem(widget+'_m_h', list[widget + '_m_h']);
                return list;
            });
        }
    },
    startChatTransfer : function(chat_id,name,transfer_id, transfer_scope) {
        return lhinst.startChatTransfer(chat_id,$('#tabs'),name, transfer_id, transfer_scope);
    },
    startChatNewWindowTransfer : function(chat_id,name,transfer_id, transfer_scope) {
        return lhinst.startChatNewWindowTransfer(chat_id,name,transfer_id, transfer_scope);
    },
    startChat : function (chat_id,name,background) {
        if ($('#tabs').length > 0) {
            lhinst.addOpenTrace('click');
            return lhinst.startChat(chat_id,$('#tabs'),this.truncate((name || 'Visitor'),10),background);
        } else {
            lhinst.startChatNewWindow(chat_id,name);
        }
    },
    truncate : function (text, length, end) {
        if (isNaN(length))
            length = 10;

        if (end === undefined)
            end = "...";

        if (text.length <= length || text.length - end.length <= length) {
            return text;
        }
        else {
            return String(text).substring(0, length-end.length) + end;
        }
    },
    storeLocalSetting : function(variable, value) {
        if (localStorage) {
            try {
                var value = localStorage.setItem(variable, value);
            } catch(e) {}
        }
    },
    removeLocalSetting : function(listId) {
        if (localStorage) {
            try {
                localStorage.removeItem(listId);
            } catch(err) {
            };
        }
    },
    loadActiveChats : async function() {
        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/loadactivechats', {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });
        return responseTrack.json();
    },
    startGroupChat : function (chat_id, name) {
        if (jQuery('#tabs').length > 0) {
            return lhinst.startGroupChat(chat_id,$('#tabs'),this.truncate(name,10));
        }
    },
    rejectGroupChat : async function (groupChatId, event) {

        if (event) {
            event.stopPropagation();
        }

        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'groupchat/leave/' + groupChatId, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });

        let response = await responseTrack.json();

        ee.emitEvent('angularLoadChatList');
    },
    startNewGroupChat : async function (lhcList, groupName, publicChat) {

        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'groupchat/newgroupajax', {
            method: "POST",
            body: JSON.stringify({"name":groupName,"public":publicChat}),
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });

        let data = await responseTrack.json();

        lhcList.update((list) => {
            list.new_group_name = "";
            list.new_group_type = "";
            return list;
        });

        lhinst.startGroupChat(data.id,jQuery('#tabs'),this.truncate(data.name,10));

        ee.emitEvent('angularLoadChatList');
    },
    startChatOperator : async function(user_id) {

        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'groupchat/startchatwithoperator/' + user_id, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });

        let data = await responseTrack.json();

        lhinst.startGroupChat(data.id,jQuery('#tabs'),this.truncate(data.name,10));
    },
    getNotificationsData : async function(id) {
        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/getnotificationsdata/(id)/' + id, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        });

        return responseTrack.json();
    }
}




