<?php if ($currentUser->hasAccessTo('lhsystem','messagecontentprotection')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/bbcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','BBCode configuration');?></a></li>
<?php endif;?>
