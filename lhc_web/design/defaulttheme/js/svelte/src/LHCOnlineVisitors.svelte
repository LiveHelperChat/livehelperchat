<svelte:options customElement={{
		tag: 'lhc-online-visitors',
		shadow: 'none'}}/>
<script>
    import { lhcOnlineVisitors, lhcList} from './stores.js';

    import { onMount } from 'svelte';
    import lhcServices from './lib/Services.js';

    export let www_dir_flags = "";
    export let track_is_online = false;
    export let soundEnabled;
    export let notificationEnabled;
    export let online_connected;
    export let max_rows;
    export let timeout = "3600";
    export let country = "none";
    export let forbiddenVisitors = "false";
    export let updateTimeout = 10;
    export let time_on_site = "";
    export let online_check = null;
    export let groupByField;

    let lhcLogic = {
        timeoutVisitors: null,
        department_dpgroups: [],
        department: [],
        time_on_site: time_on_site,
        country: country,
        max_rows: parseInt(max_rows),
        updateTimeout: parseInt(updateTimeout),
        timeout: timeout,
        lhcListRequestInProgress: false,
        timeoutControl: null,
        forbiddenVisitors : forbiddenVisitors === 'true',
        soundEnabled : soundEnabled === 'true',
        notificationEnabled : notificationEnabled === 'true',
        lastSyncSkipped : false,
        attrf_key_1 : "",
        attrf_val_1 : "",
        attrf_key_2 : "",
        attrf_val_2 : "",
        attrf_key_3 : "",
        attrf_val_3 : "",
        attrf_key_4 : "",
        attrf_val_4 : "",
        attrf_key_5 : "",
        attrf_val_5 : "",
        query : "",
        onlineusersPreviousID : [],
        wasInitiated : false,
        online_connected : online_connected === 'true',
        predicate : 'last_visit',
        reverse : true,
        groupByField : groupByField
    };

    ee.addListener('svelteOnlineUserSetting',function (settingName, value) {
        if (settingName == 'disableNewUserBNotif') {
            lhcLogic.notificationEnabled = !lhcLogic.notificationEnabled;
            lhinst.changeUserSettings('new_user_bn', lhcLogic.notificationEnabled == true ? 1 : 0);
            setInnerText('disableNewUserBNotif',lhcLogic.notificationEnabled ? 'visibility' : 'visibility_off');
        } else if (settingName == 'disableNewUserSound') {
            lhcLogic.soundEnabled = !lhcLogic.soundEnabled;
            lhinst.changeUserSettings('new_user_sound',lhcLogic.soundEnabled == true ? 1 : 0);
            setInnerText('disableNewUserSound',lhcLogic.soundEnabled ? 'volume_up':'volume_off');
        } else if (settingName == 'showConnected') {
            lhcLogic.online_connected = !lhcLogic.online_connected;
            lhinst.changeUserSettings('online_connected',lhcLogic.online_connected == true ? 1 : 0);
            setInnerText('showConnected',lhcLogic.online_connected ? 'flash_on' : 'flash_off');
        } else if (settingName == 'countryFilter') {
            lhcLogic.country = value;
            lhinst.changeUserSettingsIndifferent('ocountry',value);
            setTimeout(() => syncOnlineVisitors(),500);
        } else if (settingName == 'maxrowsFilter') {
            lhinst.changeUserSettingsIndifferent('omax_rows',value);
            lhcLogic.max_rows = parseInt(value);
            setTimeout(() => syncOnlineVisitors(),500);
        } else if (settingName == 'userTimeoutFilter') {
            lhinst.changeUserSettingsIndifferent('ouser_timeout',value);
            lhcLogic.timeout = parseInt(value);
            setTimeout(() => syncOnlineVisitors(),500);
        } else if (settingName == 'updateTimeoutFilter') {
            lhinst.changeUserSettingsIndifferent('oupdate_timeout',value);
            lhcLogic.updateTimeout = parseInt(value);
            setTimeout(() => syncOnlineVisitors(),500);
        } else if (settingName == 'timeOnSiteFilter') {
            lhinst.changeUserSettingsIndifferent('otime_on_site',value == '' ? 'none' : value);
            lhcLogic.time_on_site = value;
            setTimeout(() => syncOnlineVisitors(),500);
        } else if (settingName == 'groupBy') {
            lhcLogic.groupByField = value;
            lhinst.changeUserSettingsIndifferent('ogroup_by',value);
            setTimeout(() => syncOnlineVisitors(),500);
        } else if (settingName == 'setQuery') {
            lhcLogic.query = value;
        } else if (settingName == 'departments') {
            let departments = [];
            jQuery('.online-department-filter input[name^=department_ids]').each(function(i){
                departments.push(parseInt(this.value));
            });
            lhcLogic.department = departments;
            setTimeout(() => syncOnlineVisitors(),500);
            lhcServices.setLocalSettings('department_online', lhcLogic.department);
        }
    });

    ee.addListener('svelteOnlineUserSettingKey',function (key, value) {
        lhinst.changeUserSettingsIndifferent(key,value);
        setTimeout(() => syncOnlineVisitors(),500);
    })


    onMount(async() => {
        if (lhcLogic.forbiddenVisitors !== true) {
            ['onlineusers','widget-onvisitors','map','dashboard'].forEach(function(item){
                var itemTab = document.getElementById(item);
                if (itemTab !== null) {
                    var observer = new MutationObserver(function (event) {
                        if (itemTab.classList.contains('active') && lhcLogic.lastSyncSkipped === true) {
                            syncOnlineVisitors();
                        }
                    })
                    observer.observe(itemTab, {
                        attributes: true,
                        attributeFilter: ['class'],
                        childList: false,
                        characterData: false
                    })
                }
            });
        }

        if (window['onlineAttributeFilter']) {
            lhcLogic.attrf_key_1 = window['onlineAttributeFilter']['attrf_key_1'];
            lhcLogic.attrf_val_1 = window['onlineAttributeFilter']['attrf_val_1'];
            lhcLogic.attrf_key_2 = window['onlineAttributeFilter']['attrf_key_2'];
            lhcLogic.attrf_val_2 = window['onlineAttributeFilter']['attrf_val_2'];
            lhcLogic.attrf_key_3 = window['onlineAttributeFilter']['attrf_key_3'];
            lhcLogic.attrf_val_3 = window['onlineAttributeFilter']['attrf_val_3'];
            lhcLogic.attrf_key_4 = window['onlineAttributeFilter']['attrf_key_4'];
            lhcLogic.attrf_val_4 = window['onlineAttributeFilter']['attrf_val_4'];
            lhcLogic.attrf_key_5 = window['onlineAttributeFilter']['attrf_key_5'];
            lhcLogic.attrf_val_5 = window['onlineAttributeFilter']['attrf_val_5'];

            for (let i = 1; i <= 5; i++) {
                document.getElementById('svelte-attrf_key_'+i).value = lhcLogic['attrf_key_'+i];
                document.getElementById('svelte-attrf_val_'+i).value = lhcLogic['attrf_val_'+i];
            }
        }

        setInnerText('disableNewUserBNotif', lhcLogic.notificationEnabled ? 'visibility' : 'visibility_off');
        setInnerText('disableNewUserSound', lhcLogic.soundEnabled ? 'volume_up':'volume_off');
        setInnerText('showConnected', lhcLogic.online_connected ? 'flash_on' : 'flash_off');

        setValue('countryFilter',lhcLogic.country);
        setValue('maxrowsFilter',lhcLogic.max_rows);
        setValue('userTimeoutFilter',lhcLogic.timeout);
        setValue('updateTimeoutFilter',lhcLogic.updateTimeout);
        setValue('time_on_siteFilter',lhcLogic.time_on_site);
        setValue('groupByField',lhcLogic.groupByField);

        let departments = [];
        jQuery('.online-department-filter input[name^=department_ids]').each(function(i){
            departments.push(parseInt(this.value));
        });
        lhcLogic.department = departments;

        syncOnlineVisitors();
    });

    function setInnerText(id,value) {
        let elm = document.getElementById('svelte-'+id);
        if (elm) {
            elm.innerText = value;
        }
    }

    function setValue(id,value) {
        let elm = document.getElementById('svelte-'+id);
        if (elm) {
            elm.value = value;
        }
    }

    function getSyncFilter()
    {
        return "/(method)/ajax/(timeout)/" + lhcLogic.timeout + (lhcLogic.department_dpgroups.length > 0 ? '/(department_dpgroups)/' + lhcLogic.department_dpgroups.join('/') : '' ) + (lhcLogic.department.length > 0 ? '/(department)/' + lhcLogic.department.join('/') : '' ) + (lhcLogic.max_rows > 0 ? '/(maxrows)/' + lhcLogic.max_rows : '' ) + (lhcLogic.country != '' ? '/(country)/' + lhcLogic.country : '' ) + (lhcLogic.time_on_site != '' ? '/(timeonsite)/' + encodeURIComponent(lhcLogic.time_on_site) : '');

    }

    function deleteUser(ou) {
        if (confirm('Are you sure?')) {
            fetch(WWW_DIR_JAVASCRIPT  + 'chat/onlineusers/(deletevisitor)/'+ou.id + '/(csfr)/' + confLH.csrf_token, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRFToken": confLH.csrf_token
                }
            });
            setTimeout(() => syncOnlineVisitors(),500);
        }
    }
    function sortOn( collection, name, reverse ) {
        collection.sort(
            function( a, b ) {
                if ( a[ name ] <= b[ name ] ) {
                    return( !reverse || reverse === false ? -1 : 1);
                }
                return( !reverse || reverse === false ? 1 : -1 );
            }
        );
    }

    // http://www.bennadel.com/blog/2456-grouping-nested-ngrepeat-lists-in-angularjs.htm
    // I group the friends list on the given property.
    function groupBy( list ) {
        // First, reset the groups.
        let onlineusersGrouped = [];

        // Now, sort the collection of friend on the
        // grouping-property. This just makes it easier
        // to split the collection.
        sortOn( list, lhcLogic.groupByField );

        // I determine which group we are currently in.
        var groupValue = "_INVALID_GROUP_VALUE_";

        // As we loop over each friend, add it to the
        // current group - we'll create a NEW group every
        // time we come across a new attribute value.
        for ( var i = 0 ; i < list.length ; i++ ) {
            var friend = list[ i ];
            // Should we create a new group?
            if ( friend[ lhcLogic.groupByField ] !== groupValue ) {
                var group = {
                    label: friend[ lhcLogic.groupByField ],
                    id: i,
                    ou: []
                };
                groupValue = group.label;
                onlineusersGrouped.push( group );
            }
            // Add the friend to the currently active
            // grouping.
            group.ou.push( friend );
        }
        return onlineusersGrouped;
    }

    async function syncOnlineVisitors(){

        if (lhinst.disableSync == true || lhcLogic.forbiddenVisitors == true ) {
            return;
        }

        // Check is online visitors tab is active or widget is expanded
        // otherwise also do not sync and save resources
        var activeList = false;

        var itemTab = document.getElementById('onlineusers');
        if (itemTab !== null) {
            activeList = itemTab.classList.contains('active');
        }

        if (activeList == false){
            var mapItem = document.getElementById('map');
            if (mapItem !== null) {
                activeList = mapItem.classList.contains('active');
            }
        }

        if (activeList == false) {
            var widgetItem = document.getElementById('widget-onvisitors-body');
            if (widgetItem !== null) {
                var dashboardTab = document.getElementById('dashboard');
                if (dashboardTab !== null && dashboardTab.classList.contains('active')) {
                    activeList = true;
                }
            }
        }

        if (activeList === false) {
            lhcLogic.lastSyncSkipped = true;
            return;
        }

        lhcLogic.lastSyncSkipped = false;

        clearTimeout(lhcLogic.timeoutControl);

        try {
            const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/onlineusers' + getSyncFilter(), {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                }
            });

            if (!responseTrack.ok) {
                throw new Error("Network response was not OK [" + responseTrack.status + "] ["+ responseTrack.statusText+"]");
            }

            const data = await responseTrack.json();
            lhcOnlineVisitors.update((list) => {
                list.onlineusers = data.list;
                list.onlineusers_tt = data.tt;
                if (lhcLogic.groupByField !== 'none') {
                    list.onlineusersGrouped = groupBy(list.onlineusers);
                } else {
                    list.onlineusersGrouped = [];
                    list.onlineusersGrouped.push({label:'',id:0,ou:list.onlineusers});
                }

                list.onlineusersGrouped.forEach((item) => {
                    sortOn(item.ou,lhcLogic.predicate,lhcLogic.reverse);
                });

                return list;
            });

            let onlineCounter = document.getElementById('online-users-count');
            if (onlineCounter) {
                onlineCounter.innerText = data.list.length;
            }

            ee.emitEvent('chatAdminSyncOnlineVisitors', [data.list]);

            if (lhcLogic.notificationEnabled || lhcLogic.soundEnabled) {
                var hasNewVisitors = false;
                var newVisitors = [];

                $lhcOnlineVisitors.onlineusers.forEach((value) => {
                    var hasValue = true;
                    if (lhcLogic.onlineusersPreviousID.indexOf(value.id) === -1) {
                        hasValue = false;
                        lhcLogic.onlineusersPreviousID.push(value.id);
                    }

                    if (lhcLogic.wasInitiated === true && hasValue === false) {
                        hasNewVisitors = true;
                        newVisitors.push(value);
                    }
                });

                if (hasNewVisitors == true ) {
                    if (lhcLogic.soundEnabled && Modernizr.audio){
                        var audio = new Audio();
                        audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_visitor.ogg' :
                            Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_visitor.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_visitor.wav';
                        audio.load();
                        setTimeout(function(){
                            audio.play();
                        },500);
                    };

                    if (lhcLogic.notificationEnabled && (window.webkitNotifications || window.Notification)) {
                        angular.forEach(newVisitors, function(value, key) {
                            if (window.webkitNotifications) {
                                var havePermission = window.webkitNotifications.checkPermission();
                                if (havePermission == 0) {
                                    // 0 is PERMISSION_ALLOWED
                                    var notification = window.webkitNotifications.createNotification(
                                        WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png',
                                        value.ip+(value.user_country_name != '' ? ', '+value.user_country_name : ''),
                                        (value.page_title != '' ? value.page_title+"\n-----\n" : '')+(value.referrer != '' ? value.referrer+"\n-----\n" : '')
                                    );
                                    notification.onclick = function () {
                                        notification.cancel();
                                    };
                                    notification.show();

                                    setTimeout(function(){
                                        notification.cancel();
                                    },15000);
                                }
                            } else if(window.Notification) {
                                if (window.Notification.permission == 'granted') {
                                    var notification = new Notification(value.ip+(value.user_country_name != '' ? ', '+value.user_country_name : ''), { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: (value.page_title != '' ? value.page_title+"\n-----\n" : '')+(value.referrer != '' ? value.referrer+"\n-----\n" : '') });
                                    notification.onclick = function () {
                                        notification.close();
                                    };
                                    setTimeout(function(){
                                        notification.close();
                                    },15000);
                                }
                            }
                        })
                    }
                }

                lhcLogic.wasInitiated = true;
                if (lhcLogic.onlineusersPreviousID.length > 100) {
                    lhcLogic.wasInitiated = false;
                    lhcLogic.onlineusersPreviousID = [];
                }
            }

            if (lhcLogic.forbiddenVisitors !== true) {
                lhcLogic.timeoutControl = setTimeout(function(){
                    syncOnlineVisitors();
                },lhcLogic.updateTimeout * 1000);
            }

        } catch (error) {

            lhcLogic.timeoutControl = setTimeout(function(){
                syncOnlineVisitors();
            },lhcLogic.updateTimeout * 1000);

            console.error("There has been a problem with your fetch operation:", error);
        }
    }

    function matchesFilter(ou) {

        if (ou.nick && ou.nick.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        if (ou.user_country_code && ou.user_country_code.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        if (ou.user_country_name && ou.user_country_name.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        if (ou.page_title && ou.page_title.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        if (ou.current_page && ou.current_page.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        if (ou.referrer && ou.referrer.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        if (ou.visitor_tz && ou.visitor_tz.toLowerCase().includes(lhcLogic.query)) {
            return true;
        }

        return false;
    }

    function setSort(predicate){
        lhcLogic.predicate = predicate;
        lhcLogic.reverse = !lhcLogic.reverse;
        syncOnlineVisitors();
    }

</script>

<table class="table table-sm online-users-table" class:filter-online-active={lhcLogic.online_connected} cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="50%" colspan="2">
        <a class="material-icons" on:click={(e) => setSort('last_visit')} title="Last activity" >access_time</a>
        <a class="material-icons" on:click={(e) => setSort('time_on_site')} title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?>">access_time</a>
        <a class="material-icons" on:click={(e) => setSort('visitor_tz_time')} title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visitor local time');?>">access_time</a>
        {#if track_is_online}<a class="material-icons" on:click={(e) => setSort('last_check_time')} title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','By user status on site');?>">access_time</a>{/if}
        <a href="#" on:click={(e) => setSort('current_page')} >Page</a> | <a href="#" on:click={(e) => setSort('referrer')}>Came from</a>
    </th>

    {#if $lhcList.additionalColumns}
        {#each $lhcList.additionalColumns as column}
            {#if column.oenabl == true && !column.iconm}
                <th>
                    {#if column.icon !== ''}<i class="material-icons text-muted">{column.icon}</i>{/if}{column.name}
                </th>
            {/if}
        {/each}
    {/if}

    <!--<th nowrap="nowrap" ng-repeat="column in lhc.additionalColumns" ng-if="column.oenabl == true">
        <i ng-if="column.icon !== ''" class="material-icons">{{column.icon}}</i>{{column.name}}
    </th>-->

    <th width="1%">Action</th>
</tr>
</thead>
{#each $lhcOnlineVisitors.onlineusersGrouped as group}
<tbody>
    {#if lhcLogic.groupByField != "none"}
    <tr>
        <td colspan="6"><h5 class="group-by-{lhcLogic.groupByField}">{group.label ? group.label : "-"} ({group.ou.length})</h5></td>
    </tr>
    {/if}
    {#each group.ou as ou (ou.id)}
        {#if lhcLogic.query == '' || matchesFilter(ou)}
            <tr id="uo-vid-{ou.vid}" class="online-user-filter-row" class:online_user={online_check && ou.last_check_time_ago < (parseInt(online_check) + 3)} class:recent_visit={ou.last_visit_seconds_ago < 15} class:bg-red={online_check}>
                <td nowrap width="1%">
                    <div>
                        {ou.lastactivity_ago} ago<br/>
                        <span class="fs-11">{ou.time_on_site_front}</span>
                    </div>
                </td>
                <td>
                    {#if ou.vid}
                        <div class="btn-group" role="group" aria-label="...">
                            <a href="#" class="btn btn-xs btn-secondary" title="Copy nick" onclick="lhinst.copyContent($(this))" data-success="Copied" data-copy={ou.nick}><i class="material-icons me-0">content_copy</i></a>

                            <a href="#" on:click={(e) => {lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+ou.id})}}  class="btn btn-xs btn-secondary" id="ou-face-{ou.vid}" class:icon-user-away={ou.online_status == 1} class:icon-user-online={!ou.online_status || ou.online_status == 0} ><i class="material-icons">info_outline</i>{ou.nick}&nbsp;
                                {#if ou.user_country_code}
                                    <span><img src={www_dir_flags + "/" + ou.user_country_code + ".png"} alt={ou.user_country_name} title={ou.user_country_name} /></span>
                                {/if}
                            </a>

                            {#if ou.chat_id > 0 && ou.can_view_chat == 1}
                                <span on:click={(e) => lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewchat/'+ou.chat_id}) } class="btn btn-xs btn-success action-image"><i class="material-icons">chat</i>Chat</span>
                            {/if}

                            {#if ou.total_visits > 1}
                                <span class="btn btn-xs btn-info"><i class="material-icons">face</i>Returning ({ou.total_visits})</span>
                            {/if}

                            {#if ou.total_visits == 1}
                                <span class="btn btn-success btn-xs"><i class="material-icons">face</i>New</span>
                            {/if}

                            {#if ou.operator_message}
                                <span title="{ou.operator_user_string} has sent a message to the user" class={"btn btn-xs "+(ou.message_seen == 1 ? 'btn-success' : 'btn-danger')} ><i class="material-icons">chat_bubble_outline</i>{ou.message_seen == 1 ? "tr.msg_seen": "tr.msg_not_seen"}</span>
                            {/if}

                            {#if ou.user_country_code != ''}
                                <span class="btn btn-xs btn-primary up-case-first" ng-if="ou.user_country_code != ''">{ou.user_country_name}{ou.city != '' ? ' | '+ou.city : ''}</span><span class="btn btn-primary btn-xs"><i class="material-icons">access_time</i>{ou.visitor_tz} - {ou.visitor_tz_time}</span>
                            {/if}

                            <a href="#" class="btn btn-xs btn-secondary" on:click={(e) => lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/sendnotice/'+ou.id})}><i class="material-icons">send</i>Start a chat</a>

                        </div>
                    {/if}

                    {#if ou.page_title || ou.current_page}
                        <div class="abbr-list" >
                            <i class="material-icons" title="Page">&#xE8A0;</i><a target="_blank" href={ou.current_page} title={ou.current_page}>{ou.page_title || ou.current_page}</a>
                        </div>
                    {/if}

                    {#if ou.referrer}
                        <div class="abbr-list">
                            <i class="material-icons" title="From">&#xE8A0;</i><a target="_blank" href="http:{ou.referrer}" title={ou.referrer}>{ou.referrer}</a>
                        </div>
                    {/if}
                </td>
                {#if $lhcList.additionalColumns}
                    {#each $lhcList.additionalColumns as column}
                        {#if column.oenabl == true && !column.iconm}
                            <td>
                                {#each column.items as val}
                                    {#if ou[val]}
                                        <div class="abbr-list">{ou[val]}</div>
                                    {/if}
                                {/each}
                            </td>
                        {/if}
                    {/each}
                {/if}
                <td>
                    <div style="width:90px" ng-if="ou.vid">
                        <div class="btn-group" role="group" aria-label="...">
                            <button on:click={(e) => lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/sendnotice/'+ou.id})} class="btn btn-secondary btn-sm material-icons mat-100 me-0" title="Send message">chat</button>
                            <button on:click={deleteUser(ou)} class="btn btn-danger btn-sm material-icons mat-100 me-0" title="Delete, ID - {ou.id}">delete</button>
                        </div>
                    </div>
                </td>
            </tr>
        {/if}
    {/each}
</tbody>
{/each}
</table>