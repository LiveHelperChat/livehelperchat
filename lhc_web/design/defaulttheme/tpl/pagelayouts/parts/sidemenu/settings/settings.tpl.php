<?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
<li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="material-icons">settings_applications</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Settings')?></a></li>
<?php endif; ?> 