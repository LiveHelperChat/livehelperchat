<title><?php if (isset($Result['path'])) : ?>
<?php 
$ReverseOrder = $Result['path'];
krsort($ReverseOrder);
foreach ($ReverseOrder as $pathItem) : ?><?php echo htmlspecialchars($pathItem['title']).' '?>&laquo;<?php echo ' ';endforeach;?>
<?php endif; ?> - Live helper chat</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<link rel="icon" type="image/png" href="<?php echo erLhcoreClassDesign::design('images/favicon.ico')?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo erLhcoreClassDesign::design('images/favicon.ico')?>">
<meta name="Keywords" content="" />
<meta name="Description" content="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'description' )?>" />
<meta name="robots" content="noindex, nofollow">
<meta name="copyright" content="Remigijus Kiminas, livehelperchat.com">
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_css.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_js_install.tpl.php'));?>

