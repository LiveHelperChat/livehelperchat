<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/geoadjustment.tpl.php'));?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/geoadjustment')?>" method="post" autocomplete="off">
    <div class="form-group">
	   <label><input type="checkbox" ng-model="use_geo_adjustment" ng-init="use_geo_adjustment = <?php isset($geo_data['use_geo_adjustment']) && ($geo_data['use_geo_adjustment'] == '1') ? print 'true' : 'false' ?>" name="use_geo_adjustment" value="1" <?php isset($geo_data['use_geo_adjustment']) && ($geo_data['use_geo_adjustment'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Geo adjustments active'); ?></label>
    </div>
    
	<div ng-show="use_geo_adjustment" ng-init="custom_other = '<?php isset($geo_data['other_countries']) ? print $geo_data['other_countries'] : print 'all' ?>'">
		
		<label><input type="checkbox" name="ApplyWidget" value="on" <?php isset($geo_data['apply_widget']) && ($geo_data['apply_widget'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','Apply to chat widget status indicator these rules also? performance decrease is associated with this option')?></label>
				
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','Make chat status normal for these countries, countries can be separated by comma "de,gb,us,fr" means chat would be shown as usual only for these countries.')?></label>
		<input class="form-control" type="text" name="AvailableFor" value="<?php echo htmlspecialchars(isset($geo_data['available_for']) ? $geo_data['available_for'] : '')?>" placeholder="de,gb,us,fr" />
		
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','For')?></h4>
		
		<label><input type="radio" ng-model="custom_other" name="OtherCountries" value="all" checked="checked"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','all')?></label>
		<label><input type="radio" ng-model="custom_other" name="OtherCountries" value="custom"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','custom')?></label>
			
		<input class="form-control" type="text" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','E.g. ar,pl')?>" ng-show="custom_other == 'custom'" name="HideFor" value="<?php echo htmlspecialchars(isset($geo_data['hide_for']) ? $geo_data['hide_for'] : '')?>" placeholder="ar,pl" />
		
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','Other countries, put widget/chat status to')?></h4>
		
		<label><input type="radio" name="OtherStatus" value="offline" <?php ((isset($geo_data['other_status']) && ($geo_data['other_status'] == 'offline')) || !isset($geo_data['other_status'])) ? print 'checked="checked" ' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','offline status')?></label>
		<label><input type="radio" name="OtherStatus" value="hidden" <?php (isset($geo_data['other_status']) && ($geo_data['other_status'] == 'hidden')) ? print 'checked="checked" ' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','hidden/disabled, widget will not be shown')?></label>
		
		<div ng-show="custom_other == 'custom'">
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','For unmatched countries put chat status to')?></h4>
			<label><input type="radio" name="RestStatus" value="offline" <?php ((isset($geo_data['rest_status']) && ($geo_data['rest_status'] == 'offline')) || !isset($geo_data['rest_status'])) ? print 'checked="checked" ' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','offline status')?></label>
			<label><input type="radio" name="RestStatus" value="hidden" <?php (isset($geo_data['rest_status']) && ($geo_data['rest_status'] == 'hidden')) ? print 'checked="checked" ' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','hidden/disabled, widget will not be shown')?></label>
			<label><input type="radio" name="RestStatus" value="normal" <?php (isset($geo_data['rest_status']) && ($geo_data['rest_status'] == 'normal')) ? print 'checked="checked" ' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','normal status')?></label>
		</div>
		
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>		
	</div>
	
	<input type="submit" class="btn btn-default" name="SaveGeoAdjustment" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?>" />
</form>