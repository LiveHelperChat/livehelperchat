<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title" id="myModalLabel">
				<span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Choose what widgets you want to see')?>
			</h4>
		</div>
		<div class="modal-body">

		<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Settings updated'); ?>
        	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
        	<script>
            setTimeout(function(){
            	location.reload();
            },250);
        	</script>
        <?php endif; ?>

		<form action="<?php echo erLhcoreClassDesign::baseurl('chat/dashboardwidgets')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
		
        <?php foreach ($widgets as $widget => $title) : ?>
            <div class="checkbox">
				<label><input type="checkbox" name="WidgetsUser[]" value="<?php echo $widget?>" <?php if (in_array($widget, $user_widgets)) : ?>checked="checked"<?php endif;?>><?php echo $title?></label>
			</div>
        <?php endforeach;?>

        <input type="submit" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">
        
        </form>
        
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>