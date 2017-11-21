<?php

class erLhcoreClassSiteaccessGenerator {

	public static function listDirectory($dir = 'translations', $files = false)
	{
		$d = dir($dir);
		$directory  = array();

		while (false !== ($entry = $d->read())) {

			if ($files == false)
			{
				if (!is_dir($dir.'/'.$entry.'/') || $entry == '.' || $entry == '..' ) continue;
				$directory[] = $dir.'/'.$entry;
			} else {
				if (is_dir($dir.'/'.$entry.'/') || $entry == '.' || $entry == '..' ) continue;
				$directory[] = $dir.'/'.$entry;
			}
		}

		$d->close();

		return $directory;
	}

    public static function getLanguages()
    {
    	$translations = self::listDirectory('translations');
    	$rtl = array('ar_EG','fa_FA');

    	//$languages[$translation] = array('locale' => $translation,'content_language' => substr($translation, 0,2), 'dir_language' => in_array($translation, $rtl) ? 'rtl' : 'ltr');

    	$languages = array();
    	$languages['en_EN'] = array('locale' => 'en_EN','content_language' => 'en', 'dir_language' => 'ltr');

    	foreach ($translations as $translation) {
    		$translation = str_replace('translations/', '', $translation);
    		$languages[$translation] = array('locale' => $translation,'content_language' => substr($translation, 0,2), 'dir_language' => in_array($translation, $rtl) ? 'rtl' : 'ltr');
    	}

    	return $languages;
    }

    public static function updateSiteAccess(stdClass $input)
    {
    	$languages = self::getLanguages();
    	$languageData = $languages[$input->language];

    	$siteAccessData = erConfigClassLhConfig::getInstance()->getSetting( 'site_access_options', $input->siteaccess );

    	$siteAccessData['locale'] = $languageData['locale'];
    	$siteAccessData['content_language'] = $languageData['content_language'];
    	$siteAccessData['dir_language'] = $languageData['dir_language'];
    	$siteAccessData['theme'] = self::trimArrayElements(explode("\n", trim($input->theme)));
    	$siteAccessData['default_url']['module'] = $input->module;
    	$siteAccessData['default_url']['view'] = $input->view;

    	$cfgSite = erConfigClassLhConfig::getInstance();
    	$cfgSite->setSetting( 'site_access_options', $input->siteaccess , $siteAccessData);
    	$cfgSite->save();
    }

    public static function trimArrayElements($array){
    	foreach ($array as $key => & $value) {
    		$value = trim($value);
    	}
    	return $array;
    }
}
