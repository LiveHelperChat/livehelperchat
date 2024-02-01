<?php if ($currentUser->hasAccessTo('lhgenericbot','use') || $currentUser->hasAccessTo('lhgenericbot','use_individualization')) : ?>
<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Bot');?></b>
    <ul>
        <?php if ($currentUser->hasAccessTo('lhgenericbot','use') ) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Bot constructor');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listexceptions')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Responses for API error');?></a></li>
        <?php endif; ?>
        <?php if ($currentUser->hasAccessTo('lhgenericbot','use_individualization') ) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listtranslations')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Bot individualization');?></a></li>
        <?php endif; ?>
        <?php if ($currentUser->hasAccessTo('lhgenericbot','use') ) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listrestapi')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Rest API Calls');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/commands')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Commands');?></a></li>
        <?php endif; ?>
        <?php if ($currentUser->hasAccessTo('lhgenericbot','manage_conditions') ) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/conditions')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Conditions');?></a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>