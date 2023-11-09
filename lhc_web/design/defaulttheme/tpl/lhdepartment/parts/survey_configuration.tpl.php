<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Survey')?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'survey_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select survey'),
        'selected_id'    => isset($departament->bot_configuration_array['survey_id']) ? $departament->bot_configuration_array['survey_id'] : 0,
        'css_class'      => 'form-control form-control-sm',
        'display_name'   => 'name',
        'list_function'  => 'erLhAbstractModelSurvey::getList'
    )); ?>
</div>

<div class="form-group">
    <label><input type="checkbox" name="hide_survey_bot" value="on" <?php if (isset($departament->bot_configuration_array['hide_survey_bot']) && $departament->bot_configuration_array['hide_survey_bot'] == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Do not show survey if chat is ended in a bot status');?></label>
</div>
