<?php if ($context == 'fieldlabel') : ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'You can define multilanguage label for your custom fields. Default should be always defined.');?></p>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'JSON based options.');?></h5>

<textarea class="form-control form-control-sm mb-3" rows="3"><?php echo '{"default":"Surname","lt":"Pavardė"}';?></textarea>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Possible languages');?></h5>

<?php foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_site_access') as $siteAccess) : 
        $siteAccessOptions = erConfigClassLhConfig::getInstance()->getSetting('site_access_options', $siteAccess);
        if (isset($siteAccessOptions['content_language'])) : ?>
    <span class="badge bg-success"><?php echo htmlspecialchars($siteAccessOptions['content_language']); ?></span>
    <?php endif; endforeach; ?>

<?php endif; ?>