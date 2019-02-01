<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Nick, what should be operator nick. E.g Support Bot');?></label>
    <input type="text" class="form-control" name="Nick"  value="<?php echo htmlspecialchars($item->nick);?>" />
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