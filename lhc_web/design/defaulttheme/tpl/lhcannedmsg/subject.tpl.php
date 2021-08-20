<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Set a subject')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div role="alert" class="alert alert-info alert-dismissible fade show">
        <div id="subject-message-<?php echo $canned->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Choose a subject')?></div>
    </div>
<?php
$subjects = erLhAbstractModelSubject::getList(array('sort' => '`lh_abstract_subject`.`name` ASC'));
$subjectsChat = erLhcoreClassModelCannedMsgSubject::getList(array('filter' => array('canned_id' => $canned->id)));
$selectedSubjects = array();
foreach ($subjectsChat as $subject) {
    $selectedSubjects[] = $subject->subject_id;
}
?>
<div class="row">
    <?php foreach($subjects as $subject) : ?>
        <div class="col-3"><label><input type="checkbox" onchange="setCannedSubject($(this))" name="subject" value="<?php echo $subject->id?>" <?php if (in_array($subject->id,$selectedSubjects)) : ?>checked="checked"<?php endif?> > <?php echo htmlspecialchars($subject)?></label></div>
    <?php endforeach; ?>
</div>

<script>
    function setCannedSubject(inst){
        $('#subject-message-<?php echo $canned->id?>').text('...');
        $.postJSON(WWW_DIR_JAVASCRIPT + 'cannedmsg/subject/<?php echo $canned->id?>/(subject)/' + inst.val() + '/(status)/' + inst.is(':checked'),{'update': true}, function(data) {
            $.get(WWW_DIR_JAVASCRIPT + 'cannedmsg/subject/<?php echo $canned->id?>/?getsubjects=1', function(data) {
                $('#canned-message-subjects-<?php echo $canned->id?>').html(data);
            });
            $('#subject-message-<?php echo $canned->id?>').text(data.message);
        });
    }
</script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>