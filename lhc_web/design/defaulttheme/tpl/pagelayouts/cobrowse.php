<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language')?>">
<head>
	<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
	<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/cobrowse/compiled/cobrowse.operator.min.js');?>"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/cobrowse.css');?>" />
</head>
<body>

<?php echo $Result['content']; ?>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>