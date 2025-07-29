<?php

erLhcoreClassRestAPIHandler::setHeaders();

// Get JSON input from POST body
$input = json_decode(file_get_contents('php://input'), true);

// Get form data from JSON input only
$fileId = isset($input['file_id']) ? $input['file_id'] : null;
$securityHash = isset($input['security_hash']) ? $input['security_hash'] : null;
$chatId = isset($input['chat_id']) ? $input['chat_id'] : null;
$hash = isset($input['hash']) ? $input['hash'] : null;

// Validate required parameters
if (!empty($fileId) && !empty($securityHash)) {
    
    try {
        // Try to load the chat file
        $chatFile = erLhcoreClassModelChatFile::fetch($fileId);
        
        if ($chatFile !== false) {
            
            // Verify access permissions
            $hasAccess = false;
            
            // Check if it's a chat file and user has access to the chat
            if (!empty($chatId) && !empty($hash)) {
                try {
                    $chat = erLhcoreClassModelChat::fetch((int)$chatId);
                    if ($chat !== false && $chat->id == $chatFile->chat_id && $chat->hash === $hash) {
                        // Additional validation: check if chat is in valid status for file operations
                        if (in_array($chat->status, [
                            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
                            erLhcoreClassModelChat::STATUS_BOT_CHAT
                        ])) {
                            $hasAccess = true;
                        }
                    }
                } catch (Exception $e) {
                    // Chat not found or invalid
                }
            }
            
            // Verify security hash matches
            if ($hasAccess && $chatFile->security_hash === $securityHash) {
                                
                // Remove from database
                $chatFile->removeThis();
                
                echo json_encode(array('error' => false, 'msg' => 'File removed successfully'));
                
            } else {
                echo json_encode(array('error' => true, 'msg' => 'Access denied or invalid security hash'));
            }
            
        } else {
            echo json_encode(array('error' => true, 'msg' => 'File not found'));
        }
        
    } catch (Exception $e) {
        echo json_encode(array('error' => true, 'msg' => 'Error removing file: ' . $e->getMessage()));
    }
    
} else {
    echo json_encode(array('error' => true, 'msg' => 'Missing required parameters'));
}

exit;

?>
