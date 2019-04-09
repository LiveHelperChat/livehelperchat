<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Survey')?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'survey_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select survey'),
        'selected_id'    => isset($departament->bot_configuration_array['survey_id']) ? $departament->bot_configuration_array['survey_id'] : 0,
        'css_class'      => 'form-control',
        'display_name'   => 'name',
        'list_function'  => 'erLhAbstractModelSurvey::getList'
    )); ?>
</div>