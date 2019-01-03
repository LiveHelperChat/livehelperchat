<script>
function confirmSave(){
    if (parseInt($('#id_DepartmentID').val()) > 0 || confirm("<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','This change will be applied to all departments that use this canned message');?>")){
        return true;
    } else {
        return false;
    }
}
</script>