<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/form_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_form_enabled == true && $useFm) : ?>
<li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('form/index')?>"><i class="material-icons">&#xf066;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Forms');?></span></a></li>
<?php endif;?>