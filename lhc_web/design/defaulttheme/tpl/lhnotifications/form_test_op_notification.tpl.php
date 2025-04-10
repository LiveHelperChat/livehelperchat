<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Chat ID');?></label>
    <input type="text" name="chat_id" value="<?php echo htmlspecialchars($input->chat_id)?>" class="form-control">
</div>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','OR');?></h5>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Message');?></label>
            <textarea name="test_message" class="form-control form-control-sm"><?php echo htmlspecialchars($input->test_message)?></textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','URL, optional.');?></label>
            <input type="text" name="url" value="<?php echo htmlspecialchars($input->url)?>" class="form-control">
        </div>
    </div>
</div>
