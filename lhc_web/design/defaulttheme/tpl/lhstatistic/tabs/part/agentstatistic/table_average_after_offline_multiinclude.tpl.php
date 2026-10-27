<?php foreach ((isset($qaEvaluationForms) ? $qaEvaluationForms : array()) as $qaFormId => $qaFormObject) : ?>
<td>
    <?php echo htmlspecialchars((int)round(isset($agentStatistic_avg['qa_score_' . $qaFormId]) ? $agentStatistic_avg['qa_score_' . $qaFormId] : 0))?> %
</td>
<td>
    <?php echo htmlspecialchars(isset($agentStatistic_avg['qa_count_' . $qaFormId]) ? (int)round($agentStatistic_avg['qa_count_' . $qaFormId]) : 0)?>
</td>
<?php endforeach; ?>