<?php

$transItems = array(
    "homepage.invisible" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Invisible'),
    "homepage.visible"  => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Visible'),
    "homepage.change_visibility" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible'),
    "homepage.change_online_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings', 'Change my status to online/offline. Ctrl + F12 Shortcut.'),
    "homepage.status_offline" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account', 'Offline'),
    "homepage.status_online" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account', 'Online'),
    "homepage.always_online" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my persistent status to online'),
    "homepage.always_online_mode" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Always online'),
    "homepage.always_online_activity" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Based on activity'),

    "homepage.interact_sound" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Sound is disabled (interact with page to enable)'),
    "homepage.sound_disabled" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Sound is disabled (trying to enable...)'),
    "homepage.browser_no_sound" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Audio not supported in this browser'),

    "homepage.no_connection" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','No connection detected!'),
    "homepage.last_updated_at" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats data updated at'),
    "homepage.node_js_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','NodeJS Connection Status'),
    "homepage.sync_blocked" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Sync blocked because of inactivity'),

    "homepage.enable_notifications" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Click to enable notifications'),
    "homepage.notifications_not_supported" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Notifications not supported in this browser'),
    "homepage.notifications_blocked" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Notifications blocked (check browser settings)'),
    "homepage.notifications_like" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','You will now receive notifications from this site'),

    // Open chat
    'front_default.chat_id_to_open' => erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Chat ID to open'),
    'front_default.open_a_chat' => erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open a chat'),

    // Widgets
    'widget_title.pending_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats'),
    'widget_title.bot_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Bot chats'),
    'widget_title.active_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats'),
    'widget_title.unread_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Unread messages'),
    'widget_title.group_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Group chats'),
    'widget_title.subject_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Ongoing trigger alerts!'),
    'widget_title.transfer_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred chats'),
    'widget_title.my_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','My active and pending chats'),
    'widget_title.depgroups_stats' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Departments stats'),
    'widget_title.online_op' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online operators'),
    'widget_title.my_mails' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','My active and new mails'),
    'widget_title.active_mails' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active mails'),
    'widget_title.alarm_mails' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Alarm mails'),
    'widget_title.pending_mails' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New mails'),
    'widget_title.onlineusers' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online visitors'),

    // Group chat widget
    "group_chat.unread_messages" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','You have unread messages'),
    "group_chat.group_name" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Name'),
    "group_chat.accept_join" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept invitation and join private chat'),
    "group_chat.reject_private" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Reject for private chat'),
    "group_chat.join_public" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','You can join public chat'),
    "group_chat.already_member" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','You are member of this group chat'),
    "group_chat.private_group" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Private group'),
    "group_chat.public_group" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Public group'),
    "group_chat.new" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','New'),
    "group_chat.new_group_name" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Your new group name'),

    // Widget
    "widget.more_rows" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More rows'),
    "widget.less_rows" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Less rows'),
    "widget.collapse_expand" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout', 'collapse/expand'),
    "widget.time_ago" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout', 'Time ago'),
    "widget.wait_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface', 'Wait time'),
    "widget.last_message" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface', 'Last message'),
    "widget.last_activity_ago" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface', 'Last activity ago'),
    "widget.last_assignment_ago" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface', 'Last assignment ago'),
    "widget.went_offline_ago" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface', 'Went offline {{ago}} ago'),
    "widget.dep_group" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface', 'Department group'),
    "widget.department" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department'),
    "widget.subject" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Subject'),
    "widget.visitor" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor'),
    "widget.send_receive" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Receive or send indicator and time since it happened'),
    "widget.sort_by_last_msg_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by last message time'),
    "widget.sort_by_start_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by chat start time'),
    "widget.time_since_last_msg" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Time since last message'),
    "widget.created_at" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Created'),
    "widget.operator" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator'),
    "widget.sort_by_online_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by online status'),
    "widget.sort_by_online_name" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by operator name'),
    "widget.transferred_to_you" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transferred to you directly'),
    "widget.transferred_to_dep" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred to your department'),
    "widget.transfer_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Transfer time'),
    "widget.active" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active'),
    "widget.live_chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Live chats - (active chats + pending chats - inactive chats)'),
    "widget.pending" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Pending'),
    "widget.delete_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat'),
    "widget.sort" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort'),
    "widget.sort_pending" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by chat priority and chat start time'),
    "widget.sort_wait_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by chat wait time'),
    "widget.redirect_contact" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Redirect user to contact form.'),
    "widget.are_you_sure" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Are you sure?'),
    "widget.offline_request" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Offline request'),
    "widget.has_unread_messages" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Has unread messages'),
    "widget.open_new_window" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window'),
    "widget.accept_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat'),
    "widget.sort_by_dep" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by department'),
    "widget.sort_by_op" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by operator'),
    "widget.sort_by_chat_number" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by active chats number'),
    "widget.sort_by_chat_number_real" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by live chats numbers'),
    "widget.sort_by_nick" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by visitor nick'),
    "widget.sort_by_location" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by location'),
    "widget.load_statistic" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Load statistic'),
    "widget.location" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Location'),
    "widget.msg_v_number" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Number of messages by user'),
    "widget.more_than" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More than'),
    "widget.user_msgs" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','user messages'),
    "widget.download_xls" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Download XLS'),
    "widget.inactive_chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','inactive chats'),
    "widget.inactive_op_chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','inactive online operators chats'),
    "widget.active_op_chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','active online operators chats'),
    "widget.hard_limit" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Hard limit'),
    "widget.soft_limit" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Soft limit'),
    "widget.hard_limit_explain" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Hard limit - (active online operators chats - inactive online operators chats) (soft limit - active chats)'),
    "widget.online" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online'),
    "widget.free_slots" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Free slots (max chats - ((active chats + pending chats) - inactive chats))'),
    "widget.max" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Max'),
    "widget.chats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','chats'),
    "widget.change_op_status" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Change operator status'),
    "widget.see_op_stats" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','See operator statistic'),
    "widget.start_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Start chat'),
    "widget.taken_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Taken time to fetch information for this widget'),
    "widget.taken_time_dep" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Taken time to render departments statistic'),
    "widget.op_chats_statistic" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Operator chats statistic'),
    "widget.items_appear_here" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','All items will appear here'),

    // Widget options
    "widget_options.hide_disabled" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide disabled'),
    "widget_options.check_all" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Check all'),
    "widget_options.only_online" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only online'),
    "widget_options.only_explicit_online" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only explicit online'),
    "widget_options.hide_hidden" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide hidden'),
    "widget_options.hide_dep_groups" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide departments groups'),
    "widget_options.hide_dep" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide departments'),
    "widget_options.all_dep" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','All departments'),
    "widget_options.users" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Users'),
    "widget_options.limit" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Number of elements in list'),
    "widget_options.departments" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout', 'departments'),
    "widget_options.search_operators" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Search for operator'),
    "widget_options.search_dep" => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Search for department'),
    "widget_options.type_to_search" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Type to search'),
    "widget_options.select_country" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select country'),
    "widget_options.time_on_site" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', '+20 (More than 20 seconds spend on site) 20 (Less than 20 seconds spend on site)'),
    "widget_options.time_on_site_shrt" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site'),
    "widget_options.vis_local_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visitor local time'),
    "widget_options.status_on_site" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','By user status on site'),
    "widget_options.came_from" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'Came from'),
    "widget_options.page" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page'),
    "widget_options.only_connected" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Show only connected'),
    "widget_options.delete" => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete'),

    // Online visitors
    "widget.action" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Action'),
    "widget.ago" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago'),
    "widget.copy_nick" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Copy nick'),
    "widget.copied_nick" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Copied'),
    "widget.send_message" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message'),
    "widget.start_a_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Start a chat'),
    'widget.msg_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Seen'),
    'widget.msg_not_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Unseen'),
    'widget.second' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.'),
    'widget.first' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator'),
    'widget.new' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New'),
    'widget.returning' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'Returning'),
    'widget.returning_long' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'Returning visitor, visits in total'),
    'widget.chat' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'Chat'),
    'widget.msg_sent' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'has sent a message to the user'),
    'widget.preview_chat' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'Preview chat'),

    // bbcode
    "bbcode.strike" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Strike'),
    "bbcode.quote" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Quote'),
    "bbcode.youtube" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Youtube'),
    "bbcode.html_code" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','HTML Code'),
    "bbcode.bold" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Bold'),
    "bbcode.italic" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Italic'),
    "bbcode.underline" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Underline'),
    "bbcode.font_size" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Font Size'),
    "bbcode.color" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Color'),
    "bbcode.apply" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Apply'),
    "bbcode.insert_image_or_file" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Insert image or file'),
    "bbcode.preview" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Preview'),

    // User account
    "user_account.add_translation" => erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Add translation'),
    "user_account.search_language" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Search for language. Enter * to see all.'),
    "user_account.see_all_variations" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','See all variations'),
    "user_account.messages" => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages')

);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.trans_lhcbo', array(
    'trans' => & $transItems,
));

echo json_encode($transItems);
