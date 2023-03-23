<?php

namespace LiveHelperChat\Helpers;

class Anonymizer
{
    public static function maskPhone($phone) {

        if (strlen($phone) <= 4) {
            return $phone;
        }

        $hasSign = strpos($phone,'+') !== false ? '+' : '';

        $length = strlen($phone) - (4 + ($hasSign != '' ? 1 : 0));

        return $hasSign . str_repeat('*',  $length) . mb_substr($phone,strlen($phone)-4);

    }

    public static function maskEmail($email, $minLength = 3, $maxLength = 10, $mask = "***") {
        $atPos = strrpos($email, "@");
        $name = substr($email, 0, $atPos);
        $len = strlen($name);
        $domain = substr($email, $atPos);

        if (($len / 2) < $maxLength) $maxLength = floor($len / 2);

        $shortenedEmail = (($len > $minLength) ? substr($name, 0, $maxLength) : "");
        return  "{$shortenedEmail}{$mask}{$domain}";
    }
}

?>