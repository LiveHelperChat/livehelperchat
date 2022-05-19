<select class="form-control form-control-sm" name="expires" id="block-expires">
    <option value="3">3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days (cool off)')?></option>
    <option value="15">15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="30">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="60">60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="90">90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="120">120 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="240">240 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="360">360 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
    <option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Permanent/unlimited')?></option>
</select>