<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive');?></h1>

<ul class="circle small-list">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Archives list');?></a></li>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailarchive','configuration')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/newarchive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','New archive');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Configuration');?></a></li>
    <?php endif; ?>
</ul>