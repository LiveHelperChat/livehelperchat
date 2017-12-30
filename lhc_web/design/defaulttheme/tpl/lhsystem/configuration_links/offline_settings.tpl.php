<?php if ($currentUser->hasAccessTo('lhsystem','offlinesettings')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/offlinesettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Offline settings');?></a></li>
<?php endif; ?>