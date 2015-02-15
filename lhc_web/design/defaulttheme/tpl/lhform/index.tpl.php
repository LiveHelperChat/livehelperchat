<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','Form');?></h1>

<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Form"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','List of forms');?></a></li>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhform','generate_js')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('form/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','Page embed code');?></a></li>
    <?php endif;?>
</ul>