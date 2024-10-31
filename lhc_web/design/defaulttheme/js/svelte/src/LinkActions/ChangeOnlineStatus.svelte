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
               updateSettings({store: lhcList, e: event, attr: 'hideOnline', url: ('user/setoffline/' + (!$lhcList.hideOnline == true ? 'true' : 'false'))})
           }
       }
    }

    export let enable_shortcut = false;

    export let show_text = false;
    export let css_class = "nav-link";
</script>

<svelte:window on:keydown={enable_shortcut && keydown} ></svelte:window>

<a href={'#'} class={css_class} on:click={(e) => updateSettings({store: lhcList, e: e, attr: 'hideOnline', url: ('user/setoffline/' + (!$lhcList.hideOnline == true ? 'true' : 'false'))})} ><i id="online-offline-user" class={"material-icons "+($lhcList.hideOnline === true ? 'text-danger' : 'text-success')} title={$t("homepage.change_online_status")} >{$lhcList.hideOnline == true ? 'flash_off' : 'flash_on'}</i>{#if show_text}{#if $lhcList.hideOnline == true}{$t("homepage.status_offline")}{:else}{$t("homepage.status_online")}{/if}{/if}</a>




