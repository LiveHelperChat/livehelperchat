<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Performance widget settings')?>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                
            </button>
        </div>
        <div class="modal-body">

            <?php $columnsForTemplate = isset($columnsForTemplate) && is_array($columnsForTemplate) ? $columnsForTemplate : array(); ?>

            <?php
                // Tooltip explanations for each metric identifier
                $columnTooltips = array(
                    'cr'   => 'Chats received: total number of chats started today in active, non-archived departments.',
                    'ca'   => 'Chats answered: chats received minus abandoned chats.',
                    'wt'   => 'Wait time: average time visitors waited before an operator accepted the chat. Only chats assigned to an operator and with a wait time under 10 minutes are included.',
                    'frt'  => 'First response time: average time from when an operator accepted the chat until they sent their first message. Only closed chats where the operator actually replied (first response time > 0) are included.',
                    'aart' => 'Average response time: average time operators took to reply to visitor messages throughout the conversation. Only closed chats where the operator sent at least one reply (response time > 0) are included.',
                    'tup'  => 'Thumbs up: number of chats today where the visitor left a positive rating.',
                    'tdown'=> 'Thumbs down: number of chats today where the visitor left a negative rating.',
                    'ton'  => 'Total online time: total time the operator was logged in and active today, summed across all their online sessions.',
                    'toff' => 'Total offline time: total time the operator was offline between sessions today. Gaps longer than 90 minutes are ignored (e.g. overnight). The last open gap is only counted if it is at least 30 seconds long.'
                );
            ?>

            <?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Settings updated'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
                <script>
                    setTimeout(function(){
                        location.reload();
                    },250);
                </script>
            <?php endif; ?>

            <form action="<?php echo erLhcoreClassDesign::baseurl('statistic/performancesettings')?>/<?php if (isset($scope)) : ?>op<?php endif;?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

                <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Performance options')?></h4>
            
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th width="70%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Column')?></th>
                                <th width="30%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Position')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($columnsForTemplate as $column) : ?>
                            <tr>
                                <td>
                                    <label class="mb-0">
                                        <input type="checkbox" class="form-check-input me-1" name="dep_performance_columns[]" value="<?php echo htmlspecialchars($column['identifier'])?>" <?php if ($column['enabled']) : ?>checked="checked"<?php endif; ?> />
                                            <?php echo $column['translation']?>
                                            <?php if (isset($columnTooltips[$column['identifier']])) : ?>
                                                &nbsp;<a class="live-help-tooltip" data-bs-placement="top" title="" data-bs-toggle="tooltip" data-bs-title="<?php echo htmlspecialchars($columnTooltips[$column['identifier']])?>"><i class="material-icons">info_outline</i></a>
                                            <?php endif; ?>
                                    </label>
                                </td>
                                <td>
                                    <input type="number" min="1" class="form-control form-control-sm" name="dep_performance_position[<?php echo htmlspecialchars($column['identifier'])?>]" value="<?php echo (int)$column['position']?>" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php
                    $updateIntervalLabels = \LiveHelperChat\Models\Statistic\PerformanceWidgets::UPDATE_INTERVAL_LABELS;
                    $updateInterval = isset($updateInterval) ? (int)$updateInterval : 600;
                    $validUpdateIntervals = isset($validUpdateIntervals) && is_array($validUpdateIntervals) ? $validUpdateIntervals : array_keys($updateIntervalLabels);
                ?>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Update frequency')?></label>
                            <select name="dep_performance_update_interval" class="form-select form-select-sm" style="width:auto">
                                <?php foreach ($validUpdateIntervals as $intervalValue) : ?>
                                    <option value="<?php echo (int)$intervalValue?>" <?php if ($updateInterval === (int)$intervalValue) : ?>selected="selected"<?php endif; ?>>
                                        <?php echo isset($updateIntervalLabels[$intervalValue]) ? $updateIntervalLabels[$intervalValue] : $intervalValue . ' s' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="dep_performance_wrap_headers" name="dep_performance_wrap_headers" value="1" <?php if (isset($wrapHeaders) && $wrapHeaders) : ?>checked="checked"<?php endif; ?> />
                            <label class="form-check-label" for="dep_performance_wrap_headers"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Wrap header column values')?></label>
                            <div class="form-text text-muted small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Allow header values to wrap onto multiple lines')?></div>
                        </div>
                    </div>
                </div>

                <input type="submit" class="btn btn-secondary btn-sm" name="updatePerformanceSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">

                <p class="float-end text-muted"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Choose which columns to display and set their position (lower number appears first).')?></small></p>
            </form>

<script>
	$(function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
	});
</script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>