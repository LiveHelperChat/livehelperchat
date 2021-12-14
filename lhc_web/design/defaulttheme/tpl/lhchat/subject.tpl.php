<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Set a subject')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div role="alert" class="alert alert-info alert-dismissible fade show">
        <div id="subject-message-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Choose a subject')?></div>
    </div>
    <?php
        $subjects = erLhAbstractModelSubjectDepartment::getList(array(
                'customfilter' => array('(dep_id = ' . (int)$chat->dep_id . ' OR dep_id = 0)'),
                'filter'  => ['`lh_abstract_subject`.`internal`' => 0],
                'sort' => '`lh_abstract_subject`.`name` ASC',
                'leftjoin' => array('lh_abstract_subject' => array('`lh_abstract_subject`.`id`','`lh_abstract_subject_dep`.`subject_id`'))
        ));
        $subjectsChat = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id)));
        $selectedSubjects = array();
        foreach ($subjectsChat as $subject) {
            $selectedSubjects[] = $subject->subject_id;
        }
    ?>
    <div class="row">
    <?php foreach($subjects as $subject) : ?>
        <div class="col-3"><label><input type="checkbox" onchange="lhinst.setSubject($(this),<?php echo $chat->id?>)" name="subject" value="<?php echo $subject->subject_id?>" <?php if (in_array($subject->subject_id,$selectedSubjects)) : ?>checked="checked"<?php endif?> >&nbsp;<?php echo htmlspecialchars($subject)?></label></div>
    <?php endforeach; ?>
    </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>