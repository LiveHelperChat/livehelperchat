<?php

#[\AllowDynamicProperties]
class erLhcoreClassMailConvWorker {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        // Limit SELECT query execution time (including lock wait time) to 15 seconds
        try {
            $db->query("SET SESSION max_execution_time=15000"); // MySQL (milliseconds)
        } catch (Exception $e) {}

        try {
            $db->query("SET SESSION max_statement_time=15");    // MariaDB (seconds)
        } catch (Exception $e) {}

        // Limit maximum wait time for acquiring an InnoDB lock to 15 seconds
        try {
            $db->query("SET SESSION innodb_lock_wait_timeout=15");
        } catch (Exception $e) {}

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database'));

            $instance = \erLhcoreClassModelInstance::fetch($this->args['inst_id']);
            \erLhcoreClassInstance::$instanceChat = $instance;

            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        $mailboxId = $this->args['mailbox_id'];
        $mailbox = erLhcoreClassModelMailconvMailbox::fetch($mailboxId);

        if (!is_object($mailbox)) {
            return;
        }

        $params = array();

        // Mailbox
        if (isset($this->args['ignore_timeout'])) {
            $params['ignore_timeout'] = true;
        }

        // 1. Connection timeout (for imap_open)
        imap_timeout(IMAP_OPENTIMEOUT, 360);
        // 2. Read timeout (for reading data from the server)
        imap_timeout(IMAP_READTIMEOUT, 360);
        // 3. Write timeout (for sending data to the server)
        imap_timeout(IMAP_WRITETIMEOUT, 360);
        // Close timeout.
        imap_timeout(IMAP_CLOSETIMEOUT, 360);

        erLhcoreClassMailconvParser::syncMailbox($mailbox, $params);
    }

}

?>