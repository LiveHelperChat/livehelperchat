<?php

class erLhcoreClassIPDetect
{

    public static function getIP()
    {
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? trim(strip_tags((string)$_SERVER['REMOTE_ADDR'])) : '127.0.0.1';
        if ($remoteAddr === '') {
            $remoteAddr = '127.0.0.1';
        }

        if (erLhcoreClassSystem::$proxyMode !== true) {
            return $remoteAddr;
        }

        $trustedProxies = is_array(erLhcoreClassSystem::$trustedProxies) ? erLhcoreClassSystem::$trustedProxies : array();

        if (!self::isTrustedProxy($remoteAddr, $trustedProxies)) {
            return $remoteAddr;
        }

        $proxyHeader = is_string(erLhcoreClassSystem::$proxyHeader) && erLhcoreClassSystem::$proxyHeader !== ''
            ? erLhcoreClassSystem::$proxyHeader
            : 'HTTP_X_FORWARDED_FOR';

        if ($proxyHeader !== 'HTTP_X_FORWARDED_FOR') {
            if (isset($_SERVER[$proxyHeader])) {
                $candidate = trim((string)$_SERVER[$proxyHeader]);
                if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                    return $candidate;
                }
            }
            return $remoteAddr;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '') {
            $parts = explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']);
            // Walk right-to-left: the immediate trusted proxy appends the peer IP on the right,
            // so client-spoofed entries on the left are never trusted.
            for ($i = count($parts) - 1; $i >= 0; $i--) {
                $candidate = trim($parts[$i]);
                if (filter_var($candidate, FILTER_VALIDATE_IP) === false) {
                    continue;
                }
                if (!self::isTrustedProxy($candidate, $trustedProxies)) {
                    return $candidate;
                }
            }
        }

