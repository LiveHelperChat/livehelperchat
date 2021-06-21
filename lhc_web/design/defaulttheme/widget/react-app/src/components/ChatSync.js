import React, { PureComponent } from 'react';
import { STATUS_CLOSED_CHAT, STATUS_BOT_CHAT, STATUS_SUB_SURVEY_SHOW, STATUS_ACTIVE_CHAT, STATUS_SUB_USER_CLOSED_CHAT, STATUS_SUB_OWNER_CHANGED, STATUS_SUB_CONTACT_FORM  } from "../constants/chat-status";
import { helperFunctions } from "../lib/helperFunctions";

class ChatSync extends PureComponent {

    state = {
        intervalId : null,
        intervalCheckStatusId : null
    };

    constructor(props) {
        super(props);
        this.syncChat = this.syncChat.bind(this);
        this.checkStatusChat = this.checkStatusChat.bind(this);
    }

    syncChat(issueUpdate) {

        if (this.state.intervalId) {
            clearTimeout(this.state.intervalId);
        }

        this.props.updateMessages();
        this.setState({'intervalId': setTimeout(this.syncChat,this.props.syncInterval)});
    }

    checkStatusChat() {

        if (this.state.intervalCheckStatusId) {
            clearTimeout(this.state.intervalCheckStatusId);
        }

        this.props.updateStatus();
        this.setState({'intervalCheckStatusId': setTimeout(this.checkStatusChat, this.props.syncInterval)});
    }

    componentDidMount() {
        this.syncChat();
        this.checkStatusChat();
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            this.props.status == STATUS_CLOSED_CHAT ||
            this.props.status_sub == STATUS_SUB_SURVEY_SHOW ||
            this.props.status_sub == STATUS_SUB_USER_CLOSED_CHAT ||
            this.props.status_sub == STATUS_SUB_CONTACT_FORM
        ) {
            if (this.state.intervalId) {
                clearTimeout(this.state.intervalId);
                this.state.intervalId = null;
            }
            helperFunctions.sendMessageParent('chatClosed', []);
        } else if (!this.state.intervalId) {
            this.syncChat();
        }

        if ((this.props.status_sub != prevProps.status_sub || this.props.status != prevProps.status) || (this.props.initClose != prevProps.initClose) || (prevProps.hasSurvey != this.props.hasSurvey)) {
            this.checkStatusChat();
        }

        if ((this.props.status == STATUS_CLOSED_CHAT || this.props.status == STATUS_BOT_CHAT || this.props.status == STATUS_ACTIVE_CHAT || this.props.status_sub == STATUS_SUB_SURVEY_SHOW) && this.state.intervalCheckStatusId && this.props.hasSurvey == false) {
            clearTimeout(this.state.intervalCheckStatusId);
            this.state.intervalCheckStatusId = null;
        }
    }

    componentWillUnmount() {
        if (this.state.intervalId) {
            clearTimeout(this.state.intervalId);
        }

        if (this.state.intervalCheckStatusId) {
            clearTimeout(this.state.intervalCheckStatusId);
        }
    }

    render() {
        return null;
    }
}

export default ChatSync;
