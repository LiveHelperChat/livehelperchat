<?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
    <li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="material-icons">&#xf493;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Settings')?></span></a></li>
<?php endif; ?> 