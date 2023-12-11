<svelte:options customElement={{tag: 'lhc-multilanguage-tab',shadow: 'none'}}/>

<script>

    import { onMount } from 'svelte';
    import {lhcLanguages as languages} from '../stores.js';
    import { t } from "../i18n/i18n.js";

    export let init_langauges = -1;
    export let identifier = 'identifier';
    export let disable_new = false;

    let dialects = null;
    let departaments = [];

    onMount(() => {
        dialects = window['languageDialects'];
        if (init_langauges > -1) {
            $languages[identifier] = typeof window[identifier+init_langauges] !== 'undefined' ? window[identifier+init_langauges] : [];
        } else if (!$languages[identifier]) {
            $languages[identifier] = [];
        }

        if (window.replaceDepartments) {
            departaments = window.replaceDepartments;
        }
    })

     function getLanguagesChecked(lang) {

        var shortCode = [];

        lang.languages.forEach(function(item) {
            if (item.length == 2) {
                shortCode.push(item);
            }
        });

        lang.dep_ids && lang.dep_ids.forEach(function(item) {
            shortCode.push(departaments[item] ? departaments[item] : item);
        });

        return shortCode.length > 0 ? shortCode.join(', ') : lang.languages.join(', ');
    }

    function addLanguage() {
        languages.update((list) => {
            list[identifier].push({
                'message' : '',
                'fallback_message' : '',
                'languages' : [],
                'dialect' : {},
                'dep_ids' : []
            });
            return list;
        })

        setTimeout(function () {
            jQuery('#'+identifier+'-tabs > lhc-multilanguage-tab > li:eq(' + ($languages[identifier].length - 1) + ') a').tab('show');
        },250);
    }


</script>

{#if $languages[identifier]}
    {#each $languages[identifier] as lang, index}
        <li class="nav-item" role="presentation"><a class="nav-link" href="#lang-{identifier}-{index}" aria-controls="lang-{identifier}-{index}" role="tab" data-bs-toggle="tab" ><i class="material-icons me-0">&#xE894;</i> [{getLanguagesChecked(lang)}]</a></li>
    {/each}
{/if}

{#if !disable_new}
    <li class="nav-item" ><a class="nav-link" href="#addlanguage" on:click={addLanguage}><i class="material-icons">&#xE145;</i>{$t('user_account.add_translation')}</a></li>
{/if}