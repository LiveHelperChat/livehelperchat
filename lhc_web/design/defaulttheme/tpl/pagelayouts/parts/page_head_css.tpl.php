<?php if (erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language') == '') : ?>
    <?php if (!isset($Result['anonymous']) && (int)erLhcoreClassModelUserSetting::getSetting('dark_mode',0) == 1) : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('vendor/bootstrap/css/bootstrap-dark.min.css;css/material_font.css;css/app-dark.css;css/override.css;css/datepicker.css;css/gbot.css;css/color-picker.css');?>" />
    <?php else : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('vendor/bootstrap/css/bootstrap.min.css;css/material_font.css;css/app.css;css/override.css;css/datepicker.css;css/gbot.css;css/color-picker.css');?>" />
    <?php endif; ?>
<?php else : ?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('vendor/bootstrap/css/bootstrap.min.css;css/bootstrap-rtl.min.css;css/material_font.css;css/app.css;css/app-rtl.css;css/override_rtl.css;css/datepicker.css;css/gbot.css;css/color-picker.css');?>" />
<?php endif;?>
<?php echo isset($Result['additional_header_css']) ? $Result['additional_header_css'] : ''?>