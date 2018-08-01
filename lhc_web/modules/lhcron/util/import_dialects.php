<?php

// php cron.php -s site_admin -c cron/util/import_dialects

$row = 1;
if (($handle = fopen("doc/translation_default/locales.csv", "r")) !== FALSE) {

    $stats = array('exist' => 0, 'missing' => 0);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (erLhcoreClassModelSpeechLanguageDialect::getCount(array('customfilter' => array("lang_code = '{$data[0]}' OR short_code = '{$data[0]}'"))) > 0) {
            //echo "Existing language - ",$data[0],"\n";
            $stats['exist']++;
        } else {

            $shortCode = explode('-',$data[0]);

            $shortCodeDialect = erLhcoreClassModelSpeechLanguageDialect::findOne(array('filter' => array("short_code" => $shortCode[0])));

            if ($shortCodeDialect instanceof erLhcoreClassModelSpeechLanguageDialect) {

                $newDialect = new erLhcoreClassModelSpeechLanguageDialect();
                $newDialect->language_id = $shortCodeDialect->language_id;
                $newDialect->lang_name = $shortCodeDialect->lang_name;
                $newDialect->lang_code = $data[0];
                $newDialect->short_code = '';
                $newDialect->saveThis();

                echo "Found - ",$shortCode[0],' - ',$shortCodeDialect->dialect_name,"\n";
            } else {
                echo "Short code not found - ",$data[0],"\n";
                exit;
            }


            //echo "Missing - [",$data[0],'] - ',$data[1],"\n";
            $stats['missing']++;
        }
    }

    print_r($stats);

    fclose($handle);
}

?>