<div class="p5 meta-auto-hide p5 pl0">

    <div class="form-group mb5">
        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
            'input_name'     => 'generic_list-' . $messageId,
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Please choose'),
            'selected_id'    => (isset($metaMessage['provider_default']) ? $metaMessage['provider_default'] : 0),
            'css_class'      => 'form-control input-sm',
            'attr_id'        => $metaMessage['provider_id'],
            'display_name'   => $metaMessage['provider_name'],
            'list_function'  => $metaMessage['provider_dropdown'],
            'list_function_params'  => $metaMessage['provider_arguments'],
        )); ?>
    </div>

    <a class="btn btn-xs btn-info" onclick="lhinst.dropdownClicked(<?php echo $messageId?>)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Choose')?></a>

</div>