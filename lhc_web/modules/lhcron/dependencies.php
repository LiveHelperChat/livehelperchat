<?php

/**
 * php cron.php -s site_admin -c cron/dependencies
 *
 * Run every 1 hour or so.
 *
 * */

echo "Extensions check\n\n";

echo "Is the php_curl extension installed - " . (extension_loaded ('curl' ) ? 'Yes' : 'No')."\n";
echo "Is the mbstring extension installed - " . (extension_loaded ('mbstring' ) ? 'Yes' : 'No')."\n";
echo "Is the php-pdo extension installed - " . (extension_loaded ('pdo_mysql' ) ? 'Yes' : 'No')."\n";
echo "Is the gd extension installed - " . (extension_loaded ('gd' ) ? 'Yes' : 'No')."\n";
echo "Is the json extension installed - " . (function_exists ('json_encode' ) ? 'Yes' : 'No')."\n";
echo "Is the bcmath extension installed - " . (extension_loaded ('bcmath' ) ? 'Yes' : 'No, GEO detection will be disabled')."\n";
echo "Is the php-xml extension installed - " . (function_exists ('simplexml_load_string' ) ? 'Yes' : 'No')."\n";
echo "Is the fileinfo extension installed - " . (extension_loaded ('fileinfo' ) ? 'Yes' : 'No')."\n";
echo "Is the ldap extension installed - " . (function_exists ('ldap_search' ) ? 'Yes' : 'No, required only if you use `lhldap` extension')."\n";
echo "Is the imap extension installed - " . (extension_loaded ('imap' ) ? 'Yes' : 'No')."\n";
echo "Is the redis extension installed - " . (extension_loaded ('phpiredis' ) ? 'Yes' : 'No')."\n";
echo "Is the soap extension installed - " . (extension_loaded ('soap' ) ? 'Yes' : 'No')."\n";
echo "Is the zlib extension installed - " . (extension_loaded ('zlib' ) ? 'Yes' : 'No')."\n";
echo "Is the zip extension installed - " . (extension_loaded ('zip' ) ? 'Yes' : 'No')."\n";