<?php if ($currentUser->hasAccessTo('lhvoicevideo','configuration') ) : ?>
    <li>
        <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Voice & Video & ScreenShare');?></b>
        <ul>
            <li><a href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Configuration');?></a></li>
        </ul>
    </li>
<?php endif; ?>