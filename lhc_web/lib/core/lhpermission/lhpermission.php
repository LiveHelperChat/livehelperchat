<?php

class erLhcoreClassPermission
{

    function __construct() {}

    public static function getSession()
    {
        if (!isset(self::$persistentSession)) {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager('./pos/lhpermission')
            );
        }
        return self::$persistentSession;
    }

    public static function resetSession()
    {
        self::$persistentSession = null;
    }

    private static $persistentSession;
}