        return $remoteAddr;
    }

    public static function isTrustedProxy($ip, array $trustedProxies = array())
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        // When no explicit trusted_proxies list is configured, only trust local/private proxy addresses
        // so direct connections from public IPs cannot spoof forwarded headers.
        if (empty($trustedProxies)) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        foreach ($trustedProxies as $proxy) {
            $proxy = trim((string)$proxy);
            if ($proxy === '') {
                continue;
            }
            if (self::ip_in_range($ip, $proxy)) {
                return true;
            }
        }

        return false;
    }

    public static function getFingerprint()
    {
        $headers = array(
            'HTTP_USER_AGENT',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_CHARSET',
        );

        $parts = array();
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && $_SERVER[$header] !== '') {
                $parts[] = $_SERVER[$header];
            }
        }

        $fingerprint = '';
        if (!empty($parts)) {
            $fingerprint = sha1(implode('|', $parts));
        }

        return $fingerprint;
    }


    public static function getServerAddress()
    {
        if (array_key_exists('SERVER_ADDR', $_SERVER))
            return $_SERVER['SERVER_ADDR'];
        elseif (array_key_exists('LOCAL_ADDR', $_SERVER) && $_SERVER['LOCAL_ADDR'] != '::1')
            return $_SERVER['LOCAL_ADDR'];
        elseif (array_key_exists('SERVER_NAME', $_SERVER))
            return gethostbyname($_SERVER['SERVER_NAME']);
    }

    public static function isIgnored($ip, array $ignore_ips)
    {

        foreach ($ignore_ips as $ip_ignore) {
            if (self::ip_in_range($ip, trim($ip_ignore)) == true) {
                return true;
            }
        }

        return false;
    }


    /*
     * ip_in_range.php - Function to determine if an IP is located in a
    *                   specific range as specified via several alternative
    *                   formats.
    *
    * Network ranges can be specified as:
    * 1. Wildcard format:     1.2.3.*
    * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    * 3. Start-End IP format: 1.2.3.0-1.2.3.255
    *
    * Return value BOOLEAN : ip_in_range($ip, $range);
    *
    * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
    * 10 January 2008
    * Version: 1.2
    *
    * Source website: http://www.pgregg.com/projects/php/ip_in_range/
    * Version 1.2
    *
    * This software is Donationware - if you feel you have benefited from
    * the use of this tool then please consider a donation. The value of
    * which is entirely left up to your discretion.
    * http://www.pgregg.com/donate/
    *
    * Please do not remove this header, or source attibution from this file.
    */
    public static function ip_in_range($ip, $range)
    {
        $ip = trim((string)$ip);
        $range = trim((string)$range);

        if ($ip === '' || $range === '') {
            return false;
        }

        if ($ip === $range) {
            return true;
        }

        $ipBin = @inet_pton($ip);
        if ($ipBin === false) {
            return false;
        }

        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK or IP/CIDR format
            list($subnet, $netmask) = explode('/', $range, 2);
            $subnet = trim($subnet);
            $netmask = trim($netmask);

            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format (IPv4 only)
                if (strlen($ipBin) !== 4) {
                    return false;
                }
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                $ip_dec = ip2long($ip);
                $range_dec = ip2long($subnet);
                if ($netmask_dec === false || $ip_dec === false || $range_dec === false) {
                    return false;
                }
                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }

            // $netmask is a CIDR prefix length (IPv4 or IPv6)
            if (!ctype_digit($netmask)) {
                return false;
            }

            // Support shorthand IPv4 CIDR like 1.2.3/24
            if (strlen($ipBin) === 4 && strpos($subnet, ':') === false && substr_count($subnet, '.') < 3) {
                $x = explode('.', $subnet);
                while (count($x) < 4) {
                    $x[] = '0';
                }
                $subnet = sprintf('%u.%u.%u.%u', empty($x[0]) ? 0 : (int)$x[0], empty($x[1]) ? 0 : (int)$x[1], empty($x[2]) ? 0 : (int)$x[2], empty($x[3]) ? 0 : (int)$x[3]);
            }

            $subnetBin = @inet_pton($subnet);
            if ($subnetBin === false || strlen($ipBin) !== strlen($subnetBin)) {
                return false;
            }

            $maxBits = strlen($ipBin) * 8;
            $prefixLen = (int)$netmask;
            if ($prefixLen < 0 || $prefixLen > $maxBits) {
                return false;
            }

            $fullBytes = intdiv($prefixLen, 8);
            $remBits = $prefixLen % 8;

            if ($fullBytes > 0 && substr($ipBin, 0, $fullBytes) !== substr($subnetBin, 0, $fullBytes)) {
                return false;
            }

            if ($remBits > 0) {
                $byteMask = (0xFF << (8 - $remBits)) & 0xFF;
                if ((ord($ipBin[$fullBytes]) & $byteMask) !== (ord($subnetBin[$fullBytes]) & $byteMask)) {
                    return false;
                }
            }

            return true;
        }

        // Canonical IPv4/IPv6 comparison (e.g. ::1 vs 0:0:0:0:0:0:0:1)
        $rangeBin = @inet_pton($range);
        if ($rangeBin !== false) {
            return $ipBin === $rangeBin;
        }

        // Wildcard (255.255.*.*) or start-end (1.2.3.0-1.2.3.255) format (IPv4 only)
        if (strlen($ipBin) !== 4) {
            return false;
        }

        if (strpos($range, '*') !== false) {
            $lower = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }

        if (strpos($range, '-') !== false) {
            list($lower, $upper) = explode('-', $range, 2);
            $lower_long = ip2long(trim($lower));
            $upper_long = ip2long(trim($upper));
            $ip_long = ip2long($ip);
            if ($lower_long === false || $upper_long === false || $ip_long === false) {
                return false;
            }
            $lower_dec = (float)sprintf('%u', $lower_long);
            $upper_dec = (float)sprintf('%u', $upper_long);
            $ip_dec = (float)sprintf('%u', $ip_long);
            return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
        }

        return false;
    }
}


?>
