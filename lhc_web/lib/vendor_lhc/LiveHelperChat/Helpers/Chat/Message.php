<?php

namespace LiveHelperChat\Helpers\Chat;

class Message
{
    public static function extractFile($msg_text) {
        if (empty($msg_text)) {
            return null;
        }
        
        // Extract file from message content like [file=1999_718792694da94d3018e51319471c09b5]
        $matches = array();
        preg_match_all('/\[file="?(.*?)"?\]/', $msg_text, $matches);
        
        if (empty($matches[1])) {
            return null;
        }
        
        // Get first file match
        $fileContent = $matches[1][0];
        $parts = explode('_', $fileContent);
        
        if (count($parts) < 2) {
            return null;
        }
        
        $fileID = $parts[0];
        $hash = $parts[1];
        
        try {
            $file = \erLhcoreClassModelChatFile::fetch($fileID);
            if (is_object($file) && $hash == $file->security_hash) {
                return $file;
            }
        } catch (Exception $e) {
            // File not found or invalid
        }
        
        return null;
    }

}

?>