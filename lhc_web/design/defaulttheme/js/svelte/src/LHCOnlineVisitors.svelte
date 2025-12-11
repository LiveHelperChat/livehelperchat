<script>
    import { lhcList } from './stores.js';
    import { t } from "./i18n/i18n.js";

    import { onMount } from 'svelte';
    import lhcServices from './lib/Services.js';
    import WidgetOptionsPanel from  './Widgets/Parts/WidgetOptionsPanel.svelte';

    export let www_dir_flags = "";
    export let track_is_online = false;
    export let sound_enabled;
    export let notification_enabled;
    export let online_connected;
    export let max_rows;
    export let timeout = "3600";
    export let country = "none";
    export let forbidden_visitors = "false";
    export let update_timeout = 10;
    export let time_on_site = "";
    export let online_check = null;
    export let group_by_field;
    export let render_ui = "true";
    export let osettings_hide_action_buttons = "false";

    let lhcLogic = {
        timeoutVisitors: null,
        time_on_site: time_on_site,
        country: country,
        max_rows: parseInt(max_rows),
        updateTimeout: parseInt(update_timeout),
        timeout: timeout,
        lhcListRequestInProgress: false,
        timeoutControl: null,
        forbiddenVisitors : forbidden_visitors === 'true',
        soundEnabled : sound_enabled === 'true',
        notificationEnabled : notification_enabled === 'true',
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
        wasInitiated : false,
        online_connected : online_connected === 'true',
        predicate : 'last_visit',
        reverse : true,
        groupByField : group_by_field,
        hide_action_buttons : osettings_hide_action_buttons === "true"
    };

    let btnSecondaryClass = lhcLogic.hide_action_buttons === true ? "btn-outline-secondary" : "btn-outline-secondary";
    let btnSuccessClass = lhcLogic.hide_action_buttons === true ? "btn-outline-success" : "btn-outline-success";
    let btnDangerClass = lhcLogic.hide_action_buttons === true ? "btn-outline-danger" : "btn-outline-danger";
    let btnInfoClass = lhcLogic.hide_action_buttons === true ? "btn-outline-info" : "btn-outline-info";

    ee.addListener('svelteOnlineUserSetting',function (settingName, value) {
        lhcLogic.wasInitiated = false;
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
            $lhcList.department_online = departments;
            setTimeout(() => syncOnlineVisitors(),500);
            lhcServices.setLocalSettings('department_online', $lhcList.department_online);
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

        if (!lhcLogic.hide_action_buttons) {
            let departments = [];
            jQuery('.online-department-filter input[name^=department_ids]').each(function(i){
                departments.push(parseInt(this.value));
            });
            $lhcList.department_online = departments;
        }

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
        return "/(method)/ajax/(timeout)/" + lhcLogic.timeout + ($lhcList.department_online_dpgroups.length > 0 ? '/(department_dpgroups)/' + $lhcList.department_online_dpgroups.join('/') : '' ) + ($lhcList.department_online.length > 0 ? '/(department)/' + $lhcList.department_online.join('/') : '' ) + (lhcLogic.max_rows > 0 ? '/(maxrows)/' + lhcLogic.max_rows : '' ) + (lhcLogic.country != '' ? '/(country)/' + lhcLogic.country : '' ) + (lhcLogic.time_on_site != '' ? '/(timeonsite)/' + encodeURIComponent(lhcLogic.time_on_site) : '');
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
            lhcLogic.lastSyncSkipped = true;
            clearTimeout(lhcLogic.timeoutControl);
            lhcLogic.timeoutControl = setTimeout(function(){
                syncOnlineVisitors();
            },lhcLogic.updateTimeout * 1000);
            return;
        }

        // Check is online visitors tab is active or widget is expanded
        // otherwise also do not sync and save resources
        var activeList = false;

        var itemTab = document.getElementById('onlineusers');
        if (itemTab !== null) {
            activeList = itemTab.classList.contains('active');
        }

        if (activeList == false) {
            var mapItem = document.getElementById('map');
            if (mapItem !== null) {
                activeList = mapItem.classList.contains('active');
            }
        }

        if (activeList == false) {
            var widgetItem = document.getElementById('onlineusers-panel-list');
            if (widgetItem !== null) {
                var dashboardTab = document.getElementById('dashboard');
                if (dashboardTab !== null && dashboardTab.classList.contains('active')) {
                    activeList = true;
                }
            }
        }

        if (activeList === true && $lhcList.suspend_widgets.indexOf('onlineusers') !== -1) {
            activeList = false;
        }

        if (activeList === false) {
            lhcLogic.lastSyncSkipped = true;
            clearTimeout(lhcLogic.timeoutControl);
            lhcLogic.timeoutControl = setTimeout(function(){
                syncOnlineVisitors();
            },lhcLogic.updateTimeout * 1000);
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
            lhcList.update((list) => {
                list.onlineusers = data;
                if (lhcLogic.groupByField !== 'none') {
                    list.onlineusersGrouped = groupBy(list.onlineusers.list);
                } else {
                    list.onlineusersGrouped = [];
                    list.onlineusersGrouped.push({label:'',id:0,ou:list.onlineusers.list});
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

                $lhcList.onlineusers.list.forEach((value) => {
                    var hasValue = true;
                    if ($lhcList.onlineusersPreviousID.indexOf(value.id) === -1) {
                        hasValue = false;
                        $lhcList.onlineusersPreviousID.push(value.id);
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
                    }

                    if (lhcLogic.notificationEnabled && (window.webkitNotifications || window.Notification)) {
                        newVisitors.forEach(function(value) {
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
                if ($lhcList.onlineusersPreviousID.length > 100) {
                    lhcLogic.wasInitiated = false;
                    $lhcList.onlineusersPreviousID = [];
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

    ee.addListener('svelteDepartmentChanged',function (list,force) {
        if (list === 'department_online'){
            lhcLogic.wasInitiated = false;
            syncOnlineVisitors();
        }
    });

    ee.addListener('svelteOnlineGetFilter',function (filter) {
        filter.url = getSyncFilter();
    });

</script>

{#if lhcLogic.hide_action_buttons}
    <div class="p-2">
        <div class="row">
            <div class="col-3 pe-0">
                <input class="form-control form-control-sm" on:keyup={(e) => ee.emitEvent('svelteOnlineUserSetting',['setQuery',e.currentTarget.value])} type="text" value="" placeholder={$t('widget_options.type_to_search')}>
            </div>
            <div class="col-3 pe-2">
                <WidgetOptionsPanel lhcList={lhcList} optionsPanel={{padding_filters:0, disable_product:true, hide_department_variations:true, hide_limits:true, panelid:'department_online'}} />
            </div>
            <div class="col-3 pe-0">
                <select class="form-control form-control-sm" on:change={(e) => ee.emitEvent('svelteOnlineUserSetting',['countryFilter',e.currentTarget.value])} id="svelte-countryFilter" title={$t('widget_options.select_country')}>
                    <option value="none" selected="selected">{$t('widget_options.select_country')}</option>
                    <option value="af">Afghanistan</option>
                    <option value="ax">Åland Islands</option>
                    <option value="al">Albania</option>
                    <option value="dz">Algeria</option>
                    <option value="as">American Samoa</option>
                    <option value="ad">Andorra</option>
                    <option value="ao">Angola</option>
                    <option value="ai">Anguilla</option>
                    <option value="aq">Antarctica</option>
                    <option value="ag">Antigua and Barbuda</option>
                    <option value="ar">Argentina</option>
                    <option value="am">Armenia</option>
                    <option value="aw">Aruba</option>
                    <option value="au">Australia</option>
                    <option value="at">Austria</option>
                    <option value="az">Azerbaijan</option>
                    <option value="bs">Bahamas</option>
                    <option value="bh">Bahrain</option>
                    <option value="bd">Bangladesh</option>
                    <option value="bb">Barbados</option>
                    <option value="by">Belarus</option>
                    <option value="be">Belgium</option>
                    <option value="bz">Belize</option>
                    <option value="bj">Benin</option>
                    <option value="bm">Bermuda</option>
                    <option value="bt">Bhutan</option>
                    <option value="bo">Bolivia, Plurinational State of</option>
                    <option value="bq">Bonaire, Sint Eustatius and Saba</option>
                    <option value="ba">Bosnia and Herzegovina</option>
                    <option value="bw">Botswana</option>
                    <option value="bv">Bouvet Island</option>
                    <option value="br">Brazil</option>
                    <option value="io">British Indian Ocean Territory</option>
                    <option value="bn">Brunei Darussalam</option>
                    <option value="bg">Bulgaria</option>
                    <option value="bf">Burkina Faso</option>
                    <option value="bi">Burundi</option>
                    <option value="kh">Cambodia</option>
                    <option value="cm">Cameroon</option>
                    <option value="ca">Canada</option>
                    <option value="cv">Cape Verde</option>
                    <option value="ky">Cayman Islands</option>
                    <option value="cf">Central African Republic</option>
                    <option value="td">Chad</option>
                    <option value="cl">Chile</option>
                    <option value="cn">China</option>
                    <option value="cx">Christmas Island</option>
                    <option value="cc">Cocos (Keeling) Islands</option>
                    <option value="co">Colombia</option>
                    <option value="km">Comoros</option>
                    <option value="cg">Congo</option>
                    <option value="cd">Congo, the Democratic Republic of the</option>
                    <option value="ck">Cook Islands</option>
                    <option value="cr">Costa Rica</option>
                    <option value="ci">Côte d'Ivoire</option>
                    <option value="hr">Croatia</option>
                    <option value="cu">Cuba</option>
                    <option value="cw">Curaçao</option>
                    <option value="cy">Cyprus</option>
                    <option value="cz">Czech Republic</option>
                    <option value="dk">Denmark</option>
                    <option value="dj">Djibouti</option>
                    <option value="dm">Dominica</option>
                    <option value="do">Dominican Republic</option>
                    <option value="ec">Ecuador</option>
                    <option value="eg">Egypt</option>
                    <option value="sv">El Salvador</option>
                    <option value="gq">Equatorial Guinea</option>
                    <option value="er">Eritrea</option>
                    <option value="ee">Estonia</option>
                    <option value="et">Ethiopia</option>
                    <option value="fk">Falkland Islands (Malvinas)</option>
                    <option value="fo">Faroe Islands</option>
                    <option value="fj">Fiji</option>
                    <option value="fi">Finland</option>
                    <option value="fr">France</option>
                    <option value="gf">French Guiana</option>
                    <option value="pf">French Polynesia</option>
                    <option value="tf">French Southern Territories</option>
                    <option value="ga">Gabon</option>
                    <option value="gm">Gambia</option>
                    <option value="ge">Georgia</option>
                    <option value="de">Germany</option>
                    <option value="gh">Ghana</option>
                    <option value="gi">Gibraltar</option>
                    <option value="gr">Greece</option>
                    <option value="gl">Greenland</option>
                    <option value="gd">Grenada</option>
                    <option value="gp">Guadeloupe</option>
                    <option value="gu">Guam</option>
                    <option value="gt">Guatemala</option>
                    <option value="gg">Guernsey</option>
                    <option value="gn">Guinea</option>
                    <option value="gw">Guinea-Bissau</option>
                    <option value="gy">Guyana</option>
                    <option value="ht">Haiti</option>
                    <option value="hm">Heard Island and McDonald Islands</option>
                    <option value="va">Holy See (Vatican City State)</option>
                    <option value="hn">Honduras</option>
                    <option value="hk">Hong Kong</option>
                    <option value="hu">Hungary</option>
                    <option value="is">Iceland</option>
                    <option value="in">India</option>
                    <option value="id">Indonesia</option>
                    <option value="ir">Iran, Islamic Republic of</option>
                    <option value="iq">Iraq</option>
                    <option value="ie">Ireland</option>
                    <option value="im">Isle of Man</option>
                    <option value="il">Israel</option>
                    <option value="it">Italy</option>
                    <option value="jm">Jamaica</option>
                    <option value="jp">Japan</option>
                    <option value="je">Jersey</option>
                    <option value="jo">Jordan</option>
                    <option value="kz">Kazakhstan</option>
                    <option value="ke">Kenya</option>
                    <option value="ki">Kiribati</option>
                    <option value="kp">Korea, Democratic People's Republic of</option>
                    <option value="kr">Korea, Republic of</option>
                    <option value="kw">Kuwait</option>
                    <option value="kg">Kyrgyzstan</option>
                    <option value="la">Lao People's Democratic Republic</option>
                    <option value="lv">Latvia</option>
                    <option value="lb">Lebanon</option>
                    <option value="ls">Lesotho</option>
                    <option value="lr">Liberia</option>
                    <option value="ly">Libya</option>
                    <option value="li">Liechtenstein</option>
                    <option value="lt">Lithuania</option>
                    <option value="lu">Luxembourg</option>
                    <option value="mo">Macao</option>
                    <option value="mk">Macedonia, the Former Yugoslav Republic of</option>
                    <option value="mg">Madagascar</option>
                    <option value="mw">Malawi</option>
                    <option value="my">Malaysia</option>
                    <option value="mv">Maldives</option>
                    <option value="ml">Mali</option>
                    <option value="mt">Malta</option>
                    <option value="mh">Marshall Islands</option>
                    <option value="mq">Martinique</option>
                    <option value="mr">Mauritania</option>
                    <option value="mu">Mauritius</option>
                    <option value="yt">Mayotte</option>
                    <option value="mx">Mexico</option>
                    <option value="fm">Micronesia, Federated States of</option>
                    <option value="md">Moldova, Republic of</option>
                    <option value="mc">Monaco</option>
                    <option value="mn">Mongolia</option>
                    <option value="me">Montenegro</option>
                    <option value="ms">Montserrat</option>
                    <option value="ma">Morocco</option>
                    <option value="mz">Mozambique</option>
                    <option value="mm">Myanmar</option>
                    <option value="na">Namibia</option>
                    <option value="nr">Nauru</option>
                    <option value="np">Nepal</option>
                    <option value="nl">Netherlands</option>
                    <option value="nc">New Caledonia</option>
                    <option value="nz">New Zealand</option>
                    <option value="ni">Nicaragua</option>
                    <option value="ne">Niger</option>
                    <option value="ng">Nigeria</option>
                    <option value="nu">Niue</option>
                    <option value="nf">Norfolk Island</option>
                    <option value="mp">Northern Mariana Islands</option>
                    <option value="no">Norway</option>
                    <option value="om">Oman</option>
                    <option value="pk">Pakistan</option>
                    <option value="pw">Palau</option>
                    <option value="ps">Palestine, State of</option>
                    <option value="pa">Panama</option>
                    <option value="pg">Papua New Guinea</option>
                    <option value="py">Paraguay</option>
                    <option value="pe">Peru</option>
                    <option value="ph">Philippines</option>
                    <option value="pn">Pitcairn</option>
                    <option value="pl">Poland</option>
                    <option value="pt">Portugal</option>
                    <option value="pr">Puerto Rico</option>
                    <option value="qa">Qatar</option>
                    <option value="re">Réunion</option>
                    <option value="ro">Romania</option>
                    <option value="ru">Russian Federation</option>
                    <option value="rw">Rwanda</option>
                    <option value="bl">Saint Barthélemy</option>
                    <option value="sh">Saint Helena, Ascension and Tristan da Cunha</option>
                    <option value="kn">Saint Kitts and Nevis</option>
                    <option value="lc">Saint Lucia</option>
                    <option value="mf">Saint Martin (French part)</option>
                    <option value="pm">Saint Pierre and Miquelon</option>
                    <option value="vc">Saint Vincent and the Grenadines</option>
                    <option value="ws">Samoa</option>
                    <option value="sm">San Marino</option>
                    <option value="st">Sao Tome and Principe</option>
                    <option value="sa">Saudi Arabia</option>
                    <option value="sn">Senegal</option>
                    <option value="rs">Serbia</option>
                    <option value="sc">Seychelles</option>
                    <option value="sl">Sierra Leone</option>
                    <option value="sg">Singapore</option>
                    <option value="sx">Sint Maarten (Dutch part)</option>
                    <option value="sk">Slovakia</option>
                    <option value="si">Slovenia</option>
                    <option value="sb">Solomon Islands</option>
                    <option value="so">Somalia</option>
                    <option value="za">South Africa</option>
                    <option value="gs">South Georgia and the South Sandwich Islands</option>
                    <option value="ss">South Sudan</option>
                    <option value="es">Spain</option>
                    <option value="lk">Sri Lanka</option>
                    <option value="sd">Sudan</option>
                    <option value="sr">Suriname</option>
                    <option value="sj">Svalbard and Jan Mayen</option>
                    <option value="sz">Swaziland</option>
                    <option value="se">Sweden</option>
                    <option value="ch">Switzerland</option>
                    <option value="sy">Syrian Arab Republic</option>
                    <option value="tw">Taiwan, Province of China</option>
                    <option value="tj">Tajikistan</option>
                    <option value="tz">Tanzania, United Republic of</option>
                    <option value="th">Thailand</option>
                    <option value="tl">Timor-Leste</option>
                    <option value="tg">Togo</option>
                    <option value="tk">Tokelau</option>
                    <option value="to">Tonga</option>
                    <option value="tt">Trinidad and Tobago</option>
                    <option value="tn">Tunisia</option>
                    <option value="tr">Turkey</option>
                    <option value="tm">Turkmenistan</option>
                    <option value="tc">Turks and Caicos Islands</option>
                    <option value="tv">Tuvalu</option>
                    <option value="ug">Uganda</option>
                    <option value="ua">Ukraine</option>
                    <option value="ae">United Arab Emirates</option>
                    <option value="gb">United Kingdom</option>
                    <option value="us">United States</option>
                    <option value="um">United States Minor Outlying Islands</option>
                    <option value="uy">Uruguay</option>
                    <option value="uz">Uzbekistan</option>
                    <option value="vu">Vanuatu</option>
                    <option value="ve">Venezuela, Bolivarian Republic of</option>
                    <option value="vn">Viet Nam</option>
                    <option value="vg">Virgin Islands, British</option>
                    <option value="vi">Virgin Islands, U.S.</option>
                    <option value="wf">Wallis and Futuna</option>
                    <option value="eh">Western Sahara</option>
                    <option value="ye">Yemen</option>
                    <option value="zm">Zambia</option>
                    <option value="zw">Zimbabwe</option>
                </select>
            </div>
            <div class="col-3">
                <input type="text" class="form-control form-control-sm" id="svelte-time_on_siteFilter" on:keyup={(e) => ee.emitEvent('svelteOnlineUserSetting',['timeOnSiteFilter',e.currentTarget.value])} title={$t('widget_options.time_on_site')} placeholder={$t('widget_options.time_on_site')} value="" />
            </div>
        </div>
    </div>
{/if}

<div class="panel-list" id={lhcLogic.hide_action_buttons ? "onlineusers-panel-list" : null} style:max-height={lhcLogic.hide_action_buttons ? ($lhcList['onlineusers_m_h'] ?? '330px') : null}>

<table class={"table table-small table-sm "+(lhcLogic.hide_action_buttons ? "table-fixed" : "online-users-table")} class:filter-online-active={lhcLogic.online_connected} cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="50%" colspan={!lhcLogic.hide_action_buttons ? "2" : "1"}>
        <a class="material-icons" on:click={(e) => setSort('last_visit')} title={$t('widget.last_activity_ago')} >access_time</a>
        <a class="material-icons" on:click={(e) => setSort('time_on_site')} title={$t('widget_options.time_on_site_shrt')}>access_time</a>
        <a class="material-icons" on:click={(e) => setSort('visitor_tz_time')} title={$t('widget_options.vis_local_time')}>access_time</a>
        {#if track_is_online}<a class="material-icons" on:click={(e) => setSort('last_check_time')} title={$t('widget_options.status_on_site')}>access_time</a>{/if}
        <a href="#" on:click={(e) => setSort('current_page')} >{$t('widget_options.page')}</a> | <a href="#" on:click={(e) => setSort('referrer')}>{$t('widget_options.came_from')}</a>{#if online_check} | <span title={$t('widget_options.only_connected')} on:click={(e) => ee.emitEvent('svelteOnlineUserSetting',['showConnected'])} class="material-icons action-image">{lhcLogic.online_connected ? 'flash_on' : 'flash_off'}</span>{/if}


        <div class="float-end expand-actions">

            {#if !lhcLogic.hide_action_buttons}
            <a title={$t($lhcList.suspend_widgets.indexOf('onlineusers') === -1 ? "widget.pause_widget" : "widget.resume_widget")} on:click={(e) => lhcServices.suspendSync(lhcList,'onlineusers')} class="ms-0 me-1 text-muted material-icons">{$lhcList.suspend_widgets.indexOf('onlineusers') === -1 ? 'pause' : 'play_arrow'}</a>
            {/if}

            <a on:click={(e) => lhcServices.changeWidgetHeight(lhcList,'onlineusers',true)} class="text-muted disable-select">
                <i title={$t("widget.more_rows")}  class="material-icons">expand</i>
            </a>

            <a on:click={(e) => lhcServices.changeWidgetHeight(lhcList,'onlineusers',false)} class="text-muted disable-select">
                <i title={$t("widget.less_rows")} class="material-icons">compress</i>
            </a>


        </div>

    </th>
    {#if $lhcList.additionalColumns}
        {#each $lhcList.additionalColumns as column}
            {#if column.oenabl == true && !column.iconm}
                <th width="10%">
                    {#if column.icon !== ''}<i class="material-icons text-muted">{column.icon}</i>{/if}{column.name}
                </th>
            {/if}
        {/each}
    {/if}
    {#if !lhcLogic.hide_action_buttons}
    <th width="1%">{$t('widget.action')}</th>
    {/if}
</tr>
</thead>
{#each $lhcList.onlineusersGrouped as group}
<tbody>
    {#if lhcLogic.groupByField != "none"}
    <tr>
        <td colspan={!lhcLogic.hide_action_buttons ? "6" : "1"}><h5 class="group-by-{lhcLogic.groupByField}">{group.label ? group.label : "-"} ({group.ou.length})</h5></td>
    </tr>
    {/if}
    {#each group.ou as ou (ou.id)}
        {#if lhcLogic.query == '' || matchesFilter(ou)}
            <tr id="uo-vid-{ou.vid}" class="online-user-filter-row" class:online_user={online_check && ou.last_check_time_ago < (parseInt(online_check) + 3)} class:recent_visit={ou.last_visit_seconds_ago < 15} class:bg-red={online_check}>

                {#if !lhcLogic.hide_action_buttons}
                <td nowrap width="1%">
                    <div>
                        {ou.lastactivity_ago} {$t('widget.ago')}<br/>
                        <span class="fs-11">{ou.time_on_site_front}</span>
                    </div>
                </td>
                {/if}

                <td>
                    {#if ou.vid}
                        <div class="btn-group" role="group" aria-label="...">
                            <a href="#" class={"btn btn-xs "+btnSecondaryClass} title={$t('widget.copy_nick')} on:click={(e) => lhinst.copyContent(jQuery(e.currentTarget))} data-success={$t('widget.copied_nick')} data-copy={ou.nick}><i class="material-icons me-0">content_copy</i></a>

                            <a href="#" on:click={(e) => {lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+ou.id})}}  class={"btn btn-xs "+btnSecondaryClass} id="ou-face-{ou.vid}" class:icon-user-away={ou.online_status == 1} class:icon-user-online={!ou.online_status || ou.online_status == 0} ><i class="material-icons">info_outline</i>{#if lhcLogic.hide_action_buttons}{ou.lastactivity_ago} | {/if}{ou.nick}&nbsp;
                                {#if ou.user_country_code}
                                    <span><img src={www_dir_flags + "/" + ou.user_country_code + ".png"} alt={ou.user_country_name} title={ou.user_country_name+(ou.city != '' ? ' | '+ou.city : '')+" "+ou.visitor_tz+" - "+ou.visitor_tz_time} /></span>
                                {/if}
                            </a>

                            {#if ou.chat_id > 0 && ou.can_view_chat == 1}
                                <span title={$t('widget.preview_chat')} on:click={(e) => lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewchat/'+ou.chat_id}) } class={"btn btn-xs action-image "+btnSuccessClass}><i class="material-icons me-0">chat</i>{#if !lhcLogic.hide_action_buttons}&nbsp;{$t('widget.chat')}{/if}</span>
                            {/if}

                            {#if ou.total_visits > 1}
                                <span class={"btn btn-xs "+btnInfoClass}><i title={$t('widget.returning_long')+ " " + ou.total_visits} class="material-icons me-0">face</i>&nbsp;{#if !lhcLogic.hide_action_buttons}{$t('widget.returning')} - {/if}{ou.total_visits}</span>
                            {/if}

                            {#if ou.total_visits == 1}
                                <span class={"btn btn-xs "+btnSuccessClass} title={$t('widget.new')}><i class="material-icons me-0">face</i>{#if !lhcLogic.hide_action_buttons}&nbsp;{$t('widget.new')}{/if}</span>
                            {/if}

                            {#if ou.operator_message}
                                <span title={ou.operator_user_string+" " + $t('widget.msg_sent')} class={"btn btn-xs "+(ou.message_seen == 1 ? btnSuccessClass : btnDangerClass)} ><i class="material-icons me-0">chat_bubble_outline</i>{#if !lhcLogic.hide_action_buttons}&nbsp;{ou.message_seen == 1 ? $t("widget.msg_seen") : $t("widget.msg_not_seen")}{/if}</span>
                            {/if}

                            {#if ou.user_country_code != '' && !lhcLogic.hide_action_buttons}
                                <span class="btn btn-xs btn-outline-primary up-case-first">{ou.user_country_name}{ou.city != '' ? ' | '+ou.city : ''}</span><span class="btn btn-outline-primary btn-xs"><i class="material-icons">access_time</i>{ou.visitor_tz} - {ou.visitor_tz_time}</span>
                            {/if}

                            <a href="#" title={$t('widget.start_a_chat')} class={"btn btn-xs "+btnSecondaryClass} on:click={(e) => lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/sendnotice/'+ou.id})}><i class="material-icons me-0">send</i>{#if !lhcLogic.hide_action_buttons}&nbsp;{$t('widget.start_a_chat')}{/if}</a>

                        </div>
                    {/if}

                    {#if ou.page_title || ou.current_page}
                        <div class="abbr-list" >
                            <i class="material-icons" title={$t('widget_options.page')}>&#xE8A0;</i><a target="_blank" href={ou.current_page} title={ou.current_page}>{ou.page_title || ou.current_page}</a>
                        </div>
                    {/if}

                    {#if ou.referrer}
                        <div class="abbr-list">
                            <i class="material-icons" title={$t('widget_options.came_from')}>&#xE8A0;</i><a target="_blank" href="http:{ou.referrer}" title={ou.referrer}>{ou.referrer}</a>
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
                {#if !lhcLogic.hide_action_buttons}
                <td>
                    <div style="width:90px" >
                        <div class="btn-group" role="group" aria-label="...">
                            <button on:click={(e) => lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/sendnotice/'+ou.id})} class="btn btn-secondary btn-sm material-icons mat-100 me-0" title={$t('widget.send_message')}>chat</button>
                            <button on:click={deleteUser(ou, $t('widget.are_you_sure'))} class="btn btn-danger btn-sm material-icons mat-100 me-0" title={$t('widget_options.delete')+", ID - "+ou.id}>delete</button>
                        </div>
                    </div>
                </td>
                {/if}
            </tr>
        {/if}
    {/each}
</tbody>
{/each}
</table>

</div>