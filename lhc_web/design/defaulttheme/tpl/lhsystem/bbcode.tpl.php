<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/bbcodeconfiguration', 'BBCode configuration')?></h1>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="row pb-4">
    <?php foreach (['[b]','[i]','[u]','[listol]','[list]','[paypal]','[level]','[li]','[fs12]','[quote]','[mark]','[s]','[link_trigger]','[fs12]','[color]','[file]','[url]','[img]','[youtube]','[fupload]','[baseurl]','[survey]','[button_action]','[loc]'] as $bbcode) : ?>
        <div class="col-6">
            <h5 class="pt-2"><?php echo htmlspecialchars($bbcode)?></h5>
            <label><input type="checkbox" <?php if (in_array($bbcode,$bbcode_options['div'])) : ?>checked="checked"<?php endif; ?> value="<?php echo htmlspecialchars($bbcode)?>" name="enabled_visitor[]" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/bbcodeconfiguration', 'Disable for visitor messages')?></label><br>
            <label><input type="checkbox" <?php if (in_array($bbcode,$bbcode_options['dio'])) : ?>checked="checked"<?php endif; ?> value="<?php echo htmlspecialchars($bbcode)?>" name="enabled_operator[]" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/bbcodeconfiguration', 'Disable for operator messages')?></label>
        </div>
    <?php endforeach; ?>
    </div>

    <input type="submit" class="btn btn-sm btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
