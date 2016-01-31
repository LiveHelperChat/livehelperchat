<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/restapi_pre.tpl.php'));?>
<?php if ($system_configuration_rest_api_enabled == true && $currentUser->hasAccessTo('lhrestapi','use_admin')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('restapi/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Rest API');?></a></li>
<?php endif; ?>