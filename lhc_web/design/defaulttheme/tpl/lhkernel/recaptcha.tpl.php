<?php $recaptchaData = erLhcoreClassModelChatConfig::fetch('recaptcha_data')->data_value; ?>

<?php if (is_array($recaptchaData) && isset($recaptchaData['enabled']) && $recaptchaData['enabled'] == 1) : ?>
<input type="hidden" name="g-recaptcha" id="recaptcha-content" value="">

<script src='https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($recaptchaData['site_key'])?>'></script>

<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo htmlspecialchars($recaptchaData['site_key'])?>', {action: 'login_action'})
            .then(function(token) {
                $('#recaptcha-content').val(token);
            });
    });
</script>
<?php endif; ?>