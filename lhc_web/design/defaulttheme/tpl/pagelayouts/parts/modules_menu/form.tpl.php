<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/form_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_form_enabled == true && $currentUser->hasAccessTo('lhform','manage_fm')) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Forms');?></h5>
<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Form"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of forms');?></a></li>
    <?php if ($currentUser->hasAccessTo('lhform','generate_js')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('form/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Page embed code');?></a></li>
    <?php endif; ?>
</ul>
<?php endif; ?>