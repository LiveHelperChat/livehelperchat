<div class="p-2">
    <?php if (isset($themes) && !empty($themes)) : ?>

    <form action="<?php echo erLhcoreClassDesign::baseurl('theme/editthemebydepgroup')?>/<?php echo $depGroup->id?>" method="post">

    <?php if (isset($errors)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

    <?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Themes which will be edited all at once.');?></h6>
    <div>
        <?php foreach ($themes as $theme) : ?>
            <label class="badge bg-info me-1"><span class="material-icons">home</span><?php echo htmlspecialchars($theme['department']->name)?></label>
            <?php foreach ($theme['themes'] as $theme) : ?>
                <a href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit')?>/WidgetTheme/<?php echo $theme->id; ?>" target="_blank" class="badge bg-secondary me-1"><span class="material-icons">brush</span><?php echo htmlspecialchars($theme->name)?></a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <?php $fields = $object->getFields();?>

    <?php $translatableItem = array('identifier' => 'noonline_operators_offline'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

    <button type="submit" value="" class="btn btn-sm btn-primary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?></button>

    </form>

    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Department group departments does not have any themes assigned to them.');?>
    <?php endif; ?>

</div>

