<?php
namespace LiveHelperChat\mailConv\workers;
#[\AllowDynamicProperties]
class DeleteWorker
{
    public function perform()
    {
        $db = \ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT `conversation_id` FROM `lhc_mailconv_delete_item` WHERE `status` = 0 LIMIT :limit FOR UPDATE ');
            $stmt->bindValue(':limit',20,\PDO::PARAM_INT);
            $stmt->execute();
            $chatsId = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            // Someone is already processing. So we just ignore and retry later
            return;
        }

        if (!empty($chatsId)) {
            // Delete indexed chat's records
            $stmt = $db->prepare('UPDATE `lhc_mailconv_delete_item` SET `status` = 1 WHERE `conversation_id` IN (' . implode(',', $chatsId) . ')');
            $stmt->execute();
            $db->commit();

            $chats = \erLhcoreClassModelMailconvConversation::getList(array('filterin' => array('id' => $chatsId)));

            if (!empty($chats)) {
                try {
                    foreach ($chats as $chat) {
                        $chat->removeThis();
                    }
                } catch (\Exception $e) {
                    // Try to log error to DB
                    try {
                        \erLhcoreClassLog::write( implode(',',$chatsId) . "\n" . $e->getTraceAsString() . "\n" . $e->getMessage(),
                            \ezcLog::SUCCESS_AUDIT,
                            array(
                                'source' => 'lhc',
                                'category' => 'resque_fatal',
                                'line' => 0,
                                'file' => 0,
                                'object_id' => 0
                            )
                        );
                    } catch (\Exception $e) {

                    }
                    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                    return;
                }
            }
            if (!empty($chatsId)) {
                $stmt = $db->prepare('DELETE FROM `lhc_mailconv_delete_item` WHERE `conversation_id` IN (' . implode(',', $chatsId) . ')');
                $stmt->execute();
            }
        } else {
            $db->rollback();
        }

        if (isset($this->args['is_background']) && count($chatsId) >= 20 && \erLhcoreClassRedis::instance()->llen('resque:queue:lhc_mailconv_delete') <= 4) {
            \erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv_delete', '\LiveHelperChat\mailConv\workers\DeleteWorker', array('is_background' => true));
        }
    }
}
