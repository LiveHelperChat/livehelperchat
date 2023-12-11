<svelte:options customElement={{tag: 'change-always-online',shadow: 'none'}}/>
<script>
    import { lhcList } from '../stores.js';
    import { updateSettings } from '../Helpers/SettingsActions.js';
    import { t } from "../i18n/i18n.js";
</script>

<a href={'#'} class="dropdown-item ps-2" on:click={(e) => updateSettings({store: lhcList, e: e, attr: 'alwaysOnline', url: ('user/setalwaysonline/' + (!$lhcList.alwaysOnline == true ? 'true' : 'false'))})} title={$t("homepage.always_online")}><i class="material-icons"  >{$lhcList.hideOnline == true ? 'flash_off' : ($lhcList.alwaysOnline == true ? 'toggle_on' : 'toggle_off')}</i>{#if $lhcList.alwaysOnline == true}{$t("homepage.always_online_mode")}{:else}{$t("homepage.always_online_activity")}{/if}</a>