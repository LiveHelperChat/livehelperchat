<?php

class erLhcoreClassMailConvMailingWorker {

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

        $db->beginTransaction();
        $campaign = erLhcoreClassModelMailconvMailingCampaign::fetchAndLock($this->args['campaign_id']);

        // Campaign was terminated in the middle of process
        if ($campaign->enabled == 0) {
            return;
        }

        $campaign->status = erLhcoreClassModelMailconvMailingCampaign::STATUS_IN_PROGRESS;
        $campaign->updateThis(['update' => ['status']]);
        $db->commit();

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT `id` FROM lhc_mailconv_mailing_campaign_recipient WHERE campaign_id = :campaign_id AND status = :status LIMIT :limit FOR UPDATE ');
            $stmt->bindValue(':limit',20,PDO::PARAM_INT);
            $stmt->bindValue(':status',erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING,PDO::PARAM_INT);
            $stmt->bindValue(':campaign_id',$campaign->id,PDO::PARAM_INT);
            $stmt->execute();
            $recipientsId = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            // Someone is already processing. So we just ignore and retry later
            return;
        }

        if (!empty($recipientsId)) {
            // Delete indexed chat's records
            $stmt = $db->prepare('UPDATE `lhc_mailconv_mailing_campaign_recipient` SET status = :status WHERE id IN (' . implode(',', $recipientsId) . ')');
            $stmt->bindValue(':status',erLhcoreClassModelMailconvMailingCampaignRecipient::IN_PROGRESS,PDO::PARAM_INT);
            $stmt->execute();
            $db->commit();

            $recipients = erLhcoreClassModelMailconvMailingCampaignRecipient::getList(array('filterin' => array('id' => $recipientsId)));

            if (!empty($recipients)) {
                foreach ($recipients as $recipient) {

                    $output = self::sendEmail($recipient, $campaign);

                    if ($output['send'] == true) {
                        $recipient->status = erLhcoreClassModelMailconvMailingCampaignRecipient::SEND;
                    } else {
                        $recipient->status = erLhcoreClassModelMailconvMailingCampaignRecipient::FAILED;
                    }

                    $recipient->log = json_encode($output);
                    $recipient->opened_at = 0;
                    $recipient->send_at = time();
                    $recipient->updateThis(['update' => ['log','status','send_at']]);
                }
                
                if (count($recipients) == 20 && erLhcoreClassRedis::instance()->llen('resque:queue:lhc_mailing') <= 4) {
                    $inst_id = class_exists('erLhcoreClassInstance') ? \erLhcoreClassInstance::$instanceChat->id : 0;
                    erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailing', 'erLhcoreClassMailConvMailingWorker', array('inst_id' => $inst_id, 'campaign_id' => $campaign->id));
                }
            }
        } else {

            // Finish previous
            $db->commit();

            $db->beginTransaction();
            $campaign = erLhcoreClassModelMailconvMailingCampaign::fetchAndLock($this->args['campaign_id']);
            $campaign->status = erLhcoreClassModelMailconvMailingCampaign::STATUS_FINISHED;
            $campaign->updateThis(['update' => ['status']]);
            $db->commit();
        }
    }

    public static function sendEmail($recipient, $campaign) {

        $itemRecipientData = new erLhcoreClassModelMailconvMessage();

        // Custom mailbox per recipient
        if ($recipient->mailbox_front == '') {
            $mailbox = $itemRecipientData->mailbox = $campaign->mailbox;
            $itemRecipientData->mailbox_id = $campaign->mailbox_id;
        } else {
            $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['mail' => $recipient->mailbox_front]]);
            if (!($mailbox instanceof erLhcoreClassModelMailconvMailbox)) {
                $mailbox = $itemRecipientData->mailbox = $campaign->mailbox;
                $itemRecipientData->mailbox_id = $campaign->mailbox_id;
            } else {
                $itemRecipientData->mailbox = $mailbox;
                $itemRecipientData->mailbox_id = $mailbox->id;
            }
        }

        $itemRecipientData->to_data = $campaign->reply_email;
        $itemRecipientData->reply_to_data = $campaign->reply_name;

        $itemRecipientData->body = $campaign->body;
        $itemRecipientData->subject = $campaign->subject;

        $itemRecipientData->from_address = $recipient->recipient;
        $itemRecipientData->from_name = $recipient->recipient_name;

        $conv = new erLhcoreClassModelMailconvConversation();
        $conv->dep_id = $mailbox->dep_id;

        $recipientData = new stdClass();
        $recipientData->email = $recipient->recipient;
        $recipientData->name = $recipient->recipient_name;
        $recipientData->attr_str_1 = $recipient->recipient_attr_str_1;
        $recipientData->attr_str_2 = $recipient->recipient_attr_str_2;
        $recipientData->attr_str_3 = $recipient->recipient_attr_str_3;
        $recipientData->attr_str_4 = $recipient->recipient_attr_str_4;
        $recipientData->attr_str_5 = $recipient->recipient_attr_str_5;
        $recipientData->attr_str_6 = $recipient->recipient_attr_str_6;

        $itemRecipientData->body = erLhcoreClassGenericBotWorkflow::translateMessage($itemRecipientData->body, array('chat' => $conv, 'args' => ['recipient' => $recipientData, 'mail' => $conv, 'current_user' => $campaign->user]));
        $itemRecipientData->subject = erLhcoreClassGenericBotWorkflow::translateMessage($itemRecipientData->subject, array('chat' => $conv, 'args' => ['recipient' => $recipientData, 'mail' => $conv, 'current_user' => $campaign->user]));

        // Send as active
        if ($campaign->as_active == 1) {
            $itemRecipientData->status = erLhcoreClassModelMailconvMessage::STATUS_ACTIVE;
        }

        $itemRecipientData->custom_headers = [
            'X-LHC-RCP' => $recipient->id
        ];

        $output = [];

        // erLhcoreClassModelMailconvMailingCampaign::OWNER_CREATOR = 0
        $userId = $campaign->user_id;

        if ($campaign->owner_logic == erLhcoreClassModelMailconvMailingCampaign::OWNER_DEFAULT) {
            $userId = 0;
        } elseif ($campaign->owner_logic == erLhcoreClassModelMailconvMailingCampaign::OWNER_USER) {
            $userId = $campaign->owner_user_id;
        }

        erLhcoreClassMailconvValidator::sendEmail($itemRecipientData, $output, $userId);

        return $output;
    }

}

?>