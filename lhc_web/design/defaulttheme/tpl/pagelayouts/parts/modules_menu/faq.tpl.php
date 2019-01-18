<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/faq_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_faq_enabled == true && $useFaq) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/list')?>"><i class="material-icons">help</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','FAQ');?></a></li>
<?php endif;?>