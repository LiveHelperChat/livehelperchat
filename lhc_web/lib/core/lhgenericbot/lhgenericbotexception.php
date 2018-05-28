<?php

class erLhcoreClassGenericBotException extends Exception {

    private $paramsException = array();

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null, $params = array()) {
        // some code

        $this->paramsException = $params;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    public function getContent() {
        return $this->paramsException;
    }
}

?>