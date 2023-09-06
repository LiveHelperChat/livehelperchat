<?php
/**
 * Just extends class to support proper class loading
 * */
#[\AllowDynamicProperties]
class erLhcoreClassModelGroupMsgArchive extends erLhcoreClassModelGroupMsg {

    public static $dbTable = null;

    use erLhcoreClassDBTrait;
}

?>