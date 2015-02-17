<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_user.tpl.php'));?>
</head>
<body>


<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
		
		   <div class="btn-group pull-right" role="group" aria-label="...">
                <?php if (!isset($Result['hide_close_window'])) : ?>
        		  	<a class="btn btn-default btn-xs" onclick="lhinst.userclosedchatandbrowser();" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>"><i class="icon-cancel"></i></a>
        		<?php endif;?>	
        		  
        		  <?php if (isset($Result['show_switch_language'])) : ?>		  
        		    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
        		  <?php endif; ?>
            </div>
            
			<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo.tpl.php'));?>
		</div>
		<div class="modal-body">      
                <?php echo $Result['content'];?>  
        </div>
	</div>
</div>


<div class="container-fluid">
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_user.tpl.php'));?>
</div>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>