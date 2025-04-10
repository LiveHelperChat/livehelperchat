<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Chat ID');?></label>
    <input type="text" name="chat_id" value="<?php echo htmlspecialchars($input->chat_id)?>" class="form-control">
</div>
<h5>OR</h5>
<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Message');?></label>
    <textarea name="test_message" class="form-control form-control-sm"><?php echo htmlspecialchars($input->test_message)?></textarea>
</div>