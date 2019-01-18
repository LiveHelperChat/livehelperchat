<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
    <label><input type="checkbox" value="on" name="active" <?php if ($item->active == 1) : ?>checked="checked"<?php endif;?> value="<?php echo htmlspecialchars($item->active);?>" /> Active</label>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Priority');?></label>
    <input type="text" class="form-control" name="priority" value="<?php echo htmlspecialchars($item->priority);?>" />
</div>

<h4>Extensions defined exceptions you can translate</h4>
<?php foreach ($exceptions as $exception) : ?>
    <input type="hidden" name="code[]" value="<?php echo htmlspecialchars($exception->code)?>">
    <div class="row">
        <div class="col-6">Error code - &quot;<?php echo htmlspecialchars($exception->code)?>&quot;</div>
        <div class="col-6">Default message - &quot;<?php echo htmlspecialchars($exception->default_message)?>&quot;</div>
        <div class="col-12">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Custom message');?></label>
                <textarea class="form-control" name="message[]"><?php echo htmlspecialchars($exception->message)?></textarea>
            </div>
        </div>
    </div>
<?php endforeach; ?>
