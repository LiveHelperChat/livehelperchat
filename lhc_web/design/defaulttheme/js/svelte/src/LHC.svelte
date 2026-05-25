<svelte:options customElement={{tag: 'lhc-app', shadow : 'none'}}/>

<script>
    import { onMount } from 'svelte';
    import { lhcList } from './stores.js';
    import lhcServices from './lib/Services.js';

    // START Widget search filters
    $ : userFilterText = $lhcList.userFilterText;
    $ : depFilterText = $lhcList.depFilterText;
    $ : lmtoggle = $lhcList.lmtoggle;
    $ : rmtoggle = $lhcList.rmtoggle;
    $ : track_open_chats = $lhcList.toggleWidgetData['track_open_chats'];
    $ : group_offline_chats = $lhcList.toggleWidgetData['group_offline_chats'];

    function filterchanged() {
        lhcServices.searchProvider('users', $lhcList.userFilterText).then(function(data) {
            $lhcList['userList'] = data.items;
        }).catch(function() {
            $lhcList['userList'] = [];
        });
    }

    function filterchangedDep(){
        lhcServices.searchProvider('depswidget', $lhcList.depFilterText).then(function(data) {
            $lhcList['userDepartments'] = data.items;
            $lhcList['userDepartmentsNames'] = data.items_names;
        }).catch(function() {
            $lhcList['userDepartments'] = [];
            $lhcList['userDepartmentsNames'] = [];
        });
    }

    function menuToggle() {
        let item = document.getElementById('menu-rmtoggle');
        if (item) {

            if ($lhcList.rmtoggle) {
                item.classList.add('hide');
            } else {
                item.classList.remove('hide');
            }
        }

        item = document.getElementById('wrapper');
        if (item) {
            if ($lhcList.lmtoggle) {
                item.classList.add('toggled');
            } else {
                item.classList.remove('toggled');
            }
        }
    }

    function checkOptionStatus(option_id) {

        if (!document.getElementById(option_id)) {
            return;
        }

        let icon = document.getElementById(option_id).getElementsByTagName('i')[0];
        if ($lhcList.toggleWidgetData[option_id] === true) {
            icon.classList.remove('chat-closed');
            icon.classList.add('chat-active');
        } else {
            icon.classList.remove('chat-active');
            icon.classList.add('chat-closed');
        }
    }

    $ : userFilterText, filterchanged();
    $ : depFilterText, filterchangedDep();
    $ : lmtoggle, menuToggle();
    $ : rmtoggle, menuToggle();
    $ : track_open_chats, checkOptionStatus('track_open_chats');
    $ : group_offline_chats, checkOptionStatus('group_offline_chats');

    // END Widget search filters

    window['$_LHC_API'] = {};

    let lhcLogic = {
        timeoutActivity: null,
        timeoutActivityTime: 300,
        blockSync: false,
        timeoutControl: null,
        setTimeoutEnabled: true,
        isListLoaded: false,
        lastd_activity: Math.round(new Date().getTime()/1000),
        lhcListRequestInProgress: false,
        lhcSettingUpdateProgress: [],
        lhcSettingAllSelected: false,
        channel: null,
        abortController : new AbortController()
    };

    lhinst.channel = lhcLogic.channel = new BroadcastChannel('lhc_dashboard');

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data.action === 'lhc_open_chat') {
                startChatByID(event.data.chat_id);
            } else if (event.data.action === 'lhc_open_url' && event.data.url) {
                document.location = event.data.url;
            }
        });
    }

    lhcLogic.channel.addEventListener("message", function(event) {
        if (event.isTrusted && event.data.action) {

            if (event.data.action === 'went_active') {
                jQuery('#myModal').modal('hide');
                setActiveInterface();
                return;
            }

            var tabs = jQuery('#tabs');
            if (event.data.args.chat_id && lhinst.chatsSynchronising.indexOf(event.data.args.chat_id) !== -1) {
                if (event.data.action === 'close_chat') {
                    tabs.length > 0 && lhinst.removeDialogTab(event.data.args.chat_id,tabs,true);
                } else if (event.data.action === 'update_chat' || event.data.action === 'startbackground_chat') {
                    tabs.length > 0 && lhinst.updateVoteStatus(event.data.args.chat_id, true);
                } else if (event.data.action === 'reload_chat') {
                    lhinst.addOpenTrace('channel_message_reload');
                    tabs.length > 0 && lhinst.reloadTab(event.data.args.chat_id, tabs, event.data.args.nick, true);
                }
            } else if (event.data.action === 'startbackground_chat') {
                lhinst.addOpenTrace('channel_message_open');
                (tabs.length > 0 && lhinst.startChatBackground(event.data.args.chat_id, tabs, event.data.args.nick)) || ee.emitEvent('chatTabPreload', [event.data.args.chat_id, {focus: false}]);
            } else if (event.data.action === 'close_chat') {
                ee.emitEvent('removeSynchroChat', [parseInt(event.data.args.chat_id)]);
            } else if (event.data.args.mail_id) {
                if (event.data.action == 'close_mail') {
                    lhinst.removeDialogTabMail(event.data.args.mail_id,jQuery('#tabs'), true, true);
                }
            }
        }
    });

    ee.addListener('svelteWentActive', function(){
        jQuery('#myModal').modal('hide');
        setActiveInterface();
    });

    ee.addListener('angularStartChatbyId',function (chat_id) {
        lhinst.addOpenTrace('view_clicked');
        startChatByID(chat_id);
    });

    ee.addListener('svelteOpenChat',function (chat_id) {
        lhinst.addOpenTrace('click');
        startChatByID(chat_id,$lhcList.isCTRLPressed);
    });

    ee.addListener('svelteOpenMail',function (chat_id, subject) {
        lhinst.startMailChat(chat_id,jQuery('#tabs'),truncate(subject,10),$lhcList.isCTRLPressed);
    });

    //ee.emitEvent("svelteAction",[{'type':'info_history','msg':"History record"}]);
    ee.addListener('svelteAction',function (data) {
        addAction(data);
    });

    ee.addListener('svelteDebug',function () {
        console.log($lhcList);
        console.log(lhcLogic);
    });

    ee.addListener('angularLoadChatList',function () {
        // Always process on chat close
        lhcLogic.isListLoaded = false;
        lhcLogic.lhcListRequestInProgress = false;
        loadChatList();
    });

    ee.addListener('svelteTestNotification',function (chat_id) {
        lhcServices.getNotificationsData(chat_id).then(function (data) {
            data.forEach(function (item) {
                lhinst.playSoundNewAction(item.last_id_identifier,parseInt(item.last_id),(item.nick ? item.nick : 'Live Help'),(item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
            });
        });
    });

    ee.addListener('svelteOptionsPanelLoaded',function (listId) {
        if (typeof $lhcList['optionsPanels'][listId] !== 'undefined') {
            const panel = $lhcList['optionsPanels'][listId];
            if (panel.custom_filters && Array.isArray(panel.custom_filters)) {
                panel.custom_filters.forEach(function(custom_filter) {
                    const fieldKey = listId + '_' + custom_filter['field'];
                    $lhcList[fieldKey] = lhcServices.restoreLocalSetting(fieldKey, 'false', false) != 'false';
                });
            }
        }
    });

    ee.addListener('svelteResetTimeoutActivity',function () {
        resetTimeoutActivity();
    });

    ee.addListener('angularStartChatOperatorPublic',function (user_id) {
        lhcServices.startChatOperator(user_id);
    });

    ee.addListener('svelteallDepartmentsChanged',function (list,force) {
            allDepartmentsChanged(list,force)
    });

    ee.addListener('svelteProductChanged',function (list) {
        productChanged(list);
    });

    ee.addListener('svelteDepartmentChanged',function (list) {
        departmentChanged(list);
    });

    ee.addListener('svelteLimitChanged',function (limit) {
        lhcServices.setLocalSettings(limit, $lhcList[limit]);
        loadChatList();
    });

    ee.addListener('angularSyncDisabled',function (status) {
        syncDisabled(status);
    });

    ee.addListener('svelteToggleList',function (list) {
        toggleList(list);
    });

    ee.addListener('svelteToggleWidget',function (list,forceReload) {
        lhcServices.toggleWidget(lhcList,list,forceReload);
    });

    ee.addListener('svelteRemoveLocalSetting',function (item_id) {
        lhcServices.removeLocalSetting(item_id);
    });

    ee.addListener('svelteStoreLocalSetting',function (item_id, value) {
        lhcServices.storeLocalSetting(item_id,value);
    });

    ee.addListener('svelteNoticeUpdated', function () {
        updateNoticeData();
    });

    ee.addListener('svelteAppendActiveChats',function () {
        lhcServices.loadActiveChats().then(function(data) {
            let tabs = jQuery('#tabs');
            data.result.forEach(function(item) {
                lhinst.startChatBackground(item.id, tabs, lhcServices.truncate((item.nick || 'Visitor'),10));
            });
            setTimeout(function(){
                ee.emitEvent('angularLoadChatList');
            },1000);
        });
    });

    let widgetsItems = [];
    widgetsItems.push('actived');
    widgetsItems.push('departmentd');
    widgetsItems.push('unreadd');
    widgetsItems.push('pendingd');
    widgetsItems.push('operatord');
    widgetsItems.push('closedd');
    widgetsItems.push('mcd');
    widgetsItems.push('botd');
    widgetsItems.push('subjectd');
    widgetsItems.push('pendingmd');
    widgetsItems.push('activemd');
    widgetsItems.push('alarmmd');
    widgetsItems.push('mmd');
    widgetsItems.push('department_online');
    widgetsItems.push('depp');
    widgetsItems.push('opp');

    widgetsItems.forEach(function(listId) {
        $lhcList[listId + '_all_departments'] = lhcServices.restoreLocalSetting(listId + '_all_departments','false',false) != 'false';
        $lhcList[listId + '_hide_hidden'] = lhcServices.restoreLocalSetting(listId + '_hide_hidden','false',false) != 'false';
        $lhcList[listId + '_hide_disabled'] = lhcServices.restoreLocalSetting(listId + '_hide_disabled','false',false) != 'false';
        $lhcList[listId + '_only_online'] = lhcServices.restoreLocalSetting(listId + '_only_online','false',false) != 'false';
        $lhcList[listId + '_only_explicit_online'] = lhcServices.restoreLocalSetting(listId + '_only_explicit_online','false',false) != 'false';
        $lhcList[listId + '_m_h'] = lhcServices.restoreLocalSetting(listId + '_m_h',null,false);
        $lhcList[listId + '_mslf'] = lhcServices.restoreLocalSetting(listId + '_mslf',null,false);
    });

    ['onlineusers_m_h'].forEach(function(key) {
        $lhcList[key] = lhcServices.restoreLocalSetting(key, null, false);
    });

    async function updateNoticeData(){
        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/loadinitialdata', {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            }
        });

        const data = await responseTrack.json();

        if (data.logout || data.error_url) {
            document.location.reload();
            return;
        }

        if (data.notice) {
            $lhcList['lhcNotice'] = data.notice;
        } else {
            $lhcList['lhcNotice'] = {'message' : '', 'level' : 'primary'};
        }
    }


    function toggleList(variable) {
        $lhcList[variable] = !$lhcList[variable];
        if (localStorage) {
            try {
                localStorage.setItem(variable,$lhcList[variable]);
            } catch(err) {
            }
        }
    }

     function setActiveInterface() {
        lhcServices.setInactive('false');
        lhcLogic.isListLoaded = false; // Because inactive visitor can be for some quite time, make sure new chat's does not trigger flood of sound notifications
        lhcLogic.blockSync = false;	// Unblock sync
        lhcLogic.lhcListRequestInProgress = false; // Request can be send either way
        resetTimeoutActivity(); // Start monitoring activity again
        lhinst.disableSync = false;

        loadChatList();
        $lhcList.inActive = false;
    }

    function restoreSettingByString(value,split) {
        if (split === true){

            var values = value.split('/');
            var valuesInt = [];

            values.forEach(function(val) {
                valuesInt.push(parseInt(val));
            });

            return valuesInt;
        } else {
            return value;
        }
    }

     function departmentChanged(listId) {

        if (lhcLogic.lhcSettingUpdateProgress.indexOf(listId) !== -1) {
            return;
        }

         lhcLogic.lhcSettingUpdateProgress.push(listId);

        if ($lhcList[listId].length > 0) {

            if (lhcLogic.lhcSettingAllSelected === false) {
                $lhcList[listId + '_all_departments'] = false;
                allDepartmentsChanged(listId,false);
            }

            lhcLogic.lhcSettingAllSelected = false;

            var listValue = $lhcList[listId].join("/");

            if (listValue !== '') {
                persistenStoreSettings(listId,listValue);
                setDepartmentNames(listId);
            } else {
                removeItemFromProgressList(listId);
            }

        } else {
            persistenStoreSettings(listId, null);
        }

        lhcLogic.isListLoaded = false;
        loadChatList();
    }

    function storeLocalSetting(variable, value) {
        if (localStorage) {
            try {
                localStorage.setItem(variable, value);
            } catch(e) {}
        }
    }

    function removeLocalSetting(listId) {
        if (localStorage) {
            try {
                localStorage.removeItem(listId);
            } catch(err) {
            }
        }
    }

    function allDepartmentsChanged(listId, loadlList) {

        if ($lhcList[listId + '_all_departments'] === true) {
            storeLocalSetting(listId + '_all_departments', true);
        } else {
            removeLocalSetting(listId + '_all_departments');
        }

        if ($lhcList[listId+'_hide_hidden'] === true) {
            storeLocalSetting(listId + '_hide_hidden', true);
        } else {
            removeLocalSetting(listId + '_hide_hidden');
        }

        if ($lhcList[listId+'_hide_disabled'] === true) {
            storeLocalSetting(listId + '_hide_disabled', true);
        } else {
            removeLocalSetting(listId + '_hide_disabled');
        }

        if ($lhcList[listId+'_only_online'] === true) {
            storeLocalSetting(listId + '_only_online', true);
        } else {
            removeLocalSetting(listId + '_only_online');
        }

        if (typeof $lhcList[listId+'_hide_dep'] !== 'undefined') {
            if ($lhcList[listId+'_hide_dep'] === true) {
                storeLocalSetting(listId + '_hide_dep', true);
            } else {
                removeLocalSetting(listId + '_hide_dep');
            }
        }

        if (typeof $lhcList[listId+'_mslf'] !== 'undefined') {
            if ($lhcList[listId+'_mslf'] === true) {
                storeLocalSetting(listId + '_mslf', true);
            } else {
                removeLocalSetting(listId + '_mslf');
            }
        }

        if (typeof $lhcList[listId+'_hide_dgroup'] !== 'undefined') {
            if ($lhcList[listId+'_hide_dgroup'] === true) {
                storeLocalSetting(listId + '_hide_dgroup', true);
            } else {
                removeLocalSetting(listId + '_hide_dgroup');
            }
        }

        if (typeof $lhcList['optionsPanels'][listId] !== 'undefined') {
            const panel = $lhcList['optionsPanels'][listId];
            if (panel.custom_filters && Array.isArray(panel.custom_filters)) {
                panel.custom_filters.forEach(function(custom_filter) {
                    const fieldKey = listId + '_' + custom_filter['field'];
                    
                    if (typeof $lhcList[fieldKey] !== 'undefined') {
                        if ($lhcList[fieldKey] === true) {
                            storeLocalSetting(fieldKey, true);
                        } else {
                            removeLocalSetting(fieldKey);
                        }
                    }
                });
            }
        }

        if ($lhcList[listId + '_all_departments'] === true)
        {
            let listNew = [];

            $lhcList.userDepartments.forEach(function(department) {
                if (
                    ($lhcList[listId+'_only_explicit_online'] === false || ($lhcList[listId+'_only_explicit_online'] === true && department.oexp === true)) &&
                    ($lhcList[listId+'_hide_hidden'] === false || ($lhcList[listId+'_hide_hidden'] === true && department.hidden === false)) &&
                    ($lhcList[listId+'_hide_disabled'] === false || ($lhcList[listId+'_hide_disabled'] === true && department.disabled === false)) &&
                    ($lhcList[listId+'_only_online'] === false || ($lhcList[listId+'_only_online'] === true && department.ogen === true))
                ) {
                    listNew.push(department.id);
                }
            });

            if (listNew.length === 0) {
                listNew.push(-1);
            }

            lhcLogic.lhcSettingAllSelected = true;

            $lhcList[listId] = listNew;

        } else {
            if (loadlList === true) {
                $lhcList[listId] = [];
            }
        }

        if (loadlList === true) {
            lhcLogic.isListLoaded = false;
            loadChatList();
        }
    }

    function persistenStoreSettings(listId, listValue) {
        lhcServices.setLocalSettings(listId, listValue).then(function() {
            removeItemFromProgressList(listId);
        }).catch(function() {
            removeItemFromProgressList(listId);
        });
    }

    function removeItemFromProgressList(listId) {
        lhcLogic.lhcSettingUpdateProgress.splice(lhcLogic.lhcSettingUpdateProgress.indexOf(listId),1);
    }

    function setDepartmentNames(listId) {
        $lhcList[listId + 'Names'] = [];
        if ($lhcList[listId]) {
            $lhcList[listId].forEach(function(value) {
            if (typeof $lhcList.userDepartmentsNames !== 'undefined' && typeof $lhcList.userDepartmentsNames[value] !== 'undefined') {
                $lhcList[listId + 'Names'].push($lhcList.userDepartmentsNames[value]);
            } else if (typeof $lhcList.userDepartmentsNames !== 'undefined') {
                $lhcList[listId].splice($lhcList[listId].indexOf(value),1);
                departmentChanged(listId);
            }});
        }
    }

     function getOpenedChatIds (listId) {
        if (localStorage) {
            try {
                var achat_id = localStorage.getItem(listId);

                if (achat_id !== null && achat_id !== '') {
                    return achat_id.split(',');
                }
            } catch(e) {

            }
        }
        return [];
    }

    function truncate(text, length, end) {
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
    }

    onMount(async() => {

        const rememberedTabs = jQuery('.nav-tabs[data-remember="true"], .nav-pills[data-remember="true"]');

        const activateTabByHash = () => {
            const hash = window.location.hash;
            if (hash && rememberedTabs.length) {
            const cleanHash = hash.replace('#!#','#').replace('#/','#');
            const tabLink = rememberedTabs.find('.nav-link[href="' + cleanHash + '"]');

            if (tabLink.length) {
                try {
                if (typeof tabLink.tab === 'function') {
                    tabLink.tab('show');
                } else {
                    const bsTab = new bootstrap.Tab(tabLink[0]);
                    bsTab.show();
                }
                } catch (e) {
                console.warn('Could not activate tab:', e);
                }
            }
            }
        };

        activateTabByHash();

        // Update URL hash when tab is clicked without scrolling
        rememberedTabs.find('.nav-link').on('shown.bs.tab', function (e) {
            history.replaceState(null, null, e.target.hash);
        });

        // Handle hash changes
        window.addEventListener('hashchange', activateTabByHash);

        $lhcList.departmentd_hide_dep = lhcServices.restoreLocalSetting('departmentd_hide_dep','false',false) != 'false';
        $lhcList.departmentd_hide_dgroup = lhcServices.restoreLocalSetting('departmentd_hide_dgroup','false',false) != 'false';
        $lhcList.lmtoggle = lhcServices.restoreLocalSetting('lmtoggle','false',false) != 'false';
        $lhcList.rmtoggle = lhcServices.restoreLocalSetting('rmtoggle','false',false) != 'false';
        lhcServices.getToggleWidget(lhcList,'track_open_chats');
        lhcServices.getToggleWidget(lhcList,'group_offline_chats');

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Control' || e.ctrlKey || e.key === 'Meta' || e.metaKey) {
                $lhcList.isCTRLPressed = true;
            }
        });

        document.addEventListener('keyup', function(e) {
            if (e.key === 'Control' || e.ctrlKey || e.key === 'Meta' || e.metaKey) {
                $lhcList.isCTRLPressed = false;
            }
        });

        // Reset the ctrl key state when window loses focus
        window.addEventListener('blur', function() {
            $lhcList.isCTRLPressed = false;
        });


        var appendURL = '';
        var openedChats = getOpenedChatIds('achat_id');
        var openedgChats = getOpenedChatIds('gachat_id');
        var openedmChats = getOpenedChatIds('machat_id');

        var chat_id = 0;
        var mail_id = 0;

        var hash = window.location.hash;
        if (hash !== '') {
            var matchData = hash.match(/id-\d+$/);

            if (matchData !== null && matchData[0]) {
                chat_id = parseInt(matchData[0].replace('id-',''));
                if (openedChats.indexOf(chat_id) === -1){
                    openedChats.push(chat_id);
                }
            }

            // Support mail chats hash in URL
            if (matchData == null) {
                var matchData = hash.match(/mc\d+$/);
                if (matchData !== null && matchData[0]) {
                    mail_id = parseInt(matchData[0].replace('mc', ''));
                    if (openedmChats.indexOf(mail_id) === -1) {
                        openedmChats.push(mail_id);
                    }
                }
            }
       }

        var elm = document.getElementById('load_chat_id');

        if (elm && openedChats.indexOf(elm.value) === -1) {
            chat_id = parseInt(elm.value);
            openedChats.push(elm.value);
            window.location.hash = '#!#chat-id-'+elm.value;
        }

        var elm = document.getElementById('load_mail_id');

        if (elm && openedmChats.indexOf(elm.value) === -1) {
            mail_id = parseInt(elm.value);
            openedmChats.push(elm.value);
            window.location.hash = '#!#chat-id-mc'+elm.value;
        }


        let tabsLength = jQuery('#tabs').length;

        if (tabsLength > 0 && lhinst.disableremember === false && openedChats.length > 0) {
            appendURL = '/(chatopen)/' + openedChats.join('/');
        }

        if (tabsLength > 0 && lhinst.disableremember === false && openedgChats.length > 0) {
            appendURL += '/(chatgopen)/' + openedgChats.join('/');
        }

        if (jQuery('#tabs').length > 0 && lhinst.disableremember == false && openedmChats.length > 0) {
            appendURL += '/(chatmopen)/' + openedmChats.join('/');
        }

        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/loadinitialdata' + appendURL, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            }
        });

        const data = await responseTrack.json();

        if (data.logout || data.error_url) {
            document.location.reload();
            return;
        }

        lhcList.update((list) => {
            list.userDepartmentsNames=data.dp_names;
            list.userDepartments=data.dep_list;
            list.userProductNames=data.pr_names;
            list.userDepartmentsGroups=data.dp_groups;
            list.userGroups = data.user_groups;
            list.hideInvisible = data.im;
            list.hideOnline = data.ho;
            list.lhcVersion = data.v;
            if (data.notice) {
                list.lhcNotice = data.notice;
            }
            if (data.message_connection) {
                list.lhcMessageConnection = data.message_connection;
            }
            list.alwaysOnline = data.a_on;
            list.additionalColumns = data.col;
            list.widgetsActive = data.widgets;
            list.bot_st = data.bot_st;
            list.excludeIcons = data.exc_ic;
            list.notifIcons = data.not_ic;
            return list;
        });

        let arraySettings = [
            'subjectd',
            'subjectd_products',
            'subjectd_dpgroups',
            'subjectd_ugroups',

            'activeu',
            'pendingu',
            'oopu',
            'subjectu',

            'oppu',
            'opp_ugroups',

            'closedd',
            'closedd_products',
            'closedd_dpgroups',

            'botd',
            'botd_products',
            'botd_dpgroups',
            'botd_ugroups',

            'pendingd',
            'pendingd_products',
            'pendingd_dpgroups',
            'pendingd_ugroups',

            'unreadd',
            'unreadd_products',
            'unreadd_dpgroups',

            'mcd',
            'mcd_products',
            'mcd_dpgroups',

            'actived',
            'actived_products',
            'actived_dpgroups',
            'actived_ugroups',

            'departmentd_dpgroups',
            'departmentd',

            'operatord_dpgroups',
            'operatord_ugroups',
            'operatord',

            'mmd',
            'mmd_dpgroups',

            'alarmmd',
            'alarmmd_products',
            'alarmmd_dpgroups',
            'alarmmd_ugroups',

            'activemd',
            'activemd_products',
            'activemd_dpgroups',
            'activemd_ugroups',

            'pendingmd',
            'pendingmd_products',
            'pendingmd_dpgroups',
            'pendingmd_ugroups',

            'pendingmu',
            'activemu',
            'alarmmu',

            'department_online',
            'department_online_dpgroups'

        ];

        var limitOptions = [
            'limitb',
            'limita',
            'limitu',
            'limitp',
            'limito',
            'limitc',
            'limitd',
            'limitmc',
            'limitgc',
            'limits',
            'limitpm',
            'limitam',
            'limitalm',
            'limitmm',
            'limitdp',
            'limitop',
        ];

        data.dw_filters && Object.keys(data.dw_filters).forEach(key => {
            if (arraySettings.indexOf(key) !== -1) {
                $lhcList[key] = restoreSettingByString(data.dw_filters[key], true);
            } else if (limitOptions.indexOf(key) !== -1) {
                $lhcList[key] = data.dw_filters[key];
            }
        });

        widgetsItems.forEach(function(listId) {
            setDepartmentNames(listId);
        });

        if (data.track_activity === 1)
        {
            lhcLogic.timeoutActivityTime = data.timeout_activity;
            setupActivityMonitoring();
        }

        data.copen.forEach(function(chatOpen) {
            lhinst.addOpenTrace('opened_chats');
            lhinst.ignoreAdminSync = true;
            lhinst.startChat(chatOpen.id, jQuery('#tabs'),truncate((chatOpen.nick || 'Visitor'),10), (chatOpen.id === chat_id), 0, chatOpen.status);
            lhinst.ignoreAdminSync = false;
            addAction({'type':'mac_history', 'chat_id': chatOpen.id, 'nick': chatOpen.nick});
            if (chatOpen.id === chat_id) {
                document.getElementById('tabs').classList.add('chat-tab-selected');
            }
        });

        data.cgopen.forEach(function(chatOpen) {
            lhinst.startGroupChat(chatOpen.id,jQuery('#tabs'),truncate((chatOpen.nick || 'Visitor'),10), true);
        });

        data.cmopen.forEach(function(chatOpen) {
            lhinst.startMailChat(chatOpen.id,jQuery('#tabs'),truncate(chatOpen.subject || 'Mail',10), !(chatOpen.id === mail_id));
        });

        data.cmdel.forEach(function(chatOpen) {
            lhinst.forgetChat(chatOpen,'machat_id');
        });

        data.cdel.forEach(function(chatOpen) {
            lhinst.forgetChat(chatOpen,'achat_id');
        });

        data.cgdel.forEach(function(chatOpen) {
            lhinst.forgetChat(chatOpen,'gachat_id');
        });

        ee.emitEvent('eventLoadInitialData', [data, $lhcList, lhcServices]);

        window['$_LHC_API']['initial_data'] = data;

        lhcList.update((list) => {
            list.lhcCoreLoaded = true;
            return list;
        });
        // Verify that filter attribute are existing
        // Let say some user was removed, but visitor still had it as filter.
        // This would couse situtation then filter is applied but operator cannot remove it
        // We have to take care of this situtations.
        verifyFilters();

        loadChatList();
    });

    function addAction(data) {
        $lhcList.last_actions.unshift(data);
        $lhcList.last_actions = $lhcList.last_actions.slice(0, 5);
    }

    async function verifyFilters () {

        var userList = [], userGroups = [], userDepartmentsGroups = [], userProductNames = [];

        $lhcList.userGroups.forEach(function(value) {
            userGroups.push(value.id);
        });

        $lhcList.userDepartmentsGroups.forEach(function(value) {
            userDepartmentsGroups.push(value.id);
        });

        $lhcList.userProductNames.forEach(function(value) {
            userProductNames.push(value.id);
        });

        const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/searchprovider/users_ids/?exclude_disabled=1&q=' + $lhcList.pendingu.join(',') +','+ $lhcList.activeu.join(',')+','+ $lhcList.subjectu.join(',')+','+ $lhcList.oopu.join(',')+','+$lhcList.pendingmu.join(',')+','+ $lhcList.activemu.join(',')+','+ $lhcList.alarmmu.join(',')+','+ $lhcList.oppu.join(','), {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRFToken": confLH.csrf_token
            }
        }).catch((error) => {
            // Your error is here!
            // alert('We could not change your status! ' + error);
        });

        const data = await responseTrack.json();

            $lhcList.userList = data.items;

            $lhcList.userList.forEach(function(value) {
                userList.push(value.id);
            });

            var verifyCombinations = {
                'activeu' : userList,
                'actived_products' : userProductNames,
                'actived_ugroups' : userGroups,
                'actived_dpgroups' : userDepartmentsGroups,

                'pendingu' : userList,
                'oopu' : userList,
                'oppu' : userList,
                'opp_ugroups' : userGroups,
                'pendingd_ugroups' : userGroups,
                'operatord_ugroups' : userGroups,
                'pendingd_dpgroups' : userDepartmentsGroups,
                'pendingd_products' : userProductNames,

                'botd_dpgroups' : userDepartmentsGroups,
                'botd_products' : userProductNames,

                'departmentd_dpgroups' : userDepartmentsGroups,

                'closedd_products' : userProductNames,
                'closedd_dpgroups' : userDepartmentsGroups,

                'unreadd_dpgroups' : userDepartmentsGroups,
                'unreadd_products' : userProductNames,

                'mcd_products' : userProductNames,
                'mcd_dpgroups' : userDepartmentsGroups,

                'mmd_dpgroups' : userDepartmentsGroups,

                'operatord_dpgroups' : userDepartmentsGroups
            };

            for (const [index, list] of Object.entries(verifyCombinations)) {
                var originalList = [...$lhcList[index]];
                originalList.forEach(function(value) {
                    if (list.indexOf(value) === -1) {
                        $lhcList[index].splice($lhcList[index].indexOf(value),1);
                        productChanged(index);
                    }
                });
            }
    }

    function productChanged(listId) {

        if ($lhcList[listId].length > 0) {

            var listValue = $lhcList[listId].join("/");

            if (listValue !== '') {
                lhcServices.setLocalSettings(listId, listValue);
            }

        } else {
            lhcServices.setLocalSettings(listId, null);
        }

        lhcLogic.isListLoaded = false;
        loadChatList();
    }

    let addEvent = (function () {
        if (document.addEventListener) {
            return function (el, type, fn) {
                if (el && el.nodeName || el === window) {
                    el.addEventListener(type, fn, false);
                } else if (el && el.length) {
                    for (var i = 0; i < el.length; i++) {
                        addEvent(el[i], type, fn);
                    }
                }
            };
        } else {
            return function (el, type, fn) {
                if (el && el.nodeName || el === window) {
                    el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
                } else if (el && el.length) {
                    for (var i = 0; i < el.length; i++) {
                        addEvent(el[i], type, fn);
                    }
                }
            };
        }
    })();

    function setupActivityMonitoring() {

        var resetTimeout = function() {
            resetTimeoutActivity();
        };

        addEvent(window,'mousemove',resetTimeout);
        addEvent(document,'mousemove',resetTimeout);
        addEvent(window,'mousedown',resetTimeout);
        addEvent(window,'click',resetTimeout);
        addEvent(window,'scroll',resetTimeout);
        addEvent(window,'keypress',resetTimeout);
        addEvent(window,'load',resetTimeout);
        addEvent(document,'scroll',resetTimeout);
        addEvent(document,'touchstart',resetTimeout);
        addEvent(document,'touchend',resetTimeout);

        resetTimeoutActivity();
    }

    function resetTimeoutActivity() {
        var opener = window.opener;
        if (opener) {
            try {
                // Forward action to parent window and do not set offline mode from child window
                /*var lhcController = opener.angular.element('body').scope();
                lhcController.resetActivityFromChild();*/
                opener.ee.emitEvent('svelteResetTimeoutActivity');
            } catch(e) {
                console.log(e);
            }
        } else {
            if (lhcLogic.blockSync === false)
            {
                lhcLogic.lastd_activity = Math.round(new Date().getTime()/1000);

                clearTimeout(lhcLogic.timeoutActivity);

                lhcLogic.timeoutActivity = setTimeout(function(){

                    lhcServices.setInactive('true').then(function (data) {

                        // Operator is active in another tab/window
                        if (data.active === true) {
                            resetTimeoutActivity();
                            lhcLogic.lastd_activity = 0;
                            return ;
                        }

                        lhinst.disableSync = lhcLogic.blockSync = true;
                        lhcLogic.lhcListRequestInProgress = false; // Request can be send either way

                        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'user/wentinactive/false', 'backdrop': true, hidecallback: function() {
                            setActiveInterface();
                            lhcLogic.channel.postMessage({'action':'went_active','args':{}});
                        }});

                    });

                }, lhcLogic.timeoutActivityTime*1000);
            }
        }
    }

    function getSyncFilter()
    {
        $lhcList.custom_extension_filter = '';

        var filter = '';
        ['limita','limitu','limitp','limito','limitc','limitd','limits','limitmc','limitb','limitgc','limitmm','limitpm','limitam','limitalm','limitdp','limitop'].forEach(k => {
            filter += '/(' + k + ')/' + parseInt($lhcList[k]);
        });

        if (typeof $lhcList.widgetsActive == 'object' && $lhcList.widgetsActive.length > 0) {
            let map = {
                'my_chats' : 0 ,
                'online_operators' : 1,
                'group_chats' : 2,
                'pending_chats' : 3,
                'online_visitors' : 4,
                'unread_chats' : 5,
                'active_chats' : 6,
                'bot_chats' : 7,
                'transfered_chats' : 8,
                'departments_stats' : 9,
                'subject_chats' : 20,
                'pmails' : 10,
                'amails' : 11,
                'malarms' : 12,
                'my_mails' : 30,
                'dep_performance' : 31,
                'op_performance' : 32
            }
            let activeWidgets = [];
            $lhcList.widgetsActive.forEach(function(widget) {
                map[widget] && activeWidgets.push(map[widget]);
            })
            filter += '/(w)/'+activeWidgets.join('/');
        }

        [
            ['activeu',             'activeu'],
            ['pendingu',            'pendingu'],
            ['pendingmu',           'pendingmu'],
            ['activemu',            'activemu'],
            ['alarmmu',             'alarmmu'],
            ['oopu',                'oopu'],
            ['oppu',                'oppu'],
            ['opp_ugroups',         'oppugroups'],
            ['subjectu',            'subjectu'],
            ['actived_dpgroups',    'adgroups'],
            ['pendingmd_dpgroups',  'pmd'],
            ['activemd_dpgroups',   'amd'],
            ['alarmmd_dpgroups',    'almd'],
            ['pendingd_dpgroups',   'pdgroups'],
            ['subjectd_dpgroups',   'sdgroups'],
            ['closedd_dpgroups',    'cdgroups'],
            ['botd_dpgroups',       'bdgroups'],
            ['mcd_dpgroups',        'mdgroups'],
            ['mmd_dpgroups',        'mmdgroups'],
            ['unreadd_dpgroups',    'udgroups'],
            ['departmentd_dpgroups','ddgroups'],
            ['operatord_dpgroups',  'odpgroups'],
            ['closedd',             'closedd'],
            ['actived_products',    'activedprod'],
            ['pendingd_ugroups',    'pugroups'],
            ['operatord_ugroups',   'oopugroups'],
            ['subjectd_ugroups',    'sugroups'],
            ['actived_ugroups',     'augroups'],
            ['pendingmd_ugroups',   'pmug'],
            ['activemd_ugroups',    'amug'],
            ['alarmmd_ugroups',     'almug'],
            ['mcd_products',        'mcdprod'],
            ['unreadd_products',    'unreaddprod'],
            ['pendingd_products',   'pendingdprod'],
            ['subjectd_products',   'subjectdprod'],
            ['botd_products',       'botdprod'],
            ['closedd_products',    'closeddprod'],
        ].forEach(([key, param]) => {
            if (typeof $lhcList[key] == 'object' && $lhcList[key].length > 0) {
                filter += '/(' + param + ')/' + $lhcList[key].join('/');
            }
        });

        ['actived','subjectd','mcd','mmd','pendingmd','activemd','alarmmd','unreadd','botd','pendingd','operatord','departmentd'].forEach(key => {
            if (typeof $lhcList[key] == 'object') {
                const items = $lhcList[key].length > 0 ? $lhcList[key] : manualFilterByFilter(key);
                if (items.length > 0) {
                    filter += '/(' + key + ')/' + items.join('/');
                }
                if (key === 'pendingd' && $lhcList.toggleWidgetData['pending_chats_sort'] !== 'undefined' && $lhcList.toggleWidgetData['pending_chats_sort'] !== '') {
                    filter += '/(psort)/' + $lhcList.toggleWidgetData['pending_chats_sort'];
                }
            }
        });

        if ($lhcList.toggleWidgetData['track_open_chats'] == true) {
            filter += '/(topen)/true';
        }

        [
            ['active_chats_sort',  'acs'],
            ['bot_chats_sort',     'bcs'],
            ['closed_chats_sort',  'clcs'],
            ['onop_sort',          'onop']
        ].forEach(([key, param]) => {
            if (typeof $lhcList.toggleWidgetData[key] !== 'undefined' && $lhcList.toggleWidgetData[key] !== '') {
                filter += '/(' + param + ')/' + $lhcList.toggleWidgetData[key];
            }
        });

        widgetsItems.forEach(function(listId) {
            if ($lhcList[listId + '_mslf'] === 'true' || $lhcList[listId + '_mslf'] === true) {
                filter += '/(' + listId + '_mslf' + ')/1';
            }
        });

        if (typeof $lhcList['optionsPanels'] !== 'undefined') {
            for (const [panelId, panel] of Object.entries($lhcList['optionsPanels'])) {
                if (panel.custom_filters && Array.isArray(panel.custom_filters)) {
                    panel.custom_filters.forEach(function(custom_filter) {
                        const fieldKey = panelId + '_' + custom_filter['field'];
                        const storedValue = lhcServices.restoreLocalSetting(fieldKey, 'false', false);
                        $lhcList[fieldKey] = storedValue != 'false';
                        
                        // Add to URL filter if the custom filter is enabled
                        if ($lhcList[fieldKey] === true) {
                            filter += '/(' + custom_filter['field'] + ')/1';
                        }
                    });
                }
            }
        }

        // What subelements of widgets should be hidden
        // At the moment only departments widget users it
        var hsub = [];
        $lhcList.departmentd_hide_dep === true && hsub.push('dhdep');
        $lhcList.departmentd_hide_dgroup === true && hsub.push('dhdepg');

        if (hsub.length > 0) {
            filter += '/(hsub)/'+hsub.join('/');
        }

        // Last dynamic activity
        if (lhcLogic.lastd_activity > 0) {
            filter += '/(lda)/'+lhcLogic.lastd_activity;
        }

        lhcLogic.lastd_activity = 0;

        ee.emitEvent('eventGetSyncFilterSvelte', [$lhcList]);

        filter += $lhcList.custom_extension_filter;

        return filter;
    }

    function manualFilterByFilter(listId) {
        if ($lhcList[listId+'_only_explicit_online'] == true || $lhcList[listId+'_hide_hidden'] == true || $lhcList[listId+'_hide_disabled'] == true || $lhcList[listId+'_only_online'] == true) {

            if ($lhcList.userDepartments.length > 0) {
                var listDepartments = [];
                $lhcList.userDepartments.forEach(function(department) {
                    if (
                        ($lhcList[listId+'_only_explicit_online'] == false || ($lhcList[listId+'_only_explicit_online'] == true && department.oexp == true)) &&
                        ($lhcList[listId+'_hide_hidden'] == false || ($lhcList[listId+'_hide_hidden'] == true && department.hidden == false)) &&
                        ($lhcList[listId+'_hide_disabled'] == false || ($lhcList[listId+'_hide_disabled'] == true && department.disabled == false)) &&
                        ($lhcList[listId+'_only_online'] == false || ($lhcList[listId+'_only_online'] == true && department.ogen == true))
                    ) {
                        listDepartments.push(department.id);
                    }
                });

                if (listDepartments.length == 0) {
                    listDepartments.push(-1);
                };

                return listDepartments;
            }
        }

        return [];
    }


    async function loadChatList() {

        if (localStorage) {
            try {
                $lhcList.pending_chats_expanded = localStorage.getItem('pending_chats_expanded') != 'false';
                $lhcList.active_chats_expanded = localStorage.getItem('active_chats_expanded') != 'false';
                $lhcList.my_active_chats_expanded = localStorage.getItem('my_active_chats_expanded') != 'false';
                $lhcList.closed_chats_expanded = localStorage.getItem('closed_chats_expanded') != 'false';
                $lhcList.unread_chats_expanded = localStorage.getItem('unread_chats_expanded') != 'false';
                $lhcList.my_chats_expanded = localStorage.getItem('my_chats_expanded') != 'false';
                $lhcList.pending_mails_expanded = localStorage.getItem('pending_mails_expanded') != 'false';
                $lhcList.active_mails_expanded = localStorage.getItem('pending_mails_expanded') != 'false';
                $lhcList.my_mails_expanded = localStorage.getItem('my_mails_expanded') != 'false';

                // Just for extension reserved keywords
                $lhcList.custom_list_1_expanded = localStorage.getItem('custom_list_1_expanded') != 'false';
                $lhcList.custom_list_2_expanded = localStorage.getItem('custom_list_2_expanded') != 'false';
                $lhcList.custom_list_3_expanded = localStorage.getItem('custom_list_3_expanded') != 'false';
                $lhcList.custom_list_4_expanded = localStorage.getItem('custom_list_4_expanded') != 'false';
            } catch(err) {

            };
        }

        if (lhcLogic.blockSync === true) {
            clearTimeout(lhcLogic.timeoutControl);
            lhcLogic.timeoutControl = setTimeout(function(){
                lhcLogic.isListLoaded = false;
                lhcLogic.lhcListRequestInProgress = false;
                loadChatList();
            },confLH.back_office_sinterval);
            lhcLogic.lhcListRequestInProgress = false;
            return;
        }

        if (lhcLogic.lhcListRequestInProgress === true) {
            return;
        }

        $lhcList.lhcListRequestInProgress = lhcLogic.lhcListRequestInProgress = true;

        clearTimeout(lhcLogic.timeoutControl);

        try {
            const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/syncadmininterface' + getSyncFilter(), {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                signal: lhcLogic.abortController.signal
            });

            if (!responseTrack.ok) {
                throw new Error("Network response was not OK [" + responseTrack.status + "] ["+ responseTrack.statusText+"]");
            }

            const data = await responseTrack.json();

            lhcLogic.lhcListRequestInProgress = false;

            if (data.logout || data.error_url) {
                document.location.reload();
                return;
            }

            if (lhcLogic.blockSync == true) {
                $lhcList.lhcBLockSync = true;
                clearTimeout(lhcLogic.timeoutControl);
                lhcLogic.timeoutControl = setTimeout(function(){
                    lhcLogic.isListLoaded = false;
                    loadChatList();
                },confLH.back_office_sinterval);
                return;
            }

            if ($lhcList.lhcConnectivityProblem == true) {
                $lhcList.lhcConnectivityProblem = false;
            }

            ee.emitEvent('eventLoadChatListSvelte', [data, $lhcList, lhcLogic]);

            if (typeof data.items_processed == 'undefined') {

                var currentStatusNotifications = []; // Holds current status of chat's list,  _that.statusNotifications previous status

                var chatsToNotify = []; // Holds chat's to notify about for particular last_id_identifier item

                var notificationsData = [], notificationDataAccept = []; // Holds chat's to notify for all lists

                var tabs = jQuery('#tabs');

                for (const [key, item] of Object.entries(data.result)) {

                    if ($lhcList.suspend_widgets.indexOf(key) !== -1) {
                        continue;
                    }

                    $lhcList[key] = item;

                    if (tabs.length == 0 && (key == 'pending_chat' || key == 'my_chats')) {
                        item.list.forEach(function (chat) {
                            if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_chats == 1 && (chat.status !== 1 || (chat.status === 1 && chat.hum === true))) {
                                ee.emitEvent('chatTabPreload', [chat.id, {focus: false}]);
                            }
                        });
                    }

                    if (tabs.length > 0) {
                        if (key == 'pending_chat' || key == 'my_chats') {
                            item.list.forEach(function (chat) {
                                if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_chats == 1 && (chat.status !== 1 || (chat.status === 1 && chat.hum === true))) {
                                    if (jQuery('#chat-tab-link-' + chat.id).length == 0) {

                                        if (tabs.length > 0 && lhinst.disableremember == false) {
                                            lhinst.addOpenTrace('auto_accept_' + chat.id);
                                            lhinst.removeSynchroChat(chat.id);
                                            lhinst.startChatBackground(chat.id, tabs, truncate((chat.nick || 'Visitor'), 10));
                                            // We auto open only auto assigned chats
                                            lhcLogic.channel.postMessage({'action':'startbackground_chat','args':{'nick': truncate((chat.nick || 'Visitor'), 10), 'chat_id' : parseInt(chat.id)}});
                                            ee.emitEvent('eventLoadChatListSvelteAutoOpen', [chat, lhcLogic]);
                                        }

                                        if (lhinst.disableremember == false) {
                                            notificationDataAccept.push(chat.id);
                                        }
                                    }
                                }
                            });
                        } else if (key == 'transfer_chats') {
                            item.list.forEach(function (chat) {
                                if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && (confLH.accept_chats == 1 || jQuery('#chat-tab-link-' + chat.id).length > 0)) {
                                    if (tabs.length > 0 && lhinst.disableremember == false) {
                                        lhinst.addOpenTrace('auto_accept_transfer_' + chat.id);
                                        lhinst.startChatTransfer(chat.id,tabs,truncate((chat.nick || 'Visitor'),10),chat.transfer_id, jQuery('#chat-tab-link-' + chat.id).length == 0);

                                        // Auto open transfered chats in all tabs
                                        lhcLogic.channel.postMessage({'action':'startbackground_chat','args':{'nick': truncate((chat.nick || 'Visitor'),10), 'chat_id' : parseInt(chat.id)}});
                                    }
                                    if (lhinst.disableremember == false) {
                                        notificationDataAccept.push(chat.id);
                                    }
                                }
                            });
                        } else if (key == 'group_chats') {
                            if (tabs.length > 0 && confLH.auto_join_private  == 1) {
                                item.list.forEach(function (chat) {
                                    if (chat.type == 1 && chat.jtime == 0 && jQuery('#chat-tab-link-gc' + chat.id).length == 0) {
                                        lhinst.startGroupChat(chat.id,tabs,truncate(chat.name,10),true);
                                    }
                                });
                            }
                        } else if (key == 'support_chats') {
                            if (tabs.length > 0 && confLH.auto_join_private  == 1) {
                                item.list.forEach(function (chat) {
                                    // Operator does not have this chat in their account yet
                                    if (document.getElementById('chat-tab-li-'+chat.chat_id) === null) {
                                        lhinst.addOpenTrace('support_chat');
                                        startChatByID(chat.chat_id, true);
                                    } else if (!jQuery('#private-chat-tab-link-'+chat.chat_id).attr('private-loaded')) {
                                        jQuery('#private-chat-tab-link-'+chat.chat_id).attr('private-loaded',true);
                                        ee.emitEvent('privateChatStart', [chat.chat_id,{'unread': true}])
                                    } else if (!jQuery('#chat-tab-link-'+chat.chat_id).hasClass('active')) {
                                        jQuery('#chat-tab-link-'+chat.chat_id+' > .whatshot').removeClass('d-none');
                                    } else if (!jQuery('#private-chat-tab-link-'+chat.chat_id).hasClass('active')) {
                                        jQuery('#private-chat-tab-link-'+chat.chat_id+' > .whatshot').removeClass('d-none');
                                    }
                                });
                            }
                        } else if (key == 'pending_mails' || key == 'my_mails') {
                            if (tabs.length > 0) {
                                item.list.forEach(function (chat) {
                                    if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_mails == 1 && chat.status !== 1 && document.getElementById('chat-tab-li-mc'+chat.id) === null) {
                                        lhinst.startMailChat(chat.id,tabs,truncate(chat.subject_front,10),true);
                                    }
                                });
                            }
                        }
                    }

                    if ( item.last_id_identifier) {
                        chatsToNotify = [];

                        currentStatusNotifications = [];

                        var chatsSkipped = 0; // Do not show notification for chats if they appear at the bottom, only applies to unassigned chats

                        var itemsList = item.list;
                        if (item.last_id_identifier == 'pending_chat' && typeof $lhcList.toggleWidgetData['pending_chats_sort'] !== 'undefined' && $lhcList.toggleWidgetData['pending_chats_sort'] == 'id_asc') {
                            itemsList = item.list.slice().reverse();
                        }

                        itemsList.forEach(function(itemList) {

                            var userId = (typeof itemList.user_id !== 'undefined' ? itemList.user_id : 0);

                            var identifierElement = itemList.id + '_' + userId;

                            // No need to store anything as chat is still not notifable
                            if (item.last_id_identifier == 'bot_chats') {
                                if (!((itemList.msg_v && itemList.msg_v > $lhcList.bot_st.msg_nm && $lhcList.bot_st.bot_notifications == 1) || itemList.aalert)) {
                                    return;
                                }
                            }

                            // Don't show notification for transfered chats
                            if (item.last_id_identifier == 'transfer_chat_dep') {
                                return;
                            }

                            var alertIcons = [];

                            // Active chats notifications are done by appending alert icons
                            if (item.last_id_identifier == 'active_chats') {
                                if (itemList.aicons) {
                                    alertIcons = Object.keys(itemList.aicons);
                                    identifierElement += '_' + alertIcons.join('_');
                                }
                            }

                            currentStatusNotifications.push(identifierElement);

                            if (typeof $lhcList.statusNotifications[item.last_id_identifier] == 'undefined') {
                                $lhcList.statusNotifications[item.last_id_identifier] = [];
                            }

                            if (lhcLogic.isListLoaded == true && item.last_id_identifier == 'subject_chats') {
                                if ($lhcList.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && lhinst.chatsSynchronising.indexOf(parseInt(itemList.id)) === -1) {
                                    chatsToNotify.push(itemList.id);
                                }
                            } else if (lhcLogic.isListLoaded == true && item.last_id_identifier == 'active_chats') {
                                if ($lhcList.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && alertIcons.length > 0 && $lhcList.notifIcons.length > 0) {
                                    var iconsMonitoring = $lhcList.notifIcons.filter(function(n) {
                                        return $lhcList.excludeIcons.indexOf(n) === -1 && alertIcons.indexOf(n) !== -1;
                                    })

                                    // Operator is monitoring this notification icon
                                    if (iconsMonitoring.length > 0) {
                                        chatsToNotify.push(itemList.id + '__' + iconsMonitoring.join('__'));
                                    }
                                }
                            } else if ( 
                                lhcLogic.isListLoaded == true && 
                                (item.last_id_identifier == 'bot_chats' || chatsSkipped == 0 || itemList.status_sub_sub === 2) && 
                                (
                                        (
                                            $lhcList.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && 
                                            (userId == 0 || item.last_id_identifier == 'amails') && 
                                            confLH.ownntfonly == 0
                                        ) 
                                    || 
                                        (
                                            $lhcList.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && 
                                            userId == confLH.user_id
                                        )
                                )
                            ) {
                                if (lhinst.chatsSynchronising.indexOf(parseInt(itemList.id)) === -1) { // Don't show notification if chat is under sync already
                                    chatsToNotify.push(itemList.id);
                                }
                            } else {
                                chatsSkipped++;
                            }
                        });

                        if (chatsToNotify.length > 0) {
                            chatsToNotify.unshift(item.last_id_identifier);
                            notificationsData.push(chatsToNotify.join("/"));
                        }

                        if (lhcLogic.isListLoaded === true) {
                            compareNotificationsAndHide($lhcList.statusNotifications[item.last_id_identifier],currentStatusNotifications,item.last_id_identifier);
                        }

                        $lhcList.statusNotifications[item.last_id_identifier] = currentStatusNotifications;
                    }
                }

                if (notificationDataAccept.length > 0) {
                    notificationDataAccept.unshift('active_chat');
                    lhcServices.getNotificationsData(notificationDataAccept.join("/")).then(function (data) {
                        data.forEach(function (item) {
                            lhinst.removeBackgroundChat(parseInt(item.last_id));
                            lhinst.playSoundNewAction(item.last_id_identifier,parseInt(item.last_id),(item.nick ? item.nick : 'Live Help'),(item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
                            lhinst.backgroundChats.push(parseInt(item.last_id));
                        });
                    });
                }

                if (notificationsData.length > 0) {
                    lhcServices.getNotificationsData(notificationsData.join("/")).then(function (data) {
                        data.forEach(function (item) {
                            lhinst.playSoundNewAction(item.last_id_identifier,parseInt(item.last_id),(item.nick ? item.nick : 'Live Help'),(item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
                        });
                    });
                }
            }

            if (typeof data.ou !== 'undefined' && data.ou == 1) {
                jQuery('#lhc_op_operation').remove();
                var th = document.getElementsByTagName('head')[0];
                var s = document.createElement('script');
                s.setAttribute('id','lhc_op_operation');
                s.setAttribute('type','text/javascript');
                s.setAttribute('src',WWW_DIR_JAVASCRIPT + 'chat/loadoperatorjs');
                th.appendChild(s);
            }

            if (typeof data.fs !== 'undefined' && data.fs.length > 0) {
                data.fs.forEach(function(item) {
                    lhinst.playSoundNewAction('pending_transfered',parseInt(item.id),(item.nick ? item.nick : 'Live Help'), confLH.transLation.transfered, item.nt, item.uid);
                });
            }

            if (typeof data.mac !== 'undefined' && data.mac.length > 0) {
                var tabs = jQuery('#tabs');
                if (tabs.length > 0 && lhinst.disableremember == false) {
                    data.mac.forEach(function(item) {
                        lhinst.startChatBackground(item.id,tabs,truncate((item.nick || 'Visitor'),10),false);
                        addAction({'type':'mac', 'chat_id': item.id, 'nick': item.nick});
                    });
                }
            }

            lhcList.update((list) => {
                list.lhcUpdatedAt = data.uat;
                list.lhcVersion = data.v;
                list.hideOnline = data.ho === 1;
                list.hideInvisible = data.im === 1;
                list.alwaysOnline = data.a_on === 1;
                list.inActive = data.ina === 1;
                list.lhcListRequestInProgress = false;
                list.lhcBLockSync = false;

                if (data.aus.update_required) {
                    if (data.aus.updated === 0) {
                        list.lhcConnectivityProblem = true;
                        list.lhcConnectivityProblemExplain = 'Last activity update failed!';
                    } else {
                        list.lhcConnectivityProblem = false;
                        list.lhcUpdatedAtActivity = data.aus.updated;
                    }
                } else {
                    list.lhcUpdatedAtActivity = data.aus.updated;
                }

                return list;
            });

            if (lhcLogic.setTimeoutEnabled === true) {
                lhcLogic.timeoutControl = setTimeout(function(){
                    loadChatList();
                },confLH.back_office_sinterval);
            }

            lhcLogic.isListLoaded = true;

        } catch (error) {

            if (!(error instanceof DOMException)) {
                $lhcList.lhcConnectivityProblem = true;
                $lhcList.lhcConnectivityProblemExplain = error;
                console.error("There has been a problem with your fetch operation:", error);
            } else {
                $lhcList.lhcConnectivityProblem = true;
                $lhcList.lhcConnectivityProblemExplain = 'DOM Exception';
            }

            lhcLogic.lhcListRequestInProgress = false;

            lhcLogic.timeoutControl = setTimeout(function(){
                loadChatList();
            },confLH.back_office_sinterval);
        }


    }

    function startChatByID(chat_id, background) {
        if (!isNaN(chat_id)) {
            if (jQuery('#tabs').length > 0) {
                /*jQuery('#menu-chat-options').dropdown('toggle'); Why this one was here*/
                lhcServices.getChatData(chat_id).then(function(data) {
                    if (data.r) {
                        document.location = WWW_DIR_JAVASCRIPT + data.r;
                        return;
                    }
                    lhinst.addOpenTrace('start_chat_by_id');
                    if (!background) {
                        startChat(parseInt(chat_id),truncate((data.nick || 'Visitor'),10));
                    } else {
                        lhinst.startChatBackground(parseInt(chat_id), jQuery('#tabs'), truncate((data.nick || 'Visitor'),10),'backgroundid');
                    }
                });
            }
        }
    }

    function startChat(chat_id,name) {
        if (jQuery('#tabs').length > 0) {
            lhinst.addOpenTrace('click');
            return lhinst.startChat(chat_id,jQuery('#tabs'),truncate((name || 'Visitor'),10));
        } else {
            lhinst.startChatNewWindow(chat_id,name);
        }
    }

    function startMailChat(chat_id, name) {
        if (jQuery('#tabs').length > 0) {
            return lhinst.startMailChat(chat_id,jQuery('#tabs'),truncate(name || 'Mail',10));
        }
    }

    function compareNotificationsAndHide(oldStatus, newStatus, type) {
        if (typeof oldStatus !== 'undefined') {
            for (var i = oldStatus.length - 1; i >= 0; i--) {
                var key = oldStatus[i];
                if (-1 === newStatus.indexOf(key)) {
                    lhinst.hideNotification(key.split('_')[0], type);
                }
            }
        }
    }

    function syncDisabled(disabled) {
        lhcLogic.blockSync = disabled;
        if (lhcLogic.blockSync == true) {
            lhcLogic.abortController.abort();
            lhcLogic.abortController = new AbortController();
        }
    }


</script>