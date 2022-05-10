<div class="row">
	<div class="col-12">
		<div>
			<h5 class="header-survey-title py-2" ng-non-bindable>
            <?php if (isset($survey->configuration_array['survey_title']) && !empty($survey->configuration_array['survey_title'])) : ?>
                <?php echo htmlspecialchars($survey->configuration_array['survey_title']); ?>
            <?php else : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Please complete this short evaluation survey'); ?>
            <?php endif; ?>
            </h5>
		</div>
	</div>
</div>