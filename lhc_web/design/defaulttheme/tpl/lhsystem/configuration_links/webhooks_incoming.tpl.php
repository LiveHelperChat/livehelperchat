<?php if ($currentUser->hasAccessTo('lhwebhooks','configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/incomingwebhooks')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Incoming webhooks');?></a></li>
<?php endif; ?>