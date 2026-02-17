<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/pre_attributes_conditions.tpl.php'));?>

<div class="row">
<?php for ($ai = 0; $ai < 10; $ai++) : ?>
    <div class="col-6">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attribute key')?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_key_' . ($ai + 1), $fields['attrf_key_' . ($ai + 1)], $object)?>
                </div>
                <div class="form-group form-group-sm">
                    <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Parse condition value for mathematical outcome');?>">
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_cond_math_' . ($ai + 1), $fields['attrf_cond_math_' . ($ai + 1)], $object)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Parse for mathematical outcome');?></label>
                </div>
            </div>
            <div class="col-4">
                <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Comparison operator')?></label>
                <select class="form-control form-control-sm" name="AbstractInput_attrf_cond_<?php echo ($ai + 1)?>">
                    <option value="eq" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'eq' ? print 'selected="selected"' : '')?> >=</option>
                    <option value="neq" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'neq' ? print 'selected="selected"' : '')?>>!=</option>
                    <option value="gt" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'gt' ? print 'selected="selected"' : '')?>>&gt;</option>
                    <option value="gte" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'gte' ? print 'selected="selected"' : '')?>>&gt;=</option>
                    <option value="lt" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'lt' ? print 'selected="selected"' : '')?>>&lt;</option>
                    <option value="lte" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'lte' ? print 'selected="selected"' : '')?>>&lt;=</option>
                    <option value="like" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'like' ? print 'selected="selected"' : '')?>>Text like</option>
                    <option value="notlike" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'notlike' ? print 'selected="selected"' : '')?>>Text not like</option>
                    <option value="contains" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'contains' ? print 'selected="selected"' : '')?>>Contains</option>
                    <option value="in_list" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'in_list' ? print 'selected="selected"' : '')?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','In list, items separated by ||')?></option>
                    <option value="in_list_lowercase" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'in_list_lowercase' ? print 'selected="selected"' : '')?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','In list (lowercase before comparison), items separated by ||')?></option>
                    <option value="not_in_list" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'not_in_list' ? print 'selected="selected"' : '')?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Not in list, items separated by ||')?></option>
                    <option value="not_in_list_lowercase" <?php (isset($object->design_data_array['attrf_cond_' . ($ai + 1)]) && $object->design_data_array['attrf_cond_' . ($ai + 1)] == 'not_in_list_lowercase' ? print 'selected="selected"' : '')?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Not in list (lowercase before comparison), items separated by ||')?></option>
                </select>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attribute value')?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_val_' . ($ai + 1), $fields['attrf_val_' . ($ai + 1)], $object)?>
                </div>
                <div class="form-group form-group-sm">
                    <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Parse condition value for mathematical outcome');?>"><?php echo erLhcoreClassAbstract::renderInput('attrf_val_math_' . ($ai + 1), $fields['attrf_val_math_' . ($ai + 1)], $object)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Parse for mathematical outcome');?></label>
                </div>
            </div>
        </div>
    </div>
<?php endfor; ?>
</div>