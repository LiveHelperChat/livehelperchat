<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvsb','Set a subject')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div role="alert" class="alert alert-info alert-dismissible fade show">
        <div id="subject-message-<?php echo $message->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvsb','Choose a subject')?></div>
    </div>

<?php
$subjects = erLhAbstractModelSubjectDepartment::getList(array('customfilter' => array('(dep_id = ' . (int)$conv->dep_id . ' OR dep_id = 0)')));
$subjectsChat = erLhcoreClassModelMailconvMessageSubject::getList(array('filter' => array('message_id' => $message->id)));
$selectedSubjects = array();
foreach ($subjectsChat as $subject) {
    $selectedSubjects[] = $subject->subject_id;
}
?>
    <div class="row">
        <?php foreach($subjects as $subject) : ?>
            <div class="col-3"><label><input type="checkbox" onchange="setSubjectMailMessage($(this),<?php echo $message->id?>)" name="subject" value="<?php echo $subject->subject_id?>" <?php if (in_array($subject->subject_id,$selectedSubjects)) : ?>checked="checked"<?php endif?> ><?php echo htmlspecialchars($subject)?></label></div>
        <?php endforeach; ?>
    </div>

<script>
    function setSubjectMailMessage(inst, chat_id) {
        $('#subject-message-'+chat_id).text('...');
        $.postJSON(WWW_DIR_JAVASCRIPT + 'mailconv/apilabelmessage/'+chat_id + '/(subject)/' + inst.val() + '/(status)/' + inst.is(':checked'), {'update': true}, function(data) {
            $('#subject-message-'+chat_id).text(data.message);
        });
    }
</script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>