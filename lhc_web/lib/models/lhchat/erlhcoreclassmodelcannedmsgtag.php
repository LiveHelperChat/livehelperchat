<?php

class erLhcoreClassModelCannedMsgTag
{
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_canned_msg_tag';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
    
    public static $dbSortOrder = 'DESC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'tag' => $this->tag
        );
    }

    public function __get($var)
    {
        switch ($var) {
             
            default:
                break;
        }
    }
       
    private $replaceData = array();

    public $id = null;

    public $tag = '';    
    public $cnt = 0;    
}

?>