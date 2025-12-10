<script>
    import { t } from "../../i18n/i18n.js";

    export let lhcList = null;

    export let optionsPanel = {'panelid' : 'pendingd','limitid' : 'limitp', 'userid' : 'pendingu'};

    let userList = $lhcList[optionsPanel['userid']];
    let userGroups = $lhcList[optionsPanel['panelid'] + '_ugroups'];
    let userDepartmentsGroups = $lhcList[optionsPanel['panelid'] + '_dpgroups']
    let productList = $lhcList[optionsPanel['panelid'] + '_products'];
    let departmentList = $lhcList[optionsPanel['panelid']];
    let limitValue = $lhcList[optionsPanel['limitid']];

    $ : userGroups, $lhcList[optionsPanel['panelid'] + '_ugroups'] = userGroups;
    $ : userDepartmentsGroups, $lhcList[optionsPanel['panelid'] + '_dpgroups'] = userDepartmentsGroups;
    $ : userList, $lhcList[optionsPanel['userid']] = userList;
    $ : productList, $lhcList[optionsPanel['panelid'] + '_products'] = productList;
    $ : departmentList, $lhcList[optionsPanel['panelid']] = departmentList;
    $ : limitValue, $lhcList[optionsPanel['limitid']] = limitValue;
</script>

