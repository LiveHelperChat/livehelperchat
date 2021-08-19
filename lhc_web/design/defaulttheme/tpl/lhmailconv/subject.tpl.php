<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Set a subject')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div role="alert" class="alert alert-info alert-dismissible fade show">
        <div id="subject-message-<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Choose a subject')?></div>
    </div>
<?php
$subjects = erLhAbstractModelSubject::getList(array('sort' => '`lh_abstract_subject`.`name` ASC'));
$subjectsChat = erLhcoreClassModelMailconvResponseTemplateSubject::getList(array('filter' => array('template_id' => $item->id)));
$selectedSubjects = array();
foreach ($subjectsChat as $subject) {
    $selectedSubjects[] = $subject->subject_id;
}
?>
<div class="row">
    <?php foreach($subjects as $subject) : ?>
        <div class="col-3"><label><input type="checkbox" onchange="setTemplateSubject($(this))" name="subject" value="<?php echo $subject->id?>" <?php if (in_array($subject->id,$selectedSubjects)) : ?>checked="checked"<?php endif?> > <?php echo htmlspecialchars($subject)?></label></div>
    <?php endforeach; ?>
</div>

<script>
    function setTemplateSubject(inst){
        $('#subject-message-<?php echo $item->id?>').text('...');
        $.postJSON(WWW_DIR_JAVASCRIPT + 'mailconv/subject/<?php echo $item->id?>/(subject)/' + inst.val() + '/(status)/' + inst.is(':checked'),{'update': true}, function(data) {
            $.get(WWW_DIR_JAVASCRIPT + 'mailconv/subject/<?php echo $item->id?>/?getsubjects=1', function(data) {
                $('#response-template-subjects-<?php echo $item->id?>').html(data);
            });
            $('#subject-message-<?php echo $item->id?>').text(data.message);
        });
    }
</script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>