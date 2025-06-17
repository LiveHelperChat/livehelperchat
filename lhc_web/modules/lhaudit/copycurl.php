<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhaudit/copycurl.tpl.php');

if ($Params['user_parameters']['scope'] === 'audit') {
    $msg = erLhAbstractModelAudit::fetch($Params['user_parameters']['id']);
} else {
    $msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['id']);
}


/**
 * First pass: Find all property paths that contain non-empty arrays
 * to ensure consistent structure across all instances.
 *
 * @param mixed $item The item to process
 * @param string $path The current path
 * @param array &$arrayPaths Collection of paths that should remain arrays
 */
function findArrayPaths($item, $path = '', &$arrayPaths = []) {
    if (is_array($item)) {
        if (!empty($item) && array_keys($item) === range(0, count($item) - 1)) {
            // This is a non-empty indexed array, record its path
            $arrayPaths[$path] = true;
        }
        
        foreach ($item as $key => $value) {
            $newPath = empty($path) ? $key : $path . '.' . $key;
            findArrayPaths($value, $newPath, $arrayPaths);
        }
    }
}

/**
 * Recursively converts empty PHP arrays to empty stdClass objects,
 * except for properties that should remain as arrays based on the structure.
 *
 * @param mixed &$item The item to process
 * @param string $path Current property path
 * @param array $arrayPaths Collection of paths that should remain arrays
 */
function convertEmptyArraysToObjectsRecursive(&$item, $path = '', $arrayPaths = []) {
    if (is_array($item)) {
        // Extract the property name from the current path
        $pathParts = explode('.', $path);
        $currentProperty = end($pathParts);
        
        // Special case: 'properties' should always be an object, never an array
        if ($currentProperty === 'properties' && empty($item)) {
            $item = new stdClass();
            return;
        }
        
        if (empty($item)) {
            // Check if this path should be an array based on property name
            $shouldBeArray = false;
            
            // Look through arrayPaths to see if any path with the same property name exists
            foreach ($arrayPaths as $arrayPath => $_) {
                $arrayPathParts = explode('.', $arrayPath);
                $arrayProperty = end($arrayPathParts);
                
                // If we find a matching property name in the arrayPaths, keep this as an array
                if ($arrayProperty === $currentProperty) {
                    $shouldBeArray = true;
                    break;
                }
            }
            
            if (!$shouldBeArray) {
                $item = new stdClass();
            }
            // If shouldBeArray is true, keep it as an empty array []
        } else {
            foreach ($item as $key => &$value) {
                $newPath = empty($path) ? $key : $path . '.' . $key;
                convertEmptyArraysToObjectsRecursive($value, $newPath, $arrayPaths);
            }
            unset($value);
        }
    }
}

/**
 * Constructs a cURL command string from a JSON input.
 * Empty arrays in the request body will be encoded as empty objects {}.
 *
 * @param string $jsonInput The JSON string containing the request details.
 * @return string The constructed cURL command, or an error message if parsing fails.
 */
