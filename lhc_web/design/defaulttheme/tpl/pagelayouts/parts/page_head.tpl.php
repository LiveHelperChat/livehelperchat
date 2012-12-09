<title><?php if (isset($Result['path'])) : ?>
<?php 
$ReverseOrder = $Result['path'];
krsort($ReverseOrder);
foreach ($ReverseOrder as $pathItem) : ?>
 <?php echo $pathItem['title']?>&laquo;
<?php endforeach;?>
<?php endif; ?>

<?php echo erConfigClassLhConfig::getInstance()->conf->getSetting( 'site', 'title' )?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::design('css/style.css');?>" /> 
<link rel="icon" type="image/png" href="design/defaulttheme/images/favicon.ico" />
<link rel="shortcut icon" type="image/x-icon" href="design/defaulttheme/images/favicon.ico">
<meta name="Keywords" content="live,help,support" />
<meta name="Description" content="" />
<script type="text/javascript">
WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
WWW_DIR_JAVASCRIPT_FILES = '<?php echo erLhcoreClassDesign::design('')?>';
</script>

<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::design('js/jquery-1.7.2.min.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::design('js/jquery-ui-1.8.21.custom.min.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::design('js/modernizr.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::design('js/lh.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::design('js/jquery.hotkeys-0.7.9.min.js');?>"></script>
