<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Individual theme is picked only if one department is passed. Theme determination happens in the following order.');?></p>

<ul>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Check for passed theme');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Check for individual theme');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Check for default department theme');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Check for global default theme');?></li>
</ul>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Individual theme');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'theme_ind[]',
        'multiple'       => true,
        'selected_id'    => isset($departament->bot_configuration_array['theme_ind']) ? explode(',',$departament->bot_configuration_array['theme_ind']) : 0,
        'css_class'      => 'form-control form-control-sm',
        'display_name'   => 'name',
        'list_function'  => 'erLhAbstractModelWidgetTheme::getList'
    )); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Default theme applied per department');?></label>
        <?php foreach (explode(',',$departament->bot_configuration_array['theme_default']) as $themeDefault) : if ((int)$themeDefault > 0) : ?>
            <span class="badge bg-success ms-1"> <?php print htmlspecialchars(erLhAbstractModelWidgetTheme::fetch((int)$themeDefault)) ?></span>
        <?php endif; endforeach; ?>
    </b>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','This theme is set from');?> <a href="<?php echo erLhcoreClassDesign::baseurl('theme/default')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Default theme');?></a> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','section and checking As default department theme.');?></i></small></p>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Theme global');?></label> -

    <?php foreach (explode(',',erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value) as $themeDefault) : if ((int)$themeDefault > 0) : ?>
        <span class="badge bg-success ms-1"> <?php print htmlspecialchars(erLhAbstractModelWidgetTheme::fetch((int)$themeDefault)) ?></span>
    <?php endif; endforeach; ?>

    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','If you are using only');?> <a href="<?php echo erLhcoreClassDesign::baseurl('theme/default')?>">Default theme</a> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','option and you have more than one server you might get inconsistent theme pickup. Apply');?> <a href="<?php echo erLhcoreClassDesign::baseurl('theme/default')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Default theme');?></a> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','to department as default or choose individual theme.');?></i></small></p>
</div>