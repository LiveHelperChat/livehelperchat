<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Token');?></label>
    <input type="text" disabled maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->token)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Device');?></label>
    <input type="text" disabled maxlength="250" class="form-control form-control-sm" name="device_type" value="<?php echo htmlspecialchars($item->device_type)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Device');?></label>
    <input type="text" disabled maxlength="250" class="form-control form-control-sm" name="device_token" value="<?php echo htmlspecialchars($item->device_token)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','User ID');?></label>
    <input type="text" disabled maxlength="250" class="form-control form-control-sm" name="app_secret" value="<?php echo htmlspecialchars($item->user_id)?>" />
</div>

<div class="form-group">
    <label><input type="checkbox" value="on" <?php if ($item->notifications_status == 1) : ?>checked="checked"<?php endif; ?> name="notifications_status"> Notifications enabled</label>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Status');?></label>
    <i class="material-icons <?php if ($item->error == 0) : ?>chat-active<?php else : ?>chat-closed<?php endif;?>"><?php if ($item->error == 0) : ?>thumb_up_alt<?php else : ?>thumb_down_alt<?php endif; ?></i>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Last error');?></label>
    <p><small><?php echo htmlspecialchars($item->last_error)?></small></p>
</div>