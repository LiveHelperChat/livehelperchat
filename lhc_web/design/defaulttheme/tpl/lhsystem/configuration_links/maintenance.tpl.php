<?php if ($currentUser->hasAccessTo('lhchat','maintenance')) : ?>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Maintenance');?></a></li>
<?php endif; ?>