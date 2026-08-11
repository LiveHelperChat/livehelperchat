<svelte:options customElement={{tag: 'change-online-status',shadow: 'none'}}/>
<script>
    import { lhcList } from '../stores.js';
    import { updateSettings } from '../Helpers/SettingsActions.js';
    import { t } from "../i18n/i18n.js";

    const keydown = (event) => {
       if (event.ctrlKey === true && event.keyCode === 123) {
           if (lhinst.disableSync === true && lhinst.channel) {
               ee.emitEvent('svelteWentActive');
               lhinst.channel.postMessage({'action':'went_active','args':{}});
           } else {
               toggleOnlineOffline(event);
           }
       }
    }

    function goOnline(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        updateSettings({store: lhcList, attr: 'hideOnline', setVal: false, url: 'user/setoffline/false', setAttrs: {offlineReasonId: 0}});
    }

    function goOffline(e, reasonId) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        updateSettings({store: lhcList, attr: 'hideOnline', setVal: true, url: 'user/setoffline/true/(reason)/' + reasonId, setAttrs: {offlineReasonId: reasonId}});
    }

    function toggleOnlineOffline(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        if ($lhcList.hideOnline) {
            goOnline(e);
        } else {
            updateSettings({store: lhcList, e: e, attr: 'hideOnline', url: 'user/setoffline/true'});
        }
    }

    export let enable_shortcut = false;

    export let show_text = false;
    export let css_class = "nav-link";

    $: offlineReasonName = $lhcList.hideOnline && $lhcList.offlineReasonId ? ($lhcList.offlineReasons.find(r => r.id === $lhcList.offlineReasonId) || {}).name || '' : '';

    const handleKeydown = (event) => {
        if (enable_shortcut) {
            keydown(event);
        }
    }
</script>

<svelte:window onkeydown={handleKeydown}></svelte:window>

{#if $lhcList.offlineReasons.length > 0}
    <div class="dropdown">
        <a href={'#'} class={css_class + ' dropdown-toggle'} data-bs-toggle="dropdown" aria-expanded="false">
            <i id="online-offline-user" class={"material-icons " + ($lhcList.hideOnline === true ? 'text-danger' : 'text-success')} title={$t("homepage.change_online_status")}>
                {$lhcList.hideOnline == true ? 'flash_off' : 'flash_on'}
            </i>
            {#if $lhcList.hideOnline && offlineReasonName}
                <span class="ms-1 small">{offlineReasonName}</span>
            {/if}
            {#if show_text}
                {#if $lhcList.hideOnline == true}
                    {$t("homepage.status_offline")}
                {:else}
                    {$t("homepage.status_online")}
                {/if}
            {/if}
        </a>
        <ul class="dropdown-menu">
            {#if $lhcList.hideOnline}
            <li><a href={'#'} class="dropdown-item" on:click={goOnline}>
                <i class="material-icons text-success">flash_on</i> {$t("homepage.status_online")}
            </a></li>
            <li><hr class="dropdown-divider"></li>
            {/if}
            {#each $lhcList.offlineReasons as reason}
                <li><a href={'#'} class={"dropdown-item" + ($lhcList.hideOnline && reason.id === $lhcList.offlineReasonId ? ' fw-bold' : '')} title={reason.desc || ''} on:click={(e) => goOffline(e, reason.id)}>
                    <i class="material-icons">{reason.icon || 'flash_off'}</i>{reason.name}
                </a></li>
            {/each}
        </ul>
    </div>
{:else}
    <a href={'#'} class={css_class} on:click={toggleOnlineOffline}><i id="online-offline-user" class={"material-icons "+($lhcList.hideOnline === true ? 'text-danger' : 'text-success')} title={$t("homepage.change_online_status")} >{$lhcList.hideOnline == true ? 'flash_off' : 'flash_on'}</i>{#if show_text}{#if $lhcList.hideOnline == true}{$t("homepage.status_offline")}{:else}{$t("homepage.status_online")}{/if}{/if}</a>
{/if}





