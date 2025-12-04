# Data Flow Documentation

## 1. Chat Lifecycle Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          CHAT LIFECYCLE                                  │
└─────────────────────────────────────────────────────────────────────────┘

VISITOR STARTS CHAT
    │
    ▼
┌─────────────────────┐
│ Widget JavaScript   │  → POST /widgetrestapi/startchat
│ (React App)         │
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ startchat.php       │  → Creates erLhcoreClassModelChat
│ Controller          │  → Creates initial message
└─────────────────────┘  → Assigns bot or sets pending
    │
    ▼
┌─────────────────────┐
│ Event Dispatch      │  → 'chat.chat_started'
│                     │  → Mobile notifications
└─────────────────────┘  → Webhooks
    │
    ▼
┌─────────────────────┐
│ Database            │  → lh_chat (status=0 pending)
│                     │  → lh_msg (initial message)
└─────────────────────┘
    │
    ▼
OPERATOR ACCEPTS (or Auto-Assign)
    │
    ▼
┌─────────────────────┐
│ acceptchat.php      │  → Updates chat.user_id
│ or auto-assign cron │  → Sets status=1 (active)
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Event Dispatch      │  → 'chat.chat_accepted'
└─────────────────────┘

CHAT MESSAGING LOOP
    │
    ▼
┌─────────────────────┐     ┌─────────────────────┐
│ Visitor Message     │     │ Operator Message    │
│ /widgetrestapi/     │     │ /chat/addmsgadmin   │
│ addmsguser          │     │                     │
└─────────────────────┘     └─────────────────────┘
    │                           │
    ▼                           ▼
┌─────────────────────┐     ┌─────────────────────┐
│ Event: addmsguser   │     │ Event: web_add_     │
│ → Bot processing    │     │ msg_admin           │
│ → Notifications     │     │ → Notifications     │
└─────────────────────┘     └─────────────────────┘
    │                           │
    └───────────┬───────────────┘
                ▼
┌─────────────────────┐
│ Long Polling /      │  → Visitor and operator poll
│ WebSocket           │  → for new messages
└─────────────────────┘

CHAT CLOSE
    │
    ▼
┌─────────────────────┐
│ closechat.php       │  → Sets status=2 (closed)
│                     │  → Records duration
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Event: chat_closed  │  → Survey trigger
│                     │  → Webhooks
└─────────────────────┘  → Analytics
```

## 2. Bot Processing Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          BOT MESSAGE PROCESSING                          │
└─────────────────────────────────────────────────────────────────────────┘

VISITOR MESSAGE RECEIVED
    │
    ▼
┌─────────────────────┐
│ addmsguser.php      │  → Creates message record
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Event Dispatch      │  → 'chat.genericbot_handler'
└─────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────┐
│ erLhcoreClassGenericBot::processMessage()   │
└─────────────────────────────────────────────┘
    │
    ├──► Get bot assigned to chat (gbot_id)
    │
    ├──► Load trigger events (patterns)
    │    └── lh_generic_bot_trigger_event
    │
    ├──► Pattern Matching
    │    ├── Contains match
    │    ├── Regex match
    │    ├── Exact match
    │    └── Starts with match
    │
    ▼
┌─────────────────────┐
│ Found Matching      │  → No match: Use default_unknown trigger
│ Trigger?            │
└─────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────┐
│ Execute Trigger Actions                      │
├──────────────────────────────────────────────┤
│ ┌─────────────┐  ┌─────────────┐            │
│ │ text        │  │ rest_api    │            │
│ │ Send message│  │ Call API    │            │
│ └─────────────┘  └─────────────┘            │
│ ┌─────────────┐  ┌─────────────┐            │
│ │ collect     │  │ transfer    │            │
│ │ Gather data │  │ To operator │            │
│ └─────────────┘  └─────────────┘            │
│ ┌─────────────┐  ┌─────────────┐            │
│ │ trigger     │  │ condition   │            │
│ │ Chain next  │  │ If/else     │            │
│ └─────────────┘  └─────────────┘            │
└─────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Bot Messages        │  → Creates lh_msg records
│ Created             │  → user_id = -2 (bot)
└─────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                          REST API ACTION                                 │
└─────────────────────────────────────────────────────────────────────────┘

TRIGGER REST API ACTION
    │
    ▼
┌─────────────────────┐
│ Load API Config     │  → lh_generic_bot_rest_api
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Variable Replace    │  → {lhc.email} → visitor email
│                     │  → {collected.field} → form data
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ HTTP Request        │  → cURL call to external API
│                     │  → Timeout handling
└─────────────────────┘
    │
    ├──► Success → Execute success_trigger
    │              Store response in chat variables
    │
    └──► Failure → Execute failure_trigger
```

## 3. Operator Dashboard Sync Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                     DASHBOARD SYNCHRONIZATION                            │
└─────────────────────────────────────────────────────────────────────────┘

OPERATOR OPENS DASHBOARD
    │
    ▼
┌─────────────────────┐
│ Svelte Dashboard    │  → Initial load
│ Application         │  → GET chat lists
└─────────────────────┘
    │
    ▼
SYNC LOOP (every N seconds based on config)
    │
    ▼
┌─────────────────────┐
│ POST /chat/         │  → Send: last sync timestamp
│ syncadmininterface  │  → Send: current chat IDs
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Server Processing   │  → Query new/changed chats
│                     │  → Check pending count
└─────────────────────┘  → Check transfers
    │
    ▼
┌─────────────────────┐
│ Response            │  → New pending chats
│                     │  → Chat updates
└─────────────────────┘  → Notification triggers
    │
    ▼
