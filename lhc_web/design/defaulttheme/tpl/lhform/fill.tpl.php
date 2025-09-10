<?php if (!isset($embed_mode)) : ?>
<h1><?php echo htmlspecialchars($form->name)?></h1>
<?php endif; ?>

<?php if (erLhcoreClassFormRenderer::isCollected()) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','Information collected'); $hideSuccessButton = true; ?>

    <?php if (!isset($form->configuration_array['hide_content_on_success']) || $form->configuration_array['hide_content_on_success'] == false) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <?php if (isset($replace_array)) : ?>
        <?php echo str_replace(array_keys($replace_array), array_values($replace_array), $form->post_content);?>
    <?php else : ?>
        <?php echo $form->post_content?>
    <?php endif; ?>

    <?php if ((!isset($form->configuration_array['hide_content_on_success']) || $form->configuration_array['hide_content_on_success'] == false) && strpos($form->post_content,'name="ReturnButton"') === false) : ?>
        <a class="btn btn-secondary btn-sm" name="ReturnButton" href="<?php if (isset($action_url)) : ?><?php echo $action_url?><?php else : ?><?php echo erLhcoreClassDesign::baseurl('form/fill')?><?php endif;?>/<?php echo $form->id?>?new"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','Return');?></a>
    <?php endif; ?>

<?php else : ?>

<?php $errors = erLhcoreClassFormRenderer::getErrors();
if (!empty($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" class="form-submit-<?php echo $form->id?>" enctype="multipart/form-data" action="<?php if (isset($action_url)) : ?><?php echo $action_url?><?php else : ?><?php echo erLhcoreClassDesign::baseurl('form/fill')?><?php endif;?>/<?php echo $form->id?>">
	<?php echo $content?>

    <?php if (isset($jsVars)) : foreach ($jsVars as $index => $item) : ?>
        <input type="hidden" name="jsvar[<?php echo $index?>]" value="<?php echo htmlspecialchars($item)?>" />
    <?php endforeach;endif;?>

    <?php if (isset($custom_fields)) : ?>
    <input type="hidden" name="custom_fields" value="<?php echo htmlspecialchars(json_encode($custom_fields))?>">
    <?php endif; ?>

    <?php if (isset($chat_id)) : ?>
    <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chat_id)?>">
    <?php endif; ?>

    <?php if (isset($hash)) : ?>
    <input type="hidden" name="hash" value="<?php echo htmlspecialchars($hash)?>">
    <?php endif; ?>

    <?php if (isset($msg_id)) : ?>
    <input type="hidden" name="msg_id" value="<?php echo htmlspecialchars($msg_id)?>">
    <?php endif; ?>

    <?php if (strpos($content,'name="SubmitForm"') === false) : ?>
        <div>
            <button type="submit" class="btn btn-secondary btn-sm" name="SubmitForm">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','Submit');?>
            </button>
        </div>
    <?php endif; ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-submit-<?php echo $form->id?>');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Find all submit buttons in the form
            const submitButtons = form.querySelectorAll('button[type="submit"]');
            
            submitButtons.forEach(function(button) {
                // Store original innerHTML
                const originalInnerHTML = button.innerHTML;
                const originalText = button.textContent;
                
                // Disable the button
                button.disabled = true;
                
                // Add spinner while keeping original text
                const spinner = document.createElement('span');
                spinner.className = 'spinner-border spinner-border-sm me-2';
                spinner.setAttribute('role', 'status');
                spinner.setAttribute('aria-hidden', 'true');
                
                button.innerHTML = spinner.outerHTML + originalText;
            });
        });
    }
});
</script>

<?php endif; ?>