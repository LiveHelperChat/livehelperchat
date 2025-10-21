<div class="row">
    <div class="col-12">
        <?php if (isset($theme) && is_object($theme) && !empty($theme->bot_configuration_array['survey_title'])) : ?>
            <?php echo str_replace('{survey_title}',($survey->configuration_array['survey_title'] ? erLhcoreClassBBCode::make_clickable(htmlspecialchars(erLhcoreClassGenericBotWorkflow::translateMessage($survey->configuration_array['survey_title'], array('chat' => $chat, 'args' => ['chat' => $chat])))) : erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Please complete this short evaluation survey')), erLhcoreClassBBCodePlain::make_clickable($theme->bot_configuration_array['survey_title'], array('sender' => 0, 'clean_event' => true)));?>
        <?php else : ?>
        <div>
            <h5 class="header-survey-title py-2" ng-non-bindable>
                <?php if (isset($survey->configuration_array['survey_title']) && !empty($survey->configuration_array['survey_title'])) : ?>
                    <?php echo  erLhcoreClassBBCode::make_clickable(htmlspecialchars(erLhcoreClassGenericBotWorkflow::translateMessage($survey->configuration_array['survey_title'], array('chat' => $chat, 'args' => ['chat' => $chat])))); ?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Please complete this short evaluation survey'); ?>
                <?php endif; ?>
            </h5>
        </div>
        <?php endif; ?>
    </div>
</div>