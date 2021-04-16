<?php if ($currentUser->hasAccessTo('lhchatsettings','events')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/eventindex')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Events tracking');?></a></li>
<?php endif; ?>
