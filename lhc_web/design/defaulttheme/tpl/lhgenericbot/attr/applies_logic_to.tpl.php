<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These bot logic applies also');?></label>
    <div class="row">
        <?php echo erLhcoreClassRenderHelper::renderCheckbox(array(
            'list_function' => 'erLhcoreClassModelGenericBotBot::getList',
            'selected_id' => (isset($item->configuration_array['bot_id']) ? $item->configuration_array['bot_id'] : array()),
            'input_name' => 'bot_id[]',
            'wrap_prepend' => '<div class="col-6 fs12">',
            'wrap_append' => '</div>'
        ));
        ?>
    </div>
</div>