<div>
<div class="alert alert-danger">
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/errors/adminchatnopermission','You do not have permission to access the current chat!')?>
</div>

<?php if (isset($show_close_button) && $show_close_button == true) : ?>
<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>" class="btn btn-default" onclick="lhinst.removeDialogTab('<?php echo $chat->id ?>', $('#tabs'), true)">
<?php endif;?>
</div>
