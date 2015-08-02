<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/browseoffer_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_browseoffer_enabled == true && $useBo) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('browseoffer/index')?>"><i class="material-icons">open_in_browser</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Browse offers');?></a></li>
<?php endif; ?>	