<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/browseoffer_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_browseoffer_enabled == true && $useBo) : ?>
    <li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('browseoffer/index')?>"><i class="material-icons">&#xf70e;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Browse offers');?></span></a></li>
<?php endif; ?>	