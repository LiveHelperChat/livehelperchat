<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language')?>">
<head>

<title><?php if (isset($Result['path'])) : ?>
<?php
$ReverseOrder = $Result['path'];
krsort($ReverseOrder);
foreach ($ReverseOrder as $pathItem) : ?><?php echo htmlspecialchars($pathItem['title']).' '?>&laquo;<?php echo ' ';endforeach;?>
<?php endif; ?>
<?php echo htmlspecialchars(erLhcoreClassModelChatConfig::fetch('application_name')->current_value)?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
<link rel="icon" type="image/png" href="<?php echo erLhcoreClassDesign::design('images/favicon.ico')?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo erLhcoreClassDesign::design('images/favicon.ico')?>">
<meta name="Keywords" content="" />
<meta name="Description" content="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'description' )?>" />
<meta name="robots" content="noindex, nofollow">

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/copyright_meta.tpl.php'));?>

<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/BookReader.css');?>" />
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/BookReader/jquery-1.4.2.min.js;js/BookReader/jquery-ui-1.8.5.custom.min.js;js/BookReader/dragscrollable.js;js/BookReader/jquery.colorbox-min.js;js/BookReader/jquery.ui.ipad.js;js/BookReader/jquery.bt.min.js;js/BookReader/BookReader.js');?>"></script>

</head>
<body>

<?php echo $Result['content']; ?>

<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/BookReader/BookReaderJSSimple.js');?>"></script>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>
</body>
</html>