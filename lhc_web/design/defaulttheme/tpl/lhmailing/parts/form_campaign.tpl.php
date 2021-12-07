<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?>*</label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="enabled" value="on" <?php $item->enabled == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Enabled');?></label>
            <div><small><i>Only once the campaign is enabled we will start sending e-mails. Progress you can see in statistic tab.</i></small></div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?>*</label>
            <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
    </div>
</div>



<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail body');?>*</label>
    <textarea class="form-control"></textarea>
</div>

