<?php

class lhSecurity {

    public static $method = 'AES-256-CBC';

    public static function encrypt(string $data, string $key) : string
    {
        try {
            $ivSize = openssl_cipher_iv_length(self::$method);
            if ($ivSize === false) {
                throw new Exception('Invalid cipher method');
            }

            $iv = openssl_random_pseudo_bytes($ivSize);
            if ($iv === false) {
                throw new Exception('Could not generate random bytes');
            }

            $encrypted = openssl_encrypt($data, self::$method, $key, OPENSSL_RAW_DATA, $iv);
            if ($encrypted === false) {
                throw new Exception('Encryption failed: ' . openssl_error_string());
            }

            // For storage/transmission, we simply concatenate the IV and cipher text
            $encrypted = base64_encode($iv . $encrypted);

            return $encrypted;
        } catch (Exception $e) {
            throw new Exception('Encryption error: ' . $e->getMessage());
        }
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

$response = '';

if (isset($_POST['op']) && isset($_POST['key']) && isset($_POST['val'])) {
    $operation = $_POST['op'];
    $key = $_POST['key'];
    $value = $_POST['val'];

    if (strlen($key) < 40) {
        $response = 'Encryption key must be at least 40 characters long';
    } else {
        try {
            if ($operation == 'encrypt') {
                $response = lhSecurity::encrypt($value, $key);
            } elseif ($operation == 'decrypt') {
                $response = lhSecurity::decrypt($value, $key);
            } else {
                $response = 'Invalid operation';
            }
        } catch (Exception $e) {
            $response = 'Error: ' . $e->getMessage();
        }
    }
}

echo $response;

exit;
?>