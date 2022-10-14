<button id="generate-report-button" <?php if (!isset($_GET['doSearch']) && !($input->report > 0 && \LiveHelperChat\Models\Statistic\SavedReport::getCount(['filter' => ['id' => $input->report]]) == 1)) : ?>disabled="disabled" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Please click search first')?>"<?php endif; ?> type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/<?php echo htmlspecialchars($tabStatistic)?>/(export)/1<?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>'})" class="btn btn-outline-secondary btn-sm">
    <span class="material-icons">saved_search</span>
    <?php if ($input->report > 0 && \LiveHelperChat\Models\Statistic\SavedReport::getCount(['filter' => ['id' => $input->report]]) == 1) : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Update report')?>
    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save as report')?>
    <?php endif; ?>
</button>
<script>
    $(function() {
        $('#form-statistic-action input,#form-statistic-action select').change(function(){
            $('#generate-report-button').attr('disabled','disabled');
        });
    });
</script>