<?php
// php cron.php -s site_admin -c cron/util/generate_translation_file_ext -p extension/icclicktocall

$arrayTranslationsProcess = array();

$filesToCheck = ezcBaseFile::findRecursive('./'.$cronjobPathOption->value,
array( '@\.php$@' ),
array( '@/albums|ezcomponents|doc|translations|var|cache|bin|Zend|setttings|pos/@' ));

$locale = 'en_EN';

foreach ($filesToCheck as $filePath)
{
    $contentFile = file_get_contents($filePath);

    $Matches = array();
	preg_match_all('/<\?php echo erTranslationClassLhTranslation::getInstance\(\)->getTranslation\(\'(.*?)\',\'(.*?)\'\)(.*?)\?\>/i',$contentFile,$Matches);

	foreach ($Matches[1] as $key => $section)
	{
	    if (!isset($arrayTranslationsProcess[$section])) {
	        $arrayTranslationsProcess[$section] = array();
	    }

	    if (!in_array($Matches[2][$key],$arrayTranslationsProcess[$section])){
	        $arrayTranslationsProcess[$section][] = $Matches[2][$key];
	    }

	    $contentFile = str_replace($Matches[0][$key],'',$contentFile);
	}

	$Matches = array();
	preg_match_all('/erTranslationClassLhTranslation::getInstance\(\)->getTranslation\(\'(.*?)\',\'(.*?)\'\)/i',$contentFile,$Matches);

	foreach ($Matches[1] as $key => $section)
	{
	    if (!isset($arrayTranslationsProcess[$section])) {
	        $arrayTranslationsProcess[$section] = array();
	    }

	    if (!in_array($Matches[2][$key],$arrayTranslationsProcess[$section])){
	        $arrayTranslationsProcess[$section][] = $Matches[2][$key];
	    }
	}
}

$reader = new ezcTranslationTsBackend( $cronjobPathOption->value . '/doc' );
$reader->setOptions( array( 'format' => 'default.ts' ) );
$reader->initReader( $locale );

$manager = new ezcTranslationManager( $reader );

function translateToLanguage($apiKey,$toLanguage, $string) {
    return '';
}

foreach ($arrayTranslationsProcess as $context => $itemsToTranslate)
{
    $contextItems = array() ;

    try {
        $contextItem = $manager->getContext( $locale, $context );
    } catch (Exception $e) { // Context does not exists
        $reader->initWriter( $locale );
        $reader->storeContext( $context, $contextItems );
        $reader->deinitWriter();
        $contextItem = $manager->getContext( $locale, $context );
    }

    foreach ($itemsToTranslate as $string)
    {
       if ($locale != 'en_EN') {
           try {
                  $originalTranslation = $contextItem->getTranslation($string);

                 if ($originalTranslation != ''){
                    $contextItems[] = new ezcTranslationData( $string, $originalTranslation, NULL, ezcTranslationData::TRANSLATED );
                 } else {
                    $contextItems[] = new ezcTranslationData( $string, translateToLanguage($apiKey,substr($locale,0,2),$string), NULL, ezcTranslationData::UNFINISHED );
                 }

           } catch (Exception $e) { // Translation does not exist
                $contextItems[] = new ezcTranslationData( $string, translateToLanguage($apiKey,substr($locale,0,2),$string), NULL, ezcTranslationData::UNFINISHED );
           }
       } else {
           $contextItems[] = new ezcTranslationData( $string, '', NULL, ezcTranslationData::UNFINISHED );
       }
    }

    $reader->initWriter( $locale );
    $reader->storeContext( $context, $contextItems );
    $reader->deinitWriter();
}


