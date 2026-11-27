<?php if ($currentUser->hasAccessTo('lhjitsi', 'configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('jitsi/configuration'); ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Jitsi Calls'); ?></a></li>
<?php endif; ?>
