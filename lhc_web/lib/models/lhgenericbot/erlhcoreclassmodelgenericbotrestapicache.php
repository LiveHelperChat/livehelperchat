<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelGenericBotRestAPICache {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_rest_api_cache';

    public static $dbTableId = 'hash';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array (
            'hash' => $this->hash,
            'rest_api_id' => $this->rest_api_id,
            'response' => $this->response,
            'ctime' => $this->ctime,
        );
    }

    public function beforeSave($params = array())
    {
        if ($this->ctime == 0) {
            $this->ctime = time();
        }
    }

    public $hash = '';
    public $response = '';
    public $ctime = 0;
    public $rest_api_id = 0; // 0 - Translation cache
}
