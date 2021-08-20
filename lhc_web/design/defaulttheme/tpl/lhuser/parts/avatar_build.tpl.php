<div class="row">
    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false)) : ?>
    <div class="col-9">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
        <span class="input-group-text action-image" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/avatarbuilder/'+$('#<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>id_avatar_string').val() + '?prefix=<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>'})">
            <span class="material-icons mr-0 btn-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Avatar builder');?>">palette</span>
        </span>
            </div>
            <input maxlength="100" name="<?php isset($avatarOptions['field_name']) ? print $avatarOptions['field_name'] : print 'avatar'?>" onkeyup="$('#<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>avatar_string_img').attr('src',WWW_DIR_JAVASCRIPT + 'widgetrestapi/avatar/' + $(this).val())" id="<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>id_avatar_string" type="text" class="form-control" value="<?php echo htmlspecialchars($avatarOptions['avatar'])?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Enter any string to generate an avatar');?>"/>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-3">
        <img width="70" height="70" id="<?php isset($avatarOptions['field_prefix']) ? print $avatarOptions['field_prefix'] : ''?>avatar_string_img" src="<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($avatarOptions['avatar'])?>" alt="" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Click to set avatar');?>" />
    </div>
</div>



