Live helper chat
==============

It's an open-source powered application that brings simplicity and usability into one place. With Live Helper Chat, you can bring live support to your site for free. http://livehelperchat.com. In a production environment, serving clients handling over [10,000](https://doc.livehelperchat.com/docs/performance) chats per day with dozens of operators actively working.  

[![Live Helper Chat](https://livehelperchat.com/design/frontendnew/images/lhc.png)](https://livehelperchat.com)

[![Apple store](https://livehelperchat.com/design/defaulttheme/images/apps/apple.svg)](https://apps.apple.com/us/app/id1530399116) [![Google Play](https://livehelperchat.com/design/defaulttheme/images/apps/google-play.png?v=2)](https://play.google.com/store/apps/details?id=com.livehelperchat.chat) [![Deploy to DO](https://mp-assets1.sfo2.digitaloceanspaces.com/deploy-to-do/do-btn-blue.svg)](https://marketplace.digitalocean.com/apps/live-helper-chat/?refcode=09c74421e3c2&utm_campaign=Referral_Invite&utm_medium=Referral_Program&utm_source=CopyPaste)

[![Codemagic build status](https://api.codemagic.io/apps/5f50c50be2db272d7690ae45/5f50c50be2db272d7690ae44/status_badge.svg)](https://codemagic.io/apps/5f50c50be2db272d7690ae45/5f50c50be2db272d7690ae44/latest_build)

## Need help?
* Documentation - https://doc.livehelperchat.com
* Forum/Discussions - https://github.com/LiveHelperChat/livehelperchat/discussions
* Chat (Discord) https://discord.gg/YsZXQVh
* [Laravel version of Live Helper Chat](https://github.com/LiveHelperChat/livehelperchat_laravel)

## Donate

 * Using Github Sponsors https://github.com/sponsors/LiveHelperChat
 * Bitcoin - `bc1qlzn4qdxnl50gmzcherlp4rzql4uwn7ddpvwnp9`
 * USDT (Ethereum network) - `0x45b92C4aa29EFD3252fD04fcDDe3e6Ef8b399D75`
 * Paypal - https://paypal.me/LiveHelperChat
 * Bank Wire - IBAN: LT967044000941610547, Bank SWIFT code: CBVI LT 2X

## Quick install guide

### By cloning repository

If you clone git repository you have to install composer dependencies. Min 8.2 PHP version.

```
cd lhc_web && composer install
```

Now you can just enter in your browser `lhc_web/index.php`

### By downloading from release section

Navigate to https://github.com/LiveHelperChat/livehelperchat/releases and download most recent relese.

You do not need to install composer dependencies in that scenario.

For alternative install ways read https://doc.livehelperchat.com/docs/install

## Demo

http://livehelperchat.com/demo-12c.html 

Demo with ChatGPT and streaming support can be found at https://doc.livehelperchat.com/ Try to ask a question about Live Helper Chat. ChatGPT install instructions [here](https://github.com/LiveHelperChat/chatGPT/)

## Integrations

If you are installing extensions, make sure that your version has webhooks enabled - https://github.com/LiveHelperChat/livehelperchat/blob/master/lhc_web/settings/settings.ini.default.php#L86

 * [Mobile app](https://github.com/LiveHelperChat/lhc_messenger) flutter
 * [Agora - Voice & Video & ScreenShare](https://doc.livehelperchat.com/docs/voice-video-screenshare) powered by [agora](https://www.agora.io/en/), paid
 * [Jitsi - Voice & Video & ScreenShare](https://doc.livehelperchat.com/docs/integrating/jitsi) powered by [jitsi](https://jitsi.org/), Free out of the box integration
 * [Rest API](https://api.livehelperchat.com)
 * [Bot](https://doc.livehelperchat.com/docs/how-to-use-bot) with the possibility to integrate any third-party AI
   * E.g Rasa AI https://doc.livehelperchat.com/docs/bot/rasa-integration-intent
   * E.g ChatGPT (Chat Responses) https://doc.livehelperchat.com/docs/bot/chatgpt-responses
     * https://youtu.be/Z-DzsIygsX0 video how to setup
     * https://youtu.be/F0ou70cu7x0 video how to use with third party Rest API
   * E.g ChatGPT (Chat Completion) https://doc.livehelperchat.com/docs/bot/chatgpt-integration 
   * E.g ChatGPT (Assistant) https://github.com/LiveHelperChat/chatGPT Will be depreciated by OpenAI
   * E.g Ollama AI https://doc.livehelperchat.com/docs/bot/ollama-integration
   * E.g Gemini https://doc.livehelperchat.com/docs/bot/gemini-integration
   * E.g Dialogflow https://github.com/LiveHelperChat/dialogflowes
 * [Telegram](https://github.com/LiveHelperChat/telegram)
 * [Viber](https://github.com/LiveHelperChat/viber)
 * [Discord](https://github.com/LiveHelperChat/discord-bot)
 * [Rasa](https://doc.livehelperchat.com/docs/bot/rasa-integration)
 * [n8n](https://doc.livehelperchat.com/docs/bot/n8n-integration)
 * [Mattermost](https://github.com/LiveHelperChat/mattermost)
 * [Facebook messenger, Intsagram](https://github.com/LiveHelperChat/fbmessenger)
 * [Facebook WhatsApp](https://github.com/LiveHelperChat/fbmessenger#whatsapp-configuration)
 * [Facebook Instagram](https://github.com/LiveHelperChat/fbmessenger#whatsapp-configuration)
 * [Insult detection](https://github.com/LiveHelperChat/lhcinsult) powered by [DeepPavlov.ai](https://demo.deeppavlov.ai/#/en/insult) and [NudeNet](https://github.com/notAI-tech/NudeNet)
 * [SMS, WhatsApp](https://github.com/LiveHelperChat/twilio) (Twilio based)
 * [WhatsApp](https://doc.livehelperchat.com/docs/integrating/whatsapp) open-wa based.
 * [Elasticsearch](https://github.com/LiveHelperChat/elasticsearch) get statistic for millions of chats in seconds
 * [Node.js](https://github.com/LiveHelperChat/NodeJS-Helper)
 * [Docker](https://github.com/LiveHelperChat/docker-standalone)
 * [Background worker for heavy tasks](https://github.com/LiveHelperChat/lhc-php-resque) offload Rest API calls
 * Integrate any [third party Rest API](https://doc.livehelperchat.com/docs/bot/rest-api)
 * [Google Authentication](https://github.com/LiveHelperChat/lhcgoogleauth) login using Google account
 * [2FA](https://github.com/LiveHelperChat/2fa) `Authenticator` mobile app support
 * [Amazon S3](https://github.com/LiveHelperChat/amazon-s3) scale infinitely by storing app files in the cloud
 * [Desktop app](https://github.com/LiveHelperChat/electron) written with electron
 * [Sentiment analysis using DeepPavlov](https://github.com/LiveHelperChat/sentiment)
 * [Shopify integration](https://github.com/LiveHelperChat/pluginshopify) 
 * [MessageBird integration](https://doc.livehelperchat.com/docs/integrating/messagebird-whatsapp/) 
 * [https://bird.com](https://github.com/LiveHelperChat/mbird)
 * [CloudTalk integration](https://doc.livehelperchat.com/docs/integrating/cloudtalk/) 
 * [Chat API integration](https://doc.livehelperchat.com/docs/integrating/chat-api-com/) 
 * [LDAP](https://github.com/LiveHelperChat/lhldap)
 * [https://www.wildix.com](https://github.com/LiveHelperChat/wildixin)
 * [MS Authentification](https://github.com/LiveHelperChat/lhcmsauth)
 * [https://zapier.com](https://github.com/LiveHelperChat/zapier)
 * SSO 
   * https://github.com/LiveHelperChat/ssoprovider-demo
   * https://github.com/LiveHelperChat/ssoprovider

## Quick development guide
 * After the app is installed, disable cache and enable debug output.
   * https://github.com/LiveHelperChat/livehelperchat/blob/master/lhc_web/settings/settings.ini.default.php#L13-L16
   * Change the following values to:
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
 * To recompile back office React APP (Left toolbar, Group Chat etc...)
   * `cd lhc_web/design/defaulttheme/js/admin &&  npm run build`
 * Recompile static JS/CSS files. This is required if you change core JS files. It also avoids missing CSS/JS files if more than one server is used.
   * `php cron.php -s site_admin -c cron/util/generate_css -p 1 && gulp js-static`
 * ORM - https://doc.livehelperchat.com/docs/development/orm
 * Common classes - https://doc.livehelperchat.com/docs/development/common-classes
 * To recompile everything at once
   * `cd lhc_web/ && ./deploy.sh`

## Extensions
https://github.com/LiveHelperChat

## Translations contribution
https://explore.transifex.com/remigijus-kiminas/live-helper-chat/

## Folders structure

 * Directories content:
  * lhc_web - WEB application folder.
 
## Features

A few main features:

 * [Bot](https://doc.livehelperchat.com/docs/how-to-use-bot) with the possibility to integrate any third-party AI
 * Tens of thousands chats per day support using [ElasticSearch](https://github.com/LiveHelperChat/elasticsearch), [NodeJS](https://github.com/LiveHelperChat/NodeJS-Helper), [PHP-Resque](https://github.com/LiveHelperChat/lhc-php-resque) System is highly optimised and battle tested for high load environment
 * XMPP support for notifications about new chats (iPhone, iPad, Android, Blackberry, GTalk, etc.)
 * Chrome extension
 * Repeatable sound notifications
 * Work hours
 * See what the user sees with a screenshot feature
 * Drag & Drop widgets, minimize/maximize widgets
 * Multiple chats at the same time
 * See what users are typing before they send a message
 * Multiple operators
 * Send delayed canned messages as if it was a real user typing
 * Chats archive
 * Priority queue
 * [Chats statistic generation](https://doc.livehelperchat.com/docs/chat/statistic)
 * Advanced agents performance tracking
 * Resume chat after the user closed the chat
 * All chats in a single window with tabs interface; tabs are remembered before they are closed
 * Chat transcript print
 * Chat transcript send by mail
 * Site widget
 * Page embed mode for live support script or widget mode, or standard mode
 * Multilanguage
 * Chats transferring
 * Departments
 * Files upload
 * Chat search
 * Automatic transfers between departments
 * Option to generate JS for different departments
 * Option to prefill form fields. 
 * Option to add custom form fields, which can be either user variables or hidden fields. Useful if you are integrating with a third-party system and want to pass user_id, for example.
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
 * Option to see what user is typing before they send a message
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
 * Automatic chat closing
 * Distribution of visitors to different operators depending on their GEO / language (two different clients in different languages are contacting. It is possible to configure the distribution so that a Lithuanian-speaking client gets to a Lithuanian-speaking operator, and an English-speaking client to an English-speaking operator)
 * Custom distribution of visitors based on their attributes.
 * Subjects/Topics for chat
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
