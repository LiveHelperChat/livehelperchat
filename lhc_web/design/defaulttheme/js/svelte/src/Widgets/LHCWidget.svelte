<svelte:options customElement={{tag: 'lhc-widget', shadow: 'none'}}/>
<script>
    import { lhcList } from '../stores.js';
    import { t } from "../i18n/i18n.js";
    import lhcServices from '../lib/Services.js';

    import WidgetBodyPending from  './Parts/WidgetBodyPending.svelte';
    import WidgetOptionsPanel from  './Parts/WidgetOptionsPanel.svelte';
    import LHCOnlineVisitors from  '../LHCOnlineVisitors.svelte';

    export let type = "pending_chats";
    export let no_collapse = false;
    export let no_expand = false;
    export let show_username_title = false;
    export let hide_third_column = false;
    export let hide_2_column = false;
    export let hide_op_avatar = false;
    export let hide_ac_stats = false;
    export let hide_ac_sort = false;
    export let hide_ac_op_icon = false;
    export let custom_visitor_title = null;
    export let mh_widget = null;
    export let custom_visitor_icon = null;
    export let show_visitor_title = false;
    export let list_identifier = "pending";
    export let no_panel_id = false;
    export let custom_settings_url_icon = null;
    export let panel_list_identifier = "pendingd-panel-list";
    export let sort_identifier = "pending_chats_sort";
    export let status_id = 0;
    export let status_key = "chat_status_ids";
    export let hide_header = false
    export let hide_filter_options = false
    export let show_department_title = false
    export let show_subject_title = false
    export let default_expand = false;
    export let show_username_always = false;
    export let show_always_subject = false;
    export let right_panel_mode = false;
    export let no_additional_column = false;
    export let override_item_open = null;
    export let no_chat_preview = false;
    export let default_sort = 'id_asc';
    export let www_dir_flags = null;
    export let base_url = "chat/list";
    export let url = WWW_DIR_JAVASCRIPT + base_url +'/('+status_key+')/' + status_id;
    export let icon_class = "chat-pending";

    export let card_icon = "chat";
    export let no_link = false;
    export let no_duration = false;
    export let no_counter = false;

    export let expand_identifier = "pchats_widget_exp";

    export let custom_card_class = "";
    export let custom_title_class = "";
    export let custom_settings_url = "";

    export let additional_sort = "";
    export let data_panel_id = "";

    export let column_2_width = "20%";
    export let column_1_width = "40%";
    export let column_3_width = "20%";
    export let permissions = [];

    export let optionsPanel = {'panelid' : 'pendingd','limitid' : 'limitp', 'userid' : 'pendingu'};

    $: _optionsPanel = typeof optionsPanel === 'string' ? JSON.parse(optionsPanel) : optionsPanel;
    $: _permissions = typeof permissions === 'string' ? JSON.parse(permissions) : permissions;

    lhcServices.getToggleWidget(lhcList, expand_identifier);
    lhcServices.getToggleWidgetSort(lhcList, sort_identifier, default_sort);

</script>

