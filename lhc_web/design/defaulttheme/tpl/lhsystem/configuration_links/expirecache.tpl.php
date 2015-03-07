<?php if ($currentUser->hasAccessTo('lhsystem','expirecache')) : ?>		
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/expirecache')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Clean cache');?></a></li>			
<?php endif; ?>