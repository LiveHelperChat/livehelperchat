<div class="form-group">
    <label>Bot</label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'bot_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select bot'),
        'selected_id'    => isset($departament->bot_configuration_array['bot_id']) ? $departament->bot_configuration_array['bot_id'] : 0,
        'css_class'      => 'form-control',
        'display_name'   => 'name',
        'list_function'  => 'erLhcoreClassModelGenericBotBot::getList'
    )); ?>
</div>

<div class="form-group">
    <label><input type="checkbox" name="bot_only_offline" <?php if (isset($departament->bot_configuration_array['bot_only_offline']) && $departament->bot_configuration_array['bot_only_offline'] == true) : ?>checked="checked"<?php endif?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Transfer to bot only if department is offline')?></label>
</div>
