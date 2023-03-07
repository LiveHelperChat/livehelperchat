<label class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Read only')?>"><span class="material-icons">edit_off</span><input <?php if (($userDep instanceof erLhcoreClassModelUserDep && $userDep->ro == 1) || ($userDep instanceof erLhcoreClassModelDepartamentGroupUser && $userDep->read_only == 1)) : ?>checked="checked"<?php endif; ?> type="radio" name="ro" value="1">&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign in read only mode')?></label>
<label class="pl-4"><span class="material-icons">mode_edit</span><input type="radio" <?php if (($userDep instanceof erLhcoreClassModelUserDep && $userDep->ro == 0) || ($userDep instanceof erLhcoreClassModelDepartamentGroupUser && $userDep->read_only == 0)) : ?>checked="checked"<?php endif; ?> name="ro" value="0">&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assign as operator')?></label>

<div class="pt-3">
<label><span class="material-icons">assignment_ind</span><input type="checkbox" name="exc_indv_autoasign" value="on" <?php echo $userDep->exc_indv_autoasign == 1 ? 'checked="checked"' : '';?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assignment workflow')?></label>
</div>

<label class="d-block fs13 text-muted pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assignment priority, optional, default - 0')?></label>
<input type="text" class="form-control form-control-sm" name="assign_priority" value="<?php echo $userDep->assign_priority?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Agents with higher assignment priority will be assigned first to chat')?>" />

<div class="row pb-2">
    <div class="col-12 fs13 text-muted pb-1">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Min and Max chat priority for chat being assigned by my assign priority')?></label>
    </div>
    <div class="col-6">
        <input name="chat_min_priority" value="<?php echo $userDep->chat_min_priority?>" type="text" class="form-control form-control-sm" />
    </div>
    <div class="col-6">
        <input name="chat_max_priority" value="<?php echo $userDep->chat_max_priority?>" type="text" class="form-control form-control-sm" />
    </div>
</div>