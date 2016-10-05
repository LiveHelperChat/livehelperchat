<?php 

$enabledStars = array();
$starFields = array();
for ($i = 1; $i <= 5; $i++) 
{
	if ($survey->{'max_stars_' . $i .'_enabled'} == 1) {
		$enabledStars[] = $i;
		$starFields[] = htmlspecialchars($survey->{'max_stars_' . $i .'_title'});
	}
}

?>