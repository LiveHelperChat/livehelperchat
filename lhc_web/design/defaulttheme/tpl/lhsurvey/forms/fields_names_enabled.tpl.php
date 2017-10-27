<?php 

$enabledStars = array();
$starFields = array();

$enabledOptions = array();
$enabledFields = array();

for ($i = 1; $i <= 5; $i++) 
{
	if ($survey->{'max_stars_' . $i .'_enabled'} == 1) {
		$enabledStars[] = $i;
		$starFields[] = htmlspecialchars($survey->{'max_stars_' . $i .'_title'});
	}

	if ($survey->{'question_options_' . $i .'_enabled'} == 1) {
        $enabledFields[] = $i;
        $enabledOptions[] = htmlspecialchars($survey->{'question_options_' . $i});
	}
}

?>