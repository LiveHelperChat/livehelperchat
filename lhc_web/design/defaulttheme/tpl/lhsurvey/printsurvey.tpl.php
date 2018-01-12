<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>
<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names_enabled.tpl.php'));?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
	<tr>
	    <th width="1%" nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Survey');?></th>
	    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chat');?></th>
	    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator');?></th>

        <?php foreach ($starFields as $starField) : ?>
            <th><?php echo htmlspecialchars($starField)?></th>
        <?php endforeach; ?>

        <?php foreach ($enabledOptions as $optionField) : ?>
            <th><?php echo htmlspecialchars($optionField)?></th>
        <?php endforeach; ?>

        <?php foreach ($enabledOptionsPlain as $optionFieldPlain) : ?>
            <th><?php echo htmlspecialchars($optionFieldPlain)?></th>
        <?php endforeach; ?>

	    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Time');?></th>
	</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->survey_id)?></td>
    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->chat_id)?></td>
    	<td><?php echo htmlspecialchars($item->department_name)?></td>
    	<td><?php echo htmlspecialchars($item->user->name_official)?></td>

    	<?php $stars = array(); foreach ($enabledStars as $n) : ?>
            <td><?php echo $item->{'max_stars_' . $n}; ?></td>
        <?php endforeach; ?>

        <?php foreach ($enabledFields as $enabledField) :
				$options = $survey->{'question_options_' . $enabledField . '_items_front'};
				if (isset($options[$item->{'question_options_' . $enabledField}-1])) {
					echo '<td>',erLhcoreClassSurveyValidator::parseAnswer($options[$item->{'question_options_' . $enabledField}-1]['option']),'</td>';
				} else {
					echo '<td>',erLhcoreClassSurveyValidator::parseAnswer($item->{'question_options_' . $enabledField}),'</td>';
				}
        endforeach; ?>

        <?php foreach ($enabledFieldsPlain as $enabledFieldPlain) : ?>
            <td><?php echo htmlspecialchars($item->{'question_plain_' . $enabledFieldPlain})?></td>
        <?php endforeach; ?>

    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->ftime_front)?></td>
    </tr>
<?php endforeach; ?>
</table>
