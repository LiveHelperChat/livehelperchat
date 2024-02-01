<?php if ($currentUser->hasAccessTo('lhsystem','messagecontentprotection')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ChatMessagesGhosting"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Messages content protection');?></a></li>
<?php endif; ?>