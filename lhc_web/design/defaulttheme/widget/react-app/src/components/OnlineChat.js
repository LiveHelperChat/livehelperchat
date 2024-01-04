import React, { Component } from 'react';
import { connect } from "react-redux";
import parse from 'html-react-parser';
import { initChatUI, fetchMessages, addMessage, checkChatStatus, endChat, userTyping, minimizeWidget, setSiteAccess, updateMessage, cancelPresurvey} from "../actions/chatActions"
import { STATUS_CLOSED_CHAT, STATUS_BOT_CHAT, STATUS_SUB_SURVEY_SHOW, STATUS_SUB_USER_CLOSED_CHAT } from "../constants/chat-status";
import ChatMessage from './ChatMessage';
import ChatModal from './ChatModal';
import ChatFileUploader from './ChatFileUploader';
import ChatSync from './ChatSync';
import ChatOptions from './ChatOptions';
import ChatStatus from './ChatStatus';
import ChatIntroStatus from './ChatIntroStatus';
import ChatAbort from './ChatAbort';

import { helperFunctions } from "../lib/helperFunctions";
import { withTranslation } from 'react-i18next';

import { Suspense, lazy } from 'react';

const VoiceMessage = React.lazy(() => import('./VoiceMessage'));
const MailModal = React.lazy(() => import('./MailModal'));
const FontSizeModal = React.lazy(() => import('./FontSizeModal'));
const CustomHTML = React.lazy(() => import('./CustomHTML'));

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class OnlineChat extends Component {

    state = {
        value: '',
        changeLanguage: false,
        showBBCode : null,
        mailChat : false,
        dragging : false,
        enabledEditor : true,
        showMessages : false,
        preloadSurvey : false, // Should survey be preloaded
        gotToSurvey : false,
        voiceMode : false,
        showMail : false,
        changeFontSize : false,
        errorMode: false,
        hasNew: false,
        newId: 0, // From what index there is a new messages
        scrollButton: false,
        fontSize: 100,
        reactToMsgId: 0,
        otm: 0, // New operator messages
        messages_ui: true // Is visitor in messages UI, in case extension has overlay and messages was received
    };

    constructor(props) {
        super(props);

        // Init offline form with all attributes
        this.props.dispatch(initChatUI({
            'id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'theme' : this.props.chatwidget.get('theme')
        }));

        this.updateMessages();
        this.updateStatus();

        this.enterKeyDown = this.enterKeyDown.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
        this.endChat = this.endChat.bind(this);
        this.mailChat = this.mailChat.bind(this);
        this.changeFont = this.changeFont.bind(this);
        this.voiceCall = this.voiceCall.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.setStatusText = this.setStatusText.bind(this);
        this.dragging = this.dragging.bind(this);
        this.scrollBottom = this.scrollBottom.bind(this);
        this.focusMessage = this.focusMessage.bind(this);
        this.setEditorEnabled = this.setEditorEnabled.bind(this);
        this.sendDelay = this.sendDelay.bind(this);
        this.unhideDelayed = this.unhideDelayed.bind(this);
        this.toggleSound = this.toggleSound.bind(this);
        this.goToSurvey = this.goToSurvey.bind(this);
        this.startVoiceRecording = this.startVoiceRecording.bind(this);
        this.cancelVoiceRecording = this.cancelVoiceRecording.bind(this);
        this.onScrollMessages = this.onScrollMessages.bind(this);
        this.scrollToMessage = this.scrollToMessage.bind(this);
        this.changeFontAction = this.changeFontAction.bind(this);
        this.setLanguageAction = this.setLanguageAction.bind(this);
        this.changeLanguage = this.changeLanguage.bind(this);

        // Messages Area
        this.messagesAreaRef = React.createRef();
        this.textMessageRef = React.createRef();

        this.updateMessages = this.updateMessages.bind(this);
        this.updateMessage = this.updateMessage.bind(this);
        this.updateStatus = this.updateStatus.bind(this);
        this.abstractAction = this.abstractAction.bind(this);
        this.updateMetaAutoHide = this.updateMetaAutoHide.bind(this);
        this.setMetaUpdateState = this.setMetaUpdateState.bind(this);
        this.keyUp = this.keyUp.bind(this);

        this.delayed = false;
        this.delayQueue = [];
        this.intervalPending = null;
        this.intervalFunction = null;
        this.unhideDelayedTimer = null;
        this.pendingMetaUpdate = false;
        this.timeoutNewMessage = null;
        this.timeoutScroll = null;

        this.isTyping = false;
        this.typingStopped = null;
        this.typingStoppedAction = this.typingStoppedAction.bind(this);
        this.currentMessageTyping = '';
    }

    dragging(status) {
        this.setState({dragging : status})
    }

    goToSurvey() {
        this.props.dispatch({
            'type': 'UI_STATE',
            'data' : {attr: 'show_survey', 'val': 1}
        });
        this.setState({'gotToSurvey' : true});
    }

    setStatusText(text){
        this.props.dispatch({
            'type': 'chat_status_changed',
            'data' : {text: text}
        });
    }

    startVoiceRecording() {
        this.setState({voiceMode: true});
    }

    cancelVoiceRecording() {
        this.setState({voiceMode: false});
    }

    handleChange(event) {
        this.setState({value: event.target.value});
        helperFunctions.setSessionStorage('_ttxt',event.target.value);
    }

    onScrollMessages() {
        if (this.messagesAreaRef.current) {
            let scrollValue = this.messagesAreaRef.current.scrollHeight - this.messagesAreaRef.current.scrollTop;
            // Scroll to bottom if from bottom there is already less than 70px
            if ((scrollValue - this.messagesAreaRef.current.offsetHeight) > 70 ) {
                if (this.state.scrollButton !== true) {
                    this.setState({scrollButton: true});
                }
            } else if (this.state.scrollButton !== false) {
                this.setState({scrollButton: false, otm: 0});
                this.props.dispatch({'type' : 'UPDATE_LIVE_DATA', 'data' : {'attr': 'lfmsgid', 'val': 0}});
            }
        }
    }

    scrollToMessage() {

        if (this.state.hasNew == true) {
            clearTimeout(this.timeoutNewMessage);
            this.timeoutNewMessage = setTimeout(() => {this.setState({hasNew: false, newId: 0})},1000);
        }

        if (this.state.hasNew == true && this.state.otm > 0) {
            this.setState({otm: 0})
            try {
                document.getElementById('scroll-to-message').scrollIntoView();
            } catch (e) {
                this.scrollBottom();
            }
        } else {
            this.scrollBottom();
        }
    }

    changeFontAction(action){
        this.setState({
            fontSize: this.state.fontSize + (action == true ? 5 : -5)
        });
        helperFunctions.setLocalStorage('_dfs',this.state.fontSize);
        this.scrollBottom();
    }

    setLanguageAction(lng) {
        helperFunctions.setLocalStorage('_lng',lng);
        this.setState({
            changeLanguage: false
        });
        setSiteAccess({
            'lng' : lng,
            'id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
        });
        helperFunctions.emitEvent('change_language', [lng]);
        this.updateStatus();
    }

    componentDidMount() {

        var txtTyping = helperFunctions.getSessionStorage('_ttxt');
        if (txtTyping !== null) {
            this.setState({value: txtTyping})
        }

        var defaultFontSize = helperFunctions.getLocalStorage('_dfs');
        if (defaultFontSize !== null) {
            this.setState({fontSize: parseInt(defaultFontSize)})
        }

        // We want to focus only if widget is open
        var elm = document.getElementById('CSChatMessage');
        if (elm !== null && ((this.props.chatwidget.get('shown') === true && this.props.chatwidget.get('mode') == 'widget') || this.props.chatwidget.get('mode') == 'popup')) {
            elm.focus();

            var elmtmp = document.getElementById('CSChatMessage-tmp');
            if (elmtmp !== null) {
                document.body.removeChild(elmtmp);
            }
        }
    }

    focusMessage() {
        if (this.textMessageRef.current) {
            this.textMessageRef.current.focus();
            if (this.state.value.length > 0) {
                this.textMessageRef.current.selectionStart = this.state.value.length;
                this.textMessageRef.current.selectionEnd = this.state.value.length;
            }
        }
    }

    setEditorEnabled(status) {
        this.setState({'enabledEditor' : status});
    }

    hasClass(el, name) {
        return new RegExp('(\\s|^)'+name+'(\\s|$)').test(el.className);
    }

    addClass (el, name) {
        if (!this.hasClass(el, name)) { el.className += (el.className ? ' ' : '') +name; }
    }

    removeClass(el, name) {
        if (this.hasClass(el, name)) {
            el.className=el.className.replace(new RegExp('(\\s|^)'+name+'(\\s|$)'),' ').replace(/^\s+|\s+$/g, '');
        }
    }

    setMetaUpdateState(state) {

        if (state === false && this.pendingMetaUpdate === true){
            this.pendingMetaUpdate = false;
            this.updateMetaAutoHide(true);
            this.doScrollBottom();
        }

        if (state === true) {
            this.pendingMetaUpdate = true;
            this.updateMetaAutoHide();
            this.doScrollBottom();
        }

    }

    updateMetaAutoHide(hideFirst) {
        var block = document.getElementById('messages-scroll');
        if (!block){
            return;
        }
        ['meta-auto-hide','meta-auto-hide-normal'].forEach(function(className){
            var x = block.getElementsByClassName(className);
            if (x.length > 0) {
                var i;
                var lengthHide = hideFirst ? 0 : 1;
                for (i = 0; i < x.length - lengthHide ; i++) {
                    x[i].style.display = 'none';
                }
                var lastChild = block.lastChild;

                // Checking not null not enough, because element can be text type
                if (lastChild && typeof lastChild.getElementsByClassName !== 'undefined') {
                    x = lastChild.getElementsByClassName(className);
                    var i;
                    for (i = 0; i < x.length; i++) {
                        x[i].style.display = '';
                    }
                }
            }
        });
    }

    nextUntil(htmlElement, match, condition = true, any = false) {
        var nextUntil = [],
            until = true;

        while (htmlElement = htmlElement.nextElementSibling) {
            (until && htmlElement && !htmlElement.matches(match) == condition) ? nextUntil.push(htmlElement) : until = any;
        }
        return nextUntil;
    }

    sendDelay(params) {
        var id = params['id'],
         duration = params['duration'],
         delay = params['delay'],
         untillMessage = params['untill_message'],
         msg = document.getElementById('msg-'+id);

        if (!msg) {
            return;
        }

        if (delay > 0) {
            this.addClass(msg,'hide');
        }

        if (untillMessage == true && this.nextUntil(msg,'.message-admin', false, true).length > 0) {
            return;
        }

        setTimeout( () => {
            if (this.delayed == false) {
                if (untillMessage == true) {

                    // Call previous function if it exists
                    if (this.intervalFunction !== null) {
                        this.intervalFunction();
                    }

                    this.intervalFunction = () => {
                        if (this.nextUntil(msg,'.message-admin', false, true).length > 0) {
                            msg.parentNode.removeChild(msg);
                            this.scrollBottom(false, false);
                            this.intervalFunction = null;
                            clearInterval(this.intervalPending);
                        } else {
                            if (!this.hasClass(msg,'meta-hider'))
                            {
                                this.addClass(msg,'meta-hider');
                                this.addClass(msg,'message-row-typing');

                                this.removeClass(msg,'hide');
                                this.removeClass(msg,'fade-in-fast');

                                var elementsBody = msg.getElementsByClassName("msg-body");

                                for (var item of elementsBody) {
                                    this.removeClass(item, 'hide');
                                }

                                this.scrollBottom(false, false);
                            }
                        }
                    }

                    clearInterval(this.intervalPending);
                    this.intervalPending = setInterval(this.intervalFunction,150);
                } else {
                    this.delayed = true;

                    this.addClass(msg,'meta-hider');
                    this.addClass(msg,'message-row-typing');

                    this.nextUntil(msg,'.meta-hider').forEach((item) => {
                        this.addClass(item,'hide');
                    });

                    this.unhideDelayedTimer = setTimeout(() => {
                        this.unhideDelayed(id);
                    }, duration * 1000);

                    this.removeClass(msg,'hide');
                    this.removeClass(msg,'fade-in-fast');

                    var elementsBody = msg.getElementsByClassName("msg-body");

                    for (var item of elementsBody) {
                        this.removeClass(item, 'hide');
                    }

                    if (delay > 0) {
                        this.updateMetaAutoHide();
                        this.scrollBottom(false, false);
                    }
                }

            } else {
                this.addClass(msg,'message-row-typing');
                this.addClass(msg,'meta-hider');
                this.delayQueue.push({'id' : id, 'delay' : duration});
            }
        },delay*1000);
    }

    unhideDelayed(id) {

        var msg = document.getElementById('msg-'+id);

        if (!msg) {
            return;
        }

        this.nextUntil(msg,'.meta-hider').forEach((item) => {
            this.removeClass(item,'hide');
        });

        msg.parentNode.removeChild(msg);

        this.updateMetaAutoHide();
        this.scrollBottom();

        if (this.delayQueue.length > 0) {
            var data = this.delayQueue.shift();

            setTimeout(() => {
                this.unhideDelayed(data.id);
            }, data.delay * 1000);

            var messageBlock = document.getElementById('msg-'+data.id);

            if (messageBlock !== null) {
                this.removeClass(messageBlock,'hide');
                this.removeClass(messageBlock,'fade-in-fast');

                var elementsBody = messageBlock.getElementsByClassName("msg-body");

                for (var item of elementsBody) {
                    this.removeClass(item, 'hide');
                }
            }

        } else {
            this.delayed = false;
        }
    }

    componentWillUnmount() {
        clearInterval(this.intervalPending);
        clearInterval(this.typingStopped);
        clearTimeout(this.unhideDelayedTimer);
        clearTimeout(this.timeoutNewMessage);
        clearTimeout(this.timeoutScroll);
    }

    // https://reactjs.org/blog/2018/03/27/update-on-async-rendering.html
    getSnapshotBeforeUpdate(prevProps, prevState) {
        // Are we adding new item
        if (prevProps.chatwidget.getIn(['chatLiveData','messages']).size != this.props.chatwidget.getIn(['chatLiveData','messages']).size) {

            let setScroll = false;
            let setScrollBottom = true;
            let scrollValue = 0;

            if (this.messagesAreaRef.current) {
                scrollValue = this.messagesAreaRef.current.scrollHeight - this.messagesAreaRef.current.scrollTop;

                // Scroll to bottom if from bottom there is already less than 70px
                if ((scrollValue - this.messagesAreaRef.current.offsetHeight) < 70) {
                    scrollValue = 0;
                } else {
                    setScrollBottom = false
                }

                setScroll = true;
            }

            let hasNewMessages = this.state.hasNew;
            let oldId = hasNewMessages == true ? this.state.newId : 0;
            let otm = hasNewMessages == true ? this.state.otm : 0;

            if (prevProps.chatwidget.getIn(['chatLiveData','messages']).size != 0 && this.props.chatwidget.getIn(['chatLiveData','uw']) === false) {
                let widgetOpen = ((this.props.chatwidget.get('shown') && this.props.chatwidget.get('mode') == 'widget') || (this.props.chatwidget.get('mode') != 'widget' && document.hasFocus()));
                if (hasNewMessages == false) {
                    hasNewMessages = widgetOpen == false || window.lhcChat['is_focused'] == false || setScrollBottom == false || this.state.messages_ui === false;
                    oldId = hasNewMessages == true ? prevProps.chatwidget.getIn(['chatLiveData','messages']).size : 0;
                    otm = this.props.chatwidget.getIn(['chatLiveData','otm']);
                } else {
                    otm += this.props.chatwidget.getIn(['chatLiveData','otm']);
                }

                // Get last message
                let msg = this.props.chatwidget.hasIn(['chat_ui','msg_snippet']) && this.props.chatwidget.getIn(['chatLiveData','messages',-1,'msg']);

                helperFunctions.emitEvent('play_sound', [{msop: this.props.chatwidget.getIn(['chatLiveData','msop']), msg_body: msg, 'otm': otm, 'type' : 'new_message', 'sound_on' : (this.props.chatwidget.getIn(['usersettings','soundOn']) === true), 'widget_open' : widgetOpen}]);
            } else {
                hasNewMessages = false;
                oldId = 0;
                otm = 0;
            }

            this.setState({hasNew: hasNewMessages, newId: oldId, otm: otm, scrollButton: !setScrollBottom});

            if (setScroll == true) {
                return (
                    (scrollValue)
                );
            }

        // Are we restoring widget visibility
        } else if (prevProps.chatwidget.get('shown') === false && this.props.chatwidget.get('shown') === true) {
            return 0;
        } else if (this.props.chatwidget.getIn(['chatLiveData','error']) && (
                (this.props.chatwidget.getIn(['chatLiveData','lmsg']) && (this.state.errorMode == false || this.props.chatwidget.getIn(['chatLiveData','lmsg']) != prevProps.chatwidget.getIn(['chatLiveData','lmsg']))) ||
                (!this.props.chatwidget.getIn(['chatLiveData','lmsg']) && this.state.errorMode == false)))
        {
            this.setState({errorMode: true, value: this.props.chatwidget.getIn(['chatLiveData','lmsg'])});
        } else if (!this.props.chatwidget.getIn(['chatLiveData','error']) && prevProps.chatwidget.getIn(['chatLiveData','error'])) {
            this.setState({errorMode: false, value: ''});
        }

        return null;
    }

    componentDidUpdate(prevProps, prevState, snapshot) {

        // Update untill we are sure that messages can be shown
        if (
            this.state.showMessages === false ||
            prevProps.chatwidget.getIn(['chatLiveData','status']) != this.props.chatwidget.getIn(['chatLiveData','status']) ||
            prevProps.chatwidget.getIn(['chatLiveData','msg_to_store']).size != this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).size
        ) {
            if (this.props.chatwidget.get('newChat') == true && this.props.chatwidget.getIn(['chatLiveData','messages']).size == 1) {
                this.scrollBottom(false, true);
            } else {
                this.scrollBottom(false, prevProps.chatwidget.getIn(['chatLiveData','msg_to_store']).size != this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).size);
            }
        }

        var smartScroll = false;

        if (
            (prevState.enabledEditor === false && prevState.enabledEditor != this.state.enabledEditor && (smartScroll = true) == true) ||
            (this.props.chatwidget.get('msgLoaded') !== prevProps.chatwidget.get('msgLoaded') && (this.props.chatwidget.get('newChat') == false || (smartScroll = true) == true))
        ) {
            if (smartScroll == false) {
                this.scrollBottom(false, false);
            } else {
                this.scrollBottom(false, true);
            }

            if (!(this.props.chatwidget.getIn(['chat_ui','auto_start']) === true && this.props.chatwidget.get('mode') == 'embed') || (this.props.chatwidget.getIn(['chat_ui','auto_start']) === false && this.props.chatwidget.get('mode') == 'embed') || (prevState.enabledEditor === false && prevState.enabledEditor != this.state.enabledEditor)) {
                this.focusMessage();
                // Sometimes component is not rendered itself. We want to be 100% sure it will always have a focus.
                setTimeout(() => {
                    this.focusMessage();
                },500);
            }
        }

        if (snapshot !== null) {
            if (this.messagesAreaRef.current) {
                var msgScroller = document.getElementById('messages-scroll');
                var messageElement = document.getElementById('msg-'+this.props.chatwidget.getIn(['chatLiveData','lfmsgid']));
                if (msgScroller && messageElement && messageElement.className.indexOf('ignore-auto-scroll') === -1 && (msgScroller.scrollHeight - msgScroller.offsetHeight) > messageElement.offsetTop) {
                    this.setState({scrollButton: true});
                    this.messagesAreaRef.current.scrollTop = messageElement.offsetTop - 3;
                } else {
                    this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight - snapshot;
                }
            }
        }

        if (this.props.chatwidget.getIn(['chat_ui_state','confirm_close']) == 1 && this.state.preloadSurvey === false) {
            this.setState({'preloadSurvey':true});
        }

        // Auto focus if it's show operation
        if (prevProps.chatwidget.get('shown') === false && this.props.chatwidget.get('shown') === true && this.props.chatwidget.get('mode') == 'widget' && this.textMessageRef.current) {
            this.textMessageRef.current.focus();
        }

        // We show start form instantly if it's enabled
        if (this.props.chatwidget.getIn(['chat_ui','start_on_close']) === true && this.props.chatwidget.getIn(['chatLiveData','closed']) === true && (typeof prevProps.chatwidget.getIn(['chatLiveData','closed']) === 'undefined' || prevProps.chatwidget.hasIn(['chatLiveData','closed']) === false || prevProps.chatwidget.getIn(['chatLiveData','closed']) === false)) {
            if (!this.props.chatwidget.getIn(['chat_ui','survey_id'])) {
                this.props.endChat({"show_start": this.props.chatwidget.get('shown')});
            }
        }

        if (this.props.chatwidget.getIn(['chatLiveData','closed']) === true && this.props.chatwidget.getIn(['chatLiveData','status_sub']) === 0 &&  prevProps.chatwidget.getIn(['chatLiveData','status_sub']) === 5) {
            this.props.dispatch(initChatUI({
                'id': this.props.chatwidget.getIn(['chatData','id']),
                'hash' : this.props.chatwidget.getIn(['chatData','hash']),
                'theme' :  this.props.chatwidget.get('theme')
            }));
        }

        // At the moment not used because logic migrated to one time call componentDidMount
        if (this.props.chatwidget.get('shown') === true && (this.props.chatwidget.get('mode') == 'widget' || this.props.chatwidget.get('mode') == 'embed') && this.props.chatwidget.get('initLoaded') === true && this.props.chatwidget.get('msgLoaded') === true && (prevProps.chatwidget.get('msgLoaded') == false || prevProps.chatwidget.get('initLoaded') == false)) {

            if (this.props.chatwidget.get('mode') == 'widget') {
                this.textMessageRef.current && this.textMessageRef.current.focus();
            }

            var elm = document.getElementById('CSChatMessage-tmp');
            if (elm !== null) {
                document.body.removeChild(elm);
            }
        }
    }

    doScrollBottom(smartScroll) {
        if (this.messagesAreaRef.current) {
            var messageElement;
            if (smartScroll && (messageElement = document.getElementById('msg-'+this.props.chatwidget.getIn(['chatLiveData','lfmsgid']))) !== null && messageElement.className.indexOf('ignore-auto-scroll') === -1 ) {
                this.messagesAreaRef.current.scrollTop = messageElement.offsetTop - 3;
            } else {
                this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight + 1000;
            }
        }
    }

    scrollBottom(onlyIfAtBottom, smartScroll) {
        if (this.messagesAreaRef.current && (!onlyIfAtBottom || !this.state.scrollButton)) {

            clearTimeout(this.timeoutScroll);

            this.doScrollBottom(smartScroll);

            this.timeoutScroll = setTimeout(() => {
                this.doScrollBottom(smartScroll);
                if (this.state.showMessages === false) {
                    this.setState({'showMessages':true});
                }
            },450);
        }
    }

    abstractAction(action, params) {
         helperFunctions.emitEvent(action, params);
    }

    updateMessage(messageId) {
        this.props.dispatch(updateMessage({
            'msg_id' : messageId,
            'lmgsid' : this.props.chatwidget.getIn(['chatLiveData','lmsgid']),
            'mode' :  this.props.chatwidget.get('mode'),
            'theme' : this.props.chatwidget.get('theme'),
            'id' : this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'no_scroll' : true
        }));
    }

    updateMessages(paramsUpdate) {
        var params = {
            'chat_id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'lmgsid' : this.props.chatwidget.getIn(['chatLiveData','lmsgid']),
            'lfmsgid' : this.props.chatwidget.getIn(['chatLiveData','lfmsgid']),
            'theme' : this.props.chatwidget.get('theme'),
            'new_chat' : this.props.chatwidget.get('newChat'),
            'active_widget' : (((this.props.chatwidget.get('shown') && this.props.chatwidget.get('mode') == 'widget') || (this.props.chatwidget.get('mode') != 'widget' && document.hasFocus())) && window.lhcChat['is_focused'] == true && this.state.messages_ui !== false)
        };

        // If it's new chat check do we have last message from previous chat if so send it also
        if (params.new_chat && params.lmgsid === 0) {
            params['old_msg_id'] = this.props.chatwidget.getIn(['chatData','lmsg_id'])
        }

        this.props.dispatch(fetchMessages(params));

        if (paramsUpdate && paramsUpdate["check_focus"] && this.props.chatwidget.get('isMobile') === false) {
            this.focusMessage();
        }
    }

    updateStatus() {
        this.props.dispatch(checkChatStatus({
            'chat_id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode')
        }));
    }

    sendMessage() {

        if (this.state.value.length == 0) {
            return;
        }

        helperFunctions.setSessionStorage('_ttxt','');

        this.props.dispatch(addMessage({
            'id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'msg' : this.state.value,
            'mn' : this.props.chatwidget.hasIn(['chat_ui','mn']),
            'theme' : this.props.chatwidget.get('theme'),
            'lmgsid' : this.props.chatwidget.getIn(['chatLiveData','lmsgid'])
        }));

        this.setState({value: '', errorMode : false});

        this.currentMessageTyping = '';
        this.focusMessage();
        this.doScrollBottom();
    }

    enterKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            this.sendMessage();
            e.preventDefault();
        }
    }

    keyUp(e) {
        if (e.key !== 'Enter' && !e.shiftKey) {
            if (this.isTyping === false) {
                const { t } = this.props;
                this.isTyping = true;
                this.props.dispatch(userTyping('true',this.props.chatwidget.hasIn(['chat_ui','hide_typing']) &&  this.props.chatwidget.getIn(['chat_ui','hide_typing']) === true ? t('online_chat.visitor_typing') : this.state.value));
            } else {
                clearTimeout(this.typingStopped);
                this.typingStopped = setTimeout(this.typingStoppedAction, 6000);
                if (this.currentMessageTyping != this.state.value ) {
                    if (Math.abs(this.currentMessageTyping.length - this.state.value.length) > 6 || this.props.chatwidget.get('overrides').contains('typing')) {
                        const { t } = this.props;
                        this.currentMessageTyping = this.state.value;
                        this.props.dispatch(userTyping('true', this.props.chatwidget.hasIn(['chat_ui','hide_typing']) &&  this.props.chatwidget.getIn(['chat_ui','hide_typing']) === true ? t('online_chat.visitor_typing') : this.state.value));
                    }
                }
            }
        }
    }

    typingStoppedAction() {
        if (this.isTyping == true) {
            this.isTyping = false;
            this.props.dispatch(userTyping('false'));
        }
    }

    endChat() {
        this.props.endChat({"show_start": this.props.chatwidget.get('shown')});
    }

    toggleModal() {
        this.setState({
            showBBCode: !this.state.showBBCode
        });

        if (this.state.showBBCode) {
            this.focusMessage()
        }
    }

    mailChat() {
        this.setState({
            showMail: !this.state.showMail
        });
    }

    changeLanguage() {
        this.setState({
            changeLanguage: !this.state.changeLanguage
        });
    }

    changeFont() {
        this.setState({
            changeFontSize: !this.state.changeFontSize
        });
    }

    voiceCall() {

        const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
        const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

        const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left = (width - parseInt(800)) / 2 / systemZoom + dualScreenLeft;
        const top = (height - parseInt(600)) / 2 / systemZoom + dualScreenTop;

        var paramsWindow = "scrollbars=yes,menubar=1,resizable=1,width=800,height=600,top=" + top + ",left=" + left;
        var newWin = window.open("", helperFunctions.prefix + '_voice_popup_v2', paramsWindow);
        var needWindow = false;
        var windowCreated = false;

        // First try to find any existing window
        try {
            // It has to be new window or popup was blocked
            if (!newWin || newWin.closed || typeof newWin.closed=='undefined' || newWin.location.href === "about:blank") {
                newWin = window.open(this.props.chatwidget.get('base_url') + "voicevideo/call/" + this.props.chatwidget.getIn(['chatData', 'id']) + '/' + this.props.chatwidget.getIn(['chatData', 'hash']), helperFunctions.prefix + '_voice_popup_v2', paramsWindow);
                windowCreated = true;
            } else {
                needWindow = true;
            }
        } catch (e) { // We get cross-origin error only if window exist and it's location is other one than about:blank
            needWindow = true;
        }

        // Now if visitor has blocked popup change chat status link and just allow browser handle the rest.
        if (!newWin || newWin.closed || typeof newWin.closed=='undefined') {
            try {
                // Change href to open window
            } catch (e) {
                alert('You have disabled popups!');
            }
        } else if (windowCreated == true) {
            /*typeof chatEvents !== 'undefined' && chatEvents.sendChildEvent('endedChat', [{'sender': 'endButton'}]);
            typeof paramsPopup !== 'undefined' && paramsPopup.event !== 'undefined' && paramsPopup.event.preventDefault();*/
        } else if (needWindow === true) {
            newWin.focus();
        }

    }

    toggleSound() {
        this.props.dispatch({type : 'toggleSound', data: !this.props.chatwidget.getIn(['usersettings','soundOn'])});
        helperFunctions.sendMessageParent('toggleSound', [{'sender' : 'toolbarButton'}]);
    }

    // http://projects.wojtekmaj.pl/react-lifecycle-methods-diagram/
    insertText = (text) => {
        var caretPos = this.textMessageRef.current.selectionStart;
        this.setState({value: (this.state.value.substring(0, caretPos) + text + this.state.value.substring(caretPos))});
    }

    render() {
        const { t } = this.props;

        if (this.props.chatwidget.get('initLoaded') === false || this.props.chatwidget.get('msgLoaded') === false) {

                var msg_expand = "flex-grow-1 overflow-scroll position-relative";

                if (this.props.chatwidget.hasIn(['chat_ui','msg_expand'])) {
                    msg_expand = "overflow-scroll position-relative";
                }

                return <ChatIntroStatus value={this.state.value} profileBefore={this.props.profileBefore} msg_expand={msg_expand} messagesBefore={this.props.messagesBefore} placeholderMessage={this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : t('chat.type_here')} />;
        }
        
        if (this.props.chatwidget.hasIn(['chatLiveData','ru']) && this.props.chatwidget.getIn(['chatLiveData','ru'])) {

            location = this.props.chatwidget.get('base_url') + this.props.chatwidget.getIn(['chatLiveData','ru']);

            return (
                <React.Fragment>
                    <iframe allowtransparency="true" src={location} frameBorder="0" className="flex-grow-1 position-relative iframe-modal"/>
                </React.Fragment>
            )

        } else {

            if (this.props.chatwidget.get('chatLiveData').has('messages')) {
                var messages = this.props.chatwidget.getIn(['chatLiveData','messages']).map((msg, index) =><ChatMessage reactToMessageId={this.state.reactToMsgId} setReactingTo={(messageId) => this.setState({'reactToMsgId' : messageId})} themeId={this.props.chatwidget.get('theme')} profilePic={this.props.chatwidget.get('profile_pic')} printButton={this.props.chatwidget.getIn(['chat_ui','print_btn_msg'])} newTitle={this.props.chatwidget.getIn(['chat_ui','cnew_msgh']) || t('button.new')} newId={this.state.newId} hasNew={this.state.hasNew} voiceCall={this.voiceCall} endChat={this.props.endChat} setMetaUpdateState={this.setMetaUpdateState} sendDelay={this.sendDelay} setEditorEnabled={this.setEditorEnabled} abstractAction={this.abstractAction} updateStatus={this.updateStatus} focusMessage={this.focusMessage} updateMessage={this.updateMessage} updateMessages={this.updateMessages} scrollBottom={this.scrollBottom} id={index} key={'msg_'+index} msg={msg} />);
            } else {
                var messages = "";
            }

            var placeholder = '';
            if (this.state.dragging === true) {
                placeholder = t('chat.drop_files');
            } else if (this.props.chatwidget.getIn(['chatLiveData','closed'])) {
                placeholder = t('chat.chat_closed');
            } else {
                placeholder = this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : t('chat.type_here');
            }

            var msg_expand = "flex-grow-1 overflow-scroll position-relative";
            var bottom_messages = "bottom-message px-1";

            if (this.props.chatwidget.hasIn(['chat_ui','show_ts'])){
                bottom_messages += " show-msg-ts";
                if (this.props.chatwidget.hasIn(['chat_ui','show_ts_below'])){
                    bottom_messages += " show-msg-ts-below";
                }
            }

            if (this.props.chatwidget.hasIn(['chat_ui','msg_expand']) && this.props.chatwidget.get('mode') == 'embed') {
                msg_expand = "overflow-scroll position-relative";
                bottom_messages += " position-relative";
            }

            var message_send_style = "mx-auto w-100";

            if (this.props.chatwidget.getIn(['chatLiveData','closed']) == true) {
                message_send_style += (this.props.chatwidget.get('mode') == 'embed' ? ' pe-2' : ' pe-1');
            }

            /**
             * Survey handling logic
             * */
            var showChat = true;
            var preloadSurvey = false;
            var forceSurvey = false;

            var location = "";
            var classSurvey = "flex-grow-1 position-relative iframe-modal content-loader mb-2";

            var validSurveyState = (this.props.chatwidget.hasIn(['chatLiveData','status_sub']) &&
                    (
                        this.props.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_SURVEY_SHOW
                    ||
                        (
                            this.props.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_USER_CLOSED_CHAT &&
                            (
                                this.props.chatwidget.getIn(['chatLiveData','uid']) > 0 || this.props.chatwidget.getIn(['chatLiveData','status']) === STATUS_BOT_CHAT || this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT
                            )
                        )
                    )
                )
                ||
                (this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT && this.props.chatwidget.getIn(['chatLiveData','uid']) > 0) ||
                this.state.gotToSurvey === true;

            if ((this.props.chatwidget.hasIn(['chatLiveData','status_sub']) && this.props.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_SURVEY_SHOW) || (
                this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT &&
                this.props.chatwidget.getIn(['chatLiveData','status_sub']) != STATUS_SUB_USER_CLOSED_CHAT
            )) {
                forceSurvey = true;
            }

            if ((this.state.preloadSurvey === true || validSurveyState) && this.props.chatwidget.hasIn(['chat_ui','survey_id'])) {
                location = this.props.chatwidget.get('base_url') + "survey/fillwidget/(chatid)/" + this.props.chatwidget.getIn(['chatData', 'id']) + "/(hash)/" + this.props.chatwidget.getIn(['chatData', 'hash']);

                if (this.props.chatwidget.get('theme')) {
                    location = location + '/(theme)/' + this.props.chatwidget.get('theme');
                }

                location = location + '/(survey)/' + this.props.chatwidget.getIn(['chat_ui', 'survey_id']) + (forceSurvey === true ? '/(force)/true' : '');
                
                if (this.props.chatwidget.hasIn(['chat_ui', 'survey_url'])) {
                    location = this.props.chatwidget.getIn(['chat_ui', 'survey_url']).replace('{chat_id}',this.props.chatwidget.getIn(['chatData', 'id'])).replace('{chat_hash}',this.props.chatwidget.getIn(['chatData', 'hash'])) + (forceSurvey === true ? '?force=true' : '');
                }

                preloadSurvey = true;

                showChat = false;

                if (
                    (validSurveyState === false) ||
                    (this.props.chatwidget.hasIn(['chat_ui','survey_button']) && this.props.chatwidget.getIn(['chat_ui_state','show_survey']) === 0 &&
                        this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT &&
                        this.props.chatwidget.getIn(['chatLiveData','status_sub']) != STATUS_SUB_SURVEY_SHOW &&
                        this.props.chatwidget.getIn(['chatLiveData','status_sub']) != STATUS_SUB_USER_CLOSED_CHAT
                    ) ||
                    (this.props.chatwidget.getIn(['chat_ui_state','confirm_close']) == 1)
                ) {
                    showChat = true;
                    classSurvey = " d-none";
                }
            }

            const endTitle = this.props.chatwidget.getIn(['chat_ui','end_chat_text']) || t('button.end_chat');

            const fontSizeStyle = {fontSize: (this.props.chatwidget.hasIn(['chat_ui','font_size']) ? this.state.fontSize : '100') + '%'};

            return (
                <React.Fragment>

                    {this.props.chatwidget.getIn(['chatLiveData','abort']) && <ChatAbort closeText={t('button.close')} close={(e) => this.props.dispatch(minimizeWidget(true))} text={this.props.chatwidget.getIn(['chatLiveData','abort'])} />}

                    {this.props.chatwidget.hasIn(['chat_ui','pre_survey_url']) && this.props.chatwidget.getIn(['chatLiveData','uid']) > 0 && this.props.chatwidget.getIn(['chat_ui_state','pre_survey_done']) !== 2 && (this.props.chatwidget.getIn(['chat_ui_state','pre_survey_done']) === 1 || validSurveyState) && <ChatModal cancelClose={(e) => this.props.dispatch(cancelPresurvey(false))} confirmClose={(e) => this.props.dispatch(cancelPresurvey(true))} toggle={this.props.cancelPresurvey} dataUrl={this.props.chatwidget.getIn(['chat_ui','pre_survey_url']) + this.props.chatwidget.getIn(['chatData','id'])+"/"+this.props.chatwidget.getIn(['chatData','hash']) + (this.props.chatwidget.hasIn(['chat_ui','survey_id']) ? '/(hassurvey)/true' : '') + (this.props.chatwidget.get('theme') ? '/(theme)/' + this.props.chatwidget.get('theme') : null)} />}

                    {preloadSurvey && <React.Fragment>
                        {showChat == false && this.props.chatwidget.hasIn(['chatStatusData','result']) && !this.props.chatwidget.hasIn(['chat_ui','hide_status']) && this.props.chatwidget.getIn(['chatStatusData','result']) && <div id="chat-status-container" className={"p-2 border-bottom live-status-"+this.props.chatwidget.getIn(['chatLiveData','status'])}><ChatStatus updateStatus={this.updateStatus} vtm={this.props.chatwidget.hasIn(['chat_ui','switch_to_human']) && this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_BOT_CHAT ? this.props.chatwidget.getIn(['chatLiveData','vtm']) : 0} status={this.props.chatwidget.getIn(['chatStatusData','result'])} /></div>}
                        <iframe allowtransparency="true" src={location} frameBorder="0" className={classSurvey} />
                    </React.Fragment>}

                    {(showChat || preloadSurvey) && <ChatSync hasSurvey={preloadSurvey} syncInterval={this.props.chatwidget.getIn(['chat_ui','sync_interval'])} updateStatus={this.updateStatus} updateMessages={this.updateMessages} initClose={this.props.chatwidget.get('initClose')} dispatch={this.props.dispatch} status_sub={this.props.chatwidget.getIn(['chatLiveData','status_sub'])} status={this.props.chatwidget.getIn(['chatLiveData','status'])} theme={this.props.chatwidget.get('theme')} lmgsid={this.props.chatwidget.getIn(['chatLiveData','lmsgid'])} hash={this.props.chatwidget.getIn(['chatData','hash'])} chat_id={this.props.chatwidget.getIn(['chatData','id'])} />}

                    {showChat && <React.Fragment>

                    {this.props.chatwidget.getIn(['chat_ui_state','confirm_close']) == 1 && <ChatModal confirmClose={this.props.endChat} cancelClose={this.props.cancelClose} toggle={this.props.cancelClose} dataUrl={"/chat/confirmleave/"+this.props.chatwidget.getIn(['chatData','id'])+"/"+this.props.chatwidget.getIn(['chatData','hash'])} />}

                    {this.state.showBBCode && <ChatModal showModal={this.state.showBBCode} insertText={this.insertText} toggle={this.toggleModal} dataUrl={"/chat/bbcodeinsert?react=1"} />}

                    {this.state.changeLanguage && <ChatModal showModal={this.state.changeLanguage} setLanguage={this.setLanguageAction} insertText={this.insertText} toggle={this.changeLanguage} dataUrl={"/widgetrestapi/chooselanguage/(id)/"+this.props.chatwidget.getIn(['chatData','id'])+"/(hash)/"+this.props.chatwidget.getIn(['chatData','hash'])} />}

                    {this.state.showMail && <Suspense fallback="..."><MailModal showModal={this.state.showMail} changeFont={this.changeFont} toggle={this.mailChat} chatHash={this.props.chatwidget.getIn(['chatData','hash'])} chatId={this.props.chatwidget.getIn(['chatData','id'])} /></Suspense>}

                    {this.state.changeFontSize && <Suspense fallback="..."><FontSizeModal showModal={this.state.changeFontSize} toggle={this.changeFont} changeFont={this.changeFontAction} /></Suspense>}

                    {this.props.chatwidget.get('mode') == 'embed' && this.props.chatwidget.hasIn(['chat_ui','embed_cls']) && this.props.chatwidget.getIn(['chat_ui','embed_cls']) == 1 && <div className="close-modal-btn position-absolute">
                        {this.props.chatwidget.hasIn(['chat_ui','close_btn']) && <a onClick={this.endChat} title={endTitle} ><i className="material-icons settings text-muted">&#xf10a;</i><span className="embed-close-title">{endTitle}</span></a>}
                    </div>}

                    {this.props.chatwidget.hasIn(['chatStatusData','result']) && !this.props.chatwidget.hasIn(['chat_ui','hide_status']) && this.props.chatwidget.getIn(['chatStatusData','result']) && <div id="chat-status-container" className={"p-2 border-bottom live-status-"+this.props.chatwidget.getIn(['chatLiveData','status'])}><ChatStatus updateStatus={this.updateStatus} vtm={this.props.chatwidget.hasIn(['chat_ui','switch_to_human']) && this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_BOT_CHAT ? this.props.chatwidget.getIn(['chatLiveData','vtm']) : 0} status={this.props.chatwidget.getIn(['chatStatusData','result'])} /></div>}

                    <div className={msg_expand + (this.props.chatwidget.hasIn(['chat_ui','after_chat_status']) && this.props.chatwidget.getIn(['chat_ui','after_chat_status']) != '' ? ' has-after-chat-status' : '')} onClick={(e) => {this.setState({'reactToMsgId' : 0})}} id="messagesBlock" onScroll={this.onScrollMessages}>

                        {this.props.chatwidget.hasIn(['chat_ui','after_chat_status']) && this.props.chatwidget.getIn(['chat_ui','after_chat_status']) != '' && <Suspense fallback=""><CustomHTML setStateParent={(state) => this.setState(state)} has_new={this.state.hasNew && this.state.otm > 0} attr="after_chat_status" /></Suspense>}

                        <div className={bottom_messages} id="messages-scroll" style={fontSizeStyle} ref={this.messagesAreaRef}>
                            {this.props.chatwidget.hasIn(['chat_ui','prev_chat']) && <div dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','prev_chat'])}}></div>}
                            {messages}
                            {this.props.chatwidget.hasIn(['chatLiveData','msg_to_store']) && this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).size > 0 && this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).map((msg, index) =>
                                <div data-op-id="0" className="message-row response msg-to-store">
                                    {this.props.chatwidget.hasIn(['chat_ui','show_ts']) && !this.props.chatwidget.hasIn(['chat_ui','show_ts_below']) && <div className="msg-date">&nbsp;</div>}
                                    <div className="msg-body">
                                        {msg.split('\n').map((item, idx) => {return (<React.Fragment key={idx}>{item}<br /></React.Fragment>)})}
                                    </div>
                                    {this.props.chatwidget.hasIn(['chat_ui','show_ts']) && this.props.chatwidget.hasIn(['chat_ui','show_ts_below']) && <div className="msg-date">&nbsp;</div>}
                                </div>)}
                        </div>
                        {this.state.scrollButton && <div className="position-absolute btn-bottom-scroll fade-in" id="id-btn-bottom-scroll"><button type="button" onClick={this.scrollToMessage} className="btn btn-sm btn-secondary">{(this.state.hasNew && this.state.otm > 0 && <div><i className="material-icons">&#xf11a;</i>{this.state.otm} {(this.state.otm == 1 ? (this.props.chatwidget.getIn(['chat_ui','cnew_msg']) || t('button.new_msg')) : (this.props.chatwidget.getIn(['chat_ui','cnew_msgm']) || t('button.new_msgm')))}</div>) || (this.props.chatwidget.getIn(['chat_ui','cscroll_btn']) || t('button.scroll_bottom'))}</button></div>}
                    </div>

                    <div className={(this.props.chatwidget.get('msgLoaded') === false || this.state.enabledEditor === false ? 'd-none ' : 'd-flex ') + "flex-row border-top position-relative message-send-area"} >
                        {(this.props.chatwidget.getIn(['chatLiveData','ott']) || (this.props.chatwidget.getIn(['chatLiveData','error']) && this.props.chatwidget.getIn(['chatLiveData','error']) != 'SEND_CONNECTION') || this.props.chatwidget.get('network_down')) && <div id="id-operator-typing" className="bg-white ps-1">{this.props.chatwidget.getIn(['chatLiveData','error']) ? (this.props.chatwidget.getIn(['chatLiveData','error']).indexOf('SEND_') === -1 ? this.props.chatwidget.getIn(['chatLiveData','error']) : t('online_chat.'+this.props.chatwidget.getIn(['chatLiveData','error']).toLowerCase())) : (this.props.chatwidget.get('network_down') ? t('online_chat.send_connection') : this.props.chatwidget.getIn(['chatLiveData','ott']))}</div>}

                        {this.props.chatwidget.get('mode') == 'embed' && this.props.chatwidget.hasIn(['chat_ui','embed_cls']) && this.props.chatwidget.getIn(['chat_ui','embed_cls']) == 2 && <div className="inline-cls-btn pt-1 ps-2">
                            {this.props.chatwidget.hasIn(['chat_ui','close_btn']) && <a onClick={this.endChat} title={endTitle} ><i className="material-icons settings text-muted me-0">&#xf10a;</i></a>}
                        </div>}

                        <ChatOptions elementId="chat-dropdown-options">
                            <div className="btn-group dropup disable-select ps-1 pt-2">
                                <button type="button" className="border-0 p-0 material-icons settings text-muted" id="chat-dropdown-options" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#xf100;</button>
                                <div className={"dropdown-menu shadow bg-white rounded lhc-dropdown-menu ms-1 "+(window.lhcChat['staticJS']['dir'] == 'rtl' ? "dropdown-menu-end" : "")}>
                                    <div className="d-flex flex-row ps-1">
                                        <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.toggleSound() : '' }} onClick={this.toggleSound} title={t('chat.option_sound')}><i className={"material-icons chat-setting-item text-muted "+(this.props.chatwidget.getIn(['usersettings','soundOn']) === true ? 'sound-on-ico' : 'sound-off-ico')}>{this.props.chatwidget.getIn(['usersettings','soundOn']) === true ? <React.Fragment>&#xf102;</React.Fragment> : <React.Fragment>&#xf101;</React.Fragment>}</i></a>
                                        {this.props.chatwidget.hasIn(['chat_ui','print']) && <a tabIndex="0" target="_blank" href={this.props.chatwidget.get('base_url') + "chat/printchat/" +this.props.chatwidget.getIn(['chatData','id']) + "/" + this.props.chatwidget.getIn(['chatData','hash'])} title={t('button.print')}><i className="material-icons chat-setting-item text-muted print-ico">&#xf10c;</i></a>}
                                        {this.props.chatwidget.hasIn(['chat_ui','dwntxt']) && <a tabIndex="0" target="_blank" href={this.props.chatwidget.get('base_url') + "chat/downloadtxt/" +this.props.chatwidget.getIn(['chatData','id']) + "/" + this.props.chatwidget.getIn(['chatData','hash'])} title={t('button.dwntxt')}><i className="material-icons chat-setting-item text-muted download-ico">&#xf119;</i></a>}
                                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && this.props.chatwidget.hasIn(['chat_ui','file']) && <ChatFileUploader fileOptions={this.props.chatwidget.getIn(['chat_ui','file_options'])} onDrag={this.dragging} dropArea={this.textMessageRef} onCompletion={this.updateMessages} progress={this.setStatusText} base_url={this.props.chatwidget.get('base_url')} chat_id={this.props.chatwidget.getIn(['chatData','id'])} hash={this.props.chatwidget.getIn(['chatData','hash'])} link={true}/>}
                                        
                                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && this.props.chatwidget.getIn(['chatLiveData','status']) == 1 && this.props.chatwidget.hasIn(['chat_ui','voice']) && this.props.chatwidget.getIn(['chat_ui','voice']) === true && <a tabIndex="0" onClick={this.voiceCall} title={t('button.voice')}><i className="material-icons chat-setting-item text-muted voice-ico">&#xf117;</i></a>}
                                        
                                        
                                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && !this.props.chatwidget.hasIn(['chat_ui','bbc_btnh']) && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.toggleModal() : '' }} onClick={this.toggleModal} title={t('button.bb_code')}><i className="material-icons chat-setting-item text-muted bbcode-ico">&#xf104;</i></a>}
                                        {this.props.chatwidget.hasIn(['chat_ui','mail']) && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.mailChat() : '' }} onClick={this.mailChat} title={t('button.mail')} ><i className="material-icons chat-setting-item text-muted mail-ico">&#xf11a;</i></a>}
                                        {this.props.chatwidget.hasIn(['chat_ui','font_size']) && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.changeFont(event) : '' }} onClick={(event) => this.changeFont(event)}><i className="material-icons chat-setting-item text-muted fs-ico">&#xf11d;</i></a>}
                                        {this.props.chatwidget.hasIn(['chat_ui','close_btn']) && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.endChat() : '' }} onClick={this.endChat} title={endTitle} ><i className="material-icons chat-setting-item text-muted close-ico">&#xf10a;</i></a>}
                                        {this.props.chatwidget.hasIn(['chat_ui','lng_btnh']) && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.changeLanguage() : '' }} onClick={this.changeLanguage} title={t('button.lang')} ><i className="material-icons chat-setting-item text-muted lang-ico">&#xf11e;</i></a>}
                                    </div>
                                </div>
                            </div>
                        </ChatOptions>

                        <div className={message_send_style}>
                            {this.props.chatwidget.getIn(['chatLiveData','closed']) && this.props.chatwidget.hasIn(['chat_ui','survey_id']) && <button type="button" onClick={this.goToSurvey} className="w-100 btn btn-success">{t('online_chat.go_to_survey')}</button>}
                            {(!this.props.chatwidget.getIn(['chatLiveData','closed']) || !this.props.chatwidget.hasIn(['chat_ui','survey_id'])) && <textarea onFocus={(e) => {this.setState({'reactToMsgId' : 0})}} onTouchStart={this.scrollBottom} maxLength={this.props.chatwidget.getIn(['chat_ui','max_length'])} onKeyUp={this.keyUp} readOnly={this.props.chatwidget.getIn(['chatLiveData','closed']) || this.props.chatwidget.get('network_down')} id="CSChatMessage" placeholder={placeholder} onKeyDown={this.enterKeyDown} value={!this.props.chatwidget.getIn(['chatLiveData','closed']) ? this.state.value : ''} onChange={this.handleChange} ref={this.textMessageRef} rows="1" className={"ps-0 no-outline form-control rounded-0 form-control rounded-start-0 rounded-end-0 border-0 "+((this.props.chatwidget.get('shown') === true && this.textMessageRef.current && (/\r|\n/.exec(this.state.value) || (this.state.value.length > this.textMessageRef.current.offsetWidth/8.6))) ? 'msg-two-line' : 'msg-one-line')} />}
                        </div>

                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && !this.props.chatwidget.get('network_down') && <div className="disable-select" id="send-button-wrapper">

                                <div className="user-chatwidget-buttons pt-2 pe-1" id="ChatSendButtonContainer">

                                    {this.state.voiceMode === true && <Suspense fallback="..."><VoiceMessage onCompletion={this.updateMessages} progress={this.setStatusText} base_url={this.props.chatwidget.get('base_url')} chat_id={this.props.chatwidget.getIn(['chatData','id'])} hash={this.props.chatwidget.getIn(['chatData','hash'])} maxSeconds={this.props.chatwidget.getIn(['chat_ui','voice_message'])} cancel={this.cancelVoiceRecording} /></Suspense>}

                                    {(!this.props.chatwidget.hasIn(['chatLiveData','msg_to_store']) || this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).size == 0) && this.props.chatwidget.hasIn(['chat_ui','voice_message']) && typeof window.Audio !== "undefined" && this.state.value.length == 0 && this.state.voiceMode === false && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.startVoiceRecording() : '' }} onClick={this.startVoiceRecording} title={t('button.record_voice')}>
                                       <i className="record-icon material-icons text-muted settings me-0">&#xf10b;</i>
                                    </a>}

                                    {(!this.props.chatwidget.hasIn(['chatLiveData','msg_to_store']) || this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).size == 0) && (!this.props.chatwidget.hasIn(['chat_ui','voice_message']) || !(typeof window.Audio !== "undefined") || (this.state.value.length > 0 && this.state.voiceMode === false)) && <a tabIndex="0" onKeyDown={(e) => { if (e.key === "Enter") { e.preventDefault(); this.sendMessage();}}} onClick={this.sendMessage} title={t('button.send_msg')}>
                                       <i className={"send-icon material-icons settings me-0" + (this.state.value.length == 0 ? ' text-muted-light' : ' text-muted')}>&#xf107;</i>
                                    </a>}

                                    {this.props.chatwidget.hasIn(['chatLiveData','msg_to_store']) && this.props.chatwidget.getIn(['chatLiveData','msg_to_store']).size > 0 && <i className="in-progress-icon material-icons text-muted settings me-0">&#xf113;</i>}

                                </div>

                        </div>}



                    </div>
                    </React.Fragment>}

                </React.Fragment>
            );
        }
    }
}

export default withTranslation()(OnlineChat);
