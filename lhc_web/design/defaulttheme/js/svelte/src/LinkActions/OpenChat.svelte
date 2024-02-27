<svelte:options customElement={{tag: 'lhc-open-chat', shadow: 'none'}}/>
<script>
    import { lhcList } from '../stores.js';
    import { t } from "../i18n/i18n.js";
</script>
<div class="dropdown-item">
    <div class="input-group input-group-sm">
        <input type="text" class="form-control" bind:value={$lhcList.chat_to_open} on:keyup={(e) => {e.keyCode === 13 ? ee.emitEvent('angularStartChatbyId',[$lhcList.chat_to_open]) : ''}} placeholder={$t("front_default.chat_id_to_open")} aria-describedby="inputGroupPrepend" required>
        <button class="btn btn-secondary" on:click={(e) => ee.emitEvent('angularStartChatbyId',[$lhcList.chat_to_open])} title={$t("front_default.open_a_chat")} type="button"><i class="material-icons me-0">chat</i></button>
    </div>
</div>