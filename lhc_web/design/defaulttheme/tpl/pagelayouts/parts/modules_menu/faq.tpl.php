<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/faq_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_faq_enabled == true && $useFaq) : ?>
<li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('faq/list')?>"><i class="material-icons">&#xf2d6;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','FAQ');?></span></a></li>
<?php endif;?>