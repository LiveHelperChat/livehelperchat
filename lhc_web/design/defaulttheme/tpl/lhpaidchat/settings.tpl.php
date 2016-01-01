<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('paidchat/settings','Paid chat settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('paidchat/settings')?>" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <label class="inline"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('paidchat/settings','Enabled'); ?> <input type="checkbox" name="PaidEnabled" value="1" <?php isset($paidchat_data['paidchat_enabled']) && ($paidchat_data['paidchat_enabled'] == '1') ? print 'checked="checked"' : '' ?> /></label>
    </div>

    <div class="form-group">
        <label class="inline"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('paidchat/settings','User can not access old chat if chat is closed'); ?> <input type="checkbox" name="ClosedReadDenied" value="1" <?php isset($paidchat_data['paidchat_read_denied']) && ($paidchat_data['paidchat_read_denied'] == '1') ? print 'checked="checked"' : '' ?> /></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('paidchat/settings','Secret hash, used for verification');?></label>
        <input class="form-control" type="text" name="SecretHash" value="<?php (isset($paidchat_data['paidchat_secret_hash']) && $paidchat_data['paidchat_secret_hash'] != '') ? print $paidchat_data['paidchat_secret_hash'] : print erLhcoreClassChat::generateHash() ?>" />
    </div>

    <input type="submit" class="btn btn-default" name="StoreChatboxSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>