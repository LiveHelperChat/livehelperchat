<script>
    import lhcServices from '../../lib/Services.js';
    import { t } from "../../i18n/i18n.js";
    import { onMount, onDestroy } from 'svelte';

    export let sort_identifier = null;
    export let type = null;
    export let lhcList = null;
    export let www_dir_flags = null;
    export let no_additional_column = false;
    export let hide_third_column = false;
    export let hide_2_column = false;
    export let hide_ac_op_icon = false;
    export let hide_ac_stats = false;
    export let hide_ac_sort = false;
    export let hide_op_avatar = false;
    export let custom_visitor_title = null;
    export let custom_visitor_icon = null;
    export let show_visitor_title = false;
    export let no_chat_preview = false;
    export let show_username_always = false;
    export let show_always_subject = false;
    export let show_department_title = false;
    export let show_subject_title = false;
    export let show_username_title = false;
    export let no_expand = false;
    export let panel_id = null;
    export let permissions = [];
    export let custom_icons = [];
    export let custom_sort_icons = [];
    export let column_2_width = "20%";
    export let column_1_width = "40%";
    export let column_3_width = "20%";
    export let additional_sort = "";

    export let override_item_open = null;

    let check_row_class = !override_item_open && type !== "transfer_chats" && type !== "group_chats" && type !== "online_op" && type !== "depgroups_stats";
    let check_online = !override_item_open && type !== "my_mails" && type !== "active_mails" && type !== "pending_mails" && type !== "alarm_mails";

    function openItem(chat) {
        if (type === "group_chats") {
            lhcServices.startGroupChat(chat.id,chat.name)
        } else if (type === "my_mails" || type === "active_mails" || type === "pending_mails" || type === "alarm_mails") {
            lhcServices.startMailChat(chat.id, chat.subject_front, $lhcList.isCTRLPressed);
        } else {
            lhcServices.startChat(chat.id, chat.nick, !$lhcList.isCTRLPressed);
        }
    }

    function openRemoteItem(chat) {
        ee.emitEvent(override_item_open,[chat]);
    }

    function getClassListList(chat, list, type) {
        let classList = [];

        Object.entries(list).forEach(([key, value]) => {
            if ((type === false && !chat[value]) || (type === true && chat[value])) {
                classList.push(key);
            }
        });

        return classList.join(' ');

    }

</script>

