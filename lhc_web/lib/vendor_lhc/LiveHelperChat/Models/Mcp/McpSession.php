<?php

namespace LiveHelperChat\Models\Mcp;
#[\AllowDynamicProperties]
class McpSession {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_mcp_session';

    public static $dbTableId = 'session_id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'session_id' => $this->session_id,
            'data' => $this->data,
            'ctime' => $this->ctime,
            'utime' => $this->utime,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->session_id;
    }

    public $session_id = '';
    public $data = '';
    public $ctime = 0;
    public $utime = 0;
}

?>