┌─────────────────────┐
│ Svelte Store        │  → Update reactive stores
│ Update              │  → Re-render affected components
└─────────────────────┘
    │
    ▼
REPEAT SYNC LOOP
```

## 4. Widget Loading Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          WIDGET INITIALIZATION                           │
└─────────────────────────────────────────────────────────────────────────┘

WEBSITE LOADS
    │
    ▼
┌─────────────────────┐
│ Embed Code          │  → Loads new.js (wrapper script)
│ <script src="...">  │
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ new.js              │  → Creates iframe
│ (Vanilla JS)        │  → Establishes postMessage
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Widget React App    │  → Loads inside iframe
│                     │  → Checks for stored session
└─────────────────────┘
    │
    ├──► Has Session → Resume chat
    │    └── GET /widgetrestapi/fetchmessages
    │
    └──► No Session → Show start form
         │
         ▼
    ┌─────────────────────┐
    │ Department Check    │  → /widgetrestapi/getstatus
    │                     │  → Returns online/offline
    └─────────────────────┘
         │
         ▼
    ┌─────────────────────┐
    │ Form Display        │  → Start chat form
    │                     │  → OR offline form
    └─────────────────────┘

PROACTIVE INVITATION
    │
    ▼
┌─────────────────────┐
│ Online User Track   │  → /widgetrestapi/checkinvitation
│                     │  → Evaluates invitation rules
└─────────────────────┘
    │
    ├──► Match Found → Display invitation bubble
    │
    └──► No Match → Continue tracking
```

## 5. Email Conversation Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          EMAIL PROCESSING                                │
└─────────────────────────────────────────────────────────────────────────┘

CRON SYNCS MAILBOX
    │
    ▼
┌─────────────────────┐
│ php cron.php        │  → Connects to IMAP
│ -s site_admin       │  → Fetches new emails
│ -e mailconv         │
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Email Parser        │  → Parse headers, body
│                     │  → Extract attachments
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Conversation        │  → Match by In-Reply-To
│ Matching            │  → Match by sender email
│                     │  → Or create new conversation
└─────────────────────┘
    │
    ├──► Existing Conversation
    │    └── Add message to thread
    │    └── Update conversation status
    │
    └──► New Conversation
         │
         ▼
    ┌─────────────────────┐
    │ Match Rules         │  → Apply routing rules
    │ Processing          │  → Assign department
    │                     │  → Set priority
    └─────────────────────┘
         │
         ▼
    ┌─────────────────────┐
    │ Create Records      │  → lhc_mailconv_conversation
    │                     │  → lhc_mailconv_msg
    └─────────────────────┘

OPERATOR REPLIES
    │
    ▼
┌─────────────────────┐
│ Compose Reply       │  → /mailconv/sendreply
│                     │  → Apply templates
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ SMTP Send           │  → Connect to SMTP
│                     │  → Send with threading headers
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Record Message      │  → Store in database
│                     │  → Update conversation
└─────────────────────┘
```

## 6. Authentication Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          OPERATOR LOGIN                                  │
└─────────────────────────────────────────────────────────────────────────┘

LOGIN REQUEST
    │
    ▼
┌─────────────────────┐
│ POST /user/login    │  → username, password
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ erLhcoreClassUser   │  → Find user by username/email
│ ::authenticate()    │
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Password Verify     │  → password_verify() or md5
│                     │  → Upgrade legacy hashes
└─────────────────────┘
    │
    ├──► Success
    │    │
    │    ▼
    │    ┌─────────────────────┐
    │    │ Create Session      │  → $_SESSION['lhc_user_id']
    │    │                     │  → Regenerate session ID
    │    └─────────────────────┘
    │    │
    │    ▼
    │    ┌─────────────────────┐
    │    │ Remember Me?        │  → Set cookie with token
    │    │ (optional)          │  → Store in lh_users_remember
    │    └─────────────────────┘
    │    │
    │    ▼
    │    ┌─────────────────────┐
    │    │ Update User         │  → session_id field
    │    │                     │  → llogin timestamp
    │    └─────────────────────┘
    │    │
    │    ▼
    │    Redirect to dashboard
    │
    └──► Failure
         │
         ▼
         ┌─────────────────────┐
         │ Log Failed Attempt  │  → lh_users_login
         └─────────────────────┘
         │
         ▼
         Display error message
```

## 7. Webhook Execution Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          WEBHOOK PROCESSING                              │
└─────────────────────────────────────────────────────────────────────────┘

EVENT TRIGGERED (e.g., chat.chat_started)
    │
    ▼
┌─────────────────────┐
│ Event Dispatcher    │  → Calls webhook worker
└─────────────────────┘
    │
    ▼
┌─────────────────────┐
│ Load Webhooks       │  → SELECT FROM lh_webhook
│                     │  → WHERE event = ? AND disabled = 0
└─────────────────────┘
    │
    ▼
FOR EACH WEBHOOK
    │
    ├──► Type = 0 (Bot Trigger)
    │    │
    │    ▼
    │    ┌─────────────────────┐
    │    │ Execute Bot Trigger │  → Load trigger actions
    │    │                     │  → Process with chat context
    │    └─────────────────────┘
    │
    └──► Type = 1 (HTTP Webhook)
         │
         ▼
         ┌─────────────────────┐
         │ Prepare Payload     │  → JSON encode event data
         │                     │  → Include chat/message info
         └─────────────────────┘
         │
         ▼
         ┌─────────────────────┐
         │ HTTP POST           │  → cURL to webhook URL
         │                     │  → Custom headers
         └─────────────────────┘
         │
         ▼
         ┌─────────────────────┐
         │ Log Response        │  → Store in webhook status
         │                     │  → Handle errors
         └─────────────────────┘
```
