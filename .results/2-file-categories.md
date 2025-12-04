# Live Helper Chat - File Categorization

## File Categories Summary

This document categorizes all files in the Live Helper Chat codebase by their role in the application architecture.

---

## 1. PHP Models (137 files)
**Location:** `lib/models/`
**Purpose:** Database entity classes using `erLhcoreClassDBTrait` for ORM operations

### Chat Domain Models
- `lib/models/lhchat/erlhcoreclassmodelchat.php` - Core chat entity
- `lib/models/lhchat/erlhcoreclassmodelmsg.php` - Chat messages
- `lib/models/lhchat/erlhcoreclassmodelchatfile.php` - File attachments
- `lib/models/lhchat/erlhcoreclassmodelchatonlineuser.php` - Online visitors
- `lib/models/lhchat/erlhcoreclassmodelchataction.php` - Chat actions
- `lib/models/lhchat/erlhcoreclassmodelchatconfig.php` - Chat configuration
- `lib/models/lhchat/erlhcoreclassmodelchatblockeduser.php` - Blocked users
- `lib/models/lhchat/erlhcoreclassmodelcannedmsg.php` - Canned messages
- `lib/models/lhchat/erlhcoreclassmodelgroupchat.php` - Group chats
- `lib/models/lhchat/erlhcoreclassmodelgroupmsg.php` - Group chat messages
- `lib/models/lhchat/erlhcoreclassmodelchatarchive.php` - Archived chats
- `lib/models/lhchat/erlhcoreclassmodelchatvoicevideo.php` - Voice/Video calls
- `lib/models/lhchat/erlhcoreclassmodelchatwebhook.php` - Webhooks
- `lib/models/lhchat/erlhcoreclassmodelchatincoming.php` - Incoming webhooks

### User/Permission Models
- `lib/models/lhuser/erlhcoreclassmodeluser.php` - User accounts
- `lib/models/lhuser/erlhcoreclassmodelgroup.php` - User groups
- `lib/models/lhuser/erlhcoreclassmodelgroupuser.php` - Group memberships
- `lib/models/lhuser/erlhcoreclassmodelusersession.php` - User sessions
- `lib/models/lhuser/erlhcoreclassmodelusersetting.php` - User settings
- `lib/models/lhpermission/erlhcoreclassmodelrole.php` - Roles
- `lib/models/lhpermission/erlhcoreclassmodelrolefunction.php` - Role functions
- `lib/models/lhpermission/erlhcoreclassmodelgrouprole.php` - Group role assignments

### Department Models
- `lib/models/lhdepartament/erlhcoreclassmodeldepartament.php` - Departments
- `lib/models/lhdepartament/erlhcoreclassmodeldepartamentgroup.php` - Department groups
- `lib/models/lhdepartament/erlhcoreclassmodeldepartamentgroupuser.php` - Dept group users
- `lib/models/lhdepartament/erlhcoreclassmodeluserdep.php` - User-department assignments
- `lib/models/lhdepartament/erlhcoreclassmodeldepartamentavailability.php` - Availability tracking
- `lib/models/lhdepartament/erlhcoreclassmodeldepartamentcustomworkhours.php` - Custom work hours

