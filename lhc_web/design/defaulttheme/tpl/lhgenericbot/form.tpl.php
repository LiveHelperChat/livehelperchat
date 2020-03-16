
<ul class="nav nav-tabs mb-3" role="tablist">
    <li role="presentation" class="nav-item"><a href="#general" class="nav-link active" aria-controls="general" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','General');?></a></li>
    <li role="presentation" class="nav-item"><a href="#interface" class="nav-link" aria-controls="interface" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Interface');?></a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="general">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
            <input type="text" class="form-control" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Nick, what should be operator nick. E.g Support Bot');?></label>
            <input type="text" class="form-control" name="nick"  value="<?php echo htmlspecialchars($item->nick);?>" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Photo');?>, (jpg,png)</label>
            <input type="file" name="UserPhoto" value="" />
        </div>

        <?php if ($item->has_photo) : ?>
            <div class="form-group">
                <img src="<?php echo $item->photo_path?>" alt="" width="50" /><br />
                <label><input type="checkbox" name="DeletePhoto" value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
            </div>
        <?php endif;?>

        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/exceptions_groups_apply.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/applies_logic_to.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_1_container.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_2_container.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_3_container.tpl.php'));?>

    </div>
    <div role="tabpanel" class="tab-pane" id="interface">
        <div class="form-group">
            <label><input type="checkbox" <?php (isset($item->configuration_array['profile_hide']) && $item->configuration_array['profile_hide'] == true) ? print 'checked="checked"' : ''?> name="profile_hide" value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Hide bot profile while chat is in bot mode')?></label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="msg_hide" value="1" <?php (isset($item->configuration_array['msg_hide']) && $item->configuration_array['msg_hide'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Start chat with hidden message box')?></label>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/bot_interface_multiinclude.tpl.php'));?>

    </div>
</div>

