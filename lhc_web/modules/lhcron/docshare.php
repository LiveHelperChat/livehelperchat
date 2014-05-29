<?php 

/**
 * php cron.php -s site_admin -c cron/docshare
 *
 * Run every 10 minits or so. On this cron depends documents conversion
 *
 * */

foreach (erLhcoreClassModelDocShare::getList(array('filter' => array('converted' => 0))) as $doc) {
	echo "Converting - ",$doc->name," | ",$doc->id,"\n";
	erLhcoreClassDocShare::makeConversion($doc,true);
}

echo "Finished conversion\n";

?>