### Bot Models
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbotbot.php` - Bot definitions
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbottrigger.php` - Bot triggers
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbottriggerevent.php` - Trigger events
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbotgroup.php` - Bot trigger groups
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbotcommand.php` - Bot commands
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbotrestapi.php` - Bot REST API calls
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbotpayload.php` - Bot payloads
- `lib/models/lhgenericbot/erlhcoreclassmodelgenericbotexception.php` - Bot exceptions

### Mail Conversation Models
- `lib/models/lhmailconv/erlhcoreclassmodelmailconvconversation.php` - Email conversations
- `lib/models/lhmailconv/erlhcoreclassmodelmailconvmessage.php` - Email messages
- `lib/models/lhmailconv/erlhcoreclassmodelmailconvmailbox.php` - Mailboxes
- `lib/models/lhmailconv/erlhcoreclassmodelmailconvmatchrule.php` - Matching rules
- `lib/models/lhmailconv/erlhcoreclassmodelmailconvresponsetemplate.php` - Response templates
- `lib/models/lhmailconv/erlhcoreclassmodelmailconvfile.php` - Email attachments

### Abstract/Common Models
- `lib/models/lhabstract/erlhabstractmodelwidgettheme.php` - Widget themes
- `lib/models/lhabstract/erlhabstractmodelsurvey.php` - Surveys
- `lib/models/lhabstract/erlhabstractmodelsurveyitem.php` - Survey responses
- `lib/models/lhabstract/erlhabstractmodelautoresponder.php` - Auto responders
- `lib/models/lhabstract/erlhabstractmodeleproactivechatinvitation.php` - Proactive invitations
- `lib/models/lhabstract/erlhabstractmodelemailtemplate.php` - Email templates
- `lib/models/lhabstract/erlhabstractmodelsubject.php` - Chat subjects
- `lib/models/lhabstract/erlhabstractmodelform.php` - Forms
- `lib/models/lhabstract/erlhabstractmodelrestapikey.php` - REST API keys

---

## 2. PHP Controllers/Views (843+ files)
**Location:** `modules/lh*/`
**Purpose:** Controller logic handling HTTP requests and rendering views

### Chat Module (`modules/lhchat/`)
- `adminchat.php` - Admin chat interface
- `accept.php` - Accept chat
- `transferchat.php` - Transfer chat
- `closechat.php` - Close chat
- `addmsgadmin.php` - Add operator message
- `addmsguser.php` - Add visitor message
- `syncadmin.php` - Admin sync polling
- `syncuser.php` - User sync polling
- `onlineusers.php` - Online visitors list
- `chatwidget.php` - Widget rendering
- `start.php` - Start chat flow
- `getstatus.php` - Get widget status

### Bot Module (`modules/lhgenericbot/`)
- `bot.php` - Bot editor
- `list.php` - Bot list
- `addtrigger.php` - Add trigger
- `savetrigger.php` - Save trigger
- `import.php` - Import bot
- `downloadbot.php` - Export bot
- `commands.php` - Bot commands
- `conditions.php` - Bot conditions

### User Module (`modules/lhuser/`)
- `login.php` - Login page
- `logout.php` - Logout
- `account.php` - User account settings
- `new.php` - Create user
- `edit.php` - Edit user
- `userlist.php` - User listing
- `grouplist.php` - Group listing
- `userdepartments.php` - User department assignments

### Mail Conversation Module (`modules/lhmailconv/`)
- `index.php` - Mail list
- `view.php` - View conversation
- `sendemail.php` - Send email
- `mailbox.php` - Mailbox management
- `matchingrules.php` - Mail routing rules
- `responsetemplates.php` - Email templates

### REST API Module (`modules/lhrestapi/`)
- `chat.php` - Chat API
- `chats.php` - List chats API
- `fetchchat.php` - Fetch single chat
- `addmsgadmin.php` - Add message API
- `startchat.php` - Start chat API
- `onlineusers.php` - Online users API
- `login.php` - Authentication API
- `departments.php` - Departments API

### Other Key Modules
- `modules/lhdepartment/` - Department management
- `modules/lhstatistic/` - Statistics and reports
- `modules/lhsurvey/` - Survey functionality
- `modules/lhcannedmsg/` - Canned messages
- `modules/lhtheme/` - Theme management
- `modules/lhpermission/` - Permissions/roles
- `modules/lhwebhooks/` - Webhook configuration
- `modules/lhgroupchat/` - Operator group chats
- `modules/lhnotifications/` - Push notifications
- `modules/lhvoicevideo/` - Voice/video calls

---

## 3. PHP Core Classes (465+ files)
**Location:** `lib/core/`
**Purpose:** Business logic, validators, and service classes

### Core Framework
- `lib/core/lhcore/lhmodule.php` - Module/routing handler
- `lib/core/lhtpl/tpl.php` - Template engine
- `lib/core/lhcore/lhcacheconfig.php` - Cache configuration
- `lib/core/lhcore/lhdbtrait.php` - Database trait (ORM)
- `lib/core/lhcore/lhpagelayout.php` - Page layouts

### Chat Services
- `lib/core/lhchat/lhchat.php` - Chat service class
- `lib/core/lhchat/lhchatvalidator.php` - Chat validation
- `lib/core/lhchat/lhchatworkflow.php` - Chat workflows
- `lib/core/lhchat/lhchatstatistic.php` - Chat statistics

### Bot Services
- `lib/core/lhgenericbot/lhgenericbot.php` - Bot execution
- `lib/core/lhgenericbot/lhgenericbotworkflow.php` - Bot workflows
- `lib/core/lhgenericbot/lhgenericbotvalidator.php` - Bot validation
- `lib/core/lhgenericbot/lhgenericbotworker.php` - Bot worker
- `lib/core/lhgenericbot/actionTypes/*.php` - Bot action implementations

### User/Permission Services
- `lib/core/lhuser/lhuser.php` - User service
- `lib/core/lhuser/lhuservalidator.php` - User validation
- `lib/core/lhpermission/lhpermission.php` - Permission checking

### Mail Services
- `lib/core/lhmailconv/lhmailconv.php` - Mail service
- `lib/core/lhmailconv/lhmailconvworker.php` - Mail worker
- `lib/core/lhmailconv/lhmailconvparser.php` - Email parsing
- `lib/core/lhmailconv/lhmailconvvalidator.php` - Mail validation

### Translation Services
- `lib/core/lhtranslate/lhtranslate.php` - Translation interface
- `lib/core/lhtranslate/lhgoogletranslate.php` - Google Translate
- `lib/core/lhtranslate/lhdeepltranslate.php` - DeepL integration

---

## 4. PHP Templates (1500+ files)
**Location:** `design/defaulttheme/tpl/`
**Purpose:** View templates for rendering HTML

### Page Layouts
- `tpl/pagelayouts/main.php` - Main admin layout
- `tpl/pagelayouts/login.php` - Login layout
- `tpl/pagelayouts/widget.php` - Widget layout
- `tpl/pagelayouts/popup.php` - Popup layout
- `tpl/pagelayouts/modal.php` - Modal layout

### Module Templates
- `tpl/lhchat/*.tpl.php` - Chat templates
- `tpl/lhuser/*.tpl.php` - User templates
- `tpl/lhgenericbot/*.tpl.php` - Bot builder templates
- `tpl/lhmailconv/*.tpl.php` - Mail templates
- `tpl/lhdepartment/*.tpl.php` - Department templates
- `tpl/lhstatistic/*.tpl.php` - Statistics templates
- `tpl/lhtheme/*.tpl.php` - Theme templates

### Partial Templates
- `tpl/pagelayouts/parts/*.tpl.php` - Reusable parts
- `tpl/*/parts/*.tpl.php` - Module-specific parts
- `tpl/*_multiinclude.tpl.php` - Extension hook points

---

## 5. POS Definitions (155 files)
**Location:** `pos/`
**Purpose:** Persistent Object Schema definitions for eZ Components ORM

Each model has a corresponding POS file defining:
- Table columns and types
- Property mappings
- Lazy loading configuration

Example: `pos/lhchat/erlhcoreclassmodelchat.php`

---

## 6. React Components (Widget App)
**Location:** `design/defaulttheme/widget/react-app/src/`

### Components
- `components/App.js` - Main app container
- `components/StartChat.js` - Chat start form
- `components/OnlineChat.js` - Active chat interface
- `components/OfflineChat.js` - Offline message form
- `components/ChatMessage.js` - Message rendering
- `components/HeaderChat.js` - Chat header
- `components/BodyChat.js` - Chat body
- `components/ChatFileUploader.js` - File upload
- `components/ChatDepartment.js` - Department selection
- `components/ChatField.js` - Form fields
- `components/ChatStatus.js` - Status indicator
- `components/InlineSurvey.js` - Survey display
- `components/ProactiveInvitation.js` - Proactive chat

### Redux Store
- `store/index.js` - Store configuration
- `reducers/chatWidgetReducer.js` - Chat state reducer
- `actions/chatActions.js` - Action creators
- `constants/action-types.js` - Action type constants

---

## 7. React Components (Bot Builder)
**Location:** `design/defaulttheme/js/react/src/`

### Components
- Bot trigger editor
- Bot action configuration
- REST API method builder
- Bot response preview

---

## 8. Svelte Components (Dashboard)
**Location:** `design/defaulttheme/js/svelte/src/`

### Main Components
- `LHC.svelte` - Main dashboard container
- `LHCChat/` - Chat management components
- `LHCOnlineVisitors.svelte` - Online visitors
- `LHCStatus.svelte` - Status components
- `LHCEditor/` - Rich text editor
- `Widgets/` - Dashboard widgets

### Utilities
- `stores.js` - Svelte stores
- `lib/` - Utility functions
- `i18n/` - Internationalization
- `Helpers/` - Helper functions

---

## 9. JavaScript Utilities (Legacy/Admin)
**Location:** `design/defaulttheme/js/admin/src/`

### React Admin Components
- Canned message suggester
- Mail support interface
- Dashboard chat tabs
- Group chats interface

---

## 10. Widget Wrapper (Vanilla JS)
**Location:** `design/defaulttheme/widget/wrapper/src/`

### Core Files
- `index.js` - Entry point
- `lib/UIConstructor.js` - Widget DOM builder
- `lib/UIConstructorIframe.js` - Iframe handler
- `lib/settings.js` - Widget settings

### Widget Types
- `lib/widgets/mainWidget.js` - Main chat widget
- `lib/widgets/statusWidget.js` - Online status
- `lib/widgets/needhelpWidget.js` - Need help bubble
- `lib/widgets/msgSnippetWidget.js` - Message preview

### Utilities
- `util/chatEventsHandler.js` - Chat events
- `util/proactiveChat.js` - Proactive invitations
- `util/storageHandler.js` - Local storage
- `util/screenShare.js` - Screen sharing

---

## 11. Configuration Files

### Build Configuration
- `webpack.config.js` - Root webpack config
- `gulpfile.js` - Gulp tasks
- `design/defaulttheme/js/react/package.json`
- `design/defaulttheme/js/svelte/package.json`
- `design/defaulttheme/widget/react-app/webpack.config.js`
- `design/defaulttheme/widget/wrapper/webpack.config.js`

### Application Configuration
- `settings/settings.ini.default.php` - Default settings
- `settings/settings.ini.php` - Active settings

---

## 12. Module Definition Files
**Location:** `modules/lh*/module.php`
**Purpose:** Define URL routes, parameters, and permissions

Each module has a `module.php` defining:
- View list with parameters
- Permission functions
- URL parameter types

---

## 13. Cron Jobs
**Location:** `cron.php` and `modules/lhcron/`

### Available Cronjobs
- Chat maintenance
- Mail synchronization
- Statistics calculation
- Notification sending
- Archive processing
- Auto-responder execution
- Bot workflow processing

---

## 14. SQL Schema
**Location:** `doc/update_db/`

- `structure.json` - Full database schema
- Update scripts for migrations

---

## 15. Translation Files
**Location:** `translations/`

- Gettext `.po`/`.mo` files
- 30+ language translations

---

## 16. Documentation
**Location:** `doc/`

- `CHANGELOG.txt` - Version history
- `INSTALL.txt` - Installation guide
- `rest_api/` - API documentation
- `http_conf_examples/` - Web server configs
- `node_servers/` - Node.js integration docs

---

## File Count Summary

| Category | Approximate Count |
|----------|------------------|
| PHP Models | ~137 |
| PHP Controllers | ~843 |
| PHP Core Classes | ~465 |
| PHP Templates | ~1,511 |
| POS Definitions | ~155 |
| React Widget Components | ~40 |
| Svelte Dashboard Components | ~50 |
| JavaScript Utilities | ~100 |
| Module Definitions | ~50 |
| Configuration Files | ~20 |
| Translation Files | ~60 |
| **Total** | **~3,500+** |
