<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/questionary_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_questionary_enabled == true && $useQuestionary) : ?>
<li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('questionary/list')?>"><i class="material-icons">&#xf18e;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Questionary');?></span></a></li>
<?php endif;?>