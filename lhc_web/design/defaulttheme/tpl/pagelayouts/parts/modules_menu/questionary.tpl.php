<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/questionary_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_questionary_enabled == true && $useQuestionary) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/list')?>"><i class="material-icons">email</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Questionary');?></a></li>
<?php endif;?>