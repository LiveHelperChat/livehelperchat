Live helper chat
==============

It's an open-source powered application, which brings simplicity and usability in one place. With live helper chat you can bring live support on your site for free. http://livehelperchat.com

## Hooks for extensions
https://doc.livehelperchat.com/docs/hooks

## Quick development guide
 * After app is installed disable cache and enable debug output. 
   * https://github.com/LiveHelperChat/livehelperchat/blob/master/lhc_web/settings/settings.ini.default.php#L13-L16
   * Change the following values to
    ```
    * debug_output => true
   * templatecache => false
   * templatecompile => false
   * modulecompile => false
   ```
 * To compile JS from lhc_web folder execute. This will compile main JS and old widget javascript files.
   * `npm install && gulp`
 * To compile new widget V2
   * There is two apps [wrapper](https://github.com/LiveHelperChat/livehelperchat/tree/master/lhc_web/design/defaulttheme/widget/wrapper) and [widget](https://github.com/LiveHelperChat/livehelperchat/tree/master/lhc_web/design/defaulttheme/widget/react-app)
   * `cd lhc_web/design/defaulttheme/widget/wrapper && npm install && npm run build`
   * `cd lhc_web/design/defaulttheme/widget/react-app && npm install && npm run build && npm run build-ie`
   

## Demo
http://livehelperchat.com/demo-12c.html

## Documentation
https://doc.livehelperchat.com

## Extensions
https://github.com/LiveHelperChat

## Translations contribution
https://www.transifex.com/projects/p/live-helper-chat/

## Folders structure

 * Directories content:
  * lhc_web - WEB application folder.
  
## Rest API support
https://api.livehelperchat.com/

## Third party support

 * [Telegram](https://livehelperchat.com/telegram-integration-481a.html)
 * [Twilio](https://livehelperchat.com/twilio-support-for-sms-chat-477a.html)
 * [Facebook messenger](https://livehelperchat.com/integration-with-facebook-messenger-464a.html)
 
## Features

Few main features

 * XMPP support for notifications about new chats. (IPhone, IPad, Android, Blackberry, GTalk etc...)
 * Chrome extension
 * Repeatable sound notifications
 * Work hours
 * See what user see with screenshot feature
 * Drag & Drop widgets, minimize/maximize widgets
 * Multiple chats same time
 * See what users are typing before they send a message
 * Multiple operators
 * Send delayed canned messages as it was real user typing
 * Chats archive
 * Priority queue
 * Chats statistic generation, top chats
 * Resume chat after user closed chat
 * All chats in single window with tabs interface, tabs are remembered before they are closed
 * Chat transcript print
 * Chat transcript send by mail
 * Site widget
 * Page embed mode for live support script or widget mode, or standard mode.
 * Multilanguage
 * Chats transfering
 * Departments
 * Files upload
 * Chat search
 * Automatic transfers between departments
 * Option to generate JS for different departments
 * Option to prefill form fields. 
 * Option to add custom form fields. It can be either user variables or hidden fields. Usefull if you are integrating with third party system and want to pass user_id for example.
 * Cronjobs
 * Callbacks
 * Closed chat callback
 * Unanswered chat callback
 * Asynchronous status loading, not blocking site javascript.
 * XML, JSON export module
 * Option to send transcript to users e-mail
 * SMTP support
 * HTTPS support
 * No third parties cookies dependency
 * Previous users chats
 * Online users tracking, including geo detection
 * GEO detection using three different sources
 * Option to configure start chat fields
 * Sounds on pending chats and new messages
 * Google chrome notifications on pending messages.
 * Browser title blinking then there is pending message.
 * Option to limit pro active chat invitation messages based on pending chats.
 * Option to configure frequency for pro active chat invitation message. You can set after how many hours for the same user invitation message should be shown again.
 * Users blocking
 * Top performance with enabled cache
 * Windows, Linux and Mac native applications.
 * Advanced embed code generation with numerous options of includable code.
 * Template override system
 * Module override system
 * Support for custom extensions
 * Changeable footer and header content
 * Option to send messges to anonymous site visitors,
 * Canned messages
 * Informing then operator or user is typing.
 * Option to see what user is typing before he sends a message
 * Canned messages for desktop client
 * Voting module
 * FAQ module
 * Online users map
 * Pro active chat invitatio
 * Remember me functionality
 * Total pageviews tracking
 * Total pageviews including previous visits tracking
 * Visits tracking, how many times user has been on your page.
 * Time spent on site
 * Auto responder
 * BB Code support. Links recognition. Smiles and few other hidden features :)
 * First user visit tracking
 * Option for customers mute sounds 
 * Option for operators mute messages sounds and new pending chat's sound.
 * Option to monitor online operators.
 * Option to have different pro active messages for different domains. This can be archieved using different identifiers.
 * Dekstop client supports HTTPS
 * Protection against spammers using advanced captcha technique without requiring users to enter any captcha code.
 * Option for operator set online or offline mode.
 * Desktop client for
  * Windows
  * Linux 
  * Mac
 * Flexible permission system:
  * Roles
  * Groups
  * Users

Forum:
http://forum.livehelperchat.com/
