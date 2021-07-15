<?php foreach (erLhcoreClassModelCannedMsgSubject::getList(array('filter' => array('canned_id' => $canned->id))) as $subject) : ?>
    <button class="btn btn-xs btn-outline-info"><?php echo htmlspecialchars($subject->subject)?></button>
<?php endforeach; ?>
