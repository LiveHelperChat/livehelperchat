<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_user.tpl.php'));?>

<?php if (isset($Result['theme']) && $Result['theme']->custom_popup_css != '') : ?>
<style type="text/css">
<?php echo $Result['theme']->custom_popup_css?>
</style>
<?php endif;?>

</head>
<body>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/userchat/before_userchat.tpl.php'));?>
	<div class="modal-dialog modal-lg" id="user-popup-window">
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<div class="col-xs-6">
            		  <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo.tpl.php'));?>
            		</div>
					<div class="col-xs-6">
						<div class="btn-group pull-right" role="group" aria-label="...">
                        <?php if (!isset($Result['hide_close_window'])) : ?>

                            <?php if (isset($Result['chat']) && is_numeric($Result['chat']->id) && isset($Result['er']) && $Result['er'] == true) : ?>
                            <a class="btn btn-default btn-xs" onclick="lhinst.restoreWidget('<?php echo $Result['chat']->id,'_',$Result['chat']->hash?>');" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Switch to widget')?>"><i class="material-icons mr-0">open_in_browser</i></a>
                            <?php endif;?>

                            <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/close_popup.tpl.php'));?>
                		    <?php endif;?>

                		  <?php if (isset($Result['show_switch_language'])) : ?>
                		    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
                		  <?php endif; ?>
                        </div>
					</div>
				</div>
			</div>
			<div class="modal-body">
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/userchat/before_content.tpl.php'));?>
                    <?php echo $Result['content'];?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/userchat/after_content.tpl.php'));?>
            </div>
		</div>
	</div>

	<div class="container-fluid">
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_user.tpl.php'));?>
</div>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/userchat/after_userchat.tpl.php'));?>
<?php

if (erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output') == true) {
    $debug = ezcDebug::getInstance();
    echo $debug->generateOutput();
}
?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/dynamic_height.tpl.php'));?>
</body>
</html>