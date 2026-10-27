<?php foreach ((isset($qaEvaluationForms) ? $qaEvaluationForms : array()) as $qaFormId => $qaFormObject) : ?>
<th title="<?php echo htmlspecialchars($qaFormObject->name)?>"><?php echo htmlspecialchars($qaFormObject->name)?> (score)</th>
<th><?php echo htmlspecialchars($qaFormObject->name)?> (count)</th>
<?php endforeach; ?>