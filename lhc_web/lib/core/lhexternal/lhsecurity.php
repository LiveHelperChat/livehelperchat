<?php

class lhSecurity {

    public static $method = 'AES-256-CBC';

    public static function encrypt(string $data, string $key) : string
    {
        $ivSize = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, self::$method, $key, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }

    public static function decrypt(string $data, string $key) : string
    {
        try {
            $data = base64_decode($data);
            if ($data === false) {
                throw new Exception('Invalid base64 encoding');
            }

            $ivSize = openssl_cipher_iv_length(self::$method);
            if ($ivSize === false) {
                throw new Exception('Invalid cipher method');
            }

            if (strlen($data) <= $ivSize) {
                throw new Exception('Data is too short');
            }

            $iv = substr($data, 0, $ivSize);
            $decrypted = openssl_decrypt(substr($data, $ivSize), self::$method, $key, OPENSSL_RAW_DATA, $iv);

            if ($decrypted === false) {
                throw new Exception('Decryption failed: ' . openssl_error_string());
            }

            return $decrypted;
        } catch (Exception $e) {
            throw new Exception('Decryption error: ' . $e->getMessage());
        }
    }
}