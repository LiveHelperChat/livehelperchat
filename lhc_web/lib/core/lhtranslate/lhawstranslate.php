<?php

class erLhcoreClassTranslateAWS {

    public static function detectLanguage($paramsExecution, $text)
    {
        if (empty($text)) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
        }

        $params = [
            'version'     => 'latest',
            'region'      => $paramsExecution['aws_region']
        ];

        if ($paramsExecution['aws_access_key'] != '' && $paramsExecution['aws_secret_key']) {
            $params['credentials'] = [
                'key'    => $paramsExecution['aws_access_key'],
                'secret' => $paramsExecution['aws_secret_key']
            ];
        }

        $awsTranslate = new Aws\Translate\TranslateClient($params);

        $result = $awsTranslate->translateText([
            'SourceLanguageCode' => 'auto', // REQUIRED
            'TargetLanguageCode' => 'de', // REQUIRED
            'Text' => $text, // REQUIRED
        ]);

        return $result->get('SourceLanguageCode');
    }

    public static function translate($paramsExecution, $text, $from, $to)
    {
        if (empty($text)) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
        }

        $params = [
            'version'     => 'latest',
            'region'      => $paramsExecution['aws_region']
        ];

        if ($paramsExecution['aws_access_key'] != '' && $paramsExecution['aws_secret_key']) {
            $params['credentials'] = [
                'key'    => $paramsExecution['aws_access_key'],
                'secret' => $paramsExecution['aws_secret_key']
            ];
        }

        $awsTranslate = new Aws\Translate\TranslateClient($params);

        if (is_array($text)) {

            foreach ($text as $index => $wordItem) {

                $result = $awsTranslate->translateText([
                    'SourceLanguageCode' => $from, // REQUIRED
                    'TargetLanguageCode' => $to, // REQUIRED
                    'Text' => $wordItem['source'], // REQUIRED
                ]);

                $text[$index]['target'] = $result->get('TranslatedText');
            }

            return $text;

        } else {
            $result = $awsTranslate->translateText([
                'SourceLanguageCode' => $from, // REQUIRED
                'TargetLanguageCode' => $to, // REQUIRED
                'Text' => $text, // REQUIRED
            ]);
            return $result->get('TranslatedText');
        }
    }
}
?>
