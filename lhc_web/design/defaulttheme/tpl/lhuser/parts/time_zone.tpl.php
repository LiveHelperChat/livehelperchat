<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User time zone');?><?php if (isset($timeZoneSettings['force_choose']) && $timeZoneSettings['force_choose'] == true) : ?>*<?php endif;?></label>
    <?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
    <select <?php if (isset($can_edit_groups) && $can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> name="UserTimeZone" class="form-control">

        <?php if (isset($timeZoneSettings['force_choose']) && $timeZoneSettings['force_choose'] == true) : ?>
            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Choose');?></option>
            <option value="default" <?php $user->time_zone == 'default' ? print 'selected="selected"' : ''?>>[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Application default time zone');?> <?php echo date_default_timezone_get()?>]]</option>
        <?php else : ?>
            <option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Application default time zone');?> <?php echo date_default_timezone_get()?>]]</option>
        <?php endif; ?>

        <?php foreach ($tzlist as $zone) : ?>
            <option value="<?php echo htmlspecialchars($zone)?>" <?php $user->time_zone == $zone ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($zone)?></option>
        <?php endforeach;?>
    </select>
</div>