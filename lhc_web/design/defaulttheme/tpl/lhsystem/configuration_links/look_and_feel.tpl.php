<?php if (
    ($currentUser->hasAccessTo('lhabstract','use') && $currentUser->hasAccessTo('lhtheme','administratethemes')) ||
    $currentUser->hasAccessTo('lhchatsettings','administrate') ||
    $currentUser->hasAccessTo('lhsurvey','list_survey')
) : ?>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Visual settings for the visitor');?></h5>
    <ul>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/start_chat_form_settings.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/survey.tpl.php'));?>
    <?php if ($currentUser->hasAccessTo('lhabstract','use') && $currentUser->hasAccessTo('lhtheme','administratethemes')) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/WidgetTheme"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Widget themes');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/import')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Import new themes');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/default')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Default theme');?></a></li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<?php if ($currentUser->hasAccessTo('lhtheme','administratethemes') || $currentUser->hasAccessTo('lhtheme','personaltheme')) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Visual settings for the admin');?></h5>
<ul>
    <?php if ($currentUser->hasAccessTo('lhtheme','administratethemes')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/adminthemes')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Admin themes');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/defaultadmintheme')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Default admin theme');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhtheme','personaltheme')) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/personaltheme')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Personal theme');?></a></li>
    <?php endif; ?>
</ul>
<?php endif; ?>