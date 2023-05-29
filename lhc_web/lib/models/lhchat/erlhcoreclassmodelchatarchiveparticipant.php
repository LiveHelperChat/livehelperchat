<?php

/**
 * Just extends class to support proper class loading
 * */
class erLhcoreClassModelChatArchiveParticipant extends \LiveHelperChat\Models\LHCAbstract\ChatParticipant  {

    public static $dbTable = null;

    use erLhcoreClassDBTrait;
}

?>