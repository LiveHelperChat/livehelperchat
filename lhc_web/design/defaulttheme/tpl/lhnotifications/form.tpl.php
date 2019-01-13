
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Device');?></label>
            <p><i class="material-icons" title="<?php echo htmlspecialchars($item->uagent)?>"><?php echo ($item->device_type == 0 ? 'computer' : ($item->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($item->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($item->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?></p>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','User Agent');?></label>
            <p><?php echo htmlspecialchars($item->uagent)?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Chat ID');?></label>
            <p><?php echo htmlspecialchars($item->chat_id)?></p>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Online Visitor ID');?></label>
            <p><?php echo htmlspecialchars($item->online_user_id)?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','IP');?></label>
            <p><?php echo htmlspecialchars($item->ip)?></p>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Last error');?></label>
            <p><?php echo htmlspecialchars($item->last_error)?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Created');?></label>
            <p><?php echo htmlspecialchars($item->ctime_front)?></p>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Updated');?></label>
            <p><?php echo htmlspecialchars($item->utime_front)?></p>
        </div>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/edit','Subscriber endpoint settings');?></label>
    <textarea readonly="readonly" class="form-control" rows="5"><?php echo htmlspecialchars($item->params)?></textarea>
</div>