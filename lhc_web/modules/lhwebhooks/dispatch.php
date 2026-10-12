<?php

if (ezcInputForm::hasPostData()) {

    // Debug helper must not be reachable through CSRF from a third party page
    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        http_response_code(403);
        echo 'Invalid CSRF Token';
        exit;
    }

    try {

        if (!isset($_POST['event_name'])) {
            throw new Exception('Event name is required.');
        }

        /**
         * Call a method of a class by its name
         *
         * @param object|string $classOrObject The class name or object instance
         * @param string $methodName The name of the method to call
         * @param array $arguments Arguments to pass to the method (optional)
         * @return mixed The return value of the called method
         * @throws ReflectionException If the method doesn't exist
         */
        function callMethodByName($classOrObject, $methodName, array $arguments = []) {
            // Check if we have an object or a class name
            if (is_string($classOrObject) && class_exists($classOrObject)) {
                // Create a new reflection class for the class name
                $reflection = new ReflectionClass($classOrObject);

                // Check if the method exists
                if (!$reflection->hasMethod($methodName)) {
                    throw new ReflectionException("Method '$methodName' does not exist in class '$classOrObject'");
                }

                // Get the method
                $method = $reflection->getMethod($methodName);

                // Check if the method is static
                if ($method->isStatic()) {
                    // Call static method
                    return $method->invokeArgs(null, $arguments);
                } else {
                    // Create a new instance of the class and call the method
                    $instance = $reflection->newInstance();
                    return $method->invokeArgs($instance, $arguments);
                }
            } elseif (is_object($classOrObject)) {
                // Create a reflection class for the object
                $reflection = new ReflectionClass($classOrObject);

                // Check if the method exists
                if (!$reflection->hasMethod($methodName)) {
                    throw new ReflectionException("Method '$methodName' does not exist in " . get_class($classOrObject));
                }

                // Get the method
                $method = $reflection->getMethod($methodName);

                // Call the method on the provided object
                return $method->invokeArgs($classOrObject, $arguments);
            } else {
                throw new InvalidArgumentException("First argument must be a valid class name or object instance");
            }
        }

        /**
         * Only classes from this allow-list may be fetched by this debug helper.
         * Without it an attacker could instantiate any autoloadable class and inject
         * the resulting objects into arbitrary event handlers (unsafe reflection).
         * Names are lower cased because PHP class names are case-insensitive.
         */
        $allowedDispatchClasses = [
            'erlhcoreclassmodelchat',
            'erlhcoreclassmodelmsg',
            'erlhcoreclassmodelmailconvconversation',
            'erlhcoreclassmodelmailconvmessage',
        ];

        $args = [];

        if (isset($_POST['args']) && !empty($_POST['args'])) {
            $argsRows = explode("\n", trim($_POST['args']));

            foreach ($argsRows as $argRow) {
                $argRowParams = array_map('trim', explode('||', $argRow));

                if (count($argRowParams) !== 3) {
                    throw new Exception('Invalid argument format. Expected "argument name || class name || object id".');
                }

                if (!in_array(strtolower($argRowParams[1]), $allowedDispatchClasses, true)) {
                    throw new Exception('Requested class is not allowed to be dispatched.');
                }

                $args[$argRowParams[0]] = callMethodByName($argRowParams[1], 'fetch', [$argRowParams[2]]);
            }
        }

        $callParams = [
            'event_name' => $_POST['event_name'],
            'args' => $args,
        ];

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch($callParams['event_name'], $args);

        echo "ok";
        exit;

    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }


} else {
    $tpl = erLhcoreClassTemplate::getInstance('lhwebhooks/dispatch.tpl.php');
    echo $tpl->fetch();
    exit;
}
