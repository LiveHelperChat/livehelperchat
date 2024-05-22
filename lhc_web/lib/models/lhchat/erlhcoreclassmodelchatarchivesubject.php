<?php

/**
 * Just extends class to support proper class loading
 * */
class erLhAbstractModelChatArchiveSubject extends erLhAbstractModelSubjectChat  {

    public static $dbTable = null;

    use erLhcoreClassDBTrait;
}

?>