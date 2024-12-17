<?php

class erLhcoreClassMailConvWorker {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database'));

            $instance = \erLhcoreClassModelInstance::fetch($this->args['inst_id']);
            \erLhcoreClassInstance::$instanceChat = $instance;

            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        $mailboxId = $this->args['mailbox_id'];
        $mailbox = erLhcoreClassModelMailconvMailbox::fetch($mailboxId);

        $params = array();

        // Mailbox
        if (isset($this->args['ignore_timeout'])) {
            $params['ignore_timeout'] = true;
        }

        erLhcoreClassMailconvParser::syncMailbox($mailbox, $params);
    }

}

?>