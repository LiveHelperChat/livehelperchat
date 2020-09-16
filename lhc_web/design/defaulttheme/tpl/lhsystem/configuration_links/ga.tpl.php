<?php if ($currentUser->hasAccessTo('lhsystem','ga_configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/ga')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Events Tracking');?></a></li>
<?php endif; ?>
