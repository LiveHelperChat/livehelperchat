<?php 

if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != '') : 
$customAdminfields = json_decode($start_data_fields['custom_fields'],true); 
if (is_array($customAdminfields)) : ?>
<div class="row">
    <?php foreach ($customAdminfields as $key => $adminField) : if ($adminField['visibility'] == 'all' || $adminCustomFieldsMode == $adminField['visibility']) : ?>        
        <?php if ($adminField['fieldtype'] == 'hidden' || (isset($input_data->via_hidden[$key]) && $input_data->via_hidden[$key] == 'true')) : ?>
        
            <?php if (isset($input_data->via_hidden[$key]) && $input_data->via_hidden[$key] == 'true') : ?>
            <input class="form-control" type="hidden" name="via_hidden[<?php echo $key?>]" value="true" />
            <?php endif;?>
        
            <input class="form-control" type="hidden" name="value_items_admin[<?php echo $key?>]" value="<?php isset($input_data->value_items_admin[$key]) ? print htmlspecialchars($input_data->value_items_admin[$key]) : print htmlspecialchars($adminField['defaultvalue'])?>" />
        <?php else : $hasExtraField = true; ?>        
        <div class="col-xs-<?php echo htmlspecialchars($adminField['size'])?>">
            <div class="form-group<?php if (isset($errors['additional_admin_'.$key])) : ?> has-error<?php endif;?>">
                <label class="control-label"><?php echo htmlspecialchars($adminField['fieldname'])?>&nbsp;<?php $adminField['isrequired'] == 'true' ? print '*' : ''?></label>
                <input class="form-control" type="text" name="value_items_admin[<?php echo $key?>]" value="<?php isset($input_data->value_items_admin[$key]) ? print htmlspecialchars($input_data->value_items_admin[$key]) : print htmlspecialchars($adminField['defaultvalue'])?>" />
            </div>
        </div>
    <?php endif; endif; endforeach;?>
</div>
<?php endif; endif;?>