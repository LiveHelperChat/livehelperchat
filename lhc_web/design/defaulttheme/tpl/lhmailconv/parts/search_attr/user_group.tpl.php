<div class="col-md-2">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User group');?></label>
        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
            'input_name'     => 'group_ids[]',
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
            'selected_id'    => $input->group_ids,
            'css_class'      => 'form-control',
            'display_name'   => 'name',
            'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(false, true),
            'list_function'  => 'erLhcoreClassModelGroup::getList'
        )); ?>
    </div>
</div>