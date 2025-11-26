<svelte:options customElement={{tag: 'lhc-masking-rules', shadow: 'none'}}/>

<script>
    import { onMount } from 'svelte';
    import { t } from '../i18n/masking-i18n.js';

    export let pii_options = '{}';
    export let input_selector = 'textarea[name="AbstractInput_pattern"]';

    let rules = [];
    let patternInput = null;
    let piiOptions = {};

    $: typeLabels = {
        'regex': $t('masking.type_regex') || 'Regex',
        'email': $t('masking.type_email') || 'E-mail',
        'credit_card': $t('masking.type_credit_card') || 'Credit Card',
        'pii': $t('masking.type_pii') || 'PII',
        'secret_keys': $t('masking.type_secret_keys') || 'Secret Keys',
        'urls': $t('masking.type_urls') || 'URLs'
    };

    $: ruleTypes = [
        { value: 'regex', label: $t('masking.type_regex') || 'Regex' },
        { value: 'email', label: $t('masking.type_email') || 'E-mail' },
        { value: 'credit_card', label: $t('masking.type_credit_card') || 'Credit Card' },
        { value: 'pii', label: $t('masking.type_pii') || 'PII' },
        { value: 'secret_keys', label: $t('masking.type_secret_keys') || 'Secret Keys' },
        { value: 'urls', label: $t('masking.type_urls') || 'URLs' }
    ];

    let selectedRuleType = 'regex';

    onMount(async () => {
        
        piiOptions = typeof pii_options === 'string' ? JSON.parse(pii_options) : pii_options;
        patternInput = document.querySelector(input_selector);
        
        if (patternInput) {
            parseRules(patternInput.value);
        }
    });

    function parseRules(val) {
        try {
            if (val.trim().startsWith('[')) {
                rules = JSON.parse(val);
            } else if (val.trim() !== '') {
                const lines = val.split("\n");
                rules = [];
                lines.forEach(function(line) {
                    if (line.trim() === '') return;
                    const parts = line.split('|||');
                    const rule = { type: 'regex', pattern: parts[0] };
                    if (parts.length > 1) rule.replacement = parts[1];
                    if (parts.length > 2 && parts[0] === '__email__') rule.replacement_domain = parts[2];
                    rules.push(rule);
                });
            }
        } catch (e) {
            console.error('Error parsing rules', e);
            rules = [];
        }
    }

    function updateInput() {
        if (patternInput) {
            patternInput.value = JSON.stringify(rules);
        }
    }

    function addRule() {
        const rule = { type: selectedRuleType };

        if (selectedRuleType === 'email') {
            rule.replacement = '$';
            rule.replacement_domain = '*';
        } else if (selectedRuleType === 'credit_card') {
            rule.replacement = '*';
        } else if (selectedRuleType === 'regex') {
            rule.pattern = '';
            rule.replacement = '*';
        } else if (selectedRuleType === 'pii') {
            rule.entities = [];
        } else if (selectedRuleType === 'secret_keys') {
            rule.threshold = 'balanced';
        } else if (selectedRuleType === 'urls') {
            rule.allowedSchemes = ['https'];
            rule.blockUserinfo = true;
            rule.allowSubdomains = false;
            rule.allowList = [];
            rule.denyList = [];
            rule.allowHostedHost = false;
        }

        rules = [...rules, rule];
        updateInput();
    }

    function deleteRule(index) {
        rules = rules.filter((_, i) => i !== index);
        updateInput();
    }

    function updateRule(index, key, value) {
        rules[index][key] = value;
        rules = [...rules];
        updateInput();
    }

    function updatePiiEntity(index, entity, checked) {
        if (!rules[index].entities) rules[index].entities = [];
        
        if (checked) {
            if (rules[index].entities.indexOf(entity) === -1) {
                rules[index].entities = [...rules[index].entities, entity];
            }
        } else {
            rules[index].entities = rules[index].entities.filter(e => e !== entity);
        }
        rules = [...rules];
        updateInput();
    }

    function updateUrlScheme(index, scheme, checked) {
        if (!rules[index].allowedSchemes) rules[index].allowedSchemes = [];
        
        if (checked) {
            if (rules[index].allowedSchemes.indexOf(scheme) === -1) {
                rules[index].allowedSchemes = [...rules[index].allowedSchemes, scheme];
            }
        } else {
            rules[index].allowedSchemes = rules[index].allowedSchemes.filter(s => s !== scheme);
        }
        rules = [...rules];
        updateInput();
    }

    function updateUrlAllowList(index, value) {
        rules[index].allowList = value.split("\n").map(s => s.trim()).filter(s => s !== '');
        // Clear deny list when allow list is updated
        if (rules[index].allowList.length > 0) {
            rules[index].denyList = [];
        }
        rules = [...rules];
        updateInput();
    }

    function updateUrlDenyList(index, value) {
        rules[index].denyList = value.split("\n").map(s => s.trim()).filter(s => s !== '');
        // Clear allow list when deny list is updated
        if (rules[index].denyList.length > 0) {
            rules[index].allowList = [];
        }
        rules = [...rules];
        updateInput();
    }

    function getAllowListText(rule) {
        return (rule.allowList || []).join('\n');
    }

    function getDenyListText(rule) {
        return (rule.denyList || []).join('\n');
    }
</script>

