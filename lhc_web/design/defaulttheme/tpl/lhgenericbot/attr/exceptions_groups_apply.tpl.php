<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Exception groups to apply');?></label>
    <div class="row">
        <?php
        echo erLhcoreClassRenderHelper::renderCheckbox(array(
            'list_function' => 'erLhcoreClassModelGenericBotException::getList',
            'selected_id' => (isset($item->configuration_array['exc_group_id']) ? $item->configuration_array['exc_group_id'] : array()),
            'input_name' => 'exc_group_id[]',
            'wrap_prepend' => '<div class="col-4">',
            'wrap_append' => '</div>'
        ));
        ?>
    </div>
</div>