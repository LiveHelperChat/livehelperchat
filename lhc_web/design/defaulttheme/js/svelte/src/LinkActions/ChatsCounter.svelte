<svelte:options customElement={{tag: 'lhc-chats-counter', shadow: 'none'}}/>
<script>
    import { lhcList } from '../stores.js';
    export let type = "pending_chats";
</script>
({$lhcList.lhcCoreLoaded === true && $lhcList[type] && $lhcList[type].list ? $lhcList[type].list.length : 0})