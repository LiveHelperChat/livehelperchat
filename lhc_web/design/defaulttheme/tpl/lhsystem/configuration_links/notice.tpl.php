<?php if ($currentUser->hasAccessTo('lhsystem','notice')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/notice')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Notice messages');?></a></li>
<?php endif; ?>