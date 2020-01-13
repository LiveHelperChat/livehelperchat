<div class="form-group">
    <label>Language</label>
    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($item->name)?>">
</div>

<div class="form-group">
    <label>Siteaccess</label>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label>
    <select name="siteaccess" class="form-control">
            <option value="">Choose language</option>
        <?php foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_site_access' ) as $locale ) : ?>
            <option value="<?php echo $locale?>" <?php $item->siteaccess === $locale ? print 'selected="selected"' : ''?> ><?php echo $locale?></option>
        <?php endforeach; ?>
    </select>
</div>