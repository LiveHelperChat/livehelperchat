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

<div class="row pb-2">
    <div class="col-6">
        <label class="d-block fs13 text-muted pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Alias nick')?></label>
        <input type="text" class="form-control form-control-sm" maxlength="50" name="alias_nick" value="<?php echo $userDepAlias->nick?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Department alias')?>" />
    </div>
    <div class="col-4">
        <label class="d-block fs13 text-muted pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Avatar')?></label>
        <?php $avatarOptions = ['field_prefix' => 'avtatar_alias', 'field_name' => 'avataralias_dep', 'avatar' => $userDepAlias->avatar]; ?>
        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text " <?php /*action-image onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/avatarbuilder/'+$('#<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>id_avatar_string').val() + '?prefix=<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>'})"*/ ?> >
                <span class="material-icons me-0 btn-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Avatar builder');?>">palette</span>
            </span>
            <input maxlength="100" name="<?php isset($avatarOptions['field_name']) ? print $avatarOptions['field_name'] : print 'avatar'?>" onkeyup="$('#<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>avatar_string_img').attr('src',WWW_DIR_JAVASCRIPT + 'widgetrestapi/avatar/' + $(this).val())" id="<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>id_avatar_string" type="text" class="form-control" value="<?php echo htmlspecialchars($avatarOptions['avatar'])?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Enter any string to generate an avatar');?>"/>
        </div>
    </div>

    <div class="col-2">
        <img width="60" height="60" id="<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>avatar_string_img" src="<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($avatarOptions['avatar'])?>" alt="" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Click to set avatar');?>" />
    </div>

    <div class="col-12">
        <label class="d-block fs13 text-muted pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Photo alias')?> <input type="file" accept="image/png, image/jpeg" name="alias_photo" /></label>
        <?php if ($userDepAlias->has_photo) : ?>
            <div class="pt-1">
                <img src="<?php echo $userDepAlias->photo_path?>" alt="" width="50" /><br />
                <label><input type="checkbox" name="alias_photo_delete" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
            </div>
        <?php endif;?>

        <p class="text-muted fs13 fst-italic"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Photo has higher priority than avatar.')?></p>
    </div>

</div>



