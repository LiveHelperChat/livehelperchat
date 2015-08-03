<?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="icon-tools"></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Settings')?></a></li>
<?php endif; ?> 