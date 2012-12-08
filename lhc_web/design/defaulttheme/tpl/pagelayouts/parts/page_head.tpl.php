<title><? if (isset($Result['path'])) : ?>
<? 
$ReverseOrder = $Result['path'];
krsort($ReverseOrder);
foreach ($ReverseOrder as $pathItem) : ?>
 <?=$pathItem['title']?>&laquo;
<? endforeach;?>
<? endif; ?>

<?=erConfigClassLhConfig::getInstance()->conf->getSetting( 'site', 'title' )?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?=erLhcoreClassDesign::design('css/style.css');?>" /> 
<link rel="icon" type="image/png" href="design/defaulttheme/images/favicon.ico" />
<link rel="shortcut icon" type="image/x-icon" href="design/defaulttheme/images/favicon.ico">
<meta name="Keywords" content="live,help,support" />
<meta name="Description" content="" />
<script type="text/javascript">
WWW_DIR_JAVASCRIPT = '<?=erLhcoreClassDesign::baseurl()?>';
</script>

<script type="text/javascript" language="javascript" src="<?=erLhcoreClassDesign::design('js/jquery-1.7.2.min.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?=erLhcoreClassDesign::design('js/jquery-ui-1.8.21.custom.min.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?=erLhcoreClassDesign::design('js/lh.js');?>"></script>
<script type="text/javascript" language="javascript" src="<?=erLhcoreClassDesign::design('js/jquery.hotkeys-0.7.9.min.js');?>"></script>
