# Live Helper Chat - Technology Stack Analysis

## Core Technology Analysis

### Programming Languages
- **PHP 8.2+** - Primary backend language for server-side logic, APIs, and business logic
- **JavaScript** - Frontend interactivity, widgets, and admin dashboard
- **HTML/CSS** - Template rendering and styling

### Primary Framework
- **Custom PHP MVC Framework** - Live Helper Chat uses its own custom-built MVC framework built on top of **eZ Components** (ezcBase, ezcDatabase, ezcPersistentObject, etc.)
- The framework uses a modular architecture where each feature is encapsulated in a "module" (e.g., `lhchat`, `lhuser`, `lhgenericbot`)

### Secondary/Tertiary Frameworks
- **Slim Framework 4.14** - Used for REST API endpoints
- **React 18** - Used for:
  - Bot builder application (`design/defaulttheme/js/react`)
  - Chat widget embedded in websites (`design/defaulttheme/widget/react-app`)
  - Back office components (canned messages, mail support, dashboard chat tabs)
- **Svelte 4** - Used for the dashboard/admin interface (`design/defaulttheme/js/svelte`)
- **AngularJS** (Legacy) - Still present for some admin functionality (being phased out)

### State Management
- **Redux** with redux-thunk, redux-promise-middleware - Used in React applications
- **Svelte Stores** - Used in Svelte dashboard components
- **PHP Session Management** - Server-side state with custom session handlers
- **APC/Redis/Memcache** - For caching and shared state

### Database & ORM
- **MySQL** - Primary database
- **eZ Components PersistentObject** - ORM layer for database operations
- **Custom `erLhcoreClassDBTrait`** - Database trait providing CRUD operations, query building, and caching
- **Elasticsearch 7.x** - For advanced search capabilities

### Build Tools & Bundlers
- **Webpack 5** - JavaScript bundling for React applications
- **Rollup** - Used for Svelte application bundling
- **Gulp 5** - Task automation (JS concatenation, minification, compilation)
- **Babel** - JavaScript transpilation

### External Integrations
- **Telegram Bot API** (longman/telegram-bot)
- **Facebook Messenger SDK** (fb-messenger-sdk)
- **AWS SDK** - File storage, services integration
- **Web Push Notifications** (minishlink/web-push)
- **DeepL API** - Translation services
- **IMAP/SMTP** - Email integration (php-imap, webklex/php-imap)
- **Google Authenticator** - 2FA support
- **OAuth2** (league/oauth2-server)

### Other Technologies
- **QR Code Generation** (endroid/qr-code)
- **Markdown Processing** (michelf/php-markdown)
- **XMPP** - Real-time messaging protocol support
- **WebRTC** - Voice/video chat capabilities

---

## Domain Specificity Analysis

### Problem Domain
**Live Customer Support Platform** - A comprehensive, open-source live chat and help desk system designed for:
- Real-time customer-operator communication
- Multi-channel support (web chat, email, Telegram, Facebook Messenger)
- AI/Bot-assisted customer interactions
- Department-based ticket routing and management

### Core Business Concepts
1. **Chat/Conversation Management** - Real-time bidirectional communication between visitors and operators
2. **Department Routing** - Organizing support staff into departments with configurable routing rules
3. **Bot Automation** - Configurable chatbots with trigger-based responses and workflows
4. **Operator Management** - User roles, permissions, availability status, workload balancing
5. **Canned Responses** - Pre-defined message templates for faster responses
6. **Proactive Chat Invitations** - Auto-triggered chat invitations based on visitor behavior
7. **Email Conversations (Mail Conv)** - Integrated email ticketing system
8. **Survey & Feedback** - Post-chat surveys and customer satisfaction tracking
9. **Statistics & Reporting** - Chat metrics, operator performance, wait times

### User Interactions Supported
- **Visitor Side**: Starting chats, filling pre-chat forms, file uploads, leaving offline messages, receiving proactive invitations
- **Operator Side**: Accepting/transferring chats, multi-chat handling, using canned messages, viewing visitor info, collaborative chatting
- **Admin Side**: Configuring departments, managing users/permissions, setting up bots, viewing statistics, theme customization
- **Bot Interactions**: Automated responses, collecting information, triggering workflows, handoff to humans

### Primary Data Types & Structures
- **Chat** (`lh_chat`) - Core entity representing a conversation with status, timestamps, participants
- **Message** (`lh_msg`) - Individual messages within chats
- **User/Operator** (`lh_users`) - Support staff accounts with roles and settings
- **Department** (`lh_departament`) - Organizational units for chat routing
- **Online User** (`lh_chat_online_user`) - Tracking website visitors
- **Bot Triggers** (`lh_generic_bot_trigger`) - Automated response configurations
- **Mail Conversation** (`lhc_mailconv_conversation`) - Email-based tickets
- **Survey Items** (`lh_abstract_survey_item`) - Customer feedback data
- **Canned Messages** (`lh_canned_msg`) - Pre-defined response templates

---

## Application Boundaries

### Features Within Scope (Based on Existing Code)
1. **Real-time Chat Widget** - Embeddable JavaScript widget for websites
2. **Multi-operator Dashboard** - Svelte/React-based admin interface
3. **Department Management** - CRUD operations, routing rules, work hours
4. **Bot Builder** - Visual trigger-based bot configuration
5. **REST API** - Complete API for external integrations
6. **Email Integration** - IMAP-based email conversations
7. **File Transfers** - Chat attachments and file sharing
8. **Voice/Video Calls** - WebRTC-based communication
9. **Canned Messages** - Response templates with variables
10. **Proactive Chat** - Behavior-triggered invitations
11. **Statistics Dashboard** - Operator and chat analytics
12. **Multi-language Support** - Extensive translation system
13. **Theme Customization** - Widget and admin theming
14. **Webhook Integrations** - Incoming/outgoing webhooks
15. **XMPP Integration** - External messenger connectivity
16. **Survey System** - Post-chat feedback collection

### Architecturally Inconsistent Features Would Be
- **Standalone mobile apps** (architecture is web-focused)
- **Non-relational primary database** (heavily tied to MySQL/eZ Components)
- **Server-side rendering frameworks** like Next.js (uses custom PHP templating)
- **Microservices architecture** (monolithic by design)
- **GraphQL API** (REST-based architecture)

### Specialized Libraries/Domain Constraints
- **eZ Components** - Tightly coupled for database operations and core framework
- **Custom MVC Pattern** - Module-based with specific URL routing conventions
- **Template System** - Custom PHP-based templates (`*.tpl.php`)
- **Permission System** - Role-based with function-level permissions
- **Extension System** - Plugin architecture for customization without core modification

---

## Key Technical Patterns

### URL Routing Convention
URLs follow pattern: `/{siteaccess}/{module}/{view}/{params}`
- Example: `/site_admin/chat/adminchat/123` → `modules/lhchat/adminchat.php`

### Model Convention
- Models use `erLhcoreClassDBTrait` trait
- Static properties define table mapping: `$dbTable`, `$dbTableId`, `$dbSessionHandler`
- Lifecycle hooks: `beforeSave()`, `afterSave()`, `beforeRemove()`, etc.

### Template Override System
- Templates can be overridden via extensions or custom themes
- Search path: `extension/{ext}/design/{ext}theme/tpl/` → `design/customtheme/tpl/` → `design/defaulttheme/tpl/`

### Extension Points
- Module overrides via extension folder
- Template overrides via theme hierarchy
- Event hooks throughout core functionality
- Webhook system for external integrations
