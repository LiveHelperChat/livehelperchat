<div>
    <?php if (isset($filter_search['filtergte'][$dateFilterAttr]) || isset($filter_search['filterlte'][$dateFilterAttr])) : ?>
        <span class="material-icons text-muted ">schedule</span>
    <?php endif; ?>

    <?php if (isset($filter_search['filtergte'][$dateFilterAttr])) : ?>
        <span class="text-muted fs12 pe-1">From - <?php echo date('Y-m-d H:i:s',$filter_search['filtergte'][$dateFilterAttr]);?></span>
    <?php endif; ?>

    <?php if (isset($filter_search['filterlte'][$dateFilterAttr])) : ?>
        <span class="text-muted fs12">To - <?php echo date('Y-m-d H:i:s',$filter_search['filterlte'][$dateFilterAttr]);?></span>
    <?php endif; ?>
</div>