import React, { PureComponent } from 'react';
import { STATUS_CLOSED_CHAT, STATUS_BOT_CHAT, STATUS_SUB_SURVEY_SHOW, STATUS_ACTIVE_CHAT, STATUS_SUB_USER_CLOSED_CHAT, STATUS_SUB_OWNER_CHANGED, STATUS_SUB_CONTACT_FORM  } from "../constants/chat-status";

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

    syncChat() {
        this.props.updateMessages();
    }

    checkStatusChat() {
        this.props.updateStatus();
    }

    componentDidMount() {
        this.syncChat();
        this.checkStatusChat();
        this.setState({'intervalId': setInterval(this.syncChat, this.props.syncInterval), 'intervalCheckStatusId' : setInterval(this.checkStatusChat, this.props.syncInterval)});
    }

    componentDidUpdate(prevProps, prevState) {
        if ((
            this.props.status == STATUS_CLOSED_CHAT ||
            this.props.status_sub == STATUS_SUB_SURVEY_SHOW ||
            this.props.status_sub == STATUS_SUB_USER_CLOSED_CHAT ||
            this.props.status_sub == STATUS_SUB_CONTACT_FORM
        ) && this.state.intervalId) {
            clearInterval(this.state.intervalId);
        } else if (!this.state.intervalId) {
            this.setState({'intervalId': setInterval(this.syncChat, this.props.syncInterval)});
            this.syncChat();
        }

        if ((this.props.status_sub != prevProps.status_sub || this.props.status != prevProps.status) || (this.props.initClose != prevProps.initClose)) {
            this.checkStatusChat();
            clearInterval(this.state.intervalCheckStatusId);
            this.setState({'intervalCheckStatusId' : setInterval(this.checkStatusChat, this.props.syncInterval)});
        }

        if ((this.props.status == STATUS_CLOSED_CHAT || this.props.status == STATUS_BOT_CHAT || this.props.status == STATUS_ACTIVE_CHAT || this.props.status_sub == STATUS_SUB_SURVEY_SHOW) && this.state.intervalCheckStatusId) {
            clearInterval(this.state.intervalCheckStatusId);
        }
    }

    componentWillUnmount() {
        if (this.state.intervalId) {
            clearInterval(this.state.intervalId);
        }

        if (this.state.intervalCheckStatusId) {
            clearInterval(this.state.intervalCheckStatusId);
        }
    }

    render() {
        return null;
    }
}

export default ChatSync;
