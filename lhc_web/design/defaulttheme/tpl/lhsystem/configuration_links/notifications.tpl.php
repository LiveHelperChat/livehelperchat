<?php if ($currentUser->hasAccessTo('lhnotifications','use') ) : ?>
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Notifications');?></h5>
    <ul>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Subscribers list');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/settings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Settings');?></a></li>
    </ul>
<?php endif; ?>