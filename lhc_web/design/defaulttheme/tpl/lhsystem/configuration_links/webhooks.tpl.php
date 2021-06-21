<?php if ($currentUser->hasAccessTo('lhwebhooks','configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Webhooks');?></a></li>
<?php endif; ?>