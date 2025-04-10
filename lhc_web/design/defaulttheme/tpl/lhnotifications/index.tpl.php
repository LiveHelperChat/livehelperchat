<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Notifications')?></h1>

<b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Visitors notifications');?></b>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/settings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Visitors settings')?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Visitors subscribers list')?></a></li>
</ul>

<b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Operators notifications');?></b>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/opsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Operators settings')?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/oplist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/admin','Operators subscribers list')?></a></li>
</ul>
