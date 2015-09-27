<?php 
/**
 * Resubmit form if error was because of captcha and it's first time error
 * */
if (isset($errors['captcha']) && !isset($_POST['ResubmitCaptcha'])) : ?>
<input type="hidden" name="ResubmitCaptcha" value="true" />
<script>
$( document ).ready(function() {
	<?php if (!(isset($start_data_fields['message_auto_start']) && $start_data_fields['message_auto_start'] == true)) : ?>	
        $('#<?php echo $formResubmitId?>').submit();
    <?php endif;?>    
});
</script>
<?php endif;?>