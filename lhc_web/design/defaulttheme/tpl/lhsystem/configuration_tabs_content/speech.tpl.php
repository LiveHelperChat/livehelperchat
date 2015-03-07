<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/speech_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_content_speech_enabled == true && $currentUser->hasAccessTo('lhspeech','manage')) : ?>
<div role="tabpanel" class="tab-pane" id="speech">
   <ul class="circle small-list">
        <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Set default speech recognition language')?>" href="<?php echo erLhcoreClassDesign::baseurl('speech/defaultlanguage')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Speech language');?></a></li>
   </ul>
</div>
<?php endif;?>