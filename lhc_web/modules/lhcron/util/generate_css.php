<?php

// php cron.php -s site_admin -c cron/util/generate_css

$filesToCheck = ezcBaseFile::findRecursive('.',
    array( '@\.php$@' ),
    array( '@/./albums|./ezcomponents|./doc|./translations|./var|./cache|./bin|./Zend|./setttings|./pos/@' ));

// Set build mode
erLhcoreClassDesign::setBuildMode(true);

foreach ($filesToCheck as $filePath)
{
    $contentFile = file_get_contents($filePath);

    $Matches = array();
    preg_match_all('/erLhcoreClassDesign::designCSS\((.*?)\)/i',$contentFile,$Matches);

    if (!empty($Matches[1])) {
        foreach ($Matches[1] as $key => $section)
        {
            // Because we are in build mode it will build CSS
            erLhcoreClassDesign::designCSS(trim($section,'\''));
        }
    }

    $Matches = array();
    preg_match_all('/erLhcoreClassDesign::designJS\((.*?)\)/i',$contentFile,$Matches);

    if (!empty($Matches[1])) {
        foreach ($Matches[1] as $key => $section)
        {
            // Because we are in build mode it will build CSS
            erLhcoreClassDesign::designJS(trim($section,'\''));
        }
    }
}