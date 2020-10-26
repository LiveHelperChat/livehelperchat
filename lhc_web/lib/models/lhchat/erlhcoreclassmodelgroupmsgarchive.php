<?php

/**
 * Just extends class to support proper class loading
 * */
class erLhcoreClassModelGroupMsgArchive extends erLhcoreClassModelGroupMsg {

    public static $dbTable = null;

    use erLhcoreClassDBTrait;
}

?>