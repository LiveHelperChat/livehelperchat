<?php

class erLhcoreClassChatStatsResque {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if ($this->args['type'] == 'dep') {
            $dep = erLhcoreClassModelDepartament::fetch($this->args['id']);
            erLhcoreClassChat::updateDepartmentStats($dep, ['resque' => true]);
        }
    }

}

?>