<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
</head>
<body>

<div class="content-row">
	<div class="row">
	    <div class="columns large-6">
	        <h1><a href="<?php echo erLhcoreClassDesign::baseurl()?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Home')?>"><img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' )?>" title="<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' )?>"></a></h1>
	    </div>
	</div>
	<div class="row">
		<div class="columns large-12">
		<?php echo $Result['content'];?>
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>
		</div>
	</div>
</div>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>

</html>