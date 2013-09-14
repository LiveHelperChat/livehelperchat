<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_user.tpl.php'));?>
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
if (!!window.postMessage) {
	var heightContent = 0;
	var heightElement = $('#widget-layout');
	setInterval(function(){
		var currentHeight = heightElement.height();
		if (heightContent != currentHeight){
			heightContent = currentHeight;
			try {
				parent.postMessage('<?php echo $Result['dynamic_height_message']?>:'+(parseInt(heightContent)+<?php (isset($Result['dynamic_height_append'])) ? print $Result['dynamic_height_append'] : print 20?>), '*');
			} catch(e) {

			};
		};
	},200);
	<?php if (isset($Result['chat']) && is_numeric($Result['chat']->id)) : ?>
	parent.postMessage("lhc_ch:hash:<?php echo $Result['chat']->id,'_',$Result['chat']->hash?>", '*');
	parent.postMessage("lhc_ch:hash_resume:<?php echo $Result['chat']->id,'_',$Result['chat']->hash?>", '*');
	<?php endif; ?>
	<?php if (isset($Result['additional_post_message'])) : ?>
	parent.postMessage("<?php echo $Result['additional_post_message']?>", '*');
	<?php endif;?>
};
</script>
<?php endif;?>

</body>
</html>