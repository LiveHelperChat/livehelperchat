<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/statistic_pre.tpl.php'));?>
<?php if ($system_configuration_links_statistic_enabled == true && $currentUser->hasAccessTo('lhstatistic','viewstatistic')) : ?>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Statistic');?></a></li>
<?php endif; ?>