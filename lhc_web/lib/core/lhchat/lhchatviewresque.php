<?php

class erLhcoreClassViewResque {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        $viewId = $this->args['view_id'];

        $search = erLhAbstractModelSavedSearch::fetch($viewId);
        
        if ($search instanceof erLhAbstractModelSavedSearch && $search->updated_at < time() - 2 * 60 && $search->requested_at > time() - 5 * 60) {
            self::updateView($search);
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
            throw new Exception('Unknown view - '.$search->scope);
        }
    }
    
}

?>