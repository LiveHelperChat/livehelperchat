<?php if ($currentUser->hasAccessTo('lhnotifications','use') ) : ?>
<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Notifications');?></b>
    <ul>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Subscribers list');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/settings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Settings');?></a></li>
    </ul>
</li>
<?php endif; ?>