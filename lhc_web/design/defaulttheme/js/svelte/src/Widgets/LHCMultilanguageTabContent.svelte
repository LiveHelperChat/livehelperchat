<svelte:options customElement={{tag: 'lhc-multilanguage-tab-content',shadow: 'none'}}/>

<script>

    import { onMount } from 'svelte';
    import {lhcLanguages as languages} from '../stores.js';
    import BBCodeToolbar from  './Parts/BBCodeToolbar.svelte';
    import DepartamentSelection from  './Parts/DepartamentSelection.svelte';
    import { t } from "../i18n/i18n.js";

    export let init_langauges = -1;
    export let identifier = 'identifier';

    export let enable_department = false;
    export let language_field_name = null;
    export let tab_class = "tab-pane";

    let dialects = null;
    let fields = [];

    let query = '*';

    onMount(() => {

        dialects = window['languageDialects'];

        if (init_langauges > -1) {
            $languages[identifier] = typeof window[identifier+init_langauges] !== 'undefined' ? window[identifier+init_langauges] : [];
            query = '';
        } else if (!$languages[identifier]) {
            $languages[identifier] = [];
        }

        if (typeof window[identifier + 'Fields'] !== 'undefined') {
            fields = window[identifier + 'Fields'];
        }

        // Check selected dialects
        languages.update((list) => {
            list[identifier].forEach((lang) => {
                dialects.forEach((langDialtect) => {
                    isSelectedDialect(lang,langDialtect);
                });
            });
            return list;
        })

    })

    function deleteLanguage(field) {
        languages.update((list) => {
            list[identifier].splice(list[identifier].indexOf(field),1);
            return list;
        })
        jQuery('#'+identifier+'-tabs a:first').tab('show');
    }

    function changeSelection(lang, dialect) {

        if (lang.dialect[dialect.lang.id] === true) {
            dialect.items.forEach(function(item){
                if (item.short_code != '' && lang.languages.indexOf(item.short_code) === -1) {
                    lang.languages.push(item.short_code);
                }

                if (lang.languages.indexOf(item.lang_code) === -1){
                    lang.languages.push(item.lang_code);
                }
            });

            languages.update((list) => {
                list[identifier][list[identifier].indexOf(lang)] = lang;
                return list;
            });
            // Unchecked
        } else {
            dialect.items.forEach(function(item){
                var idx = null;

                if (item.short_code != '') {
                    idx = lang.languages.indexOf(item.short_code);
                    if (idx > -1) {
                        lang.languages.splice(idx, 1);
                    }
                }

                idx = lang.languages.indexOf(item.lang_code);
                if (idx > -1) {
                    lang.languages.splice(idx, 1);
                }
            });

            languages.update((list) => {
                list[identifier][list[identifier].indexOf(lang)] = lang;
                return list;
            });

        }
    }

    function toggleSelection(lang, language) {
        var idx = lang.languages.indexOf(language);
        // Is currently selected
        if (idx > -1) {
            lang.languages.splice(idx, 1);
        } else {
            lang.languages.push(language);
        }

        languages.update((list) => {
            list[identifier][list[identifier].indexOf(lang)] = lang;
            return list;
        });

    }

   function isSelectedDialect(lang, dialect) {

        if (typeof lang.dialect === 'undefined') {
            lang.dialect = [];
        }

        var allChecked = true;

        dialect.items.forEach(function(item){
            if (lang.languages.indexOf(item.lang_code) === -1 || (item.short_code != '' && lang.languages.indexOf(item.short_code) === -1)){
                allChecked = false;
            }
        });

        lang.dialect[dialect.lang.id] = allChecked;
    }

    function selectDepartment(eventData, lang) {
        languages.update((list) => {
            list[identifier][list[identifier].indexOf(lang)].dep_ids.push(eventData.detail.dep_id);
            return list;
        });
    }
    function unselectDepartment(eventData, lang) {
        languages.update((list) => {
            list[identifier][list[identifier].indexOf(lang)].dep_ids.splice(list[identifier][list[identifier].indexOf(lang)].dep_ids.indexOf(eventData.detail.dep_id),1);
            return list;
        });
    }

