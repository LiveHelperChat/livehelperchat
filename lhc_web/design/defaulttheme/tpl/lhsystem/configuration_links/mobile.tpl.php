<?php if ($currentUser->hasAccessTo('lhmobile','manage')) : ?>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Mobile');?></h4>
<ul class="circle small-list">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('mobile/settings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Settings');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('mobile/sessions')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Sessions');?></a></li>
</ul>
<?php endif; ?>