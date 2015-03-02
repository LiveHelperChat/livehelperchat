<?php if ($currentUser->hasAccessTo('lhchat','administrategeoconfig')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','GEO detection configuration');?></a></li>
<?php endif; ?>