<div id="rules-container">
    {#each rules as rule, index}
        <div class="card mb-2">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>{typeLabels[rule.type] || rule.type.toUpperCase()}</strong>
                    <button type="button" class="btn btn-sm btn-danger" on:click={() => deleteRule(index)}>{$t('masking.delete')}</button>
                </div>

                <div>
                    {#if rule.type === 'regex'}
                        <div class="row">
                            <div class="col-6">
                                <label>{$t('masking.pattern')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.pattern || ''} on:change={(e) => updateRule(index, 'pattern', e.target.value)}>
                            </div>
                            <div class="col-6">
                                <label>{$t('masking.replacement')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.replacement || '*'} on:change={(e) => updateRule(index, 'replacement', e.target.value)}>
                            </div>
                        </div>
                    {:else if rule.type === 'email'}
                        <div class="row">
                            <div class="col-6">
                                <label>{$t('masking.replacement')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.replacement || '$'} on:change={(e) => updateRule(index, 'replacement', e.target.value)}>
                            </div>
                            <div class="col-6">
                                <label>{$t('masking.replacement_domain')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.replacement_domain || '*'} on:change={(e) => updateRule(index, 'replacement_domain', e.target.value)}>
                            </div>
                        </div>
                    {:else if rule.type === 'credit_card'}
                        <div class="row">
                            <div class="col-6">
                                <label>{$t('masking.replacement')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.replacement || '*'} on:change={(e) => updateRule(index, 'replacement', e.target.value)}>
                            </div>
                        </div>
                    {:else if rule.type === 'pii'}
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label>{$t('masking.replacement_mask_hint')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.replacement || ''} on:change={(e) => updateRule(index, 'replacement', e.target.value)} placeholder="e.g. *">
                            </div>
                            <div class="col-12">
                                <label>{$t('masking.entities')}</label>
                                <div class="row">
                                    {#each Object.entries(piiOptions) as [key, label]}
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pii-{index}-{key}" checked={rule.entities && rule.entities.indexOf(key) !== -1} on:change={(e) => updatePiiEntity(index, key, e.target.checked)}>
                                                <label class="form-check-label" for="pii-{index}-{key}">{label}</label>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>
                    {:else if rule.type === 'secret_keys'}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{$t('masking.threshold')}</label>
                                    <select class="form-control form-control-sm" value={rule.threshold || 'balanced'} on:change={(e) => updateRule(index, 'threshold', e.target.value)}>
                                        <option value="strict">{$t('masking.threshold_strict')}</option>
                                        <option value="balanced">{$t('masking.threshold_balanced')}</option>
                                        <option value="permissive">{$t('masking.threshold_permissive')}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{$t('masking.replacement_mask_hint')}</label>
                                    <input type="text" class="form-control form-control-sm" value={rule.replacement || ''} on:change={(e) => updateRule(index, 'replacement', e.target.value)} placeholder="e.g. *">
                                </div>
                            </div>
                        </div>
                    {:else if rule.type === 'urls'}
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label>{$t('masking.replacement_mask_hint')}</label>
                                <input type="text" class="form-control form-control-sm" value={rule.replacement || ''} on:change={(e) => updateRule(index, 'replacement', e.target.value)} placeholder="e.g. *">
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={(rule.allowedSchemes || ['https']).indexOf('https') !== -1} on:change={(e) => updateUrlScheme(index, 'https', e.target.checked)}>
                                    <label class="form-check-label">HTTPS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={(rule.allowedSchemes || []).indexOf('http') !== -1} on:change={(e) => updateUrlScheme(index, 'http', e.target.checked)}>
                                    <label class="form-check-label">HTTP</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={(rule.allowedSchemes || []).indexOf('ftp') !== -1} on:change={(e) => updateUrlScheme(index, 'ftp', e.target.checked)}>
                                    <label class="form-check-label">FTP</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={(rule.allowedSchemes || []).indexOf('mailto') !== -1} on:change={(e) => updateUrlScheme(index, 'mailto', e.target.checked)}>
                                    <label class="form-check-label">Mailto</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={rule.blockUserinfo !== undefined ? rule.blockUserinfo : true} on:change={(e) => updateRule(index, 'blockUserinfo', e.target.checked)}>
                                    <label class="form-check-label">{$t('masking.block_user_info')}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={rule.allowSubdomains || false} on:change={(e) => updateRule(index, 'allowSubdomains', e.target.checked)}>
                                    <label class="form-check-label">{$t('masking.allow_subdomains')}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked={rule.allowHostedHost || false} on:change={(e) => updateRule(index, 'allowHostedHost', e.target.checked)}>
                                    <label class="form-check-label">{$t('masking.allow_hosted_host')}</label>
                                </div>
                            </div>
                            <div class="col-6 mt-2">
                                <label>{$t('masking.allow_list')}</label>
                                <textarea class="form-control form-control-sm" rows="3" placeholder="e.g. example.com" value={getAllowListText(rule)} on:change={(e) => updateUrlAllowList(index, e.target.value)} disabled={(rule.denyList || []).length > 0}></textarea>
                            </div>
                            <div class="col-6 mt-2">
                                <label>{$t('masking.deny_list')}</label>
                                <textarea class="form-control form-control-sm" rows="3" placeholder="e.g. example.com" value={getDenyListText(rule)} on:change={(e) => updateUrlDenyList(index, e.target.value)} disabled={(rule.allowList || []).length > 0}></textarea>
                            </div>
                             <div class="col-12 mt-1">
                                <small class="text-muted"><em>{$t('masking.list_exclusive_note')}</em></small>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/each}
</div>

<div class="row mt-2">
    <div class="col-auto">
        <select class="form-control form-control-sm" bind:value={selectedRuleType}>
            {#each ruleTypes as ruleType}
                <option value={ruleType.value}>{ruleType.label}</option>
            {/each}
        </select>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-sm btn-secondary" on:click={addRule}>{$t('masking.add_rule')}</button>
    </div>
</div>
