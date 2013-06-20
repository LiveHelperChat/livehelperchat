<div>
<div data-alert class="alert-box alert"><a href="#" class="close">×</a>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/errors/adminchatnopermission','You do not have permission to access the current chat!')?>
</div>

<?php if (isset($show_close_button) && $show_close_button == true) : ?>
<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>" class="tiny button round alert" onclick="lhinst.removeActiveDialogTag($('#tabs'))">
<?php endif;?>
</div>