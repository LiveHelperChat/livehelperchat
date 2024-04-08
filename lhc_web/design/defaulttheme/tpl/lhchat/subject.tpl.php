<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Set a subject')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div role="alert" class="alert alert-info alert-dismissible m-0 mb-2 p-1 fade show">
        <div id="subject-message-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Choose a subject')?></div>
    </div>

    <?php
        $subjects = erLhAbstractModelSubjectDepartment::getList(array(
                'limit' => false,
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
        $hasPinned = false;
        foreach ($subjects as $subject) {
            if ($subject->subject->pinned == 1) {
                $hasPinned = true;
                break;
            }
        }
    ?>

    <?php if ($hasPinned) : ?>
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Pinned')?></h5>
    <div class="row">
        <?php foreach($subjects as $subject) : ?>
        <?php if ($subject->subject->pinned == 1) : ?>
            <div class="col-3"><label <?php if ($subject->subject->color != '') : ?> class="subject-custom"<?php endif;?> ><input type="checkbox" onchange="lhinst.setSubject($(this),<?php echo $chat->id?>)" name="subject" value="<?php echo $subject->subject_id?>" <?php if (in_array($subject->subject_id,$selectedSubjects)) : ?>checked="checked"<?php endif?> >&nbsp;<?php if ($subject->subject->color != '') : ?><span class="color" style="margin-top:-3px;background-color:#<?php echo $subject->subject->color?>"></span><?php endif;?><?php echo htmlspecialchars($subject)?></label></div>
        <?php endif; endforeach; ?>
    </div>
    <hr>
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Standard')?></h5>
    <?php endif; ?>
    <div class="row">
    <?php foreach($subjects as $subject) : if ($subject->subject->pinned == 0) :  ?>
        <div class="col-3"><label <?php if ($subject->subject->color != '') : ?> class="subject-custom"<?php endif;?> ><input type="checkbox" onchange="lhinst.setSubject($(this),<?php echo $chat->id?>)" name="subject" value="<?php echo $subject->subject_id?>" <?php if (in_array($subject->subject_id,$selectedSubjects)) : ?>checked="checked"<?php endif?> >&nbsp;<?php if ($subject->subject->color != '') : ?><span class="color" style="margin-top:-3px;background-color:#<?php echo $subject->subject->color?>"></span><?php endif;?><?php echo htmlspecialchars($subject)?></label></div>
    <?php endif; endforeach; ?>
    </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>