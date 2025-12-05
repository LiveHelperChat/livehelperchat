# UI Domain - User Interface Layer

## Overview

Live Helper Chat has a multi-layered UI architecture combining server-side PHP templates with multiple client-side JavaScript frameworks.

## Server-Side Templates

### Template System

Templates use raw PHP with the `.tpl.php` extension. The `erLhcoreClassTemplate` class handles template rendering.

**Template instantiation pattern:**
```php
// From modules/lhgenericbot/edit.php
$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/edit.tpl.php');
$tpl->set('bot', $bot);
$tpl->set('tab', $tab);
```

**Template path resolution order:**
1. Extension theme: `extension/{ext}/design/{ext}theme/tpl/{path}`
2. Custom theme: `design/customtheme/tpl/{path}`
3. Default theme: `design/defaulttheme/tpl/{path}`

### Template Variables

Variables are set using `$tpl->set()` and accessed directly in templates:

```php
// In controller
$tpl->set('chat', $chatObject);
$tpl->set('messages', $messagesList);

// In template (design/defaulttheme/tpl/lhchat/chat.tpl.php)
<div class="chat-container">
    <h1><?php echo htmlspecialchars($chat->nick); ?></h1>
    <?php foreach ($messages as $msg): ?>
        <div class="message"><?php echo $msg->msg; ?></div>
    <?php endforeach; ?>
</div>
```

### Page Layouts

Controllers return a `$Result` array that specifies the page layout:

```php
// From any controller file
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'login';  // Uses pagelayouts/login.php
$Result['path'] = array(
    array('title' => 'System', 'url' => erLhcoreClassDesign::baseurl('system/configuration'))
);
```

## Client-Side: React Widget

### Component Structure

The chat widget (`design/defaulttheme/widget/react-app/`) uses React 18 with Redux:

```javascript
// src/store/index.js
import { applyMiddleware, createStore } from "redux";
import rootReducer from "../reducers/index";
import thunk from "redux-thunk"
import promise from "redux-promise-middleware"

const middleware = applyMiddleware(promise, thunk)
const store = createStore(rootReducer, middleware);
```

### Redux State Shape

```javascript
// src/reducers/chatWidgetReducer.js
const initialState = {
    chatData: {},
    messages: [],
    isTyping: false,
    status: 'pending',
    // ...
};
```

### Action Pattern

```javascript
// Action creator with thunk
export const sendMessage = (message) => {
    return (dispatch, getState) => {
        dispatch({ type: 'SEND_MESSAGE_PENDING' });
        return api.sendMessage(message)
            .then(response => {
                dispatch({ type: 'SEND_MESSAGE_FULFILLED', payload: response });
            });
    };
};
```

## Client-Side: Svelte Dashboard

### Store Pattern

The dashboard uses Svelte writable stores (`design/defaulttheme/js/svelte/src/stores.js`):

```javascript
import { writable } from 'svelte/store';

export const lhcList = writable({
    optionsPanels: {},
    onlineusers: {"list": []},
    pendingd: [],
    actived: [],
    botd: [],
    limitp: "10",
    limita: "10",
    isListLoaded: false,
    // ... many more properties
});
```

### Component Exports

```javascript
// src/main.js
export { default as LHC } from './LHC.svelte';
export { default as LHCStatus } from './LHCStatus.svelte';
export { default as OpenChat } from './LinkActions/OpenChat.svelte';
export { default as ChatsCounter } from './LinkActions/ChatsCounter.svelte';
export { default as LHCWidget } from './Widgets/LHCWidget.svelte';
export { default as LHCEditor } from './LHCEditor/LHCEditor.svelte';
```

## Best Practices

### Template Best Practices

1. **Always escape output:**
   ```php
   <?php echo htmlspecialchars($user->name); ?>
   ```

2. **Use design helpers for URLs:**
   ```php
   <a href="<?php echo erLhcoreClassDesign::baseurl('chat/single')?>">
   ```

3. **Include partials correctly:**
   ```php
   <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_status.tpl.php')); ?>
   ```

### React Best Practices

1. **Connect components to Redux store:**
   ```javascript
   import { connect } from 'react-redux';
   
   const mapStateToProps = (state) => ({
       messages: state.chatwidget.messages
   });
   
   export default connect(mapStateToProps)(ChatMessages);
   ```

2. **Use action creators for API calls:**
   ```javascript
   dispatch(fetchMessages(chatId));
   ```

### Svelte Best Practices

1. **Subscribe to stores reactively:**
   ```svelte
   <script>
       import { lhcList } from './stores.js';
   </script>
   
   {#each $lhcList.pendingd as chat}
       <ChatItem {chat} />
   {/each}
   ```

2. **Update stores immutably:**
   ```javascript
   lhcList.update(state => ({
       ...state,
       pendingd: newPendingList
   }));
   ```

## File Organization

```
design/defaulttheme/
├── tpl/                          # PHP templates
│   ├── lhchat/                   # Chat module templates
│   ├── lhuser/                   # User module templates
│   └── pagelayouts/              # Page layout templates
├── widget/
│   └── react-app/
│       └── src/
│           ├── actions/          # Redux actions
│           ├── components/       # React components
│           ├── reducers/         # Redux reducers
│           └── store/            # Store configuration
└── js/
    ├── svelte/src/               # Svelte dashboard
    │   ├── LHC.svelte
    │   ├── stores.js
    │   └── Widgets/
    └── react/src/                # Bot builder React app
```
