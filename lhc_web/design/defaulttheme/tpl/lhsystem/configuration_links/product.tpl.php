<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/product_pre.tpl.php'));?>
<?php if ($system_configuration_links_product_enabled == true && $currentUser->hasAccessTo('lhproduct','manage_product')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('product/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Product');?></a></li>
<?php endif; ?>