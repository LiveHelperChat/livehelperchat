<?php if ($currentUser->hasAccessTo('lhsystem','configurerecaptcha')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/recaptcha')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Captcha settings');?></a></li>
<?php endif; ?>
