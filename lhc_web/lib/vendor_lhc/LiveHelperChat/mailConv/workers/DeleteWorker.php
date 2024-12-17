<?php
namespace LiveHelperChat\mailConv\workers;
#[\AllowDynamicProperties]
class DeleteWorker
{
    public function perform()
    {
        $db = \ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database'));

            $instance = \erLhcoreClassModelInstance::fetch($this->args['inst_id']);
            \erLhcoreClassInstance::$instanceChat = $instance;

            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT `conversation_id`,`filter_id` FROM `lhc_mailconv_delete_item` WHERE `status` = 0 LIMIT :limit FOR UPDATE ');
            $stmt->bindValue(':limit',20,\PDO::PARAM_INT);
            $stmt->execute();
            $chatsId = [];
            $chatsIdFilter = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $itemArchive) {
                $chatsId[] = $itemArchive['conversation_id'];
                $chatsIdFilter[$itemArchive['conversation_id']] = $itemArchive['filter_id'];
            }
            
        } catch (\Exception $e) {
            // Someone is already processing. So we just ignore and retry later
            return;
        }

        $filters = \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::getList(['filterin' => ['id' => array_unique($chatsIdFilter)]]);

        $archiveIds = [];
        foreach ($filters as $filter) {
            if ($filter->archive_id > 0) {
                $archiveIds[] = $filter->archive_id;
            }
        }

        $archives = [];
        if (!empty($archiveIds)) {
            $archives = \LiveHelperChat\Models\mailConv\Archive\Range::getList(['filterin' => ['id' => array_unique($archiveIds)]]);
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
                        if (isset($filters[$chatsIdFilter[$chat->id]]) && isset($archives[$filters[$chatsIdFilter[$chat->id]]->archive_id])) {
                            $archives[$filters[$chatsIdFilter[$chat->id]]->archive_id]->process([$chat], ['ignore_imap' => $filters[$chatsIdFilter[$chat->id]]->delete_policy === 1]);
                        } else {
                            $chat->ignore_imap = $filters[$chatsIdFilter[$chat->id]]->delete_policy === 1;
                            $chat->removeThis();
                        }
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

                // Update processed_records stats
                $statUpdate = [];
                foreach ($chatsIdFilter as $conversationId => $filterId) {
                    if (!isset($statUpdate[$filterId])) {
                        $statUpdate[$filterId] = 0;
                    }
                    $statUpdate[$filterId]++;
                }

                foreach ($statUpdate as $filterId => $recordsProcessed) {
                    $stmt = $db->prepare('UPDATE `lhc_mailconv_delete_filter` SET processed_records = processed_records + '. (int)$recordsProcessed .' WHERE `id` = ' . $filterId);
                    $stmt->execute();
                }

            }
        } else {
            $db->rollback();
        }

        if (isset($this->args['is_background']) && count($chatsId) >= 20 && \erLhcoreClassRedis::instance()->llen('resque:queue:lhc_mailconv_delete') <= 4) {
            $inst_id = class_exists('erLhcoreClassInstance') ? \erLhcoreClassInstance::$instanceChat->id : 0;
            \erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv_delete', '\LiveHelperChat\mailConv\workers\DeleteWorker', array('inst_id' => $inst_id, 'is_background' => true));
        }
    }
}