<table class="table table-sm mb-0 table-small table-fixed" class:list-chat-table={check_row_class || override_item_open}>

    {#if type != 'depgroups_stats' || (!$lhcList.departmentd_hide_dgroup && $lhcList[type].list.length > 0)}
    <thead>
    <tr>
        <th width={column_1_width}>

            {#if type == 'online_op'}

            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'onn_dsc','onn_asc',true)} title={$t("widget.sort_by_online_name")}>

                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'onn_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'onn_asc'} class="material-icons chat-active">account_box</i>
                {#if !hide_ac_stats}
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'onn_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'onn_asc'} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'onn_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'onn_asc' ? 'trending_up' : 'trending_down'}</i>
                {/if}
            </a>

            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'onl_dsc','onl_asc',true)} title={$t("widget.sort_by_online_status")}>
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'onl_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'onl_asc'} class="material-icons chat-active">flash_on</i>
                {#if !hide_ac_stats}
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'onl_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'onl_asc'} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'onl_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'onl_asc' ? 'trending_up' : 'trending_down'}</i>
                {/if}
            </a>

                {#if type == 'online_op'}<span class="text-success" title={$t("widget.online")}>{$lhcList[type].op_on}</span>{/if}
            {:else if type == 'depgroups_stats'}
                <i title={$t("widget.dep_group")} class="material-icons">&#xE84F;</i>
            {:else if type == 'pending_chats'}
                <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'id_dsc','id_asc',true)}>
                    <i title={$t("widget.visitor")} class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'id_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'id_asc'} class="chat-active material-icons">face</i>
                    <i title={$t("widget.sort_pending")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] === 'id_dsc' ? 'trending_up' : 'trending_down'}</i>
                </a>
            {:else if type == 'active_chats'}
                <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'loc_dsc','loc_asc',true)} >
                    <i title={$t("widget.location")} class="material-icons">location_on</i>
                    <i class:text-muted={($lhcList.toggleWidgetData[sort_identifier] != 'loc_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'loc_dsc')} title={$t("widget.sort_by_location")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'loc_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'loc_asc' ? 'trending_up' : 'trending_down'}</i>
                </a>&nbsp;&nbsp;&nbsp;<a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'u_dsc','u_asc',true)}>
                    <i title={$t("widget.visitor")} class="material-icons">face</i>
                    <i class:text-muted={($lhcList.toggleWidgetData[sort_identifier] != 'u_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'u_dsc')} title={$t("widget.sort_by_nick")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'u_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'u_asc' ? 'trending_up' : 'trending_down'}</i>
                </a>
            {:else if type == 'group_chats'}
                <i title={$t("group_chat.group_name")} class="material-icons">group</i>
            {:else}
                <i title={$t("widget." + (custom_visitor_title ? custom_visitor_title : "visitor"))} class="material-icons">{custom_visitor_icon ? custom_visitor_icon : 'face'}</i>{#if show_visitor_title}{$t("widget." + (custom_visitor_title ? custom_visitor_title : "visitor"))}{/if}
            {/if}

            {#if type == 'transfer_chats'}
                {$t("widget.transferred_to_you")}
            {/if}

            {#each custom_sort_icons as iconSortData}
                | <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList, iconSortData.sort_identifier, iconSortData.sort_options[0], iconSortData.sort_options[1], true)} >
                    <i title={iconSortData.title} class={'material-icons chat-active ' + ($lhcList.toggleWidgetData[iconSortData.sort_identifier] != iconSortData.sort_options[1] && $lhcList.toggleWidgetData[iconSortData.sort_identifier] != iconSortData.sort_options[0] ? 'text-muted' : '')}>
                        {$lhcList.toggleWidgetData[iconSortData.sort_identifier] == iconSortData.sort_options[1] ? iconSortData['sort_icon_' + iconSortData.sort_options[1]] : iconSortData['sort_icon_' + iconSortData.sort_options[0]]}
                    </i>
                </a>
                {#if $lhcList.toggleWidgetData[iconSortData.sort_identifier] == iconSortData.sort_options[0] || $lhcList.toggleWidgetData[iconSortData.sort_identifier] == iconSortData.sort_options[1]}<a class="text-muted" on:click={(e) => lhcServices.toggleWidgetSort(lhcList, iconSortData.sort_identifier, '', '',true)} title="Remove sort"><span class="material-icons">close</span></a>{/if}
            {/each}

        </th>

        {#if no_additional_column === false && check_row_class && $lhcList.additionalColumns}
            {#each $lhcList.additionalColumns as column}
                {#if column.cenabl == true && !column.iconm}
                    <th width="20%">
                        {#if column.icon !== ''}<i class:text-muted={!additional_sort || !column.sorten || ($lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_asc' && $lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_dsc')} class="material-icons chat-active">{column.icon}</i>{/if}{column.name}
                        {#if additional_sort !== "" && column.sorten}
                            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,additional_sort,column.items[0] + '_dsc', column.items[0] + '_asc',true)}>
                                <i class:text-muted={$lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_asc' && $lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_dsc'} class="material-icons">{$lhcList.toggleWidgetData[additional_sort] == column.items[0] + '_dsc' || $lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_asc' ? 'trending_up' : 'trending_down'}</i>
                            </a>
                       {/if}
                    </th>
                {/if}
            {/each}
        {/if}

        {#if type == 'subject_chats' || type == 'alarm_mails' || show_always_subject}
            <th width="25%">
                <span title={$t("widget.subject")} class="material-icons">label</span>{#if show_subject_title}{$t("widget.subject")}{/if}
            </th>
        {/if}

        {#if type == 'bot_chats'}
            <th width="25%">
                <span title={$t("widget.bot")} class="material-icons">android</span>{$t("widget.bot")}
            </th>
        {/if}

        {#if !hide_2_column}
        <th width={column_2_width}>

            {#if type === 'depgroups_stats'}
                <i title={$t("widget_title.pending_chats")} class="material-icons chat-pending">chat</i>
            {/if}

            {#if type === 'online_op'}
                <i title={$t("widget.last_assignment_ago")}  class="material-icons">assignment_ind</i>
            {/if}

            {#if type === 'my_mails' || type === 'alarm_mails' || type === 'active_mails' || type === 'pending_mails'}
            <i title={$t("widget.wait_time")} class="material-icons">access_time</i>
            {/if}

            {#if type === 'pending_chats'}
                <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'wtime_dsc','wtime_asc',true)}> <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'wtime_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'wtime_asc'} title={$t("widget.wait_time")} class="material-icons chat-active">access_time</i><i title={$t("widget.sort_wait_time")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] === 'wtime_dsc' ? 'trending_up' : 'trending_down'}</i></a>
            {/if}

            {#if type === 'my_chats'}
                <i title={$t("widget.last_message")} class="material-icons">access_time</i>
            {/if}

            {#if type === 'unread_chats'}
                <i title={$t("widget.time_ago")} class="material-icons">access_time</i>
            {/if}

            {#if type === 'active_chats' || type === 'bot_chats'}
            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'lmt_dsc','lmt_asc',true)}>
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'lmt_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'lmt_dsc'} title={$t("widget.sort_by_last_msg_time")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'lmt_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'lmt_asc' ? 'trending_up' : 'trending_down'}</i>
            </a>
            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'id_dsc','id_asc',true)}>
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'id_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'id_dsc'} title={$t("widget.sort_by_start_time")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'id_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'id_asc' ? 'trending_up' : 'trending_down'}</i>
            </a>
            {/if}

            {#if type == 'subject_chats'}
                <i title={$t("widget.time_since_last_msg")}  class="material-icons">access_time</i>
            {/if}

            {#if type == 'transfer_chats'}
                <i title={$t("widget.created_at")} class="material-icons">access_time</i>
            {/if}

        </th>
        {/if}

        {#if type == 'online_op'}
        <th width="15%">
            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'rac_dsc','rac_asc',true)}>
                {#if !hide_ac_op_icon}<i title={$t("widget.live_chats")} class="material-icons chat-active" class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'rac_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'rac_dsc'}>sms</i>{/if}
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'rac_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'rac_dsc'} title={$t("widget.sort_by_chat_number_real")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'rac_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'rac_asc' ? 'trending_up' : 'trending_down'}</i>
            </a>
            {#if !hide_ac_sort}
            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'ac_dsc','ac_asc',true)}>
                {#if !hide_ac_op_icon}<i title={$t("widget_title.active_chats")} class="material-icons chat-active" class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'ac_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'ac_asc'}>chat</i>{/if}
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'ac_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'ac_asc'} title={$t("widget.sort_by_chat_number")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'ac_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'ac_asc' ? 'trending_up' : 'trending_down'}</i>
            </a>
            {/if}
        </th>
        {/if}

        {#if type == 'active_chats'}
            <th width="20%">
                <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'op_dsc','op_asc',true)}>
                    <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'op_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'op_dsc'} title={$t("widget.sort_by_op")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'op_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'op_asc' ? 'trending_up' : 'trending_down'}</i>
                </a>
            </th>
        {/if}

        {#if type == 'depgroups_stats'}
        <th width="8%"><i title={$t("widget_title.active_chats")} class="material-icons chat-active">chat</i></th>
        <th width="8%"><i title={$t("widget_title.bot_chats")} class="material-icons chat-active">android</i></th>
        {/if}

        {#if type == 'active_mails' || type == 'alarm_mails' || show_username_always}
        <th width="20%"><i title={$t('widget.operator')} class="material-icons">face</i>{#if show_username_title}{$t('widget.operator')}{/if}</th>
        {/if}

        {#if !hide_third_column}
        <th width={column_3_width}>

            {#if type == 'depgroups_stats'}
            <i title={$t("widget.load_statistic")} class="material-icons text-info">donut_large</i>
            {/if}

            {#if type == 'group_chats'}
                <i title={$t("widget.time_ago")} class="material-icons">access_time</i>
            {:else if type == 'active_chats'}
                <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'dep_dsc','dep_asc',true)}>
                    <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'dep_asc' && $lhcList.toggleWidgetData[sort_identifier] != 'dep_dsc'} title={$t("widget.sort_by_dep")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'dep_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'dep_asc' ? 'trending_up' : 'trending_down'}</i>
                </a>
            {:else if type != 'transfer_chats' && type != 'depgroups_stats'}
                <i title={$t("widget.department")} class="material-icons">home</i>{#if show_department_title}{$t("widget.department")}{/if}
            {/if}

            {#if no_expand === false && type != 'depgroups_stats'}
            <div class="float-end expand-actions">
                <a on:click={lhcServices.changeWidgetHeight(lhcList,panel_id,true)} class="text-muted disable-select">
                    <i title={$t("widget.more_rows")}  class="material-icons">expand</i>
                </a>
                <a on:click={lhcServices.changeWidgetHeight(lhcList,panel_id,false)} class="text-muted disable-select">
                    <i title={$t("widget.less_rows")} class="material-icons">compress</i>
                </a>
            </div>
            {/if}

        </th>
        {/if}

        {#if type == 'depgroups_stats'}
            <th width="20%">
                <i title={$t("widget.op_statistic")} class="material-icons text-info">support_agent</i>
                <div class="float-end expand-actions">
                    <a on:click={lhcServices.changeWidgetHeight(lhcList,panel_id,true)} class="text-muted disable-select">
                        <i title={$t("widget.more_rows")}  class="material-icons">expand</i>
                    </a>
                    <a on:click={lhcServices.changeWidgetHeight(lhcList,panel_id,false)} class="text-muted disable-select">
                        <i title={$t("widget.less_rows")} class="material-icons">compress</i>
                    </a>
                </div>
            </th>
        {/if}

    </tr>
    </thead>
    {/if}



    {#if type == 'depgroups_stats'}

        {#if !$lhcList.departmentd_hide_dgroup}
            <tbody>
            {#each $lhcList[type].list as depgroup (depgroup.id)}
                <tr>
                    <td><div class="abbr-list" title={depgroup.name}><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_group_ids)/' + depgroup.id + '/(chat_status_ids)/0/1/5'}>{depgroup.name}</a></div></td>
                    <td><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_group_ids)/' + depgroup.id + '/(chat_status_ids)/0'}>{depgroup.pchats_cnt ? depgroup.pchats_cnt : 0}</a></td>
                    <td><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_group_ids)/' + depgroup.id + '/(chat_status_ids)/1'}>{depgroup.achats_cnt ? depgroup.achats_cnt : 0}</a></td>
                    <td><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_group_ids)/' + depgroup.id + '/(chat_status_ids)/5'}>{depgroup.bchats_cnt ? depgroup.bchats_cnt : 0}</a></td>
                    <td nowrap title='{depgroup.inachats_cnt ? depgroup.inachats_cnt : "0"} {$t("widget.inactive_chats")+".\n"}{depgroup.inopchats_cnt ? depgroup.inopchats_cnt : "0"} {$t("widget.inactive_op_chats")+".\n"}{depgroup.acopchats_cnt ? depgroup.acopchats_cnt : "0"} {$t("widget.active_op_chats")+".\n"}{$t("widget.hard_limit")} - {depgroup.max_load_h ? depgroup.max_load_h : "0"}, {$t("widget.soft_limit")} - {depgroup.max_load ? depgroup.max_load : "0"}.{"\n"+$t("widget.hard_limit_explain")}'>
                        {#if permissions.indexOf('lhstatistic_statisticdep') !== -1}
                            <a href="#" on:click={(e) => lhcServices.openModal('statistic/departmentstats/'+depgroup.id + '/(type)/group')} >
                                <span class={(depgroup.max_load_h && depgroup.max_load_h - ((depgroup.acopchats_cnt ? depgroup.acopchats_cnt : 0) - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{depgroup.max_load_h ? (depgroup.max_load_h - ((depgroup.acopchats_cnt ? depgroup.acopchats_cnt : 0) - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0))) : 'n/a'}</span>&nbsp;({depgroup.max_load ? (depgroup.max_load - ((depgroup.achats_cnt ? depgroup.achats_cnt : 0) - (depgroup.inachats_cnt ? depgroup.inachats_cnt : 0))) : 'n/a'})
                            </a>
                        {:else}
                            <span class={(depgroup.max_load_h && depgroup.max_load_h - ((depgroup.acopchats_cnt ? depgroup.acopchats_cnt : 0) - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{depgroup.max_load_h ? (depgroup.max_load_h - ((depgroup.acopchats_cnt ? depgroup.acopchats_cnt : 0) - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0))) : 'n/a'}</span>&nbsp;({depgroup.max_load ? (depgroup.max_load - ((depgroup.achats_cnt ? depgroup.achats_cnt : 0) - (depgroup.inachats_cnt ? depgroup.inachats_cnt : 0))) : 'n/a'})
                        {/if}
                    </td>
                    <td>
                        {#if permissions.indexOf('lhstatistic_statisticdep') !== -1}
                            <a href="#" on:click={(e) => lhcServices.openModal('statistic/departmentstats/' + depgroup.id + '/(type)/group/(tab)/op')}>
                                {depgroup.max_load_op ? depgroup.max_load_op : 'n/a'} ({depgroup.max_load_op_h ? depgroup.max_load_op_h : 'n/a'})
                            </a>
                        {:else}
                            {depgroup.max_load_op ? depgroup.max_load_op : 'n/a'} ({depgroup.max_load_op_h ? depgroup.max_load_op_h : 'n/a'})
                        {/if}
                    </td>
                </tr>
            {/each}
            </tbody>
        {/if}

        {#if !$lhcList.departmentd_hide_dep && $lhcList.departments_stats.list.length > 0}
        <thead>
        <tr>
            <th width="40%"><i title={$t("widget.department")} class="material-icons">home</i></th>
            <th width="12%"><i title={$t("widget_title.pending_chats")} class="material-icons chat-pending">chat</i></th>
            <th width="12%"><i title={$t("widget_title.active_chats")} class="material-icons chat-active">chat</i></th>
            <th width="12%"><i title={$t("widget_title.bot_chats")} class="material-icons chat-active">android</i></th>
            <th width="11%"><i title={$t("widget.load_statistic")} class="material-icons text-info">donut_large</i></th>
            <th width="11%"><i title={$t("widget.op_statistic")} class="material-icons text-info">support_agent</i></th>
        </tr>
        </thead>

        <tbody>

        {#each $lhcList.departments_stats.list as department (department.id)}
        <tr>
            <td>
                <div class="abbr-list" title={department.name}><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_ids)/' + department.id + '/(chat_status_ids)/0/1/5'}>{department.name}</a></div>
            </td>
            <td><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_ids)/' + department.id + '/(chat_status_ids)/0'}>{department.pending_chats_counter ? department.pending_chats_counter : 0}</a></td>
            <td><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_ids)/' + department.id + '/(chat_status_ids)/1'}>{department.active_chats_counter ? department.active_chats_counter : 0}</a></td>
            <td><a class="d-block" href={WWW_DIR_JAVASCRIPT + 'chat/list/(department_ids)/' + department.id + '/(chat_status_ids)/5'}>{department.bot_chats_counter ? department.bot_chats_counter : 0}</a></td>
            <td nowrap title='{department.inactive_chats_cnt ? department.inactive_chats_cnt : "0"} {$t("widget.inactive_chats")+".\n"}{department.inop_chats_cnt ? department.inop_chats_cnt : "0"} {$t("widget.inactive_op_chats")+".\n"}{department.acop_chats_cnt ? department.acop_chats_cnt : "0"} {$t("widget.active_op_chats")+".\n"}{$t("widget.hard_limit")} - {department.max_load_h ? department.max_load_h : "0"}, {$t("widget.soft_limit")} - {department.max_load ? department.max_load : "0"}.{"\n"+$t("widget.hard_limit_explain")}'>
                {#if permissions.indexOf('lhstatistic_statisticdep') !== -1}
                    <a href="#" on:click={(e) => lhcServices.openModal('statistic/departmentstats/'+department.id)}>
                        <span class={(department.max_load_h && department.max_load_h - ((department.acop_chats_cnt ? department.acop_chats_cnt : 0) - (department.inop_chats_cnt ? department.inop_chats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{department.max_load_h ? (department.max_load_h - ((department.acop_chats_cnt ? department.acop_chats_cnt : 0) - (department.inop_chats_cnt ? department.inop_chats_cnt : 0))) : 'n/a'}</span>&nbsp;({department.max_load ? (department.max_load - ((department.active_chats_counter ? department.active_chats_counter : 0) - (department.inactive_chats_cnt ? department.inactive_chats_cnt : 0))) : 'n/a'})
                    </a>
                {:else}
                    <span class={(department.max_load_h && department.max_load_h - ((department.acop_chats_cnt ? department.acop_chats_cnt : 0) - (department.inop_chats_cnt ? department.inop_chats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{department.max_load_h ? (department.max_load_h - ((department.acop_chats_cnt ? department.acop_chats_cnt : 0) - (department.inop_chats_cnt ? department.inop_chats_cnt : 0))) : 'n/a'}</span>&nbsp;({department.max_load ? (department.max_load - ((department.active_chats_counter ? department.active_chats_counter : 0) - (department.inactive_chats_cnt ? department.inactive_chats_cnt : 0))) : 'n/a'})
                {/if}
            </td>
            <td>
                {#if permissions.indexOf('lhstatistic_statisticdep') !== -1}
                    <a href="#" on:click={(e) => lhcServices.openModal('statistic/departmentstats/' + department.id + '/(tab)/op')}>
                        {department.max_load_op ? department.max_load_op : 'n/a'} ({department.max_load_op_h ? department.max_load_op_h : 'n/a'})
                    </a>
                {:else}
                    {department.max_load_op ? department.max_load_op : 'n/a'} ({department.max_load_op_h ? department.max_load_op_h : 'n/a'})
                {/if}
            </td>
        </tr>
        {/each}


        </tbody>
        {/if}


    {:else if type == 'transfer_chats'}

        <tbody>
        {#each $lhcList[type].list as chat}
            <tr>
                <td>

                    {#if chat.country_code}
                        <span><img src={www_dir_flags + "/" + chat.country_code + ".png"} alt={chat.country_name} title={chat.country_name} /></span>
                    {/if}

                    <a title={$t("widget.open_new_window")} class="material-icons" on:click={(e) => lhcServices.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id,chat.transfer_scope)}>open_in_new</a>

                    <a title="[{chat.id}]" on:click={(e) => {chat.transfer_scope == 1 ? lhcServices.previewMail(chat.id,e) : lhcServices.previewChat(chat.id,e)}} class="material-icons">info_outline</a>

                    <a on:click={(e) => lhcServices.startChatTransfer(chat.id,chat.nick,chat.transfer_id,chat.transfer_scope)} title={$t("widget.accept_chat")}>{chat.nick}</a>

                </td>
                <td nowrap="nowrap" colspan="2">
                    <div class="abbr-list">{chat.time_front}</div>
                </td>
            </tr>
        {/each}
        </tbody>


        {#if $lhcList.transfer_dep_chats.list.length > 0}
        <thead>
        <tr>
            <th width="60%"><i title="Visitor" class="material-icons">face</i> {$t("widget.transferred_to_dep")}</th>
            <th width="40%" colspan="2"><i title={$t("widget.transfer_time")} class="material-icons">access_time</i></th>
        </tr>
        </thead>
        <tbody>
        {#each $lhcList['transfer_dep_chats'].list as chat}
            <tr>
                <td>
                    {#if chat.country_code}
                        <span><img src={www_dir_flags + "/" + chat.country_code + ".png"} alt={chat.country_name} title={chat.country_name} /></span>
                    {/if}
                    <a title={$t("widget.open_new_window")} class="material-icons" on:click={(e) => lhcServices.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id,chat.transfer_scope)}>open_in_new</a>
                    <a title="[{chat.id}]" on:click={(e) => {chat.transfer_scope == 1 ? lhcServices.previewMail(chat.id,e) : lhcServices.previewChat(chat.id,e)}} class="material-icons">info_outline</a>
                    <a on:click={(e) => lhcServices.startChatTransfer(chat.id,chat.nick,chat.transfer_id,chat.transfer_scope)} title={$t("widget.accept_chat")}>{chat.nick}</a>
                </td>
                <td nowrap="nowrap" colspan="2">
                    <div class="abbr-list">{chat.time_front}</div>
                </td>
            </tr>
        {/each}
        </tbody>
        {/if}



    {:else}
        <tbody>
        {#each $lhcList[type].list as chat (chat.id)}
            <tr on:click={(e) => (check_row_class && openItem(chat)) || (override_item_open && openRemoteItem(chat))} class:user-away-row={check_online && check_row_class && chat.user_status_front == 2} class:user-online-row={check_online && check_row_class && !chat.user_status_front}>
                <td>

                    <div class="abbr-list">

                    {#if chat.country_code}
                        <span><img src={www_dir_flags + "/" + chat.country_code + ".png"} alt={chat.country_name} title={chat.country_name} /></span>
                    {/if}


                    {#each custom_icons as iconData}
                        {#if iconData.icon_attr_type == 'bool' || iconData.icon_attr_type == 'cmp'}
                            {#if (
                                (chat[iconData['icon_attr']] && iconData['icon_attr_true'] && iconData.icon_attr_type == 'bool') ||
                                (iconData.icon_attr_type == 'bool' && !chat[iconData['icon_attr']] && iconData['icon_attr_false']) ||
                                (iconData.icon_attr_type == 'cmp' && chat[iconData['icon_attr']] == iconData['icon_attr_val'])
                            )}
                                <span title={iconData['title'] ? iconData['title'] : null} class={iconData.class + " me-0 " + (iconData.class_false ? getClassListList(chat, iconData.class_false, false) : '') + " " + (iconData.class_true ? getClassListList(chat, iconData.class_true, true) : '')} on:click={(e) => iconData['click'] ? ee.emitEvent(iconData['click'],[chat]) : null} >{((chat[iconData['icon_attr']] && iconData.icon_attr_type == 'bool')|| (chat[iconData['icon_attr']] == iconData['icon_attr_val'] && iconData.icon_attr_type == 'cmp')) ? iconData['icon_attr_true'] : iconData['icon_attr_false']}</span>
                            {/if}
                        {:else if iconData.icon_attr_type == 'string'}
                            {#if iconData['class']}
                                   <span title={iconData['title'] ? iconData['title'] : null} class={iconData.class}>{iconData['icon_attr_prepend'] ? iconData['icon_attr_prepend'] : ''}{chat[iconData.icon_attr]}{iconData['icon_attr_append'] ? iconData['icon_attr_append'] : ''}</span>
                                {:else}
                                   {iconData['icon_attr_prepend'] ? iconData['icon_attr_prepend'] : ''}{chat[iconData.icon_attr]}{iconData['icon_attr_append'] ? iconData['icon_attr_append'] : ''}
                            {/if}
                        {/if}
                    {/each}

                    {#if type == 'online_op'}


                        {#if chat.avatar && !hide_op_avatar}
                            <img class="rounded-circle" src={chat.avatar} alt="" width="20" />
                        {/if}

                        {#if chat.user_id != $lhcList.current_user_id && permissions.indexOf('lhgroupchat_use') !== -1}
                            <a href="#" on:click={(e) => lhcServices.startChatOperator(chat.user_id)} title={$t("widget.start_chat")}><i class="material-icons me-0">chat</i></a>
                        {/if}

                        {#if chat.offline_since_s}
                            <i class="material-icons me-0" style:color={chat.offline_since_s.c ? chat.offline_since_s.c  : null} title={$t("widget.went_offline_ago",{'ago': chat.offline_since})}>{"clock_loader_"+chat.offline_since_s.i}</i>
                        {/if}

                        {#if permissions.indexOf('lhuser_setopstatus') !== -1}
                            <i class="material-icons me-0 action-image" class:text-success={chat.hide_online != 1} class:text-danger={chat.hide_online == 1} on:click={(e) => lhcServices.openModal('user/setopstatus/'+chat.user_id)} title={$t("widget.change_op_status")} >{chat.hide_online == 1 ? 'flash_off' : 'flash_on'}</i>
                        {:else}
                            <i class="material-icons me-0" class:text-success={chat.hide_online != 1} class:text-danger={chat.hide_online == 1}>{chat.hide_online == 1 ? 'flash_off' : 'flash_on'}</i>
                        {/if}

                        {#if permissions.indexOf('lhstatistic_userstats') !== -1}
                            <a class:text-muted={chat.ro} href="#" title={$t("widget.last_activity_ago") + " " + chat.lastactivity_ago + " " + $t("widget.see_op_stats")} on:click={(e) => lhcServices.openModal('statistic/userstats/'+chat.user_id)}>{chat.name_official}{#if chat.hide_online && chat.offline_since}<span title={$t("widget.went_offline_ago",{'ago': chat.offline_since})}>, {chat.offline_since}</span>{/if}</a>
                        {:else}
                            <span title={$t("widget.last_activity_ago") + " " + chat.lastactivity_ago}>{chat.name_official}</span>{#if chat.hide_online && chat.offline_since}, <span title={$t("widget.went_offline_ago",{'ago': chat.offline_since})}>{chat.offline_since}</span>{/if}
                        {/if}

                        {#if chat.lac_ago_s > 35}<span title={$t("widget.no_ping_for") + " " + chat.lastactivity_ago} class="material-icons text-danger">wifi_off</span>{/if}

                    {:else if type == 'group_chats'}

                            <a class="d-block action-image" on:click={(e) => lhcServices.startGroupChat(chat.id,chat.name)}>
                                {#if chat.is_member == true && (!chat.ls_id || chat.ls_id < chat.last_msg_id)}
                                    <i class="material-icons text-warning" title={$t("group_chat.unread_messages")}>whatshot</i>
                                {/if}
                                <i class="material-icons">{chat.type == 1 ? 'security' : 'public'}</i>{#if chat.user_id == $lhcList.current_user_id}<i class="material-icons">account_balance</i>{/if}[{chat.tm}] {chat.name}
                            </a>

                    {:else}

                            {#if type == 'subject_chats'}
                                <span class="material-icons chat-pending me-0" class:chat-active={chat.status == 1 || chat.status == 5}>{chat.status == 5 ? 'android' : 'chat'}</span>
                            {/if}

                            {#if type == 'pending_chats' && permissions.indexOf('lhchat_deletechat') !== -1}
                                <a title={$t("widget.delete_chat")} class="material-icons float-end" on:click={(e) => {lhcServices.deleteChat(chat.id);e.stopPropagation()}}>delete</a>
                            {/if}

                            {#if chat.can_edit_chat && type == 'pending_chats' && permissions.indexOf('lhchat_redirectcontact') !== -1}
                                <a class="material-icons me-0" title={$t("widget.redirect_contact")} on:click={(e) => lhcServices.redirectContact(chat.id,$t("widget.are_you_sure"),e)}>reply</a>
                            {/if}

                            {#if type == 'my_mails' || type == 'active_mails' || type == 'pending_mails' || type == 'alarm_mails'}

                                {#if type == 'my_mails' && chat.status != 1}
                                    <i title="Pending chat" class="material-icons me-0 chat-unread">&#xE80E;</i>
                                {/if}

                                <a title="{chat.id}" on:click={(e) => lhcServices.previewMail(chat.id,e)} class="material-icons">info_outline</a>

                                {#if type == 'alarm_mails'}
                                    <i class={"material-icons me-0 " + (chat.status == 1 ? 'chat-active' : 'chat-pending')} >mail_outline</i>
                                {/if}

                                <span title={chat.from_address}>{chat.from_name} | {chat.subject_front}</span>

                            {:else if !no_chat_preview}
                               <a title="[{chat.id}] {chat.time_created_front}" on:click={(e) => lhcServices.previewChat(chat.id,e)} class="material-icons me-0">info_outline</a>
                            {/if}

                            {#if chat.status_sub == 7}<i class="material-icons me-0" title={$t("widget.offline_request")}>mail</i>{/if}

                            {#if type == 'bot_chats'}
                                <span title={$t("widget.msg_v_number")}>[{chat.msg_v || 0}]</span> {#if chat.msg_v > $lhcList.bot_st.msg_nm}<i title={$t("widget.more_than") + ' ' + String($lhcList.bot_st.msg_nm) + ' ' + $t("widget.user_msgs")} class="material-icons text-warning me-0">whatshot</i>{/if}
                            {/if}

                            {#if chat.hum && (type == 'active_chats' || type == 'my_chats')}
                                <i title={$t("widget.has_unread_messages")} class="material-icons me-0 text-danger">feedback</i>
                            {/if}

                            {#if chat.aicons}
                                {#each Object.entries(chat.aicons) as [icon_key,icon]}
                                    {#if $lhcList.excludeIcons.length == 0 || $lhcList.excludeIcons.indexOf(icon.i) === -1}
                                        {#if icon.i && icon.i.includes('/')}
                                            <img src={icon.i} title={icon.t ? icon.t : icon.i} />
                                        {:else}
                                            <i class="material-icons me-0" style:color={icon.c ? icon.c : '#6c757d'} title={icon.t ? icon.t : icon.i} >{icon.i || icon}</i>
                                        {/if}
                                    {/if}
                                {/each}
                            {/if}

                            {#if no_additional_column === false && check_row_class && $lhcList.additionalColumns}
                                {#each $lhcList.additionalColumns as column}
                                    {#if column.cenabl == true && column.iconm == true}
                                        {#each column.items as val}
                                            {#if chat[val]}
                                                <span class="material-icons me-0" on:click={(e) => {column.iconp && lhcServices.openModal('chat/icondetailed/'+chat.id + '/' + column.id,e)}} title={column.iconp == true ? 'Click for more information | ' : ''}{chat[val + '_tt'] ? chat[val + '_tt'] : chat[val]} style:color={column.icon[chat[val]] && column.icon[chat[val]].color ? column.icon[chat[val]].color : '#CECECE'}>{column.icon[chat[val]] && column.icon[chat[val]].icon ? column.icon[chat[val]].icon : 'unknown_document'}</span>
                                            {/if}
                                        {/each}
                                    {/if}
                                 {/each}
                             {/if}

                            {#if type != 'my_mails' && type != 'active_mails' && type != 'pending_mails' && type != 'alarm_mails'}
                                <span class:fw-bold={chat.nb} style:color={chat.nc ? chat.nc : null}  >{chat.nick ? chat.nick : ''}</span><small>{(type == 'pending_chats' || type == 'subject_chats') && chat.plain_user_name !== undefined ? ' | ' + chat.plain_user_name : ''}</small>
                            {/if}

                            {#if chat.subject_list && (type == 'pending_chats' || type == 'bot_chats' || type == 'active_chats' || type == 'my_chats')}
                                {#each chat.subject_list as subjectitem}
                                    <span class="badge bg-info fs12 me-1" style:background-color|important={subjectitem.c ? '#'+subjectitem.c : null}>{subjectitem.n}</span>
                                {/each}
                            {/if}

                    {/if}

                    </div>
                </td>

                {#if no_additional_column === false && check_row_class && $lhcList.additionalColumns}
                    {#each $lhcList.additionalColumns as column}
                        {#if column.cenabl == true && !column.iconm}
                            <td>
                            {#each column.items as val}
                                {#if chat[val]}
                                    <div class="abbr-list">{chat[val]}</div>
                                {/if}
                            {/each}
                            </td>
                        {/if}
                    {/each}
                {/if}

                {#if type == 'subject_chats' || type == 'alarm_mails' || show_always_subject}
                    <td>
                        {#if chat.subject_list}
                            {#each chat.subject_list as subjectitem}
                                <span class="badge bg-info fs12 me-1" style:background-color|important={subjectitem.c ? '#'+subjectitem.c : null}>{subjectitem.n}</span>
                            {/each}
                        {/if}
                    </td>
                {/if}

                {#if type == 'bot_chats'}
                    <td title={chat.gbot_id}>
                        {#if chat.bot_short_name}
                            {chat.bot_short_name}
                        {:else}
                            <span class="material-icons">android</span>{chat.gbot_id}
                        {/if}
                    </td>
                {/if}

                {#if !hide_2_column}
                <td class:align-middle={type == "online_op"}>

                    {#if type == 'group_chats'}

                        {#if chat.is_member == true && chat.jtime == 0}
                            <button title={$t("group_chat.accept_join")} class="btn btn-xs btn-outline-info" on:click={(e) => lhcServices.startGroupChat(chat.id,chat.name)}>Accept invite</button>
                        {/if}

                        {#if chat.is_member == true && chat.jtime == 0}
                            <button title={$t("group_chat.reject_private")} class="btn btn-xs btn-outline-danger" on:click={(e) => lhcServices.rejectGroupChat(chat.id, e)}>Reject invite</button>
                        {/if}

                        {#if chat.is_member == false && chat.jtime == 0}
                            <button title={$t("group_chat.join_public")} class="btn btn-xs btn-outline-info" on:click={(e) => lhcServices.startGroupChat(chat.id,chat.name)}>Join public chat</button>
                        {/if}

                        {#if chat.is_member == true && chat.jtime > 0}
                            <button title={$t("group_chat.already_member")} class="btn btn-xs btn-outline-secondary" on:click={(e) => lhcServices.startGroupChat(chat.id,chat.name)}>Member</button>
                        {/if}

                    {/if}

                    {#if (chat.can_edit_chat && type == 'pending_chats') || type == 'my_mails' || type == 'pending_mails'}
                        <div class="abbr-list" title="{chat.wait_time_pending || '00 s.'}">{chat.wait_time_pending || '00 s.'}</div>
                    {/if}

                    {#if type == 'active_mails'}
                        <div class="abbr-list" title="{chat.pnd_time_front}">{chat.pnd_time_front || '00 s.'}</div>
                    {/if}

                    {#if type == 'alarm_mails'}
                        <div class="abbr-list">{!chat.status ? chat.wait_time_pending : chat.wait_time_response}</div>
                    {/if}

                    {#if type == 'online_op'}
                        <div class="abbr-list" title={$t("widget.last_assignment_ago")}>{chat.last_accepted_ago || "-"}</div>
                    {/if}

                    {#if type == 'active_chats' || type == 'subject_chats' || type == 'bot_chats'}
                        <div class="abbr-list" title="Chat started at - {chat.time_created_front}">
                            <span class="material-icons text-success" title={$t("widget.send_receive")} class:text-danger={chat.pnd_rsp}>{chat.pnd_rsp === true ? 'call_received' : 'call_made'}</span>{#if chat.last_msg_time_front}{chat.last_msg_time_front}{:else}&#x2709;{/if}
                        </div>
                    {/if}

                    {#if type == 'unread_chats'}
                        <div class="abbr-list" title="{chat.unread_time.hours} h. {chat.unread_time.minits} m. {chat.unread_time.seconds} s.">{chat.unread_time.hours} h. {chat.unread_time.minits} m. {chat.unread_time.seconds} s.</div>
                    {/if}


                    {#if type == 'my_chats'}
                        <div class="abbr-list" title={chat.status == 1 ? $t("widget.active")  : $t("widget.pending")}>
                            {#if chat.status != 1}<i title={$t("widget.pending")} class="material-icons chat-unread">&#xE80E;</i>{/if}<span class="material-icons text-success" title={$t("widget.send_receive")} class:text-danger={chat.pnd_rsp}>{chat.pnd_rsp === true ? 'call_received' : 'call_made'}</span>{#if !chat.status}&#x23F3; {chat.wait_time_pending}{:else}{chat.last_msg_time_front}{/if}
                        </div>
                    {/if}
                </td>
                {/if}

                {#if type == 'online_op'}
                    <td title={$t("widget.op_chats_statistic")} class="align-middle"  class:text-danger={chat.max_chats && chat.max_chats > 0 && chat.free_slots <= 0} class:text-success={chat.max_chats && chat.max_chats > 0 && chat.free_slots >= 1}>
                        {chat.live_chats} {#if !hide_ac_stats} <abbr title={$t("widget.live_chats")+"\n("+chat.active_chats + " + " + chat.pending_chats + " - " + chat.inactive_chats + ")"}>l.c</abbr>, {chat.max_chats && chat.max_chats > 0 ? chat.free_slots : ' n/a'} <abbr title={$t("widget.free_slots") + "\n" + chat.max_chats + " - ((" + chat.active_chats + " + " + chat.pending_chats + ") - "+chat.inactive_chats+")"}>f.s</abbr>{/if}
                    </td>
                {/if}

                {#if type == 'active_chats'}
                    <td>
                        <div class="abbr-list" title="{chat.n_off_full} | {chat.plain_user_name}">{chat.n_office}</div>
                    </td>
                {/if}

                {#if type == 'active_mails' || type == 'alarm_mails' || show_username_always}
                    <td>
                        {chat.plain_user_name ? chat.plain_user_name : ''}
                    </td>
                {/if}

                {#if !hide_third_column}
                <td class:align-middle={type == "online_op"}>
                    {#if type == 'online_op'}
                        <div class="abbr-list" title="{chat.departments_names.join(', ')}">{chat.departments_names.join(", ")}</div>
                    {:else if type == 'group_chats'}
                        <div class="abbr-list" title="{chat.time_front}">{chat.time_front}</div>
                    {:else}
                        <div class="abbr-list" title="{chat.department_name}{chat.product_name ? ' | '+chat.product_name : ''}">

                            {#if (type === "active_chats" || type === "pending_chats") && permissions.indexOf('lhstatistic_statisticdep') !== -1 }
                                <a class="text-primary" on:click={(e) => lhcServices.openModal('statistic/departmentstats/'+chat.dep_id,e)}><i class="material-icons">donut_large</i>{chat.department_name}{chat.product_name ? ' | '+chat.product_name : ''}</a>
                            {:else}
                                {chat.department_name}{chat.product_name ? ' | '+chat.product_name : ''}
                            {/if}

                        </div>
                    {/if}
                </td>
                {/if}


            </tr>
        {/each}

        {#if type == 'group_chats'}
            <tr>
                <td>
                    <input type="text" name="" class="form-control form-control-sm" bind:value={$lhcList.new_group_name} placeholder={$t("group_chat.new_group_name")}>
                </td>
                <td>
                    <select class="form-control form-control-sm" bind:value={$lhcList.new_group_type} >
                        <option value="1">{$t("group_chat.private_group")}</option>
                        {#if permissions.indexOf('lhgroupchat_public_chat') !== -1}
                            <option value="">{$t("group_chat.public_group")}</option>
                        {/if}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 d-block" on:click={() => lhcServices.startNewGroupChat(lhcList,$lhcList.new_group_name,$lhcList.new_group_type)}>{$t("group_chat.new")}</button>
                </td>
            </tr>
        {/if}

        </tbody>
    {/if}
</table>


