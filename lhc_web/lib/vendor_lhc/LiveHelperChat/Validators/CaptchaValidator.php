<?php

namespace LiveHelperChat\Validators;

class CaptchaValidator {

    public static function getCaptchaSettings(): array
    {
        $recaptchaData = \erLhcoreClassModelChatConfig::fetch('recaptcha_data')->data_value;

        if (!is_array($recaptchaData)) {
            $recaptchaData = array();
        }

        $normalizedData = array_merge(array(
            'enabled' => 0,
            'provider' => 'google',
            'site_key' => '',
            'secret_key' => '',
            'turnstile_site_key' => '',
            'turnstile_secret_key' => '',
        ), $recaptchaData);

        $normalizedData['enabled'] = (int)$normalizedData['enabled'];
        $normalizedData['provider'] = in_array($normalizedData['provider'], array('google', 'turnstile')) ? $normalizedData['provider'] : 'google';
        $normalizedData['site_key'] = (string)$normalizedData['site_key'];
        $normalizedData['secret_key'] = (string)$normalizedData['secret_key'];
        $normalizedData['turnstile_site_key'] = (string)$normalizedData['turnstile_site_key'];
        $normalizedData['turnstile_secret_key'] = (string)$normalizedData['turnstile_secret_key'];

        return $normalizedData;
    }

    public static function validateAuthCaptcha(array $postData, string $context): array
    {
        $captchaSettings = self::getCaptchaSettings();

        if ((int)$captchaSettings['enabled'] !== 1) {
            return array(
                'valid' => true,
                'provider' => $captchaSettings['provider'],
                'reason' => 'disabled'
            );
        }

        if ($captchaSettings['provider'] === 'google') {
            if ($captchaSettings['site_key'] === '' || $captchaSettings['secret_key'] === '') {
                return array(
                    'valid' => false,
                    'provider' => 'google',
                    'reason' => 'config_missing'
                );
            }

            $token = isset($postData['g-recaptcha']) ? trim((string)$postData['g-recaptcha']) : '';

            if ($token === '') {
                return array(
                    'valid' => false,
                    'provider' => 'google',
                    'reason' => 'missing_token'
                );
            }

            return self::verifyGoogleRecaptchaV3($captchaSettings['secret_key'], $token, $context);
        }

        if ($captchaSettings['provider'] === 'turnstile') {
            if ($captchaSettings['turnstile_site_key'] === '' || $captchaSettings['turnstile_secret_key'] === '') {
                return array(
                    'valid' => false,
                    'provider' => 'turnstile',
                    'reason' => 'config_missing'
                );
            }

            $token = isset($postData['cf-turnstile-response']) ? trim((string)$postData['cf-turnstile-response']) : '';

            if ($token === '') {
                return array(
                    'valid' => false,
                    'provider' => 'turnstile',
                    'reason' => 'missing_token'
                );
            }

            return self::verifyCloudflareTurnstile($captchaSettings['turnstile_secret_key'], $token, $context);
        }

        return array(
            'valid' => false,
            'provider' => 'unknown',
            'reason' => 'provider_invalid'
        );
    }

    public static function verifyGoogleRecaptchaV3(string $secretKey, string $token, string $context): array
    {
        $response = self::postCaptchaVerifyRequest('https://www.google.com/recaptcha/api/siteverify', array(
            'secret' => $secretKey,
            'response' => $token,
        ));

        if ($response['success'] !== true || !is_array($response['data'])) {
            return array(
                'valid' => false,
                'provider' => 'google',
                'reason' => 'request_failed'
            );
        }

        $result = $response['data'];

        if (!(isset($result['success']) && ($result['success'] == 1 || $result['success'] === true))) {
            return array(
                'valid' => false,
                'provider' => 'google',
                'reason' => 'provider_rejected'
            );
        }

        if (!(isset($result['score']) && (float)$result['score'] >= 0.1)) {
            return array(
                'valid' => false,
                'provider' => 'google',
                'reason' => 'low_score'
            );
        }

        if (!(isset($result['action']) && $result['action'] === $context)) {
            return array(
                'valid' => false,
                'provider' => 'google',
                'reason' => 'action_mismatch'
            );
        }

        return array(
            'valid' => true,
            'provider' => 'google',
            'reason' => 'validated'
        );
    }

    public static function verifyCloudflareTurnstile(string $secretKey, string $token, string $context): array
    {
        $params = array(
            'secret' => $secretKey,
            'response' => $token,
        );

        $visitorIp = \erLhcoreClassIPDetect::getIP();
        if ($visitorIp !== '') {
            $params['remoteip'] = $visitorIp;
        }

        $response = self::postCaptchaVerifyRequest('https://challenges.cloudflare.com/turnstile/v0/siteverify', $params);

        if ($response['success'] !== true || !is_array($response['data'])) {
            return array(
                'valid' => false,
                'provider' => 'turnstile',
                'reason' => 'request_failed'
            );
        }

        $result = $response['data'];

        if (!(isset($result['success']) && ($result['success'] == 1 || $result['success'] === true))) {
            return array(
                'valid' => false,
                'provider' => 'turnstile',
                'reason' => 'provider_rejected'
            );
        }

        if (isset($result['action']) && $result['action'] !== '' && $result['action'] !== $context) {
            return array(
                'valid' => false,
                'provider' => 'turnstile',
                'reason' => 'action_mismatch'
            );
        }

        return array(
            'valid' => true,
            'provider' => 'turnstile',
            'reason' => 'validated'
        );
    }

    public static function postCaptchaVerifyRequest(string $url, array $params): array
    {
        if (!function_exists('curl_init')) {
            return array(
                'success' => false,
                'data' => null,
                'reason' => 'curl_missing'
            );
        }

        $ch = curl_init();

        if ($ch === false) {
            return array(
                'success' => false,
                'data' => null,
                'reason' => 'curl_init_failed'
            );
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            curl_close($ch);
            return array(
                'success' => false,
                'data' => null,
                'reason' => 'curl_exec_failed'
            );
        }

        curl_close($ch);

        $decodedResponse = json_decode($responseBody, true);

        if (!is_array($decodedResponse)) {
            return array(
                'success' => false,
                'data' => null,
                'reason' => 'invalid_json'
            );
        }

        return array(
            'success' => true,
            'data' => $decodedResponse
        );
    }
}
