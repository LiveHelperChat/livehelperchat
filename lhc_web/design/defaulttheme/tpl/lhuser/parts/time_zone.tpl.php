<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User time zone');?></label>
    <?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
    <select name="UserTimeZone" class="form-control">
    		<option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Application default time zone');?>]]</option>
    	<?php foreach ($tzlist as $zone) : ?>
    		<option value="<?php echo htmlspecialchars($zone)?>" <?php $user->time_zone == $zone ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($zone)?></option>
    	<?php endforeach;?>
    </select>
</div>