<?php if (isset($onlineAttributeFilter)) : ?>
<script>
    var onlineAttributeFilter = <?php echo json_encode($onlineAttributeFilter);?>;
</script>
<?php endif; ?>
<div class="col-1 offset-sm-2 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_key_1" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_key_1',this.value])" ng-model="online.attrf_key_1" value="" title="1. Attribute key" placeholder="1. Attribute key">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_val_1" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_val_1',this.value])" ng-model="online.attrf_val_1" value="" title="Attribute value, separate multiple by ||" placeholder="Attribute value, separate multiple by ||">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_key_2" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_key_2',this.value])" ng-model="online.attrf_key_2" value="" title="2. Attribute key" placeholder="2. Attribute key">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_val_2" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_val_2',this.value])" ng-model="online.attrf_val_2" value="" title="Attribute value, separate multiple by ||" placeholder="Attribute value, separate multiple by ||">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_key_3" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_key_3',this.value])" ng-model="online.attrf_key_3" value="" title="3. Attribute key" placeholder="3. Attribute key">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_val_3" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_val_3',this.value])" ng-model="online.attrf_val_3" value="" title="Attribute value, separate multiple by ||" placeholder="Attribute value, separate multiple by ||">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_key_4" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_key_4',this.value])" ng-model="online.attrf_key_4" value="" title="4. Attribute key" placeholder="4. Attribute key">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_val_4" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_val_4',this.value])" ng-model="online.attrf_val_4" value="" title="Attribute value, separate multiple by ||" placeholder="Attribute value, separate multiple by ||">
</div>
<div class="col-1 pb-2 pe-0">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_key_5" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_key_5',this.value])" ng-model="online.attrf_key_5" value="" title="5. Attribute key" placeholder="5. Attribute key">
</div>
<div class="col-1 pb-2">
    <input type="text" class="form-control form-control-sm" id="svelte-attrf_val_5" onkeyup="ee.emitEvent('svelteOnlineUserSettingKey',['oattrf_val_5',this.value])" ng-model="online.attrf_val_5" value="" title="Attribute value, separate multiple by ||" placeholder="Attribute value, separate multiple by ||">
</div>