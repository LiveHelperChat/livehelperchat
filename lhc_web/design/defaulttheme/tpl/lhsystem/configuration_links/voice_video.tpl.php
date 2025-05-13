<?php if ($currentUser->hasAccessTo('lhvoicevideo','configuration') ) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Voice & Video & ScreenShare');?></h5>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Configuration');?></a></li>
</ul>
<?php endif; ?>