<?php if ($currentUser->hasAccessTo('lhvoicevideo','configuration') || $currentUser->hasAccessTo('lhjitsi', 'configuration')) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Voice & Video & ScreenShare');?></h5>
<ul>
    <?php if ($currentUser->hasAccessTo('lhvoicevideo','configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Agora Calls');?></a></li>
    <?php endif; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/jitsi.tpl.php'));?>
</ul>
<?php endif; ?>