<div class={"p-" + (optionsPanel.hasOwnProperty('padding_filters') ? optionsPanel.padding_filters : 2)}>
    <div class="row">

        {#if !optionsPanel.hasOwnProperty('hide_department_filter') || optionsPanel.hide_department_filter === false}
        <div class={"col-"+ (optionsPanel.hasOwnProperty('userid') ? 6 : (!optionsPanel.hasOwnProperty('hide_limits') ? 10 : 12))+" pe-0"}>
            <div class="btn-group btn-block btn-block-department">
                <button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    {$lhcList[optionsPanel['panelid']].length == 0 ? $t("widget_options.all_dep") : ($lhcList[optionsPanel['panelid']].length == 1 && !(optionsPanel.hasOwnProperty('no_names_department') && optionsPanel.no_names_department === true) ? $lhcList[optionsPanel['panelid'] + "Names"].join(", ") : '['+$lhcList[optionsPanel['panelid']].length+'] ' + $t("widget_options.departments"))}
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li class="dropdown-result">
                        <ul class="list-unstyled dropdown-lhc">

                            {#if !optionsPanel.hasOwnProperty('hide_department_variations')}
                            <li><label><input type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_all_departments']} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > {$t("widget_options.check_all")}</label></li>
                            <li><label><input type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_only_online']} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > {$t("widget_options.only_online")}</label></li>
                            <li><label><input type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_only_explicit_online']} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > {$t("widget_options.only_explicit_online")}</label></li>
                            <li><label><input type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_hide_hidden']} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > {$t("widget_options.hide_hidden")}</label></li>
                            {/if}

                            {#if optionsPanel.hasOwnProperty('hide_department') && optionsPanel.hide_department == true}
                                <li><label><input type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_hide_dep']} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > <i class="material-icons">home</i>{$t("widget_options.hide_dep")}</label></li>
                            {/if}

                            {#if optionsPanel.hasOwnProperty('hide_depgroup') && optionsPanel.hide_depgroup == true}
                                <li><label><input type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_hide_dgroup']} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > <i class="material-icons">&#xE84F;</i>{$t("widget_options.hide_dep_groups")}</label></li>
                            {/if}

                            {#if !optionsPanel.hasOwnProperty('hide_department_variations')}
                                <li class="border-bottom"><label><input data-stopPropagation="true" type="checkbox" bind:checked={$lhcList[optionsPanel['panelid'] + '_hide_disabled']}  on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} > {$t("widget_options.hide_disabled")}</label></li>
                            {/if}

                            {#if !optionsPanel.hasOwnProperty('disable_product') || optionsPanel.disable_product == false}
                                {#each $lhcList.userProductNames as product (product.id)}
                                    <li data-stopPropagation="true"><label><input type="checkbox" bind:group={productList} value={product.id} on:change={(e) => ee.emitEvent('svelteProductChanged',[optionsPanel['panelid'] + '_products'])}><i class="material-icons">&#xE8CC;</i>{product.name}</label></li>
                                {/each}
                                {#if $lhcList.userProductNames.length > 0}
                                <li class="border-bottom"></li>
                                {/if}
                            {/if}

                            {#each $lhcList.userDepartmentsGroups as department (department.id)}
                                <li data-stopPropagation="true"><label><input bind:group={userDepartmentsGroups} on:change={(e) => {ee.emitEvent('svelteProductChanged',[optionsPanel['panelid'] + '_dpgroups'])}} value={department.id} type="checkbox" ><i title="Department group" class="material-icons">&#xE84F;</i>{department.name}</label></li>
                            {/each}

                            {#if $lhcList.userDepartmentsGroups.length > 0}
                            <li class="border-bottom"></li>
                            {/if}

                            <li class="p-1"><input type="text" data-stopPropagation="true" bind:value={$lhcList.depFilterText} placeholder={$t("widget_options.search_dep")} class="form-control form-control-sm" /></li>

                            <li class="dropdown-result" style="max-height: 218px;min-height: 218px">
                                <ul class="list-unstyled dropdown-lhc">
                                    {#each $lhcList.userDepartments as department (department.id)}
                                        {#if !(($lhcList[optionsPanel['panelid'] + '_only_explicit_online'] == true && department.oexp == false) || ($lhcList[optionsPanel['panelid']+'_hide_hidden'] == true && department.hidden == true) || ($lhcList[optionsPanel['panelid']+'_hide_disabled'] == true && department.disabled == true) || ($lhcList[optionsPanel['panelid']+'_only_online'] && department.ogen == false))}
                                            <li data-stopPropagation="true"><label><input bind:group={departmentList} on:change={(e) => {ee.emitEvent('svelteDepartmentChanged',[optionsPanel['panelid']])}} value={department.id} type="checkbox"><i title={$t("widget.department")} class="material-icons" class:chat-active={department.slc} >home</i>{department.name}</label></li>
                                        {/if}
                                    {/each}
                                </ul>
                            </li>

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        {/if}

        {#if optionsPanel.hasOwnProperty('userid')}
        <div class="col-4 pe-0">
            <div class="btn-group btn-block btn-block-department">
                <button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    {$t("widget_options.users")}
                </button>
                <ul class="dropdown-menu dropdown-lhc" role="menu">
                    <li class="p-1"><input type="text" data-stopPropagation="true" bind:value={$lhcList.userFilterText} placeholder={$t("widget_options.search_operators")} class="filter-text-input form-control form-control-sm"></li>

                    <li class="dropdown-result">
                        <ul class="list-unstyled dropdown-lhc">

                            {#if optionsPanel.hasOwnProperty('custom_filters')}

                                {#each optionsPanel.custom_filters as custom_filter} 
                                    <li data-stopPropagation="true"><label title={custom_filter['title']}><input bind:checked={$lhcList[optionsPanel['panelid'] + '_' + custom_filter['field']]} on:change={(e) => {ee.emitEvent('svelteallDepartmentsChanged',[optionsPanel['panelid'],true])}} type="checkbox" ><i class="material-icons me-0">{custom_filter['icon']}</i>{custom_filter['label']}</label></li>
                                {/each}

                                <li class="border-bottom"></li>
                            {/if}

                            {#each $lhcList.userList as userItem (userItem.id)}
                                <li data-stopPropagation="true"><label><input bind:group={userList} on:change={(e) => {ee.emitEvent('svelteProductChanged',[optionsPanel['userid']])}} value={userItem.id} type="checkbox" ><i title="User" class="material-icons">account_box</i>{userItem.name || userItem.name_official}</label></li>
                            {/each}

                            {#if $lhcList.userGroups.length > 0}<li class="border-top"></li>{/if}

                            {#each $lhcList.userGroups as userGroup (userGroup.id)}
                                <li data-stopPropagation="true"><label><input bind:group={userGroups} on:change={(e) => {ee.emitEvent('svelteProductChanged',[optionsPanel['panelid']+"_ugroups"])}} value={userGroup.id} type="checkbox"><i title="User group" class="material-icons">people</i>{userGroup.name}</label></li>
                            {/each}

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        {/if}

        {#if !optionsPanel.hasOwnProperty('hide_limits')}
        <div class={"col-" + (optionsPanel.hasOwnProperty('limits_width') ? optionsPanel.limits_width : 2)}>
            <select class="form-control form-control-sm btn-light" bind:value={limitValue} on:change={(e) => {ee.emitEvent('svelteLimitChanged',[optionsPanel['limitid']])}} title={$t("widget_options.limit")}>
                <option value="5" selected={$lhcList[optionsPanel['limitid']] == 5}>5</option>
                <option value="10" selected={$lhcList[optionsPanel['limitid']] == 10}>10</option>
                <option value="25" selected={$lhcList[optionsPanel['limitid']] == 25}>25</option>
                <option value="50" selected={$lhcList[optionsPanel['limitid']] == 50}>50</option>
                <option value="100" selected={$lhcList[optionsPanel['limitid']] == 100}>100</option>
            </select>
        </div>
        {/if}

    </div>
</div>