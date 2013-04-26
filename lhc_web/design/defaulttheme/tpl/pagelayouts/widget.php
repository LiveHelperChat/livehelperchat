<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/widget.css;css/widget_override.css');?>" />
</head>
<body<?php isset($Result['pagelayout_css_append']) ? print ' class="'.$Result['pagelayout_css_append'].'" ' : ''?>>

<div id="widget-layout" class="row">
	<div class="columns large-12">
       <?php echo $Result['content']; ?>
     </div>
</div>

<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/app.js');?>"></script>

<?php if (isset($Result['dynamic_height'])) : ?>
<script>
var heightContent = 0;
var heightElement = $('#widget-layout');
setInterval(function(){
	var currentHeight = heightElement.height();
	if (heightContent != currentHeight){
		heightContent = currentHeight;
		parent.postMessage('<?php echo $Result['dynamic_height_message']?>:'+(parseInt(heightContent)+<?php (isset($Result['dynamic_height_append'])) ? print $Result['dynamic_height_append'] : print 20?>), '*');
	};
},200);
</script>
<?php endif;?>

</body>
</html>