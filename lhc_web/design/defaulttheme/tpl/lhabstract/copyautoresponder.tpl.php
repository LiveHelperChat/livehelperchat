<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/copyautoresponder','Copied!'); ?>
<script>
setTimeout(function(){
    parent.location.reload();
},1000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>

<?php endif; ?>

<form action="" method="post">

    <?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
	    <label><input type="checkbox" name="CopyDepartments[]" value="<?php echo $departament['id']?>" />&nbsp; <?php echo htmlspecialchars($departament['name'])?></label><br>
	<?php endforeach; ?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-default" name="CopyAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/copyautoresponder','Copy');?>" />
    </div>

</form>