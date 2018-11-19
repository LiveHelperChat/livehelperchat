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
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length(self::$method);
        $iv = substr($data, 0, $ivSize);
        $data = openssl_decrypt(substr($data, $ivSize), self::$method, $key, OPENSSL_RAW_DATA, $iv);

        return $data;
    }
}