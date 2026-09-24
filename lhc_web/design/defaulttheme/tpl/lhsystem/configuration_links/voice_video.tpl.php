<?php if ($currentUser->hasAccessTo('lhvoicevideo','configuration') || $currentUser->hasAccessTo('lhvoicevideo','sessions')) : ?>
    <li>
        <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Voice & Video & ScreenShare');?></b>
        <ul>
            <?php if ($currentUser->hasAccessTo('lhvoicevideo','configuration')) : ?>
            <li><a href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Configuration');?></a></li>
            <?php endif; ?>
            <?php if ($currentUser->hasAccessTo('lhvoicevideo','sessions')) : ?>
            <li><a href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/sessions')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Call history');?></a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>