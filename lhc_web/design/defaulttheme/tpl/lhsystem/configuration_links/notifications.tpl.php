<?php if ($currentUser->hasAccessTo('lhnotifications','use') ) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Visitors notifications');?></h5>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/settings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Visitors settings')?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Visitors subscribers list')?></a></li>
</ul>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Operators notifications');?></h5>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/opsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Operators settings')?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/oplist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Operators subscribers list')?></a></li>
</ul>
<?php endif; ?>