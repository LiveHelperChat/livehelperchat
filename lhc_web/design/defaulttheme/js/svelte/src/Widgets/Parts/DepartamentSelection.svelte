<script>

    import { createEventDispatcher, onMount } from 'svelte'

    const dispatch = createEventDispatcher()

    export let selected_departments = [];
    export let index_block = 0;

    let departaments = {};
    let keyword = "";

    onMount(() => {
        departaments = window.replaceDepartments;
    });

    function selectDepartment(dep_id) {
        if (selected_departments.indexOf(parseInt(dep_id)) === -1){
            dispatch('department_select', {
                dep_id: parseInt(dep_id)
            })
        }
    }

    function deleteElement(dep_id) {
        dispatch('department_unselect', {
            dep_id: parseInt(dep_id)
        })
    }
</script>

<div class="row">
    <div class="col-12">
        <label>Department filter</label>
    </div>
    <div class="col-4">
        <div class="form-group">
            <div class="btn-block-department">
                <ul class="nav">
                    <li class="dropdown w-100">
                        <button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown show"  data-bs-toggle="dropdown" aria-expanded="true">Choose department</button>
                        <ul class="dropdown-menu" role="menu" data-popper-placement="top-start">
                            <li class="btn-block-department-filter">
                                <input bind:value={keyword} type="text" class="form-control input-sm">
                                <div class="selected-items-filter"></div>
                            </li>
                            <li class="dropdown-result">
                                <ul class="list-unstyled dropdown-lhc">
                                    <li data-stoppropagation="true" class="search-option-item fw-bold">
                                    <label><input class="me-1" checked="checked" type="radio" value="0">Any</label></li>
                                    {#each Object.entries(departaments) as [id, name]}
                                        {#if keyword == "" || name.includes(keyword)}
                                            <li data-stoppropagation="true" class="search-option-item"><label><input class="me-1" name="selector-department_id-0" on:change={(e) => {selectDepartment(id)}} type="radio" value={id}>{name}</label></li>
                                        {/if}
                                    {/each}
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-8">
        {#if selected_departments}
            {#each selected_departments as dep_id}
                <span role="tabpanel" on:click={(e) => deleteElement(dep_id)} title="Click to remove" class="badge bg-secondary m-1 action-image">
                    {departaments[dep_id] ? departaments[dep_id] : dep_id} <span class="material-icons text-warning me-0">delete</span>
                    <input type="hidden" name="dep_ids[{index_block}][]" value="{dep_id}">
                </span>
            {/each}
        {/if}
    </div>
</div>