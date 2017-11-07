<?php

/**
 * Responsible for executing !<command> based queries
 * 
 * */
class erLhcoreClassChatCommand
{

    private static $supportedCommands = array(
        '!name' => 'self::setName',
        '!email' => 'self::setEmail',
        '!phone' => 'self::setPhone',
        '!goto' => 'self::redirectTo',
        '!translate' => 'self::startTranslation',
        '!screenshot' => 'self::takeScreenshot',
        '!contactform' => 'self::contactForm',
        '!block' => 'self::blockUser',
        '!close' => 'self::closeChat',
        '!closed' => 'self::closeChatDialog',
        '!delete' => 'self::deleteChat',
        '!pending' => 'self::pendingChat',
        '!active' => 'self::activeChat',
        '!remark' => 'self::addRemark',
        '!info' => 'self::info',
        '!help' => 'self::help',
    	'!note' => 'self::notice',
    	'!hold' => 'self::hold',
    	'!transferforce' => 'self::transferforce'
    );

    private static function extractCommand($message)
    {
        $params = explode(' ', $message);
        
        $commandData['command'] = array_shift($params);
        $commandData['argument'] = trim(implode(' ', $params));
        
        return $commandData;
    }

    /**
     * Processes command
     */
    public static function processCommand($params)
    {
        $commandData = self::extractCommand($params['msg']);
        
        if (key_exists($commandData['command'], self::$supportedCommands)) {
            $params['argument'] = $commandData['argument'];
            return call_user_func_array(self::$supportedCommands[$commandData['command']], array(
                $params
            ));
        } else { // Perhaps some extension has implemented this command?
            $commandResponse = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.customcommand', array('command' => $commandData['command'], 'params' => $params));
            
            if (isset($commandResponse['processed']) && $commandResponse['processed'] == true) {
                return $commandResponse;
            }
        }
        
        return array(
            'processed' => false,
            'process_status' => ''
        );
    }

