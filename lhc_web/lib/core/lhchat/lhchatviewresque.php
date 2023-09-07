<?php
#[\AllowDynamicProperties]
class erLhcoreClassViewResque {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        $viewId = $this->args['view_id'];

        $search = erLhAbstractModelSavedSearch::fetch($viewId);
        
        if ($search instanceof erLhAbstractModelSavedSearch && $search->updated_at < time() - 2 * 60 && $search->requested_at > time() - 5 * 60) {

            $startTime = microtime();

            self::updateView($search);

            erLhcoreClassViewResque::logSlowView($startTime, microtime(), $search);

        }
    }

    public static function logSlowView( $start_time, $end_time, $savedSearch )
    {
        $start = explode(' ', $start_time);
        $end = explode(' ', $end_time);
        $time = $end[0] + $end[1] - $start[0] - $start[1];

        if ($time > 1) {
            erLhcoreClassLog::write($time,
                ezcLog::SUCCESS_AUDIT,
                array(
                    'source' => 'View',
                    'category' => 'slow_view',
                    'line' => __LINE__,
                    'file' => __FILE__,
                    'object_id' => $savedSearch->id
                )
            );
        }

    }

    public static function updateView($search)
    {
        if ($search->scope == 'chat') {
            $filterSearch = $search->params_array['filter'];

            if ($search->days > 0) {
                $filterSearch['filtergte']['time'] = time() - $search->days * 24 * 3600;
            }

            $totalRecords = erLhcoreClassModelChat::getCount($filterSearch);

            $search->updated_at = time();

            if ($search->total_records != $totalRecords) {
                $search->total_records = $totalRecords;
                $search->updateThis(['update' => ['updated_at','total_records']]);
            } else {
                $search->updateThis(['update' => ['updated_at']]);
            }
        } else {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('views.update_vew', array(
                'search' => $search
            ));
        }
    }
    
}

?>