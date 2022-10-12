<div class="row">
<?php for ($ai = 0; $ai < 10; $ai++) : ?>
    <div class="col-6">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attribute key')?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_key_' . ($ai + 1), $fields['attrf_key_' . ($ai + 1)], $object)?>
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
                </select>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attribute value')?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_val_' . ($ai + 1), $fields['attrf_val_' . ($ai + 1)], $object)?>
                </div>
            </div>
        </div>
    </div>
<?php endfor; ?>
</div>