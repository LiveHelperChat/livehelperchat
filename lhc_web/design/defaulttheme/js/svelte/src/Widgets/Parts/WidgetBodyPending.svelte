<script>
    import lhcServices from '../../lib/Services.js';
    import { t } from "../../i18n/i18n.js";

    export let sort_identifier = null;
    export let type = null;
    export let lhcList = null;
    export let www_dir_flags = null;
    export let panel_id = null;
    export let permissions = [];
    export let column_2_width = "20%";
    export let column_1_width = "40%";
    export let column_3_width = "20%";
    export let additional_sort = "";

    let check_row_class = type !== "transfer_chats" && type !== "group_chats" && type !== "online_op" && type !== "depgroups_stats";

    function openItem(chat) {
        if (type === "group_chats") {
            lhcServices.startGroupChat(chat.id,chat.name)
        } else {
            lhcServices.startChat(chat.id,chat.nick)
        }
    }

</script>

<table class="table table-sm mb-0 table-small table-fixed" class:list-chat-table={check_row_class}>

    {#if type != 'depgroups_stats' || (!$lhcList.departmentd_hide_dgroup && $lhcList[type].list.length > 0)}
    <thead>
    <tr>
        <th width={column_1_width}>

            {#if type == 'online_op'}
            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'onl_dsc','onl_asc',true)}>
                <i title={$t("widget.operator")} class="material-icons">account_box</i>
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'onl_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'onl_asc'} title={$t("widget.sort_by_online_status")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'onl_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'onl_asc' ? 'trending_up' : 'trending_down'}</i>
            </a>
            {:else if type == 'depgroups_stats'}
                <i title={$t("widget.dep_group")} class="material-icons">&#xE84F;</i>
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
                <i title={$t("widget.visitor")} class="material-icons">face</i>
            {/if}

            {#if type == 'transfer_chats'}
                {$t("widget.transferred_to_you")}
            {/if}

            {#if type == 'pending_chats'}
                <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'id_dsc','id_asc',true)}><i title={$t("widget.sort")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] === 'id_dsc' ? 'trending_up' : 'trending_down'}</i></a>
            {/if}

        </th>

        {#if check_row_class && $lhcList.additionalColumns}
            {#each $lhcList.additionalColumns as column}
                {#if column.cenabl == true && !column.iconm}
                    <th width="20%">
                        {#if column.icon !== ''}<i class="material-icons text-muted">{column.icon}</i>{/if}{column.name}
                        {#if additional_sort !== "" && column.sorten}
                            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,additional_sort,column.items[0] + '_dsc', column.items[0] + '_asc',true)}>
                                <i class:text-muted={$lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_asc' && $lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_dsc'} class="material-icons">{$lhcList.toggleWidgetData[additional_sort] == column.items[0] + '_dsc' || $lhcList.toggleWidgetData[additional_sort] != column.items[0] + '_asc' ? 'trending_up' : 'trending_down'}</i>
                            </a>
                       {/if}
                    </th>
                {/if}
            {/each}
        {/if}

        {#if type == 'subject_chats'}
            <th width="25%">
                <span class="material-icons">label</span>
            </th>
        {/if}

        <th width={column_2_width}>

            {#if type === 'depgroups_stats'}
                <i title={$t("widget_title.pending_chats")} class="material-icons chat-pending">chat</i>
            {/if}

            {#if type === 'online_op'}
                <i title={$t("widget.last_activity_ago")}  class="material-icons">access_time</i>
            {/if}

            {#if type === 'pending_chats'}
            <i title={$t("widget.wait_time")} class="material-icons">access_time</i>
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

        {#if type == 'online_op'}
        <th width="15%">
            <a on:click={(e) => lhcServices.toggleWidgetSort(lhcList,sort_identifier,'ac_dsc','ac_asc',true)}>
                <i title={$t("widget_title.active_chats")} class="material-icons chat-active">chat</i>
                <i class:text-muted={$lhcList.toggleWidgetData[sort_identifier] != 'ac_dsc' && $lhcList.toggleWidgetData[sort_identifier] != 'ac_asc'} title={$t("widget.sort_by_chat_number")} class="material-icons">{$lhcList.toggleWidgetData[sort_identifier] == 'ac_dsc' || $lhcList.toggleWidgetData[sort_identifier] != 'ac_asc' ? 'trending_up' : 'trending_down'}</i>
            </a>
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
        <th width="12%"><i title={$t("widget_title.active_chats")} class="material-icons chat-active">chat</i></th>
        <th width="12%"><i title={$t("widget_title.bot_chats")} class="material-icons chat-active">android</i></th>
        {/if}

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
                <i title={$t("widget.department")} class="material-icons">home</i>
            {/if}

            <div class="float-end expand-actions">
                <a on:click={lhcServices.changeWidgetHeight(lhcList,panel_id,true)} class="text-muted disable-select">
                    <i title={$t("widget.more_rows")}  class="material-icons">expand</i>
                </a>
                <a on:click={lhcServices.changeWidgetHeight(lhcList,panel_id,false)} class="text-muted disable-select">
                    <i title={$t("widget.less_rows")} class="material-icons">compress</i>
                </a>
            </div>
        </th>

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
                                <span class={(depgroup.max_load_h && depgroup.max_load_h - (depgroup.acopchats_cnt - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{depgroup.max_load_h ? (depgroup.max_load_h - (depgroup.acopchats_cnt - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0))) : 'n/a'}</span>&nbsp;({depgroup.max_load ? (depgroup.max_load - (depgroup.achats_cnt - (depgroup.inachats_cnt ? depgroup.inachats_cnt : 0))) : 'n/a'})
                            </a>
                        {:else}
                            <span class={(depgroup.max_load_h && depgroup.max_load_h - (depgroup.acopchats_cnt - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{depgroup.max_load_h ? (depgroup.max_load_h - (depgroup.acopchats_cnt - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0))) : 'n/a'}</span>&nbsp;({depgroup.max_load ? (depgroup.max_load - (depgroup.achats_cnt - (depgroup.inachats_cnt ? depgroup.inachats_cnt : 0))) : 'n/a'})
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
            <th width="21%"><i title={$t("widget.load_statistic")} class="material-icons text-info">donut_large</i></th>
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
                        <span class={(department.max_load_h && department.max_load_h - (department.acop_chats_cnt - (department.inop_chats_cnt ? department.inop_chats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{department.max_load_h ? (department.max_load_h - (department.acop_chats_cnt - (department.inop_chats_cnt ? department.inop_chats_cnt : 0))) : 'n/a'}</span>&nbsp;({department.max_load ? (department.max_load - (department.active_chats_counter - (department.inactive_chats_cnt ? department.inactive_chats_cnt : 0))) : 'n/a'})
                    </a>
                {:else}
                    <span class={(department.max_load_h && department.max_load_h - (department.acop_chats_cnt - (department.inop_chats_cnt ? department.inop_chats_cnt : 0)) <= 3) ? 'text-danger fw-bold' : ''}>{department.max_load_h ? (department.max_load_h - (department.acop_chats_cnt - (department.inop_chats_cnt ? department.inop_chats_cnt : 0))) : 'n/a'}</span>&nbsp;({department.max_load ? (department.max_load - (department.active_chats_counter - (department.inactive_chats_cnt ? department.inactive_chats_cnt : 0))) : 'n/a'})
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

                    <a title={$t("widget.open_new_window")} class="material-icons" on:click={(e) => lhcServices.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id)}>open_in_new</a>

                    <a title="[{chat.id}]" on:click={(e) => lhcServices.previewChat(chat.id,e)} class="material-icons">info_outline</a>

                    <a on:click={(e) => lhcServices.startChatTransfer(chat.id,chat.nick,chat.transfer_id)} title={$t("widget.accept_chat")}>{chat.nick}</a>

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
                    <a title={$t("widget.open_new_window")} class="material-icons" on:click={(e) => lhcServices.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id)}>open_in_new</a>
                    <a title="[{chat.id}]" on:click={(e) => lhcServices.previewChat(chat.id,e)} class="material-icons">info_outline</a>
                    <a on:click={(e) => lhcServices.startChatTransfer(chat.id,chat.nick,chat.transfer_id)} title={$t("widget.accept_chat")}>{chat.nick}</a>
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
            <tr on:click={(e) => check_row_class && openItem(chat)} class:user-away-row={check_row_class && chat.user_status_front == 2} class:user-online-row={check_row_class && !chat.user_status_front}>
                <td>

                    {#if type == 'online_op'}

                        {#if chat.avatar}
                            <img class="rounded-circle" src={chat.avatar} alt="" width="20" />
                        {/if}

                        {#if chat.user_id != $lhcList.current_user_id && permissions.indexOf('lhgroupchat_use') !== -1}
                            <a href="#" on:click={(e) => lhcServices.startChatOperator(chat.user_id)} title={$t("widget.start_chat")}><i class="material-icons">chat</i></a>
                        {/if}

                        {#if permissions.indexOf('lhuser_setopstatus') !== -1}
                            <i class="material-icons action-image" on:click={(e) => lhcServices.openModal('user/setopstatus/'+chat.user_id)} title={$t("widget.change_op_status")} >{chat.hide_online == 1 ? 'flash_off' : 'flash_on'}</i>
                        {:else}
                            <i class="material-icons">{chat.hide_online == 1 ? 'flash_off' : 'flash_on'}</i>
                        {/if}

                        {#if permissions.indexOf('lhstatistic_userstats') !== -1}
                            <a class:text-muted={chat.ro} href="#" title={$t("widget.see_op_stats")} on:click={(e) => lhcServices.openModal('statistic/userstats/'+chat.user_id)}>{chat.hide_online == 1 ? chat.offline_since : ''} {chat.name_official}</a>
                        {:else}
                            {chat.hide_online == 1 ? chat.offline_since : ''} {chat.name_official}
                        {/if}

                    {:else if type == 'group_chats'}

                        <div class="abbr-list">
                            <a class="d-block action-image" on:click={(e) => lhcServices.startGroupChat(chat.id,chat.name)}>
                                {#if chat.is_member == true && (!chat.ls_id || chat.ls_id < chat.last_msg_id)}
                                    <i class="material-icons text-warning" title={$t("group_chat.unread_messages")}>whatshot</i>
                                {/if}
                                <i class="material-icons">{chat.type == 1 ? 'security' : 'public'}</i>{#if chat.user_id == $lhcList.current_user_id}<i class="material-icons">account_balance</i>{/if}[{chat.tm}] {chat.name}
                            </a>
                        </div>

                    {:else}
                        <div class="abbr-list" >

                            {#if type == 'subject_chats'}
                                <span class="material-icons chat-pending me-0" class:chat-active={chat.status == 1 || chat.status == 5}>{chat.status == 5 ? 'android' : 'chat'}</span>
                            {/if}

                            {#if type == 'pending_chats' && permissions.indexOf('lhchat_deletechat') !== -1}
                                <a title={$t("widget.delete_chat")} class="material-icons float-end" on:click={(e) => {lhcServices.deleteChat(chat.id);e.stopPropagation()}}>delete</a>
                            {/if}

                            {#if chat.country_code}
                                <span><img src={www_dir_flags + "/" + chat.country_code + ".png"} alt={chat.country_name} title={chat.country_name} /></span>
                            {/if}

                            {#if chat.can_edit_chat && type == 'pending_chats' && permissions.indexOf('lhchat_redirectcontact') !== -1}
                                <a class="material-icons me-0" title={$t("widget.redirect_contact")} on:click={(e) => lhcServices.redirectContact(chat.id,$t("widget.are_you_sure"),e)}>reply</a>
                            {/if}

                            <a title="[{chat.id}] {chat.time_created_front}" on:click={(e) => lhcServices.previewChat(chat.id,e)} class="material-icons me-0">info_outline</a>

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
                                        <i class="material-icons me-0" style:color={icon.c ? icon.c : '#6c757d'} title={icon.t ? icon.t : icon.i} >{icon.i || icon}</i>
                                    {/if}
                                {/each}
                            {/if}

                            {#if $lhcList.additionalColumns}
                                {#each $lhcList.additionalColumns as column}
                                    {#if column.cenabl == true && column.iconm == true}
                                        {#each column.items as val}
                                            {#if chat[val]}
                                                <span class="material-icons me-0" on:click={(e) => {column.iconp && lhcServices.openModal('chat/icondetailed/'+chat.id + '/' + column.id,e)}} title={column.iconp == true ? 'Click for more information | ' : ''}{chat[val + '_tt'] ? chat[val + '_tt'] : chat[val]} style:color={column.icon[chat[val]].color}>{column.icon[chat[val]].icon}</span>
                                            {/if}
                                        {/each}
                                    {/if}
                                 {/each}
                             {/if}

                            {chat.nick}<small>{(type == 'pending_chats' || type == 'subject_chats') && chat.plain_user_name !== undefined ? ' | ' + chat.plain_user_name : ''}</small>
                        </div>
                    {/if}
                </td>

                {#if check_row_class && $lhcList.additionalColumns}
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

                {#if type == 'subject_chats'}
                    <td>
                        {#if chat.subject_list}
                            {#each chat.subject_list as subjectitem}
                                <span class="badge bg-info fs12 me-1" >{subjectitem}</span>
                            {/each}
                        {/if}
                    </td>
                {/if}

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

                    {#if chat.can_edit_chat && type == 'pending_chats'}
                        <div class="abbr-list" title="{chat.wait_time_pending}">{chat.wait_time_pending}</div>
                    {/if}

                    {#if type == 'online_op'}
                        <div class="abbr-list" title="{chat.lastactivity_ago}">{chat.lastactivity_ago}</div>
                    {/if}

                    {#if type == 'active_chats' || type == 'subject_chats' || type == 'bot_chats'}
                        <div class="abbr-list" title="Chat started at - {chat.time_created_front}">
                            <span class="material-icons text-success" title={$t("widget.send_receive")} class:text-danger={chat.pnd_rsp}>{chat.pnd_rsp === true ? 'call_received' : 'call_made'}</span>{chat.last_msg_time_front ? chat.last_msg_time_front : '&#x2709;'}
                        </div>
                    {/if}

                    {#if type == 'unread_chats'}
                        <div class="abbr-list" title="{chat.unread_time.hours} h. {chat.unread_time.minits} m. {chat.unread_time.seconds} s.">{chat.unread_time.hours} h. {chat.unread_time.minits} m. {chat.unread_time.seconds} s.</div>
                    {/if}


                    {#if type == 'my_chats'}
                        <div class="abbr-list" title={chat.status == 1 ? $t("widget.active")  : $t("widget.pending")}>
                            {#if chat.status != 1}<i title={$t("widget.pending")} class="material-icons chat-unread">&#xE80E;</i>{/if}<span class="material-icons text-success" title={$t("widget.send_receive")} class:text-danger={chat.pnd_rsp}>{chat.pnd_rsp === true ? 'call_received' : 'call_made'}</span>{chat.status == 0 ? '&#x23F3; '+chat.wait_time_pending : chat.last_msg_time_front}
                        </div>
                    {/if}
                </td>

                {#if type == 'online_op'}
                    <td title='{$t("widget.max")} - {chat.max_chats && chat.max_chats > 0 ? chat.max_chats : "n/a"} {$t("widget.chats")}' class="align-middle"  class:text-danger={chat.max_chats && chat.max_chats > 0 && chat.max_chats - chat.active_chats <= 0} class:text-success={chat.max_chats && chat.max_chats > 0 && chat.max_chats - chat.active_chats >= 1}>
                        {chat.active_chats} <abbr title={$t("widget_title.active_chats")}>a.c</abbr>, {chat.max_chats && chat.max_chats > 0 ? (chat.max_chats - chat.active_chats) : ' n/a'} <abbr title={$t("widget.free_slots")}>f.s</abbr>
                    </td>
                {/if}

                {#if type == 'active_chats'}
                    <td>
                        <div class="abbr-list" title="{chat.n_off_full} | {chat.plain_user_name}">{chat.n_office}</div>
                    </td>
                {/if}

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


