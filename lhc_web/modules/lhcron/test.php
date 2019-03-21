<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */


$reader = new GeoIp2\Database\Reader('var/external/geoip/GeoLite2-Country.mmdb');
$countryData = $reader->country('45.65.244.130');

print_r($countryData->raw);

/*$normalizedObject = new stdClass();
$normalizedObject->country_code = isset($countryData->raw['country']) ? strtolower($countryData->raw['country']['iso_code']) : '';
$normalizedObject->country_name = isset($countryData->raw['country']) ? $countryData->raw['country']['names']['en'] : '';
$normalizedObject->city = '';
$normalizedObject->lat = '';
$normalizedObject->lon = '';*/



/*erLhcoreClassLog::write("Message debug" ,
        ezcLog::SUCCESS_AUDIT,
        array(
          'source' => 'CLASS',
          'category' => 'updateInternal',
          'line' => __LINE__,
          'file' => __FILE__,
          'object_id' => 4
      )
);*/

?>