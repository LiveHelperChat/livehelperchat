<?php

class erLhcoreClassTranslateDeepL {
    private static $translator;

    private static function getTranslator($apiKey){
        if (self::$translator == null){
            try {
                self::$translator = new DeepL\Translator(
                    $apiKey,
                    []
                );
            } catch (DeepL\DeepLException $e) {
                throw new RuntimeException('Failed to initialize deepl translator class', 0, $e);
            }
        }
        return self::$translator;
    }

    public static function detectLanguage($apiKey, $text){
        if (empty($text)){
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
        }
        if (empty($apiKey)){
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing DeepL API key'));
        }
        try{
            return self::getTranslator($apiKey)->translateText($text, null, 'en-US')->detectedSourceLang;
        } catch (DeepL\DeepLException $e){
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not detect language'));
        }
    }

    public static $validMapping = [
        'en-us' => 'en-US',
        'en' => 'en-US',
        'en-gb' => 'en-GB',
    ];


    public static function translate($apiKey, $text, $fromLanguage, $toLanguage) {
        if (empty($text)){
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
        }
        try {
            // If formality is passed in the language code, use it but otherwise prefer formal
            $splitLanguage = explode('_', $toLanguage, 2);
            $toLanguage = $splitLanguage[0];
            $formal = $splitLanguage[1] ?? 'prefer_more';
            $formality = $formal === 'less' ? 'prefer_less' : 'prefer_more';
            $fromLanguage = explode('-',$fromLanguage)[0];

            if (isset(self::$validMapping[$toLanguage])) {
                $toLanguage = self::$validMapping[$toLanguage];
            }

            return self::getTranslator($apiKey)->translateText($text, $fromLanguage, $toLanguage, [
                'formality' => $formality,
            ])->text;
        } catch (Exception $e) {
            throw new RuntimeException("Failed translate from {$fromLanguage} to {$toLanguage}", 0, $e);
        }
    }
}
?>
