<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/speech_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_speech_enabled && $currentUser->hasAccessTo('lhspeech','manage')) : ?>
<li role="presentation" class="nav-item"><a class="nav-link" href="#speech" aria-controls="speech" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Speech');?></a></li>
<?php endif;?>