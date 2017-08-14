<?php 
if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != '') : 
$customAdminfields = json_decode($start_data_fields['custom_fields'],true);
if (is_array($customAdminfields)) : ?>
<div class="row">
    <?php foreach ($customAdminfields as $key => $adminField) : if ($adminField['visibility'] == 'all' || $adminCustomFieldsMode == $adminField['visibility']) : ?>        
        <?php if ($adminField['fieldtype'] == 'hidden' || (isset($input_data->via_hidden[$key]) && $input_data->via_hidden[$key] == 't')) : ?>
        
            <?php if (isset($input_data->via_hidden[$key]) && $input_data->via_hidden[$key] == 't') : ?>
            <input class="form-control" type="hidden" name="via_hidden[<?php echo $key?>]" value="t" />
            <?php endif;?>
        
            <?php if (isset($input_data->via_encrypted[$key]) && $input_data->via_encrypted[$key] == 't') : ?>
            <input class="form-control" type="hidden" name="via_encrypted[<?php echo $key?>]" value="t" />
            <?php endif;?>
        
            <input class="form-control" type="hidden" name="value_items_admin[<?php echo $key?>]" value="<?php isset($input_data->value_items_admin[$key]) ? print htmlspecialchars($input_data->value_items_admin[$key]) : print htmlspecialchars($adminField['defaultvalue'])?>" />
        <?php elseif ($adminField['fieldtype'] == 'dropdown') : ?>
            <?php if (!isset($adminField['showcondition']) || $adminField['showcondition'] == 'always' || ($adminField['showcondition'] == 'uempty' && ($input_data->username == '' || isset($_POST['show_admin_item'][$key])))) : ?>
            <input type="hidden" name="show_admin_item[<?php echo $key?>]" value="true" />
            <div class="col-xs-<?php echo htmlspecialchars($adminField['size'])?>">
                <div class="form-group<?php if (isset($errors['additional_admin_'.$key])) : ?> has-error<?php endif;?>">
                    <label class="control-label" id="label-<?php echo htmlspecialchars('additional_admin_'.$key)?>"><?php echo htmlspecialchars($adminField['fieldname'])?>&nbsp;<?php $adminField['isrequired'] == 'true' ? print '*' : ''?></label>
                    <select name="value_items_admin[<?php echo $key?>]" class="form-control">
                        <option value="">Please choose</option>
                        <?php foreach (explode("\n",$adminField['options']) as $option) : ?>
                            <option <?php isset($input_data->value_items_admin[$key]) && $option == $input_data->value_items_admin[$key] ? print 'selected="selected"' : ''?> value="<?php echo htmlspecialchars($option)?>"><?php echo htmlspecialchars($option)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
        <?php else : $hasExtraField = true; ?>
            <?php if (!isset($adminField['showcondition']) || $adminField['showcondition'] == 'always' || ($adminField['showcondition'] == 'uempty' && ($input_data->username == '' || isset($_POST['show_admin_item'][$key])))) : ?>
            <input type="hidden" name="show_admin_item[<?php echo $key?>]" value="true" />
            <div class="col-xs-<?php echo htmlspecialchars($adminField['size'])?>">
                <div class="form-group<?php if (isset($errors['additional_admin_'.$key])) : ?> has-error<?php endif;?>">
                    <label class="control-label" id="label-<?php echo htmlspecialchars('additional_admin_'.$key)?>"><?php echo htmlspecialchars($adminField['fieldname'])?>&nbsp;<?php $adminField['isrequired'] == 'true' ? print '*' : ''?></label>
                    <input class="form-control" aria-labelledby="label-<?php echo htmlspecialchars('additional_admin_'.$key)?>" type="text" name="value_items_admin[<?php echo $key?>]" <?php $adminField['isrequired'] == 'true' ? print 'aria-required="true" required' : ''?> value="<?php isset($input_data->value_items_admin[$key]) ? print htmlspecialchars($input_data->value_items_admin[$key]) : print htmlspecialchars($adminField['defaultvalue'])?>" />
                </div>
            </div>
            <?php endif; ?>
    <?php endif; endif; endforeach;?>
</div>
<?php endif; endif;?>