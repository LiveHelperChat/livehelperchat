<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" ng-app lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_user.tpl.php'));?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/jquery-ui-1.10.4.custom.css');?>" />
<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/jquery-ui-1.10.4.custom.min.js');?>"></script>
</head>
<body>

<div class="container-fluid">

<div class="row">
    <div class="col-xs-10">
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo.tpl.php'));?>
    </div>
    <?php if (!isset($Result['hide_close_window'])) : ?>
    <div class="col-xs-2 pt20">
		<input type="button" class="btn btn-defaut pull-right closebutton" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatandbrowser();" />
	</div>
	<?php endif;?>
</div>

<div class="row">
    <div class="col-xs-12">
    <?php echo $Result['content']; ?>
    </div>
</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_user.tpl.php'));?>
</div>

<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/angular.min.js');?>"></script>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>