{#if $lhcList.lhcCoreLoaded === true && $lhcList[type] && $lhcList[type].list}

    <div class={(right_panel_mode === false ? "card card-dashboard" : "")+" card-" + list_identifier + " " + ($lhcList[type].list.length > 0 ? "has-chats" : "")} data-panel-id={data_panel_id || type} >

    {#if hide_header == false}
        <div class={custom_card_class + " card-header"}>

            {#if custom_settings_url}
                <i class="material-icons me-0 action-image" on:click={(e) => lhcServices.openModal(custom_settings_url)}>{custom_settings_url_icon || 'settings_applications'}</i>
            {/if}

            {#if !no_link}
                <a href={url}>
                    <i class={"material-icons " + icon_class}>{card_icon}</i><span class={"d-none d-lg-inline "+custom_title_class}>{$t("widget_title." + type)}</span> {#if !no_counter}<span class={custom_title_class}>({$lhcList[type].list.length}{$lhcList[type].list.length == $lhcList[_optionsPanel['limitid']] ? '+' : ''})</span>{/if}
                </a>
            {:else}
                <i class={"material-icons " + icon_class}>{card_icon}</i><span class={"d-none d-lg-inline "+custom_title_class}>{$t("widget_title." + type)}</span> {#if !no_counter}<span class={custom_title_class}>({$lhcList[type].list.length}{$lhcList[type].list.length == $lhcList[_optionsPanel['limitid']] ? '+' : ''})</span>{/if}
            {/if}

            {#if type == 'online_op'}<span class="text-success" title={$t("widget.online")}>{$lhcList[type].op_on}</span>{/if}

            {#if type == 'depgroups_stats' && _permissions.indexOf('lhstatistic_exportxls') !== -1}
                <a class="material-icons" target="_blank" href={WWW_DIR_JAVASCRIPT + 'statistic/departmentstatusxls'} title={$t("widget.download_xls")}>file_download</a>
            {/if}

            {#if !no_expand}
            <a title={$t("widget.collapse_expand")} on:click={(e) => lhcServices.toggleWidget(lhcList,expand_identifier)} class="fs24 float-end material-icons exp-cntr">{$lhcList.toggleWidgetData[expand_identifier] == false ? 'expand_less' : 'expand_more'}</a>
            {/if}

            {#if !no_duration && $lhcList[type].tt}
            <span title={$t("widget.taken_time")} class="d-none d-xl-inline badge me-1 float-end bg-light text-muted p-1 fs11 fw-light border">
                {$lhcList[type].tt} s.
            </span>
            {/if}

            {#if type == 'depgroups_stats' && $lhcList['departments_stats'].tt}
                <span title={$t("widget.taken_time_dep")} class="d-none d-xl-inline badge me-1 float-end bg-light text-muted p-1 fs11 fw-light border">
                    {$lhcList['departments_stats'].tt} s.
                </span>
            {/if}

        </div>
     {/if}

        {#if no_collapse === true || $lhcList.toggleWidgetData[expand_identifier] !== true}
            <div>

                {#if !hide_filter_options}
                <WidgetOptionsPanel optionsPanel={_optionsPanel} lhcList={lhcList} />
                {/if}

                {#if $lhcList[type].list.length > 0 || type === 'onlineusers' || type === 'depgroups_stats'}
                    {#if type == 'onlineusers'}
                        <LHCOnlineVisitors {...$$props}></LHCOnlineVisitors>
                    {:else}
                        <div class="panel-list" id={no_panel_id === false ? panel_list_identifier : null} style:max-height={mh_widget ? mh_widget : ($lhcList[_optionsPanel['panelid'] + '_m_h'] ?? '330px')}>
                        <WidgetBodyPending hide_2_column={hide_2_column} hide_ac_sort={hide_ac_sort} hide_ac_stats={hide_ac_stats} hide_ac_op_icon={hide_ac_op_icon} hide_op_avatar={hide_op_avatar} hide_third_column={hide_third_column} custom_visitor_icon={custom_visitor_icon} show_visitor_title={show_visitor_title} custom_visitor_title={custom_visitor_title} show_username_title={show_username_title} show_subject_title={show_subject_title} show_department_title={show_department_title} no_expand={no_expand} show_always_subject={show_always_subject} show_username_always={show_username_always} override_item_open={override_item_open} no_chat_preview={no_chat_preview} no_additional_column={no_additional_column} additional_sort={additional_sort} column_1_width={column_1_width} column_3_width={column_3_width} column_2_width={column_2_width} permissions={_permissions} www_dir_flags={www_dir_flags} custom_sort_icons={_optionsPanel['custom_sort_icons'] ? _optionsPanel['custom_sort_icons'] : []} custom_icons={_optionsPanel['custom_icons'] ? _optionsPanel['custom_icons'] : []} panel_id={_optionsPanel['panelid']} lhcList={lhcList} type={type} sort_identifier={sort_identifier} />
                        </div>
                    {/if}
                {/if}

                {#if $lhcList[type].list.length === 0 && type !== 'onlineusers' && type !== 'depgroups_stats'}
                    <div class="m-1 alert alert-light"><i class="material-icons">search</i>{$t("widget.items_appear_here")}</div>
                {/if}

            </div>
        {/if}
    </div>

{/if}