<?php

namespace LiveHelperChat\mailConv\helpers;

class MergeHelper
{
    public static function merge(\erLhcoreClassModelMailconvConversation $target, array $source, array $paramsExecution = array())
    {
        if (empty($source)) {
            throw new \Exception('Please choose at-least one source mail!');
        }

        if (key_exists($target->id, $source)) {
            throw new \Exception('Source and target mails should be different!');
        }

        $db = \ezcDbInstance::get();

        try {

            $db->beginTransaction();
            $sourceIds = [];

            foreach ($source as $sourceMail) {
                $sourceIds[] = $sourceMail->id;
                foreach (\erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $sourceMail->id]]) as $mailMessage) {
                    // Update conversation
                    $mailMessage->conversation_id_old = $mailMessage->conversation_id;
                    $mailMessage->conversation_id = $target->id;
                    $mailMessage->updateThis(['update' => ['conversation_id_old','conversation_id']]);

                    // Update files association
                    foreach (\erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $mailMessage->id]]) as $messageFile) {
                        $messageFile->conversation_id = $mailMessage->conversation_id;
                        $messageFile->updateThis(['update' => ['conversation_id']]);
                    }
                }

                // Remove merge conversation record
                $sourceMail->removeThis();
            }

            $db->commit();

            $target->total_messages = \erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id' => $target->id]]);
            $target->updateThis(['update' => ['total_messages']]);

            // Merge action message
            if (isset($paramsExecution['user_id'])) {
                \erLhcoreClassMailconvWorkflow::logInteraction($paramsExecution['name_support'] . ' [' . $paramsExecution['user_id'] .'] ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has merge merged') . ' ' . implode(', ', $sourceIds) . ' ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','into') . ' ' . $target->id, $paramsExecution['name_support'], $target->id);
            }

        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    public static function unMerge($message, array $paramsExecution = array()) {

        $messages = \erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id_old' => $message->conversation_id_old]]);

        $conversationOld = \erLhcoreClassModelMailconvConversation::fetch($message->conversation_id);

        $newConversationId = $message->conversation_id_old;

        reset($messages);

        $messageFirst = current($messages);
        $messageLast = end($messages);

        $db = \ezcDbInstance::get();

        try {
            $db->beginTransaction();

            $conversation = new \erLhcoreClassModelMailconvConversation();

            foreach ($messages as $oldMessage) {
                $oldMessage->conversation_id_old = 0;
                $oldMessage->conversation_id = $newConversationId;
                $oldMessage->updateThis(['update' => ['conversation_id','conversation_id_old']]);

                // Update files association
                foreach (\erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $oldMessage->id]]) as $messageFile) {
                    $messageFile->conversation_id = $oldMessage->conversation_id;
                    $messageFile->updateThis(['update' => ['conversation_id']]);
                }

                // Update attachments
                if ($conversation instanceof \erLhcoreClassModelMailconvConversation &&
                    $conversation->has_attachment != \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX &&
                    $oldMessage->has_attachment != \erLhcoreClassModelMailconvMessage::ATTACHMENT_EMPTY
                ) {
                    if (
                        ($oldMessage->has_attachment == \erLhcoreClassModelMailconvMessage::ATTACHMENT_MIX) ||
                        (
                            $conversation->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE &&
                            $oldMessage->has_attachment == \erLhcoreClassModelMailconvMessage::ATTACHMENT_FILE
                        ) ||
                        (
                            $conversation->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE &&
                            $oldMessage->has_attachment == \erLhcoreClassModelMailconvMessage::ATTACHMENT_INLINE
                        )
                    ) {
                        $conversation->has_attachment = \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX;
                    } elseif ($conversation->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) {
                        $conversation->has_attachment = $oldMessage->has_attachment;
                    }
                }
            }

            $matchAttributes = ['dep_id','user_id','from_name','from_address','date','udate','mailbox_id','undelivered','lang','mailbox_id','ctime'];

            foreach ($matchAttributes as $matchAttribute) {
                $conversation->{$matchAttribute} = $messageFirst->{$matchAttribute};
            }

            $matchAttributesConversation = ['phone','mail_variables','match_rule_id','priority','start_type','opened_at','follow_up_id'];
            foreach ($matchAttributesConversation as $matchAttribute) {
                $conversation->{$matchAttribute} = $conversationOld->{$matchAttribute};
            }

            // Logical attributes
            $conversation->status = $conversationOld->status;
            $conversation->body = $messageLast->alt_body != '' ? $messageLast->alt_body : strip_tags($messageLast->body);
            $conversation->subject = $messageLast->subject;
            $conversation->total_messages = count($messages);
            $conversation->pending_sync = 0;
            $conversation->last_message_id = $messageLast->id;
            $conversation->message_id = $messageFirst->id;
            $conversation->conv_duration = \erLhcoreClassChat::getCount(['filter' => ['conversation_id' => $newConversationId]],'lhc_mailconv_msg','SUM(conv_duration)');
            $conversation->pnd_time = time();
            $conversation->accept_time = 0;
            $conversation->tslasign = 0;
            $conversation->cls_time = 0;
            $conversation->wait_time = 0;

            // In case we want to have independent priority
            /*
             * $filteredMatchingRules = array();
            foreach (erLhcoreClassModelMailconvMatchRule::getList(['filternot' => ['dep_id' => 0], 'filter' => ['active' => 1]]) as $matchingRule) {
                if (in_array($conversation->mailbox_id, $matchingRule->mailbox_ids)) {
                    $filteredMatchingRules[] = $matchingRule;
                }
            }
            $filteredPriorityMatchingRules = array();
            foreach (erLhcoreClassModelMailconvMatchRule::getList(['filter' => ['dep_id' => 0, 'active' => 1]]) as $matchingRule) {
                if (in_array($conversation->mailbox_id, $matchingRule->mailbox_ids)) {
                    $filteredPriorityMatchingRules[] = $matchingRule;
                }
            }

            $matchingRuleSelected = \erLhcoreClassMailconvParser::getMatchingRuleByMessage($messageFirst, $filteredMatchingRules);

            if (!($matchingRuleSelected instanceof \erLhcoreClassModelMailconvMatchRule)) {
                throw new \Exception('Matching rule could not be found!');
            }

            $priorityConversation = $matchingRuleSelected->priority;

            // Rule without department has higher priority
            $matchingPriorityRuleSelected = \erLhcoreClassMailconvParser::getMatchingRuleByMessage($message, $filteredPriorityMatchingRules);
            if ($matchingPriorityRuleSelected instanceof erLhcoreClassModelMailconvMatchRule && $matchingPriorityRuleSelected->priority > $priorityConversation) {
                $priorityConversation = $matchingPriorityRuleSelected->priority;
            }

            $conversation->match_rule_id = $matchingRuleSelected->id;
            $conversation->priority = $priorityConversation;*/

            // Save without triggering internal events
            \erLhcoreClassModelMailconvConversation::getSession()->save($conversation);

            // Switch ID to old one
            $stmt = $db->prepare("UPDATE `lhc_mailconv_conversation` SET `id` = :id WHERE `id` = :new_id");
            $stmt->bindValue(':id',$newConversationId,\PDO::PARAM_INT);
            $stmt->bindValue(':new_id',$conversation->id,\PDO::PARAM_INT);
            $stmt->execute();

            $conversation->id = $newConversationId;

            // remarks - We don't have this
            $db->commit();

            // Just to trigger internal update event
            $conversation->updateThis(['update' => ['total_messages']]);

            // Update total messages of old conversation
            $conversationOld->total_messages = \erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id' => $conversationOld->id]]);
            $conversationOld->updateThis(['update' => ['total_messages']]);

            if (isset($paramsExecution['user_id'])) {
                \erLhcoreClassMailconvWorkflow::logInteraction($paramsExecution['name_support'] . ' [' . $paramsExecution['user_id'] .'] ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has un-merged') . ' ' . $conversationOld->id . ' ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','into') . ' ' . $newConversationId, $paramsExecution['name_support'], $newConversationId);
                \erLhcoreClassMailconvWorkflow::logInteraction($paramsExecution['name_support'] . ' [' . $paramsExecution['user_id'] .'] ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has un-merged') . ' ' . $conversationOld->id . ' ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','into') . ' ' . $newConversationId, $paramsExecution['name_support'], $conversationOld->id);
            }

        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}
