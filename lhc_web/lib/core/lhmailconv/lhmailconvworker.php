<?php

class erLhcoreClassMailConvWorker {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

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