<div class="meta-message meta-message-<?php echo $messageId?>">
    <div class="meta-auto-hide pt-1 pb-1 pr-1 pl-2">
        <div class="form-group mb-1">
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'generic_list-' . $messageId,
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Please choose'),
                'selected_id'    => (isset($metaMessage['provider_default']) ? $metaMessage['provider_default'] : 0),
                'css_class'      => 'form-control form-control-sm',
                'attr_id'        => $metaMessage['provider_id'],
                'display_name'   => $metaMessage['provider_name'],
                'list_function'  => $metaMessage['provider_dropdown'],
                'list_function_params'  => $metaMessage['provider_arguments'],
            )); ?>
        </div>
        <button type="button" class="btn btn-sm btn-secondary" data-id="<?php echo $messageId?>" onclick="lhinst.dropdownClicked(<?php echo $messageId?>,$(this))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Choose')?></button>
    </div>
</div>