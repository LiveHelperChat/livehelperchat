<?php if ($currentUser->hasAccessTo('lhjitsi', 'configuration')) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Jitsi Calls'); ?></h5>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('jitsi/configuration'); ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Configuration'); ?></a></li>
</ul>
<?php endif; ?>
