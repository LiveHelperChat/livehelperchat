<!-- https://svelte.dev/docs#run-time-custom-element-api -->
<svelte:options tag="source-token"/>

<script>
    import {onMount} from 'svelte';
    export let type = '';
</script>

<span class="token {type}" style="background:red;">
	<slot></slot>
</span>

<style>
    span{
        background:red!important;
    }
</style>