<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_user.tpl.php'));?>
</head>
<body>

<div class="content-row">

<div class="row">
    <div class="columns small-7">
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo.tpl.php'));?>
    </div>
    
    <div class="columns small-5 pt20">
    	<ul class="button-group radius right">
    	  <?php if (!isset($Result['hide_close_window'])) : ?>
		  	<li><a class="secondary tiny button" onclick="lhinst.userclosedchatandbrowser();" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>"><i class="icon-cancel"></i></a></li>
		  <?php endif;?>		  
		  <?php if (isset($Result['show_switch_language'])) : ?>
		  <li>
		    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>			
		  </li>
		  <?php endif; ?>
		</ul>
	
	
	</div>	
</div>

<div class="row">
    <div class="columns twelve">
    <?php echo $Result['content']; ?>
    </div>
</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_user.tpl.php'));?>
</div>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>