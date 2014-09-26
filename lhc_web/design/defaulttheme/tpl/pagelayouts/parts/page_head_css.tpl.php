<?php if (erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language') == '') : ?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/normalize.css;css/foundation-ltr.css;css/app.css;css/colorbox.css;css/override.css;css/fontello.css;css/datepicker.css');?>" />
<?php else : ?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/normalize.css;css/foundation-rtl.css;css/app.css;css/app-rtl.css;css/colorbox.css;css/override_rtl.css;css/fontello.css;css/datepicker.css');?>" />
<?php endif;?>
<!--[if lt IE 9]>
	<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/ie8-and-down.css');?>" />
<![endif]-->
<?php echo isset($Result['additional_header_css']) ? $Result['additional_header_css'] : ''?>