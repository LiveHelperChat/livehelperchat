<?php

class erLhcoreClassIPDetect {

	private static $couldflareRun = false;

	public static function getIP(){

		if (self::$couldflareRun == false){
			self::cloudflareInit();
			self::$couldflareRun = true;
		}
		
	   if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) {
			$_SERVER['REMOTE_ADDR'] = str_replace(' ', '', $_SERVER['HTTP_X_FORWARDED_FOR']);	
            $parts = explode(',', $_SERVER['REMOTE_ADDR']);
            $_SERVER['REMOTE_ADDR'] = $parts[0];
		}
		
		$_SERVER['REMOTE_ADDR'] = strip_tags($_SERVER['REMOTE_ADDR']);
		
		return $_SERVER["REMOTE_ADDR"];
	}

	public static function getServerAddress() {
		if (array_key_exists('SERVER_ADDR', $_SERVER))
		    return $_SERVER['SERVER_ADDR'];
		elseif (array_key_exists('LOCAL_ADDR', $_SERVER) && $_SERVER['LOCAL_ADDR'] != '::1')
		    return $_SERVER['LOCAL_ADDR'];
		elseif (array_key_exists('SERVER_NAME', $_SERVER))
		    return gethostbyname($_SERVER['SERVER_NAME']);
	}
	
	public static function isIgnored($ip, array $ignore_ips) {
		
		foreach ($ignore_ips as $ip_ignore) {
			if (self::ip_in_range($ip, $ip_ignore) == true){
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
	public static function ip_in_range($ip, $range) {
		
		if ($ip == $range) {
			return true;
		} elseif (strpos($range, '/') !== false) {
			// $range is in IP/NETMASK format
			list($range, $netmask) = explode('/', $range, 2);
			if (strpos($netmask, '.') !== false) {
				// $netmask is a 255.255.0.0 format
				$netmask = str_replace('*', '0', $netmask);
				$netmask_dec = ip2long($netmask);
				return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
			} else {
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode('.', $range);
				while(count($x)<4) $x[] = '0';
				list($a,$b,$c,$d) = $x;
				$range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
				$range_dec = ip2long($range);
				$ip_dec = ip2long($ip);
	
				# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
				#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));
	
				# Strategy 2 - Use math to create it
				$wildcard_dec = pow(2, (32-$netmask)) - 1;
				$netmask_dec = ~ $wildcard_dec;
	
				return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
			}
		} else {
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
			if (strpos($range, '*') !==false) { // a.b.*.* format
				// Just convert to A-B format by setting * to 0 for A and 255 for B
				$lower = str_replace('*', '0', $range);
				$upper = str_replace('*', '255', $range);
				$range = "$lower-$upper";
			}
	
			if (strpos($range, '-')!==false) { // A-B format
				list($lower, $upper) = explode('-', $range, 2);
				$lower_dec = (float)sprintf("%u",ip2long($lower));
				$upper_dec = (float)sprintf("%u",ip2long($upper));
				$ip_dec = (float)sprintf("%u",ip2long($ip));
				return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
			}
							
			return false;
		}
	}
	
	public static function cloudflareInit() {

		if (strpos($_SERVER["REMOTE_ADDR"], ":") === FALSE) {
			$cf_ip_ranges = array("204.93.240.0/24","204.93.177.0/24","199.27.128.0/21","173.245.48.0/20","103.21.244.0/22","103.22.200.0/22","103.31.4.0/22","141.101.64.0/18","108.162.192.0/18","190.93.240.0/20","188.114.96.0/20","197.234.240.0/22","198.41.128.0/17","162.158.0.0/15");
			// IPV4: Update the REMOTE_ADDR value if the current REMOTE_ADDR value is in the specified range.
			foreach ($cf_ip_ranges as $range) {
				if (self::ipv4_in_range($_SERVER["REMOTE_ADDR"], $range)) {
					if ($_SERVER["HTTP_CF_CONNECTING_IP"]) {
						$_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
					}
					break;
				}
			}
		} else {
			$cf_ip_ranges = array("2400:cb00::/32", "2606:4700::/32", "2803:f800::/32");
			$ipv6 = self::get_ipv6_full($_SERVER["REMOTE_ADDR"]);
			foreach ($cf_ip_ranges as $range) {
				if (self::ipv6_in_range($ipv6, $range)) {
					if ($_SERVER["HTTP_CF_CONNECTING_IP"]) {
						$_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
					}
					break;
				}
			}
		}
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

	/*
	 * Modified by James Greene <james@cloudflare.com> to include IPV6 support
	* (original version only supported IPV4).
	* 21 May 2012
	*/


	// decbin32
	// In order to simplify working with IP addresses (in binary) and their
	// netmasks, it is easier to ensure that the binary strings are padded
	// with zeros out to 32 characters - IP addresses are 32 bit numbers
	public static function decbin32 ($dec) {
		return str_pad(decbin($dec), 32, '0', STR_PAD_LEFT);
	}

	// ipv4_in_range
	// This function takes 2 arguments, an IP address and a "range" in several
	// different formats.
	// Network ranges can be specified as:
	// 1. Wildcard format:     1.2.3.*
	// 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
	// 3. Start-End IP format: 1.2.3.0-1.2.3.255
	// The function will return true if the supplied IP is within the range.
	// Note little validation is done on the range inputs - it expects you to
	// use one of the above 3 formats.
	public static function ipv4_in_range($ip, $range) {
		if (strpos($range, '/') !== false) {
			// $range is in IP/NETMASK format
			list($range, $netmask) = explode('/', $range, 2);
			if (strpos($netmask, '.') !== false) {
				// $netmask is a 255.255.0.0 format
				$netmask = str_replace('*', '0', $netmask);
				$netmask_dec = ip2long($netmask);
				return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
			} else {
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode('.', $range);
				while(count($x)<4) $x[] = '0';
				list($a,$b,$c,$d) = $x;
				$range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
				$range_dec = ip2long($range);
				$ip_dec = ip2long($ip);

				# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
				#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

				# Strategy 2 - Use math to create it
				$wildcard_dec = pow(2, (32-$netmask)) - 1;
				$netmask_dec = ~ $wildcard_dec;

				return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
			}
			} else {
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
				if (strpos($range, '*') !==false) { // a.b.*.* format
					// Just convert to A-B format by setting * to 0 for A and 255 for B
					$lower = str_replace('*', '0', $range);
					$upper = str_replace('*', '255', $range);
					$range = "$lower-$upper";
				}

				if (strpos($range, '-')!==false) { // A-B format
					list($lower, $upper) = explode('-', $range, 2);
					$lower_dec = (float)sprintf("%u",ip2long($lower));
					$upper_dec = (float)sprintf("%u",ip2long($upper));
					$ip_dec = (float)sprintf("%u",ip2long($ip));
					return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
				}
				return false;
			}
	}

	public static function ip2long6($ip) {
		if (substr_count ( $ip, '::' )) {
			$ip = str_replace ( '::', str_repeat ( ':0000', 8 - substr_count ( $ip, ':' ) ) . ':', $ip );
		}

		$ip = explode(':', $ip);
		$r_ip = '';

		foreach ($ip as $v) {
			$r_ip .= str_pad(base_convert($v, 16, 2), 16, 0, STR_PAD_LEFT);
		}

		return base_convert($r_ip, 2, 10);
	}

	// Get the ipv6 full format and return it as a decimal value.
	public static function get_ipv6_full($ip)
	{
		$pieces = explode ("/", $ip, 2);
		$left_piece = $pieces[0];
		$right_piece = isset($pieces[1]) ? $pieces[1] : '';

		// Extract out the main IP pieces
		$ip_pieces = explode("::", $left_piece, 2);
		$main_ip_piece = $ip_pieces[0];
		$last_ip_piece = $ip_pieces[1];

		// Pad out the shorthand entries.
		$main_ip_pieces = explode(":", $main_ip_piece);
		foreach($main_ip_pieces as $key=>$val) {
		$main_ip_pieces[$key] = str_pad($main_ip_pieces[$key], 4, "0", STR_PAD_LEFT);
		}

		// Check to see if the last IP block (part after ::) is set
		$last_piece = "";
		$size = count($main_ip_pieces);
		if (trim($last_ip_piece) != "") {
		$last_piece = str_pad($last_ip_piece, 4, "0", STR_PAD_LEFT);

		// Build the full form of the IPV6 address considering the last IP block set
		for ($i = $size; $i < 7; $i++) {
		$main_ip_pieces[$i] = "0000";
		}
		$main_ip_pieces[7] = $last_piece;
		}
		else {
			// Build the full form of the IPV6 address
			for ($i = $size; $i < 8; $i++) {
					$main_ip_pieces[$i] = "0000";
			}
		}
					// Rebuild the final long form IPV6 address
    	$final_ip = implode(":", $main_ip_pieces);

	    return self::ip2long6($final_ip);
	}

	// Determine whether the IPV6 address is within range.
	// $ip is the IPV6 address in decimal format to check if its within the IP range created by the cloudflare IPV6 address, $range_ip.
	// $ip and $range_ip are converted to full IPV6 format.
	// Returns true if the IPV6 address, $ip, is within the range from $range_ip. False otherwise.
	public static function ipv6_in_range($ip, $range_ip) {
		$pieces = explode ( "/", $range_ip, 2 );
		$left_piece = $pieces [0];
		$right_piece = $pieces [1];

		// Extract out the main IP pieces
		$ip_pieces = explode ( "::", $left_piece, 2 );
		$main_ip_piece = $ip_pieces [0];
		$last_ip_piece = $ip_pieces [1];

		// Pad out the shorthand entries.
		$main_ip_pieces = explode ( ":", $main_ip_piece );
		foreach ( $main_ip_pieces as $key => $val ) {
			$main_ip_pieces [$key] = str_pad ( $main_ip_pieces [$key], 4, "0", STR_PAD_LEFT );
		}

		// Create the first and last pieces that will denote the IPV6 range.
		$first = $main_ip_pieces;
		$last = $main_ip_pieces;

		// Check to see if the last IP block (part after ::) is set
		$last_piece = "";
		$size = count ( $main_ip_pieces );
		if (trim ( $last_ip_piece ) != "") {
			$last_piece = str_pad ( $last_ip_piece, 4, "0", STR_PAD_LEFT );

			// Build the full form of the IPV6 address considering the last IP block set
			for($i = $size; $i < 7; $i ++) {
				$first [$i] = "0000";
				$last [$i] = "ffff";
			}
			$main_ip_pieces [7] = $last_piece;
		} else {
			// Build the full form of the IPV6 address
			for($i = $size; $i < 8; $i ++) {
				$first [$i] = "0000";
				$last [$i] = "ffff";
			}
		}

		// Rebuild the final long form IPV6 address
		$first = self::ip2long6 ( implode ( ":", $first ) );
		$last = self::ip2long6 ( implode ( ":", $last ) );
		$in_range = ($ip >= $first && $ip <= $last);

		return $in_range;
	}
}


?>