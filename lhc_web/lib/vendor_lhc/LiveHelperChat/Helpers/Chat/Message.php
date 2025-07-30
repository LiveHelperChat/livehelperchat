<?php

namespace LiveHelperChat\Helpers\Chat;

class Message
{
    public static function extractFile($msgObject) {
        // Support both message object and string for backward compatibility
        if (is_string($msgObject)) {
            $msg_text = $msgObject;
            $meta_msg = '';
        } elseif (is_object($msgObject)) {
            $msg_text = isset($msgObject->msg) ? $msgObject->msg : '';
            $meta_msg = isset($msgObject->meta_msg) ? $msgObject->meta_msg : '';
        } else {
            return null;
        }
        
        // First check for attachments in meta_msg
        if (!empty($meta_msg)) {
            $metaData = json_decode($meta_msg, true);
            if (isset($metaData['content']['attachements']) && is_array($metaData['content']['attachements']) && !empty($metaData['content']['attachements'])) {
                $firstAttachment = $metaData['content']['attachements'][0];
                if (isset($firstAttachment['id']) && isset($firstAttachment['security_hash'])) {
                    try {
                        $file = \erLhcoreClassModelChatFile::fetch($firstAttachment['id']);
                        if (is_object($file) && $firstAttachment['security_hash'] == $file->security_hash) {
                            return array(
                                'file' => $file,
                                'type' => 'meta_msg'
                            );
                        }
                    } catch (Exception $e) {
                        // File not found or invalid, continue to check message content
                    }
                }
            }
        }
        
        // Fallback to checking message content if no valid attachment found in meta_msg
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
                return array(
                    'file' => $file,
                    'type' => 'inline'
                );
            }
        } catch (Exception $e) {
            // File not found or invalid
        }
        
        return null;
    }

}

?>