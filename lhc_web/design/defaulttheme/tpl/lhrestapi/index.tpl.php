<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/index','Rest API');?></h1>

<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/RestAPIKey"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/index','Rest API Keys');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/RestAPIKeyRemote"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/index','Rest API Remote Keys');?></a></li>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhaimcp','use')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('aimcp/key')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/index','MCP Setup');?></a></li>
    <?php endif; ?>
</ul>