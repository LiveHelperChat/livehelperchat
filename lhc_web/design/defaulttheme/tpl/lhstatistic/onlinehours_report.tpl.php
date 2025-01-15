<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Report');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div class="modal-body">
        <?php $ratio = ((isset($filter['filterlte']['time']) ? $filter['filterlte']['time'] : time()) - $filter['filtergte']['time']); ?>

        <?php
        $groupedData = [];
        foreach ($items as $session) {
            $groupedData[$session->user_id][] = $session;
        }
        ?>
      <table class="table table-sm">
         <tr><th nowrap="">User</th></tr>
            <?php foreach ($groupedData as $userId => $durations)  : ?>
            <tr>
                <td title="<?php echo htmlspecialchars($userId)?>"><?php echo htmlspecialchars($durations[0]->user_name)?></td>
            </tr>
           <tr>
                <td style="white-space: nowrap">
                    <?php $lastStartTime = $filter['filtergte']['time']; foreach ($durations as $duration) : if ($duration->duration == 0) {continue;};if ($lastStartTime < $duration->time) : ?><div class="d-inline-block bg-danger border-end text-truncate text-white ps-1" title="<?php echo date('Ymd') == date('Ymd',$lastStartTime) ? date(erLhcoreClassModule::$dateHourFormat,$lastStartTime) : date(erLhcoreClassModule::$dateDateHourFormat,$lastStartTime);?> - <?php echo $duration->time_front;?>" style="width: <?php echo round((($duration->time - $lastStartTime) / $ratio)*100,2)?>%;white-space: nowrap"><?php echo erLhcoreClassChat::formatSeconds($duration->time - $lastStartTime);?> | <?php echo date('Ymd') == date('Ymd',$lastStartTime) ? date(erLhcoreClassModule::$dateHourFormat,$lastStartTime) : date(erLhcoreClassModule::$dateDateHourFormat,$lastStartTime);?></div><?php endif; ?><div class="d-inline-block bg-success border-right text-truncate text-white ps-1" title="<?php echo $duration->time_front;?> - <?php echo $duration->lactivity_front;?>, <?php echo $duration->duration_front;?>" style="width: <?php echo round(( (isset($filter['filterlte']['time']) && $duration->lactivity > $filter['filterlte']['time'] ? ($filter['filterlte']['time'] - $duration->time) : $duration->duration) / $ratio)*100,2)?>%;white-space: nowrap"><?php echo $duration->duration_front;?> | <?php echo $duration->time_front;?><?php $lastStartTime = $duration->lactivity;?></div><?php endforeach; ?>
                    <?php if ($duration->lactivity < time() - 60 && (!isset($filter['filterlte']['time']) || $filter['filterlte']['time'] > $duration->lactivity)) : ?>
                        <div class="d-inline-block bg-danger border-end text-truncate text-white ps-1" style="width: <?php echo round((((isset($filter['filterlte']['time']) ? $filter['filterlte']['time'] : time()) - $duration->lactivity) / $ratio)*100,2);?>%"><span class="material-icons">flash_off</span></div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
      </table>

    </div>
    <input type="hidden" name="export_action" value="doExport">
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
    </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>