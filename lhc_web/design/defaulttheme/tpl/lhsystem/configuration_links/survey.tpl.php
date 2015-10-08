<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/survey_pre.tpl.php'));?>
<?php if ($system_configuration_links_survey_enabled == true && $currentUser->hasAccessTo('lhsurvey','manage_survey')) : ?>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Survey"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Surveys');?></a></li>
<?php endif; ?>