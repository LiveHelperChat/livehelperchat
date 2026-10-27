<?php
$qaTimeAppend = '';
if (isset($input)) {
    if (isset($input->timefrom) && $input->timefrom != '') {
        $qaTimeAppend .= '/(timefrom)/' . urlencode($input->timefrom);
    }
    if (isset($input->timeto) && $input->timeto != '') {
        $qaTimeAppend .= '/(timeto)/' . urlencode($input->timeto);
    }
}
?>
<?php foreach ((isset($qaEvaluationForms) ? $qaEvaluationForms : array()) as $qaFormId => $qaFormObject) : ?>
    <?php
    $qaScore = isset($info->{'qa_score_' . $qaFormId}) ? (float)$info->{'qa_score_' . $qaFormId} : 0;
    $qaCount = isset($info->{'qa_count_' . $qaFormId}) ? (int)$info->{'qa_count_' . $qaFormId} : 0;
    $qaUrl = erLhcoreClassDesign::baseurl('form/collected') . '/' . (int)$qaFormId . '/(evaluated_user_ids)/' . (int)$info->userId . $qaTimeAppend;
    ?>
    <td nowrap>
        <?php if ($qaCount > 0) : ?>
            <a href="<?php echo $qaUrl?>"><?php echo (int)round($qaScore);?> %</a>
        <?php else : ?>
            n/a
        <?php endif;?>
    </td>
    <td nowrap>
        <?php if ($qaCount > 0) : ?>
            <a href="<?php echo $qaUrl?>"><?php echo $qaCount;?></a>
        <?php else : ?>
            n/a
        <?php endif;?>
    </td>
<?php endforeach; ?>