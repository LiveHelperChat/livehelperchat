<div class="p5 meta-auto-hide p5 pl0">

    <div class="form-group mb5">
        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
            'input_name'     => 'generic_list-' . $messageId,
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Please choose'),
            'selected_id'    => 0,
            'css_class'      => 'form-control',
            'attr_id'        => $metaMessage['provider_id'],
            'display_name'   => $metaMessage['provider_name'],
            'list_function'  => $metaMessage['provider_dropdown']
        )); ?>
    </div>

    <a class="btn btn-xs btn-info" onclick="lhinst.dropdownClicked(<?php echo $messageId?>)">Choose</a>

</div>