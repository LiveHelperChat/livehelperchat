<?php foreach (erLhcoreClassModelMailconvResponseTemplateSubject::getList(array('filter' => array('template_id' => $item->id))) as $subject) : ?>
    <button type="button" class="btn btn-xs btn-outline-info"><?php echo htmlspecialchars($subject->subject)?></button>
<?php endforeach; ?>
