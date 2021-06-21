<?php

// php cron.php -s site_admin -c cron/util/generate_css -p 1

if ($cronjobPathOption->value == 1)
{
    echo "Removing previous files...\n";

    // Remove
    if (file_exists('design/defaulttheme/js/js_static')) {
        ezcBaseFile::removeRecursive('design/defaulttheme/js/js_static');
    }

    // Remove
    if (file_exists('design/defaulttheme/css/css_static')) {
        ezcBaseFile::removeRecursive('design/defaulttheme/css/css_static');
    }

    mkdir('design/defaulttheme/js/js_static');

    mkdir('design/defaulttheme/css/css_static');
}


echo "Searching for files...\n";
$filesToCheck = ezcBaseFile::findRecursive('.',
    array( '@\.php$@' ),
    array( '@/./albums|./ezcomponents|./doc|./translations|./var|./cache|./bin|./Zend|./setttings|./pos/@' ));

echo count($filesToCheck) . " files were found\n";

// Set build mode
erLhcoreClassDesign::setBuildMode(true);

$cfgSite = erConfigClassLhConfig::getInstance();

// Different siteacess can have different themes and extensions so it means different overrides

foreach ($cfgSite->getSetting( 'site', 'available_site_access' ) as $siteAccess) {
    $siteAccessOptions = $cfgSite->getSetting('site_access_options', $siteAccess);

    echo "Processing {$siteAccess} siteacess\n";

    // Set siteAccess
    erLhcoreClassSystem::instance()->SiteAccess = $siteAccess;
    erLhcoreClassSystem::instance()->ThemeSite = $siteAccessOptions['theme'];

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

        $replace = array(
            'angular.lhc.min.js' => 'angular.lhc.js',
            'lh.min.js' => 'lh.js',
        );

        if (!empty($Matches[1])) {
            foreach ($Matches[1] as $key => $section)
            {
                // Because we are in build mode it will build CSS
                erLhcoreClassDesign::designJS(trim(str_replace(array_keys($replace),array_values($replace),$section),'\''),trim($section,'\''));
            }
        }
    }
}

echo "Completed\n";