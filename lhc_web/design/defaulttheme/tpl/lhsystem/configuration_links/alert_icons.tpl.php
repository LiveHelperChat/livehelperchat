<?php if ($currentUser->hasAccessTo('lhchat','administrate_alert_icon')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ChatAlertIcon"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Alert icons');?></a></li>
<?php endif; ?>