import React, { Component } from 'react';
import { connect } from "react-redux";
import parse from 'html-react-parser';

import { initChatUI, fetchMessages, addMessage, checkChatStatus, endChat, userTyping} from "../actions/chatActions"

import ChatMessage from './ChatMessage';
import ChatModal from './ChatModal';
import ChatFileUploader from './ChatFileUploader';
import ChatSync from './ChatSync';
import ChatOptions from './ChatOptions';
import ChatStatus from './ChatStatus';
import { helperFunctions } from "../lib/helperFunctions";

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class OnlineChat extends Component {

    state = {
        value: '',
        showBBCode : null,
        dragging : false,
        enabledEditor : true
    };


    constructor(props) {
        super(props);

        // Init offline form with all attributes
        this.props.dispatch(initChatUI({
            'id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'theme' : this.props.chatwidget.get('theme')
        }));

        this.enterKeyDown = this.enterKeyDown.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
        this.endChat = this.endChat.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.setStatusText = this.setStatusText.bind(this);
        this.dragging = this.dragging.bind(this);
        this.scrollBottom = this.scrollBottom.bind(this);
        this.focusMessage = this.focusMessage.bind(this);
        this.setEditorEnabled = this.setEditorEnabled.bind(this);
        this.sendDelay = this.sendDelay.bind(this);
        this.unhideDelayed = this.unhideDelayed.bind(this);
        this.toggleSound = this.toggleSound.bind(this);

        // Messages Area
        this.messagesAreaRef = React.createRef();
        this.textMessageRef = React.createRef();

        this.updateMessages = this.updateMessages.bind(this);
        this.updateStatus = this.updateStatus.bind(this);
        this.updateMetaAutoHide = this.updateMetaAutoHide.bind(this);
        this.setMetaUpdateState = this.setMetaUpdateState.bind(this);
        this.keyUp = this.keyUp.bind(this);

        this.delayed = false;
        this.delayQueue = [];
        this.intervalPending = null;
        this.pendingMetaUpdate = false;

        this.isTyping = false;
        this.typingStopped = null;
        this.typingStoppedAction = this.typingStoppedAction.bind(this);
        this.currentMessageTyping = '';
    }

    dragging(status) {
        this.setState({dragging : status})
    }

    setStatusText(text){
        this.props.dispatch({
            'type': 'chat_status_changed',
            'data' : {text: text}
        });
    }

    handleChange(event) {
        this.setState({value: event.target.value});

        if (sessionStorage) {
            try {
                sessionStorage.setItem('lhc_ttxt', event.target.value);
            } catch(e) {}
        };
    }

    componentDidMount() {
        if (sessionStorage && sessionStorage.getItem('lhc_ttxt') !== null) {
            this.setState({value: sessionStorage.getItem('lhc_ttxt')})
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
            this.updateMetaAutoHide();
            this.scrollBottom();
        }

        if (state === true) {
            this.pendingMetaUpdate = true;
            this.updateMetaAutoHide();
            this.scrollBottom();
        }

    }

    updateMetaAutoHide(){
        var block = document.getElementById('messages-scroll');
        var x = block.getElementsByClassName("meta-auto-hide");
        if (x.length > 0) {
            var i;
            for (i = 0; i < x.length - 1; i++) {
                x[i].style.display = 'none';
            }
            var lastChild = block.lastChild;

            if (lastChild) {
                x = lastChild.getElementsByClassName("meta-auto-hide");
                var i;
                for (i = 0; i < x.length; i++) {
                    x[i].style.display = '';
                }
            }
        }
    }

    nextUntil(htmlElement, match) {
        var nextUntil = [],
            until = true;
        while (htmlElement = htmlElement.nextElementSibling) {
            (until && htmlElement && !htmlElement.matches(match)) ? nextUntil.push(htmlElement) : until = false;
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

        if (untillMessage == true && this.nextUntil(msg,'message-admin').length > 0) {
            return;
        }

        setTimeout( () => {
            if (this.delayed == false) {
                if (untillMessage == true) {
                    clearInterval(this.intervalPending);
                    this.intervalPending = setInterval(() => {
                        if (this.nextUntil(msg,'message-admin').length > 0) {
                            this.unhideDelayed(id);
                            clearInterval(this.intervalPending);
                        } else {
                            if (!this.hasClass(msg,'meta-hider'))
                            {
                                this.addClass(msg,'meta-hider');
                                this.addClass(msg,'message-row-typing');
                                this.removeClass(msg,'hide');

                                var elementsBody = msg.getElementsByClassName("msg-body");

                                for (var item of elementsBody) {
                                    this.removeClass(item, 'hide');
                                }

                                this.scrollBottom();
                            }
                        }
                    },500);
                } else {
                    this.delayed = true;

                    this.addClass(msg,'meta-hider');
                    this.addClass(msg,'message-row-typing');

                    this.nextUntil(msg,'meta-hider').forEach((item) => {
                        this.addClass(item,'hide');
                    });

                    setTimeout(() => {
                        this.unhideDelayed(id);
                    }, duration * 1000);

                    this.removeClass(msg,'hide');

                    var elementsBody = msg.getElementsByClassName("msg-body");

                    for (var item of elementsBody) {
                        this.removeClass(item, 'hide');
                    }

                    if (delay > 0) {
                        this.updateMetaAutoHide();
                        this.scrollBottom();
                    }
                }

            } else {
                this.addClass(msg,'message-row-typing');
                this.delayQueue.push({'id' : id, 'delay' : duration});
            }
        },delay*1000);
    }

    unhideDelayed(id) {

        var msg = document.getElementById('msg-'+id);

        if (!msg) {
            return;
        }

        this.nextUntil(msg,'meta-hider').forEach((item) => {
            this.removeClass(item,'hide');
        });

        msg.parentNode.removeChild(msg);

        this.updateMetaAutoHide();
        this.scrollBottom();

        if (this.delayQueue.length > 0) {
            var data = this.delayQueue.pop();
            setTimeout(() => {
                this.unhideDelayed(data.id);
            }, data.delay * 1000);

            var messageBlock = document.getElementById('msg-'+data.id);
            this.removeClass(messageBlock,'hide');

            var elementsBody = messageBlock.getElementsByClassName("msg-body");

            for (var item of elementsBody) {
                this.removeClass(item, 'hide');
            }

        } else {
            this.delayed = false;
        }
    }

    componentWillUnmount() {
        clearInterval(this.intervalPending);
        clearInterval(this.typingStopped);
    }

    // https://reactjs.org/blog/2018/03/27/update-on-async-rendering.html
    getSnapshotBeforeUpdate(prevProps, prevState) {
        // Are we adding new item
        if (prevProps.chatwidget.getIn(['chatLiveData','messages']).size != this.props.chatwidget.getIn(['chatLiveData','messages']).size) {
            if (prevProps.chatwidget.getIn(['chatLiveData','messages']).size != 0 && this.props.chatwidget.getIn(['chatLiveData','uw']) === false) {
                helperFunctions.emitEvent('play_sound', [{'type' : 'new_message','sound_on' : (this.props.chatwidget.getIn(['usersettings','soundOn']) === true), 'widget_open' : ((this.props.chatwidget.get('shown') && this.props.chatwidget.get('mode') == 'widget') || document.hasFocus())}]);
            }

            if (this.messagesAreaRef.current){
                let scrollValue = this.messagesAreaRef.current.scrollHeight - this.messagesAreaRef.current.scrollTop;
                return (
                    (scrollValue)
                );
            }

        // Are we restroing widget visibility
        } else if (prevProps.chatwidget.get('shown') === false && this.props.chatwidget.get('shown') === true) {
            return 0;
        }

        return null;
    }

    componentDidUpdate(prevProps, prevState, snapshot) {

        if (
            (prevState.enabledEditor === false && prevState.enabledEditor != this.state.enabledEditor) ||
            this.props.chatwidget.get('msgLoaded') !== prevProps.chatwidget.get('msgLoaded')
        ) {
            this.scrollBottom();
            this.focusMessage();
        }

        if (snapshot !== null) {
            if (this.messagesAreaRef.current) {
                this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight - snapshot;
            }
        }
    }

    scrollBottom() {
        if (this.messagesAreaRef.current) {
            this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight + 1000;
            setTimeout(() => {
                this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight + 1000;
            },450);
        }
    }

    updateMessages() {
        this.props.dispatch(fetchMessages({
            'chat_id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'lmgsid' : this.props.chatwidget.getIn(['chatLiveData','lmsgid']),
            'theme' : this.props.chatwidget.get('theme'),
            'new_chat' : this.props.chatwidget.get('newChat')
        }));
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

        if (sessionStorage && sessionStorage.getItem('lhc_ttxt') !== null) {
            sessionStorage.setItem('lhc_ttxt','');
        }

        this.props.dispatch(addMessage({
            'id': this.props.chatwidget.getIn(['chatData','id']),
            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
            'msg' : this.state.value,
            'lmgsid' : this.props.chatwidget.getIn(['chatLiveData','lmsgid'])
        }));
        this.setState({value: ''});
        this.currentMessageTyping = '';
        this.focusMessage();
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
                this.isTyping = true;
                this.props.dispatch(userTyping('true',this.state.value));
            } else {
                clearTimeout(this.typingStopped);
                this.typingStopped = setTimeout(this.typingStoppedAction, 3000);

                if (this.currentMessageTyping != this.state.value ) {
                    if ( Math.abs(this.currentMessageTyping.length - this.state.value.length) > 6) {
                        this.currentMessageTyping = this.state.value;
                        this.props.dispatch(userTyping('true',this.state.value));
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
        this.props.endChat();
    }

    toggleModal() {
        this.setState({
            showBBCode: !this.state.showBBCode
        });

        if (this.state.showBBCode) {
            this.focusMessage()
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

        if (this.props.chatwidget.get('initLoaded') === false) {
            return null;
        }

        if (((this.props.chatwidget.hasIn(['chatLiveData','status_sub']) && (this.props.chatwidget.getIn(['chatLiveData','status_sub']) == 5 || this.props.chatwidget.getIn(['chatLiveData','status_sub']) == 3)) || (this.props.chatwidget.getIn(['chatLiveData','status']) == 2)) && this.props.chatwidget.hasIn(['chat_ui','survey_id'])) {
            var location = this.props.chatwidget.get('base_url') + "/survey/fillwidget/(chatid)/" + this.props.chatwidget.getIn(['chatData', 'id']) + "/(hash)/" + this.props.chatwidget.getIn(['chatData', 'hash']);

            if (this.props.chatwidget.get('theme')) {
                location = location + '/(theme)/' + this.props.chatwidget.get('theme');
            }

            location = location + '/(survey)/' + this.props.chatwidget.getIn(['chat_ui', 'survey_id']);

            return (
                <React.Fragment>
                    <iframe allowtransparency="true" src={location} frameBorder="0" className="flex-grow-1 position-relative iframe-modal"/>
                </React.Fragment>
            )

        } else if (this.props.chatwidget.hasIn(['chatLiveData','ru']) && this.props.chatwidget.getIn(['chatLiveData','ru'])) {

            location = this.props.chatwidget.get('base_url') + this.props.chatwidget.getIn(['chatLiveData','ru']);

            return (
                <React.Fragment>
                    <iframe allowtransparency="true" src={location} frameBorder="0" className="flex-grow-1 position-relative iframe-modal"/>
                </React.Fragment>
            )

        } else {

            if (this.props.chatwidget.get('chatLiveData').has('messages')) {
                var messages = this.props.chatwidget.getIn(['chatLiveData','messages']).map((msg, index) =><ChatMessage setMetaUpdateState={this.setMetaUpdateState} sendDelay={this.sendDelay} setEditorEnabled={this.setEditorEnabled} updateStatus={this.updateStatus} focusMessage={this.focusMessage} updateMessages={this.updateMessages} scrollBottom={this.scrollBottom} id={index} key={'msg_'+index} msg={msg} />);
            } else {
                var messages = "";
            }

            var placeholder = '';
            if (this.state.dragging === true) {
                placeholder = 'Drop your files here.';
            } else if (this.props.chatwidget.getIn(['chatLiveData','closed'])) {
                placeholder = 'This chat is closed now. You can close window.';
            } else {
                placeholder = this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : "Type your message here...";
            }

            return (
                <React.Fragment>

                    <ChatSync syncInterval={this.props.chatwidget.getIn(['chat_ui','sync_interval'])} updateStatus={this.updateStatus} updateMessages={this.updateMessages} initClose={this.props.chatwidget.get('initClose')} dispatch={this.props.dispatch} status_sub={this.props.chatwidget.getIn(['chatLiveData','status_sub'])} status={this.props.chatwidget.getIn(['chatLiveData','status'])} theme={this.props.chatwidget.get('theme')} lmgsid={this.props.chatwidget.getIn(['chatLiveData','lmsgid'])} hash={this.props.chatwidget.getIn(['chatData','hash'])} chat_id={this.props.chatwidget.getIn(['chatData','id'])} />

                    {this.state.showBBCode && <ChatModal showModal={this.state.showBBCode} insertText={this.insertText} toggle={this.toggleModal} dataUrl={"/chat/bbcodeinsert?react=1"} />}

                    {this.props.chatwidget.hasIn(['chatStatusData','result']) && !this.props.chatwidget.hasIn(['chat_ui','hide_status']) && <div className="p-1"><ChatStatus updateStatus={this.updateStatus} status={this.props.chatwidget.getIn(['chatStatusData','result'])} /></div>}

                    <div className="flex-grow-1 overflow-scroll position-relative" id="messagesBlock">
                        <div className="bottom-message pl-1 pr-1" id="messages-scroll" ref={this.messagesAreaRef}>
                            {messages}
                        </div>
                    </div>

                    {(this.props.chatwidget.getIn(['chatLiveData','ott']) || this.props.chatwidget.getIn(['chatLiveData','error'])) && <div id="id-operator-typing">{this.props.chatwidget.getIn(['chatLiveData','error']) || this.props.chatwidget.getIn(['chatLiveData','ott'])}</div>}

                    <div className={(this.props.chatwidget.get('msgLoaded') === false || this.state.enabledEditor === false ? 'd-none ' : 'd-flex ') + "flex-row border-top position-relative message-send-area"} >
                        <ChatOptions elementId="chat-dropdown-options">
                            <div className="btn-group dropup pt-1 disable-select pl-2 pt-2">
                                <i className="material-icons settings text-muted" id="chat-dropdown-options" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">settings</i>
                                <div className="dropdown-menu shadow bg-white rounded">
                                    <div className="d-flex flex-row">
                                        <a href="#" onClick={this.toggleSound} title="Enable/Disable sound about new messages from the operator"><i className="material-icons chat-setting-item text-muted">{this.props.chatwidget.getIn(['usersettings','soundOn']) === true ? 'volume_up' : 'volume_off'}</i></a>
                                        {this.props.chatwidget.hasIn(['chat_ui','print']) && <a target="_blank" href={this.props.chatwidget.get('base_url') + "/chat/printchat/" +this.props.chatwidget.getIn(['chatData','id']) + "/" + this.props.chatwidget.getIn(['chatData','hash'])} title="Print"><i className="material-icons chat-setting-item text-muted">print</i></a>}
                                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && this.props.chatwidget.hasIn(['chat_ui','file']) && <ChatFileUploader fileOptions={this.props.chatwidget.getIn(['chat_ui','file_options'])} onDrag={this.dragging} dropArea={this.textMessageRef} onCompletion={this.updateMessages} progress={this.setStatusText} base_url={this.props.chatwidget.get('base_url')} chat_id={this.props.chatwidget.getIn(['chatData','id'])} hash={this.props.chatwidget.getIn(['chatData','hash'])} link={true}/>}
                                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && <a href="#" onClick={this.toggleModal} title="BB Code"><i className="material-icons chat-setting-item text-muted">face</i></a>}
                                        {this.props.chatwidget.hasIn(['chat_ui','close_btn']) && <a onClick={this.endChat} href="#" title="End the chat"><i className="material-icons chat-setting-item text-muted">close</i></a>}
                                    </div>
                                </div>
                            </div>
                        </ChatOptions>

                        <div className="mx-auto pb-1 w-100">
                            <textarea onFocus={this.scrollBottom} onTouchStart={this.scrollBottom} autoFocus={true} aria-label="Type your message here..." onKeyUp={this.keyUp} readOnly={this.props.chatwidget.getIn(['chatLiveData','closed'])} id="CSChatMessage" placeholder={placeholder} onKeyDown={this.enterKeyDown} value={this.state.value} onChange={this.handleChange} ref={this.textMessageRef} rows="1" className="pl-0 no-outline form-control rounded-0 form-control border-left-0 border-right-0 border-0" />
                        </div>

                        {!this.props.chatwidget.getIn(['chatLiveData','closed']) && <div className="disable-select">
                            <div className="user-chatwidget-buttons" id="ChatSendButtonContainer">
                                <a onClick={this.sendMessage} title="Send">
                                   <i className="material-icons text-muted settings">send</i>
                                </a>
                            </div>
                        </div>}
                    </div>

                </React.Fragment>
            );
        }
    }
}

export default OnlineChat;
