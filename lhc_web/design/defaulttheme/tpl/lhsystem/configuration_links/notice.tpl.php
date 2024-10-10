<?php if ($currentUser->hasAccessTo('lhsystem','notice')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/notice')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Notice message');?></a></li>
<?php endif; ?>