</script>
{#if $languages[identifier]}
{#each $languages[identifier] as lang, index}
    <div role="tabpanel" class={tab_class} id="lang-{identifier}-{index}">
        <div class="row mb-1">
            {#if tab_class == "tab-pane"}
            <div class="col-1"><a class="btn btn-sm btn-danger d-block" on:click={(e) => deleteLanguage(lang)}><i class="material-icons me-0">&#xE15B;</i></a></div>
            {/if}
            <div class="col-11"><input type="text" bind:value={query} placeholder={$t('user_account.search_language')} class="form-control form-control-sm"></div>
        </div>
        <div class="form-group">
            <div class="row" style="max-height: 200px;overflow-y: scroll">
                {#if dialects}
                {#each dialects as langDialtect}
                    <div class="col-3" style:display={query == '*' || (lang.dialect && lang.dialect[langDialtect.lang.id] && query == '') || (query != '' && langDialtect.lang.name.toLowerCase().includes(query.toLowerCase()) === true) ? "block" : "none"} >
                    <div>
                        <label class="fs12 mb-0"><input bind:checked={lang.dialect[langDialtect.lang.id]} on:change={(e) => changeSelection(lang,langDialtect)} type="checkbox" value="on"> {langDialtect.lang.name}</label>
                        <a title={$t('user_account.see_all_variations')}><i on:click={(e) => (langDialtect.show_dialect = !langDialtect.show_dialect)} class="material-icons me-0">list</i></a>
                    </div>
                    {#each langDialtect.items as langDialtectItem}
                        <div style:display={langDialtect.show_dialect ? 'block' :'none'}>
                            <label class="fs12 mb-0">
                                <input name={language_field_name ? language_field_name : "languages["+index+"][]"} type="checkbox" value={langDialtectItem.lang_code} checked={lang.languages.indexOf(langDialtectItem.lang_code) > -1} on:click={(e) => toggleSelection(lang,langDialtectItem.lang_code)}> {langDialtectItem.lang_name} [{langDialtectItem.lang_code}]
                            </label>
                            {#if langDialtectItem.short_code}
                            <br/>
                            <label class="fs12  mb-0">
                                <input name={language_field_name ? language_field_name : "languages["+index+"][]"} type="checkbox" value={langDialtectItem.short_code} checked={lang.languages.indexOf(langDialtectItem.short_code) > -1} on:click={(e) => toggleSelection(lang,langDialtectItem.short_code)}> {langDialtectItem.lang_name} [{langDialtectItem.short_code}]
                            </label>
                            {/if}
                            <br/>
                        </div>
                    {/each}
                    </div>
                {/each}
                {/if}
            </div>
        </div>

        {#if enable_department}
        <DepartamentSelection on:department_select={(dep_id) => {selectDepartment(dep_id, lang)}} on:department_unselect={(dep_id) => {unselectDepartment(dep_id, lang)}} index_block={index} selected_departments={lang.dep_ids}></DepartamentSelection>
        {/if}

        {#if fields.length > 0}
        <div class="row">
            {#each fields as field}
                <div class={"col-"+(field.column ? field.column : '12')}>
                {#if field.type === 'header_block'}
                    <h4>{field.name}</h4>
                {:else}
                    <div class="form-group">
                        {#if field.name_literal}<label>{field.name_literal}</label>{/if}
                        <BBCodeToolbar selector="#{field.name}-{index}"></BBCodeToolbar>
                        <textarea class="form-control" rows="2" id={field.name+"-"+index} name={field.name+"["+index+"]"} bind:value={lang[field['bind_name']]}></textarea>
                    </div>
                {/if}
                </div>
            {/each}
        </div>
        {/if}

    </div>
{/each}
{/if}