    /**
     * Updates chat nick.
     *
     * @param array $params            
     *
     * @return boolean
     */
    public static function setName($params)
    {
        
        // Update object attribute
        $params['chat']->nick = $params['argument'];
        
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET nick = :nick WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':nick', $params['chat']->nick, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Nick changed!')
        );
    }
    
    /**
     * Just adds message from operator
     *
     * @param array $params
     *
     * @return boolean
     */
    public static function notice($params)
    {
    	return array(
    			'processed' => true,
    			'process_status' => '',
    			'raw_message' => $params['argument']
    	);
    }

    /**
     * Just adds message from operator
     *
     * @param array $params
     *
     * @return boolean
     */
    public static function hold($params)
    {
        $params['chat']->status_sub = erLhcoreClassModelChat::STATUS_SUB_ON_HOLD;

        if ($params['argument'] != '') {
            // Store as message to visitor
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = $params['argument'];
            $msg->chat_id = $params['chat']->id;
            $msg->user_id = $params['user']->id;
            $msg->time = time();
            $msg->name_support = $params['user']->name_support;
            $msg->saveThis();
        }

        // Reset auto responder on hold command
        if ($params['chat']->auto_responder !== false) {
            $params['chat']->auto_responder->active_send_status = 0;
            $params['chat']->auto_responder->saveThis();
        }

        // Update last user msg time so auto responder work's correctly
        $params['chat']->last_op_msg_time = $params['chat']->last_user_msg_time = time();

        // All ok, we can make changes
        erLhcoreClassChat::getSession()->update($params['chat']);

        return array(
            'custom_args' => array(
              'hold_added' => true
            ),
            'processed' => true,
            'raw_message' => '!hold',
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat status changed on-hold!')
        );
    }

    /**
     * Updates chat email.
     *
     * @param array $params            
     *
     * @return boolean
     */
    public static function setEmail($params)
    {
        
        // Update object attribute
        $params['chat']->email = $params['argument'];
        
        if (! isset($params['no_ui_update'])) {
            // Schedule interface update
            $params['chat']->operation_admin .= "lhinst.updateVoteStatus(" . $params['chat']->id . ");";
        }
        
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET email = :email, operation_admin = :operation_admin WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':email', $params['chat']->email, PDO::PARAM_STR);
        $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'E-mail changed!')
        );
    }

    /**
     * Option to transfer user to another user via command line directly
     * */
    public static function transferforce($params)
    {
        $user = erLhcoreClassModelUser::findOne(array('filter' => array('username' => $params['argument'])));
        
        // Try find user by e-mail
        if (!($user instanceof erLhcoreClassModelUser)) {
            $user = erLhcoreClassModelUser::findOne(array('filter' => array('email' => $params['argument'])));
        }
        
        if ($user instanceof erLhcoreClassModelUser) {
            
            $permissionsArray = erLhcoreClassRole::accessArrayByUserID($params['user']->id);
            
            if ($params['chat']->user_id == $params['user']->id || erLhcoreClassRole::canUseByModuleAndFunction($permissionsArray, 'lhchat', 'allowtransferdirectly')) {
                                                
                $params['chat']->user_id = $user->id;
                $params['chat']->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
                $params['chat']->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','Chat has been transfered to'),ENT_QUOTES) . ' - ' . (string)$user;
                $params['chat']->user_typing  = time();
                     
                // Change department if user cannot read current department, so chat appears in right menu
                $filter = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($user->id);
                if ($filter !== true && !in_array($params['chat']->dep_id, $filter)) {
                    $dep_id = erLhcoreClassUserDep::getDefaultUserDepartment($user->id);                    
                    if ($dep_id > 0) {
                        $params['chat']->dep_id = $dep_id;                       
                    }
                }
                
                $params['chat']->status_sub_sub = erLhcoreClassModelChat::STATUS_SUB_SUB_TRANSFERED;
    
                // Update UI
                if (! isset($params['no_ui_update'])) {
                    $params['chat']->operation_admin .= "lhinst.updateVoteStatus(" . $params['chat']->id . ");";
                }
    
                // All ok, we can make changes
                erLhcoreClassChat::getSession()->update($params['chat']);
                
                // Chat was transfered callback
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered_force', array('chat' => & $params['chat']));
                
                return array(
                    'processed' => true,
                    'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','Chat has been transfered to') . ' - ' . (string)$user
                );
            
            } else {
                return array(
                    'processed' => true,
                    'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'You do not have permission to transfer chat directly!')
                );
            }            
        } else {
            return array(
                'processed' => true,
                'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'User could not be found!')
            );
        }
    }
    
    /**
     * Updates chat phone
     *
     * @param array $params            
     *
     * @return boolean
     */
    public static function setPhone($params)
    {
        
        // Update object attribute
        $params['chat']->phone = $params['argument'];
        
        if (! isset($params['no_ui_update'])) {
            // Schedule interface update
            $params['chat']->operation_admin .= "lhinst.updateVoteStatus(" . $params['chat']->id . ");";
        }
        
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET phone = :phone, operation_admin = :operation_admin WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':phone', $params['chat']->phone, PDO::PARAM_STR);
        $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Phone changed!')
        );
    }

    /**
     * Redirects user to specified URL
     *
     * @param array $params            
     *
     * @return boolean
     */
    public static function redirectTo($params)
    {
        
        // Update object attribute
        $params['chat']->operation .= 'lhc_chat_redirect:' . str_replace(':', '__SPLIT__', $params['argument']) . "\n";
        
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET operation = :operation WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':operation', $params['chat']->operation, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'User was redirected!')
        );
    }

    public static function startTranslation($params)
    {
        // Schedule interface update
        $params['chat']->operation_admin .= "lhc.methodCall('lhc.translation','startTranslation',{'btn':$('#start-trans-btn-{$params['chat']->id}'),'chat_id':'{$params['chat']->id}'});";
        
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => ''
        );
    }

    public static function takeScreenshot($params)
    {
        // Update object attribute
        $params['chat']->operation .= "lhc_screenshot\n";
        
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET operation = :operation WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':operation', $params['chat']->operation, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Screenshot was scheduled!')
        );
    }

    public static function contactForm($params)
    {
        if (isset($params['no_ui_update'])) {
            erLhcoreClassChatHelper::redirectToContactForm($params);
        } else {
            
            // Schedule interface update
            $params['chat']->operation_admin .= "lhinst.redirectContact('{$params['chat']->id}');";
            
            // Update only
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
            $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
            $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'User was redirected to contact form!')
        );
    }

    public static function blockUser($params)
    {
        if (isset($params['no_ui_update'])) {
            $params['chat']->blockUser();
        } else {
            
            // Schedule interface update
            $params['chat']->operation_admin .= "lhinst.blockUser('{$params['chat']->id}');";
            
            // Update only
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
            $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
            $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'User was blocked!')
        );
    }
    
    public static function info($params)
    {
        $infoArray = array();
        $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Department').' - '.(string)$params['chat']->department;
        
        if ($params['chat']->referrer != '') {
            $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Started chat from').' - '.(string)$params['chat']->referrer;
        }
        
        if ($params['chat']->session_referrer != '') {
            $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Refered from').' - '.(string)$params['chat']->session_referrer;
        }
        
        if ($params['chat']->online_user !== false && $params['chat']->online_user->current_page != '') {
            $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Current page').' - '.(string)$params['chat']->online_user->current_page;
        }
        
        if ($params['chat']->email != '') {
            $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'E-mail').' - '.(string)$params['chat']->email;
        }
        
        if ($params['chat']->phone != '') {
            $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Phone').' - '.(string)$params['chat']->phone;
        }
        
        if ($params['chat']->country_name != '') {
            $infoArray[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Country').' - '.(string)$params['chat']->country_name;
        }
        
        return array(
            'processed' => true,
            'process_status' => '',
            'ignore' => true,
            'info' => implode("\n", array_filter($infoArray))
        );
    }
    
    public static function help()
    {                    
        return array(
            'processed' => true,
            'process_status' => '',
            'ignore' => true,
            'info' => implode("\n", array_keys(self::$supportedCommands))
        );
    }

    public static function closeChat($params)
    {
        if (isset($params['no_ui_update'])) {
            
            $permissionsArray = erLhcoreClassRole::accessArrayByUserID($params['user']->id);
            
            if ($params['chat']->user_id == $params['user']->id || erLhcoreClassRole::canUseByModuleAndFunction($permissionsArray, 'lhchat', 'allowcloseremote')) {
                erLhcoreClassChatHelper::closeChat($params);
                return array(
                    'processed' => true,
                    'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat was closed!')
                );
            } else {
                return array(
                    'processed' => true,
                    'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'You do not have permission to close a chat!')
                );
            }
        } else {
            // Schedule interface update
            $params['chat']->operation_admin .= "lhinst.closeActiveChatDialog('{$params['chat']->id}',$('#tabs'),true);";
            
            // Update only
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
            $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
            $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
            $stmt->execute();
            
            return array(
                'processed' => true,
                'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat was closed!')
            );
        }
    }
    
    /**
     * 
     * @param array $params
     * 
     * @return multitype:boolean string
     */
    public static function closeChatDialog($params)
    {
        // Schedule interface update
        $params['chat']->operation_admin .= "lhinst.removeDialogTab('{$params['chat']->id}',$('#tabs'),true);";
                
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat was closed!')
        );        
    }
    
    /**
     * Deletes a chat
     */
    public static function deleteChat($params)
    {
        if (isset($params['no_ui_update'])) {
            
            $permissionsArray = erLhcoreClassRole::accessArrayByUserID($params['user']->id);
            
            if (erLhcoreClassRole::canUseByModuleAndFunction($permissionsArray, 'lhchat', 'deleteglobalchat') || (erLhcoreClassRole::canUseByModuleAndFunction($permissionsArray, 'lhchat', 'deletechat') && $params['chat']->user_id == $params['user']->id)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.delete', array(
                    'chat' => & $params['chat'],
                    'user' => $params['user']
                ));
                $params['chat']->removeThis();
                
                return array(
                    'processed' => true,
                    'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat was deleted!')
                );
            } else {
                return array(
                    'processed' => true,
                    'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'You do not have permission to delete a chat!')
                );
            }
        } else {
            // Schedule interface update
            $params['chat']->operation_admin .= "lhinst.deleteChat('{$params['chat']->id}',$('#tabs'),true);";
            
            // Update only
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
            $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
            $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
            $stmt->execute();
            
            return array(
                'processed' => true,
                'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat was deleted!')
            );
        }
    }

    /**
     * Changes stat status to pending
     */
    public static function pendingChat($params)
    {
        erLhcoreClassChatHelper::changeStatus(array(
            'user' => $params['user'],
            'chat' => & $params['chat'],
            'status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            'allow_close_remote' => erLhcoreClassRole::canUseByModuleAndFunction(erLhcoreClassRole::accessArrayByUserID($params['user']->id), 'lhchat', 'allowcloseremote')
        ));
        
        if (! isset($params['no_ui_update'])) {
            $params['chat']->operation_admin .= "lhinst.updateVoteStatus(" . $params['chat']->id . ");";
            
            // Update only
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
            $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
            $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat status was changed to pending!')
        );
    }
    
    public static function activeChat($params)
    {
        erLhcoreClassChatHelper::changeStatus(array(
            'user' => $params['user'],
            'chat' => & $params['chat'],
            'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            'allow_close_remote' => erLhcoreClassRole::canUseByModuleAndFunction(erLhcoreClassRole::accessArrayByUserID($params['user']->id), 'lhchat', 'allowcloseremote')
        ));
        
        if (! isset($params['no_ui_update'])) {
            $params['chat']->operation_admin .= "lhinst.updateVoteStatus(" . $params['chat']->id . ");";
            
            // Update only
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin WHERE id = :id');
            $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
            $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Chat status was changed to active!')
        );
    }
    
    /**
     * Add remarks to chat
     * */
    public static function addRemark($params)
    {     
        $params['chat']->remarks = $params['argument'];
        
        if (! isset($params['no_ui_update'])) {
            $params['chat']->operation_admin .= "lhinst.updateVoteStatus(" . $params['chat']->id . ");";
        }
      
        // Update only
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET operation_admin = :operation_admin,remarks = :remarks WHERE id = :id');
        $stmt->bindValue(':id', $params['chat']->id, PDO::PARAM_INT);
        $stmt->bindValue(':operation_admin', $params['chat']->operation_admin, PDO::PARAM_STR);
        $stmt->bindValue(':remarks', $params['chat']->remarks, PDO::PARAM_STR);
        $stmt->execute();
              
        return array(
            'processed' => true,
            'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Remarks were saved!')
        );
    }
}

?>