<script>
function confirmSave(){
    if (parseInt($('input[name=DepartmentID\\[\\]]:checked').length) > 0 || confirm("<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','This change will be applied to all departments that use this canned message');?>")){
        return true;
    } else {
        return false;
    }
}
</script>