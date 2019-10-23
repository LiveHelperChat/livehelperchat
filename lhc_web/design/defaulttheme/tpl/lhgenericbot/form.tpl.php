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

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Exception groups to apply');?></label>
    <div class="row">
        <?php
        echo erLhcoreClassRenderHelper::renderCheckbox(array(
            'list_function' => 'erLhcoreClassModelGenericBotException::getList',
            'selected_id' => (isset($item->configuration_array['exc_group_id']) ? $item->configuration_array['exc_group_id'] : array()),
            'input_name' => 'exc_group_id[]',
            'wrap_prepend' => '<div class="col-4">',
            'wrap_append' => '</div>'
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These bot logic applies also');?></label>
    <div class="row">
        <?php echo erLhcoreClassRenderHelper::renderCheckbox(array(
            'list_function' => 'erLhcoreClassModelGenericBotBot::getList',
            'selected_id' => (isset($item->configuration_array['bot_id']) ? $item->configuration_array['bot_id'] : array()),
            'input_name' => 'bot_id[]',
            'wrap_prepend' => '<div class="col-6 fs12">',
            'wrap_append' => '</div>'
        ));
        ?>
    </div>
</div>


<div class="form-group">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_str_1.tpl.php'));?>
    <input type="text" class="form-control" name="attr_str_1"  value="<?php echo htmlspecialchars($item->attr_str_1);?>" />
</div>

<div class="form-group">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_str_2.tpl.php'));?>
    <input type="text" class="form-control" name="attr_str_2"  value="<?php echo htmlspecialchars($item->attr_str_2);?>" />
</div>

<div class="form-group">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_str_3.tpl.php'));?>
    <input type="text" class="form-control" name="attr_str_3"  value="<?php echo htmlspecialchars($item->attr_str_3);?>" />
</div>