<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

erLhcoreClassLog::write("Message debug" ,
        ezcLog::SUCCESS_AUDIT,
        array(
          'source' => 'CLASS',
          'category' => 'updateInternal',
          'line' => __LINE__,
          'file' => __FILE__,
          'object_id' => 4
      )
);

?>