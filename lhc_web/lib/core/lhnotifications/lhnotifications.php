<?php

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class erLhcoreClassNotifications {

    public static function getSession()
    {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhnotifications' )
            );
        }
        return self::$persistentSession;
    }

    public static function validateTestNotification(& $input, $subscriber) {

        $definition = array(
            'chat_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'chat_id' ) ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter chat id!');
        } else {
            $input->chat_id = $form->chat_id;
        }

        if (empty($Errors)) {
            try {
                $res = self::sendNotification(erLhcoreClassModelChat::fetch($input->chat_id), $subscriber);

                if ($res !== true && $res['success'] == false) {
                    throw new Exception(htmlspecialchars($res['message']));
                }

            } catch (Exception $e) {
                $Errors[] = $e->getMessage();
            }

        }

        return $Errors;
    }

    public static function validateSubscriber(& $bot) {

        $definition = array(
            'Name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Nick' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot name!');
        } else {
            $bot->name = $form->Name;
        }

        if ( !$form->hasValidData( 'Nick' ) || $form->Nick == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot nick!');
        } else {
            $bot->nick = $form->Nick;
        }

        return $Errors;
    }

    public static function sendNotification($item, $subscriber)
    {
        $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 4, 'sort' => 'id DESC','filter' => array('chat_id' => $item->id))));
        $messagesContent = '';
        foreach ($messages as $msg ) {
            if ($msg->user_id != -1) {
                $messagesContent .= ($msg->user_id == 0 ? htmlspecialchars($item->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars(trim($msg->msg))."\n";
            }
        }

        $subscriptionDestination = Subscription::create(json_decode($subscriber->params, true));

        $nSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings');
        $data = (array)$nSettings->data;

        $auth = array(
            'VAPID' => array(
                'subject' => $data['subject'],
                'publicKey' => $data['public_key'],
                'privateKey' =>  $data['private_key'] // in the real world, this would be in a secret file
            ),
        );

        $webPush = new WebPush($auth);
        $webPush->setAutomaticPadding(2000);

        $title = (string)$item->department;

        $themeAppend = '';

        // Set icon from theme if required
        if ($subscriber->theme instanceof erLhAbstractModelWidgetTheme) {

            $notificationConfiguration = $subscriber->theme->notification_configuration_array;

            if (isset($notificationConfiguration['ndomain']) && !empty($notificationConfiguration['ndomain'])) {
                $data['http_host'] = $notificationConfiguration['ndomain'];
            }

            $icon = $subscriber->theme->notification_icon_url;
            if ($icon != '') {
                $data['badge'] = $data['icon'] = (strpos($icon,'http') === false) ? 'https://' . $data['http_host'] . $icon : $icon;
            }

            if (isset($notificationConfiguration['ntitle']) && !empty($notificationConfiguration['ntitle'])) {
                $title = $notificationConfiguration['ntitle'];
            }

            $themeAppend = '/(theme)/' . $subscriber->theme_id;
        }

        $payload = array(
            'renotify' => $data['renotify'],
            'rinteraction' => $data['require_interaction'],
            'icon' =>  $data['icon'],
            'badge' =>  $data['badge'],
            'tag' => 'lhc_chat_' . $item->id,
            'msg' => trim($messagesContent),
            'title' => $title,
            'data' => array(
                'cid' => $item->id,
                'ch' => $item->hash,
                'url' => 'https://' . $data['http_host'] . erLhcoreClassDesign::baseurldirect('notifications/read') . $themeAppend
            )
        );

        if (isset($data['vibrate']) && $data['vibrate'] != '') {
            $payload['vibrate'] = explode(',',$data['vibrate']);
        }

        $res = $webPush->sendNotification(
            $subscriptionDestination,
            json_encode($payload),
            true,
            ['topic' => 'lhc_chat_' . $item->id]
        );

        return $res;
    }

    public static function informAboutUnreadMessages()
    {
        $items = erLhcoreClassModelChat::getList(array( 'filtergt' => array('last_op_msg_time' => (time() - (3600))), 'filterlt' => array('last_op_msg_time' => (time() - (90))), 'filter' => array('has_unread_op_messages' => 1, 'unread_op_messages_informed' => 0)));

        foreach ($items as $item) {
            $item->has_unread_op_messages = 0;
            $item->unread_op_messages_informed = 1;
            $item->updateThis();
        }

        $stats = array();

        foreach ($items as $item) {

            $subscriber = erLhcoreClassModelNotificationSubscriber::findOne(array('sort' => 'id DESC', 'filter_custom' => array('`chat_id` = ' . (int)$item->id . ($item->online_user_id > 0 ? ' OR `online_user_id` = ' . (int)$item->online_user_id : ''))));

            if ($subscriber instanceof erLhcoreClassModelNotificationSubscriber) {
                 self::sendNotification($item, $subscriber);
                $stats[] = $item->id;
            }
        }

        return $stats;
    }

    private static $persistentSession;
    private static $instance = null;
}

?>