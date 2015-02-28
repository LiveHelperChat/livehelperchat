<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/form_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_form_enabled == true && $useFm) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('form/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Forms');?></a></li>
<?php endif;?>