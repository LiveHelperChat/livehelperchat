<svelte:options customElement={{tag: 'change-visibility-mode',shadow: 'none'}}/>
<script>
    import { lhcList } from '../stores.js';
    import { updateSettings } from '../Helpers/SettingsActions.js';
    import { t } from "../i18n/i18n.js";

    export let css_class = "dropdown-item ps-2";
    export let hide_text = false;

</script>

<a href={'#'} class={css_class} on:click={(e) => updateSettings({store: lhcList, e: e, attr: 'hideInvisible', url: ('user/setinvisible/' + (!$lhcList.hideInvisible == true ? 'true' : 'false'))})} ><i id="vi-in-user" class="material-icons" title={$t("homepage.change_visibility")} >{$lhcList.hideInvisible == true ? 'visibility_off' : 'visibility'}</i>{#if !hide_text}{#if $lhcList.hideInvisible == true}{$t("homepage.invisible")}{:else}{$t("homepage.visible")}{/if}{/if}</a>





