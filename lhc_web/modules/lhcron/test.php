<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

use Pachico\Magoo\Magoo;

$magoo = new Magoo();
$magoo->pushCreditCardMask()
    ->pushEmailMask()
    ->pushByRegexMask('/(email)+/m');

$mySensitiveString = 'Player posts 1234567812345678, 5168-8922-0218-9400 or 1234-5678-1234-5678  5168892202189400 Agents will see this 1234********5678';

echo $magoo->getMasked($mySensitiveString);

?>