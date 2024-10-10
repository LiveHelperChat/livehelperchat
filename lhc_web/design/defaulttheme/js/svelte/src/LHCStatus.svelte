<svelte:options customElement={{
		tag: 'lhc-status',
		shadow: 'none'}}/>
<script>
    import { lhcList } from './stores.js';

    export let hide_quick_notification = 0;

    /* Version change monitoring */
    let lhcVersionCounter = 8;
    let firstVersion = 0;

    $ : lhcVersion = $lhcList.lhcVersion;
    $ : lhcNotice = $lhcList.lhcNotice;

    function versionChanged() {
        if (firstVersion == 0) {
            firstVersion = lhcVersion;
        }
        if (firstVersion > 0 && lhcVersion > 0 && firstVersion != lhcVersion) {
            $lhcList.lhcPendingRefresh = true;
            setInterval(() => {
                lhcVersionCounter = lhcVersionCounter - 1;
                if (lhcVersionCounter == 0) {
                    document.location.reload(true);
                }
            }, 1000)
        }
    }

    $ : lhcVersion, versionChanged();
</script>

{#if $lhcList.lhcPendingRefresh || $lhcList.lhcConnectivityProblem || $lhcList.inActive || $lhcList.lhcNotice.message}
<div ng-non-bindable class="version-updated float-start">
    {#if $lhcList.lhcNotice.message}<div class={"text-"+$lhcList.lhcNotice.level}><i class={"material-icons "+"text-"+$lhcList.lhcNotice.level}>brand_awareness</i>{$lhcList.lhcNotice.message}</div>{/if}
    {#if $lhcList.lhcPendingRefresh}<div><i class="material-icons">update</i>This window will be automatically refreshed in {lhcVersionCounter} seconds due to a version update.</div>{/if}
    {#if $lhcList.lhcConnectivityProblem}<div>You have weak internet connection or the server has problems. Try to refresh the  page. Error code {$lhcList.lhcConnectivityProblemExplain}</div>{/if}
    {#if $lhcList.inActive}<div>You went offline because of inactivity. Please close other chat windows if you have any</div>{/if}
</div>
{/if}

{#if hide_quick_notification == 0 && !($lhcList.lhcPendingRefresh || $lhcList.lhcConnectivityProblem == true || $lhcList.inActive == true || $lhcList.lhcNotice.message) && $lhcList.last_actions.length > 0}
    <div ng-non-bindable class="text-muted float-start fs12 abbr-list d-none d-sm-block">

        {#if $lhcList.last_actions_index < $lhcList.last_actions.length - 1}<span class="material-icons action-image" on:click={(e) => $lhcList.last_actions_index++}>expand_more</span>{/if}
        {#if $lhcList.last_actions_index > 0}<span on:click={(e) => $lhcList.last_actions_index--} class="material-icons action-image">expand_less</span>{/if}
        {#if $lhcList.last_actions_index > 0}<span class="material-icons">hourglass_full</span>{/if}

        {#if $lhcList.last_actions[$lhcList.last_actions_index].type == 'user_wrote'}<span><b>{$lhcList.last_actions[$lhcList.last_actions_index].nick}</b> - <i>{$lhcList.last_actions[$lhcList.last_actions_index].msg}</i> in chat - {$lhcList.last_actions[$lhcList.last_actions_index].chat_id}</span>{/if}
        {#if $lhcList.last_actions[$lhcList.last_actions_index].type != 'user_wrote' && $lhcList.last_actions[$lhcList.last_actions_index].type != 'info_history' && $lhcList.last_actions[$lhcList.last_actions_index].type != 'mac' && $lhcList.last_actions[$lhcList.last_actions_index].type != 'mac_history'}<span><b>{$lhcList.last_actions[$lhcList.last_actions_index].nick}</b> - <i>{$lhcList.last_actions[$lhcList.last_actions_index].msg}</i> - {$lhcList.last_actions[$lhcList.last_actions_index].chat_id}</span>{/if}
        {#if $lhcList.last_actions[$lhcList.last_actions_index].type == 'mac'}<span><b>{$lhcList.last_actions[$lhcList.last_actions_index].nick}</b> - active chat was opened - {$lhcList.last_actions[$lhcList.last_actions_index].chat_id}</span>{/if}
        {#if $lhcList.last_actions[$lhcList.last_actions_index].type == 'mac_history'}<span><b>{$lhcList.last_actions[$lhcList.last_actions_index].nick}</b> - previously loaded chat was opened - {$lhcList.last_actions[$lhcList.last_actions_index].chat_id}</span>{/if}
        {#if $lhcList.last_actions[$lhcList.last_actions_index].type == 'info_history'}<span>{$lhcList.last_actions[$lhcList.last_actions_index].msg}</span>{/if}

    </div>
{/if}

