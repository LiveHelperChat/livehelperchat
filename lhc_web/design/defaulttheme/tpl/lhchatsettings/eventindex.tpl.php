<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Events tracking')?></h4>
<ul>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhsystem','ga_configuration')) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/ga')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Events Tracking');?></a></li>
    <?php endif; ?>

    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchatsettings','events')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/eventlist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','List of events tracking settings by department');?></li>
    <?php endif; ?>
</ul>