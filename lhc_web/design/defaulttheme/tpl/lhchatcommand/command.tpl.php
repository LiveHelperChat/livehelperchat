<?php $modalHeaderTitle = htmlspecialchars($command->name);$modalSize = 'xs';$modalHeaderClass = 'p-1';?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chatcommand/command')?>/<?php echo $chat->id?>/<?php echo $command->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

    <?php if (isset($commandExecution)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Processed!');$hideSuccessButton = true; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div data-alert class="alert alert-danger alert-dismissible fade show p-2 ps-4" id="whisper-command-warning" style="display: none">
        <ul class="ps-1 m-0">
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','You are in whisper mode! To execute commands properly switch to write mode.')?></li>
        </ul>
    </div>

    <?php if (isset($errors)) : $hideErrorButton = true;?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

    <?php foreach ($command->fields_array as $fieldIndex => $field) : ?>
    <div class="form-group">
        <label><?php echo htmlspecialchars($field['name'])?><?php if ((isset($field['required']) && $field['required'] == 'required') || !isset($field['required'])) : ?>*<?php endif;?></label>
        <?php if ($field['type'] == 'textarea') : ?>
            <textarea class="form-control form-control-sm<?php if (isset($errors['field_'.$fieldIndex])) : ?> is-invalid<?php endif;?>" name="field_<?php echo $fieldIndex?>" placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? '')?>" rows="<?php echo isset($field['rows']) && (int)$field['rows'] > 0 ? (int)$field['rows'] : 2?>"><?php if (isset($commandArguments['field_' . $fieldIndex])) : ?><?php echo htmlspecialchars($commandArguments['field_' . $fieldIndex])?><?php endif;?></textarea>
        <?php else : ?>
            <input type="text" name="field_<?php echo $fieldIndex?>" placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? '')?>" class="form-control form-control-sm<?php if (isset($errors['field_'.$fieldIndex])) : ?> is-invalid<?php endif;?>" value="<?php if (isset($commandArguments['field_' . $fieldIndex])) : ?><?php echo htmlspecialchars($commandArguments['field_' . $fieldIndex])?><?php endif;?>" />
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if (isset($commandExecution)) : ?>
    <script>
        lhinst.addmsgadmin(<?php echo $chat->id?>,<?php echo json_encode($commandExecution)?>);
        setTimeout(function(){$('#myModal').modal('hide');},2000);
    </script>
    <?php endif;?>

    <div class="d-flex align-items-center justify-content-center">
        <button type="submit" id="chat-command-button" name="ProcessCommand" <?php if (isset($commandExecution)) : ?>disabled="disabled"<?php endif;?> class="btn btn-primary btn-sm modal-submit-disable"><i class="material-icons">done</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Accept')?></button>
    </div>

    <input type="hidden" id="chat-command-id" value="<?php echo $chat->id?>" />

</form>

<script>
    function checkIsWhisper(){
        if ($('#CSChatMessage-<?php echo $chat->id?>').attr("whisper") == 1) {
            $('#whisper-command-warning').show();
            $('#chat-command-button').prop("disabled","disabled");
        } else {
            $('#whisper-command-warning').hide();
            $('#chat-command-button').removeAttr("disabled");
        }setTimeout(function(){
            if ($('#myModal.show').length > 0){
                checkIsWhisper();
            }
        },1500);
    }
    checkIsWhisper();
</script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>