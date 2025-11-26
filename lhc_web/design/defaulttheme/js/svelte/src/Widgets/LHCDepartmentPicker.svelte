<svelte:options customElement={{tag: 'lhc-department-picker', shadow: 'none'}}/>

<script>
    import { onMount } from 'svelte';

    export let input_name = 'department_ids[]';
    export let input_selector = '';
    export let selected_ids = '[]';
    export let ajax_url = 'chat/searchprovider/deps';
    export let placeholder = 'Choose department';
    export let base_url = '';
    export let json_input = '';

    let departments = [];
    let selectedDepartments = [];
    let searchQuery = '';
    let isLoading = false;
    let hiddenInput = null;
    let jsonInput = null;
    let loadedDepartmentNames = {};

    onMount(() => {
        // Find json input if json_input name provided (output only)
        if (json_input) {
            jsonInput = document.querySelector('input[name="' + json_input + '"], textarea[name="' + json_input + '"]');
        }

        // Parse initial selected IDs
        try {
            const parsed = typeof selected_ids === 'string' ? JSON.parse(selected_ids) : selected_ids;
            if (Array.isArray(parsed)) {
                selectedDepartments = parsed.map(item => {
                    if (typeof item === 'object' && item.id !== undefined) {
                        loadedDepartmentNames[item.id] = item.name || `Department #${item.id}`;
                        return item.id;
                    }
                    return item;
                });
            }
        } catch (e) {
            console.error('Error parsing selected_ids', e);
        }

        // Find hidden input if selector provided
        if (input_selector) {
            hiddenInput = document.querySelector(input_selector);
            if (hiddenInput && hiddenInput.value) {
                try {
                    const parsed = JSON.parse(hiddenInput.value);
                    if (Array.isArray(parsed)) {
                        selectedDepartments = parsed.map(item => {
                            if (typeof item === 'object' && item.id !== undefined) {
                                loadedDepartmentNames[item.id] = item.name || `Department #${item.id}`;
                                return item.id;
                            }
                            return item;
                        });
                    }
                } catch (e) {
                    console.error('Error parsing hidden input value', e);
                }
            }
        }

        // Load initial departments
        loadDepartments();
    });

    function getBaseUrl() {
        if (base_url) return base_url;
        if (typeof window.WWW_DIR_JAVASCRIPT !== 'undefined') return window.WWW_DIR_JAVASCRIPT;
        return '/';
    }

    async function loadDepartments(query = '') {
        isLoading = true;
        try {
            const url = new URL(getBaseUrl() + ajax_url, window.location.origin);
            if (query) {
                url.searchParams.set('q', query);
            }

            const response = await fetch(url.toString());
            const data = await response.json();

            if (data.items) {
                departments = data.items;

                // Update loaded department names
                departments.forEach(dept => {
                    loadedDepartmentNames[dept.id] = dept.name;
                });
                loadedDepartmentNames = {...loadedDepartmentNames};
            }
        } catch (e) {
            console.error('Error loading departments', e);
        }
        isLoading = false;
    }

    function handleSearch(event) {
        searchQuery = event.target.value;
        loadDepartments(searchQuery);
    }

    function selectDepartment(dept) {
        // Check if already selected
        if (!selectedDepartments.includes(dept.id)) {
            loadedDepartmentNames[dept.id] = dept.name;
            selectedDepartments = [...selectedDepartments, dept.id];
            updateHiddenInput();
        }
    }

    function removeDepartment(deptId) {
        selectedDepartments = selectedDepartments.filter(id => id !== deptId);
        updateHiddenInput();
    }

    function updateHiddenInput() {
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(selectedDepartments);
        }
        if (jsonInput) {
            jsonInput.value = JSON.stringify(selectedDepartments);
        }
    }

    function isSelected(deptId) {
        return selectedDepartments.includes(deptId);
    }

    function getDepartmentName(deptId) {
        return loadedDepartmentNames[deptId] || `Department #${deptId}`;
    }
</script>

<div class="row">
    <div class="col-4">
        <div class="btn-block-department" data-limit="1">
            <ul class="nav">
                <li class="dropdown w-100">
                    <button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {placeholder}
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li class="btn-block-department-filter">
                            <input 
                                data-scope={input_name.replace('[]', '')} 
                                ajax-provider={ajax_url}
                                type="text" 
                                class="form-control input-sm" 
                                value={searchQuery}
                                on:input={handleSearch}
                            />
                            <div class="selected-items-filter">
                                {#each selectedDepartments as deptId}
                                    <div class="fs12">
                                        <!-- svelte-ignore a11y-click-events-have-key-events a11y-no-static-element-interactions a11y-missing-attribute -->
                                        <a data-stoppropagation="true" class="delete-item" data-value={deptId} on:click|preventDefault={() => removeDepartment(deptId)}>
                                            <input type="hidden" value={deptId} name={input_name} />
                                            <i class="material-icons chat-unread">delete</i>{getDepartmentName(deptId)}
                                        </a>
                                    </div>
                                {/each}
                            </div>
                        </li>
                        <li class="dropdown-result">
                            <ul class="list-unstyled dropdown-lhc">
                                {#if isLoading}
                                    <!-- svelte-ignore a11y-click-events-have-key-events a11y-no-noninteractive-element-interactions -->
                                    <li data-stoppropagation="true" class="search-option-item text-center">
                                        <small>Loading...</small>
                                    </li>
                                {:else if departments.length === 0}
                                    <!-- svelte-ignore a11y-click-events-have-key-events a11y-no-noninteractive-element-interactions -->
                                    <li data-stoppropagation="true" class="search-option-item text-center">
                                        <small>No departments found</small>
                                    </li>
                                {:else}
                                    {#each departments as dept}
                                        <!-- svelte-ignore a11y-click-events-have-key-events a11y-no-noninteractive-element-interactions -->
                                        <li data-stoppropagation="true" class="search-option-item">
                                            <label>
                                                <input 
                                                    title={dept.id}
                                                    class="me-1" 
                                                    type="radio" 
                                                    name="selector-{input_name}"
                                                    checked={isSelected(dept.id)}
                                                    on:change={() => selectDepartment(dept)}
                                                    value={dept.id}
                                                />
                                                {dept.name}
                                            </label>
                                        </li>
                                    {/each}
                                {/if}
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-8">
        {#each selectedDepartments as deptId}
            <!-- svelte-ignore a11y-click-events-have-key-events a11y-no-static-element-interactions a11y-interactive-supports-focus -->
            <span role="tabpanel" title="Click to remove" class="badge bg-secondary m-1 action-image" on:click={() => removeDepartment(deptId)}>
                {getDepartmentName(deptId)} <span class="material-icons text-warning me-0">delete</span>
            </span>
        {/each}
    </div>
</div>
