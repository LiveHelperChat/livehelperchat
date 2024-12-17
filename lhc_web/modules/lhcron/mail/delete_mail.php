<?php

/*
 * php cron.php -s site_admin -c cron/mail/delete_mail
 *
 * Deletes mails in the background based on filter setup. In this case it just schedules records to be deleted.
 * To finish deletion process you have to run php cron.php -s site_admin -c cron/mail/delete_mail_item
 * */
$fp = fopen("cache/cron_mail_delete_mail.lock", "w+");

// Gain the lock
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Couldn't get the lock! Another process is already running\n";
    fclose($fp);
    return;
} else {
    echo "Lock acquired. Starting process!\n";
}

foreach (\LiveHelperChat\Models\mailConv\Delete\DeleteFilter::getList([
    'filterin' => ['status' => [
        \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_PENDING,
        \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_IN_PROGRESS,
    ]]
]) as $filterData) {

    // Lock filter object
    $db = ezcDbInstance::get();
    $db->beginTransaction();
        $filterData->syncAndLock();
        if ($filterData->status == \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_PENDING || ($filterData->started_at < (time() - 1800) && $filterData->status == \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_IN_PROGRESS)) {
            $filterData->status = \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_IN_PROGRESS;
            $filterData->started_at = time();
        } else {
            $db->commit();
            continue;
        }
        $filterData->updateThis(['update' => ['status','started_at']]);
    $db->commit();

    $filterSQL = json_decode($filterData->filter,true);
    $has_items = true;

    // Schedule records for deletion
    for ($i = 1; $i < 200; $i++) {
        $filterSQL['limit'] = 40;
        $filterSQL['offset'] = 0;
        $filterSQL['filtergt']['id'] = $filterData->last_id;
        $filterSQL['sort'] = '`id` ASC';

        $itemsToDelete = erLhcoreClassModelMailconvConversation::getList($filterSQL);

        if (empty($itemsToDelete)) {
            $filterData->status = \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_FINISHED;
            $has_items = false;
            echo "Finished items - ",$filterData->id,"\n";
            break;
        } else {
            foreach ($itemsToDelete as $item) {
                $deleteItem = new \LiveHelperChat\Models\mailConv\Delete\DeleteItem();
                $deleteItem->filter_id = $filterData->id;
                $deleteItem->conversation_id = $item->id;
                $deleteItem->saveThis();
                $filterData->last_id = $item->id;
            }
            $filterData->updateThis(['update' => ['last_id']]);
        }
    }

    if ($has_items == true) {
        $filterData->status = \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_PENDING;
    }

    $filterData->finished_at = time();
    $filterData->updateThis(['update' => ['status','last_id','finished_at']]);
}

flock($fp, LOCK_UN); // release the lock
fclose($fp);

?>
