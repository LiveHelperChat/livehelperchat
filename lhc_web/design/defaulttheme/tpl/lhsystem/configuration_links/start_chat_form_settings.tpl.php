<?php if ($currentUser->hasAccessTo('lhchatsettings','administrate')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/startchatformsettingsindex')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Start chat form settings');?></a></li>
<?php endif; ?>