<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','Documents sharer');?></h1>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','General');?></h4>
<ul class="circle small-list">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','Browse your documents');?></a></li>
    <?php if ( erLhcoreClassUser::instance()->hasAccessTo('lhdocshare','change_configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','Configuration');?></a></li>
    <?php endif;?>
</ul>