<?php if (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') : ?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/normalize.css;css/foundation-ltr.css;css/app.css;css/colorbox.css;css/override.css');?>" />
<?php else : ?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/normalize.css;css/foundation-rtl.css;css/app.css;css/app-rtl.css;css/colorbox.css;css/override_rtl.css');?>" />
<?php endif;?>