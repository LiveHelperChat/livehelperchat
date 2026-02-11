<?php $recaptchaData = erLhcoreClassUserValidator::getCaptchaSettings(); ?>
<?php $captchaAction = isset($captchaAction) ? $captchaAction : 'login_action'; ?>

<?php if ((int)$recaptchaData['enabled'] === 1 && $recaptchaData['provider'] === 'google') : ?>
    <input type="hidden" name="g-recaptcha" id="recaptcha-content" value="">

    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($recaptchaData['site_key'])?>"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo htmlspecialchars($recaptchaData['site_key'])?>', {action: '<?php echo htmlspecialchars($captchaAction)?>'})
                .then(function(token) {
                    $('#recaptcha-content').val(token);
                });
        });
    </script>
<?php elseif ((int)$recaptchaData['enabled'] === 1 && $recaptchaData['provider'] === 'turnstile') : ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <div class="cf-turnstile mt-2"
         data-sitekey="<?php echo htmlspecialchars($recaptchaData['turnstile_site_key'])?>"
         data-action="<?php echo htmlspecialchars($captchaAction)?>">
    </div>
<?php endif; ?>