function constructCurlCommandFromJson(string $jsonInput): string
{
    // Attempt to decode the JSON input into an associative array
    $data = json_decode($jsonInput, true);

    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Error: Invalid JSON input. " . json_last_error_msg();
    }

    // Initialize the cURL command string
    $curlCommand = 'curl';

    // --- Extract and add the HTTP method ---
    if (isset($data['method'])) {
        $method = strtoupper($data['method']);
        $curlCommand .= ' -X ' . $method;
    } else {
        return "Error: 'method' not found in JSON input.";
    }

    // --- Extract and add the URL ---
    if (isset($data['params_request']['url'])) {
        $url = $data['params_request']['url'];
        // Quote the URL properly with single quotes and escape any existing single quotes
        $curlCommand .= " '" . str_replace("'", "'\\''", $url) . "'";
    } else {
        return "Error: 'params_request.url' not found in JSON input.";
    }

    // --- Extract and add headers ---
    if (isset($data['params_request']['headers']) && is_array($data['params_request']['headers'])) {
        foreach ($data['params_request']['headers'] as $header) {
            // Use single quotes to preserve the header value exactly
            $curlCommand .= " \
    -H '" . str_replace("'", "'\\''", $header) . "'";
        }
    }

    // --- Extract and add the request body (if method is POST, PUT, PATCH, etc.) ---
    // Common methods that typically have a body.
    $methodsWithBody = ['POST', 'PUT', 'PATCH', 'DELETE'];
    if (in_array($method, $methodsWithBody) && isset($data['params_request']['body'])) {
        // Get the body data
        $bodyData = $data['params_request']['body'];

        // First scan the data to identify all array-type properties
        $arrayPaths = [];
        findArrayPaths($bodyData, '', $arrayPaths);
        
        // First, find all property names that contain a non-empty array
        $nonEmptyArrayPropertyNames = [];
        $propertyPathMap = [];
        
        // Create a recursive function using a reference to itself
        $processArrays = null;
        $processArrays = function($data, $path = '') use (&$processArrays, &$nonEmptyArrayPropertyNames, &$propertyPathMap) {
            if (is_array($data)) {
                // Keep track of path -> property name mapping
                if (!empty($path)) {
                    $pathParts = explode('.', $path);
                    $propertyName = end($pathParts);
                    
                    // Skip 'properties' since it should always be an object
                    if ($propertyName === 'properties') {
                        // Don't record it in arrayPaths to ensure it becomes an object
                    } else {
                        if (!isset($propertyPathMap[$propertyName])) {
                            $propertyPathMap[$propertyName] = [];
                        }
                        $propertyPathMap[$propertyName][] = $path;
                        
                        // If this is a non-empty sequential array
                        if (!empty($data) && array_keys($data) === range(0, count($data) - 1)) {
                            $nonEmptyArrayPropertyNames[$propertyName] = true;
                        }
                    }
                }
                
                // Continue traversing
                foreach ($data as $key => $value) {
                    $newPath = empty($path) ? $key : $path . '.' . $key;
                    $processArrays($value, $newPath);
                }
            }
        };
        
        // Scan the data to find all properties with non-empty arrays
        $processArrays($bodyData, '');
        
        // Now mark all paths with property names that have a non-empty array somewhere
        foreach ($nonEmptyArrayPropertyNames as $propertyName => $_) {
            if (isset($propertyPathMap[$propertyName])) {
                foreach ($propertyPathMap[$propertyName] as $path) {
                    $arrayPaths[$path] = true;
                    
                    // Extract the property name for logging/debugging
                    $pathParts = explode('.', $path);
                    $propName = end($pathParts);
                }
            }
        }
        
        // Now convert empty arrays to objects, respecting the array paths
        convertEmptyArraysToObjectsRecursive($bodyData, '', $arrayPaths);

        // Re-encode the (potentially modified) body data to a JSON string.
        $bodyJson = json_encode($bodyData, JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return "Error: Could not encode modified body to JSON. " . json_last_error_msg();
        }
        
        // Add the data payload using a file input approach which is safer for complex JSON
        // This avoids issues with shell escaping of quotes within the JSON
        $curlCommand .= " --data-binary @-";
        $curlCommand .= " << 'CURL_DATA_EOF'\n" . $bodyJson . "\nCURL_DATA_EOF";
    }

    return $curlCommand;
}

if ($msg instanceof erLhAbstractModelAudit) {
    $debugData = json_decode($msg->message,true);
} else {
    $debugData = isset($msg->meta_msg_array['content']['html']['content']) ? json_decode($msg->meta_msg_array['content']['html']['content'],true) : '';
}

if (isset($debugData['params_request'])) {
    if (isset($debugData['params_request']['body']) && !is_array($debugData['params_request']['body'])) {
        $bodyJSON = json_decode(str_replace(["\n","\r\n"],"",$debugData['params_request']['body']),true);
        if (is_array($bodyJSON)) {
            $debugData['params_request']['body'] = $bodyJSON;
        }
    }
    $debugData = json_encode($debugData);
} else {
    if ($msg instanceof erLhAbstractModelAudit) {
        $debugData = $msg->meta_msg_array['content']['html']['content'];
    } else {
        $debugData = $msg->message;
    }

}

$tpl->set('command',constructCurlCommandFromJson($debugData));

echo $tpl->fetch();
exit;
?>