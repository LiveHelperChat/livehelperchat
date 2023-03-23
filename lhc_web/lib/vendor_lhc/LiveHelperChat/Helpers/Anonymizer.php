<?php

namespace LiveHelperChat\Helpers;

class Anonymizer
{
    public static function maskPhone($phone) {
        $hasSign = strpos($phone,'+') !== false ? '+' : '';
        return $hasSign . str_repeat('*', strlen($phone) - (4 + ($hasSign != '' ? 1 : 0)) ) . mb_substr($phone,strlen($phone)-4);
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