<?php foreach ((isset($qaEvaluationForms) ? $qaEvaluationForms : array()) as $qaFormId => $qaFormObject) : ?>
<?php
$qaSum = 0;
$qaCount = 0;
foreach ($agentStatistic as $agent) {
    $qaSum += $agent->{'qa_sum_' . $qaFormId} ?? 0;
    $qaCount += $agent->{'qa_count_' . $qaFormId} ?? 0;
}
?>
<td>
    <span class="text-nowrap"><?php echo htmlspecialchars($qaCount > 0 ? round($qaSum / $qaCount, 2) : 0)?> % <span class="material-icons text-muted fs-6 align-middle" role="img" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average across all evaluations')?>" aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average across all evaluations')?>">assessment</span></span>
</td>
<td>
    <span class="text-nowrap"><?php echo htmlspecialchars(isset($agentStatistic_avg['qa_count_' . $qaFormId]) ? $agentStatistic_avg['qa_count_' . $qaFormId] : 0)?> <span class="material-icons text-muted fs-6 align-middle" role="img" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average across agents with non-zero values')?>" aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average across agents with non-zero values')?>">person</span></span>
</td>
<?php endforeach; ?>