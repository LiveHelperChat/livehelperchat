<?php if ($currentUser->hasAccessTo('lhgenericbot','use') ) : ?>
<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Bot');?></b>
    <ul>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Bot list');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listexceptions')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Bot exceptions');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listtranslations')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Translations groups');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listrestapi')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Rest API Calls');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/commands')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Commands');?></a></li>
    </ul>
</li>
<?php endif; ?>